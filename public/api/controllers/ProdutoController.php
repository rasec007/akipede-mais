<?php
// api/controllers/ProdutoController.php
require_once __DIR__ . '/../config/db.php';

class ProdutoController {
    private $db;
    private $table_name = "produto";

    public function __construct($db) {
        $this->db = $db;
    }

    public function read($loja_id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($loja_id) {
            $query .= " WHERE loja = :loja_id";
        }
        $query .= " ORDER BY dt_criado DESC";

        $stmt = $this->db->prepare($query);
        if ($loja_id) {
            $stmt->bindParam(':loja_id', $loja_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByUser($user_id) {
        $query = "
            SELECT p.* 
            FROM " . $this->table_name . " p
            INNER JOIN loja l ON p.loja = l.id_loja
            WHERE l.users = :user_id
            ORDER BY p.nome ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastError() {
        $errorInfo = $this->db->errorInfo();
        return $errorInfo[2] ?? "Erro desconhecido";
    }

    public function create($data) {
        $defaults = [
            'nome' => '',
            'loja' => null,
            'descricao' => '',
            'valor_venda' => 0,
            'valor_promocional' => null,
            'valor_custo' => null,
            'ativo' => true,
            'cod_produto' => '',
            'foto' => '',
            'categoria' => null,
            'agendamento' => 0,
            'mostar_valor' => true,
            'qtd_minima' => 0,
            'qtd_atual' => 0
        ];

        $finalData = array_merge($defaults, $data);

        // Sanitize UUIDs and numbers
        if (empty($finalData['categoria'])) $finalData['categoria'] = null;
        if (empty($finalData['loja'])) $finalData['loja'] = null;
        
        // Convert prices to numeric safely
        foreach(['valor_venda', 'valor_promocional', 'valor_custo'] as $priceField) {
            if (isset($finalData[$priceField]) && !is_null($finalData[$priceField])) {
                $val = $finalData[$priceField];
                // Só limpa se for uma string contendo vírgula ou símbolo de moeda
                if (is_string($val) && (strpos($val, ',') !== false || strpos($val, 'R$') !== false)) {
                    $val = str_replace(['R$', ' ', '.'], '', $val);
                    $val = str_replace(',', '.', $val);
                }
                $finalData[$priceField] = (float)$val;
            }
        }

        // Convert booleans for PgSQL
        $finalData['ativo'] = filter_var($finalData['ativo'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        $finalData['mostar_valor'] = filter_var($finalData['mostar_valor'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';

        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, loja, descricao, valor_venda, valor_promocional, valor_custo, ativo, cod_produto, foto, categoria, agendamento, mostar_valor, qtd_minima, qtd_atual) 
                  VALUES (:nome, :loja, :descricao, :valor_venda, :valor_promocional, :valor_custo, :ativo, :cod_produto, :foto, :categoria, :agendamento, :mostar_valor, :qtd_minima, :qtd_atual)
                  RETURNING id_produto";
        
        $stmt = $this->db->prepare($query);
        
        foreach (array_keys($defaults) as $key) {
            $stmt->bindValue(":$key", $finalData[$key]);
        }
        
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id_produto'];
        }
        return false;
    }

    public function update($id, $data) {
        $fields = ['nome', 'loja', 'descricao', 'valor_venda', 'valor_promocional', 'valor_custo', 'ativo', 'cod_produto', 'foto', 'categoria', 'agendamento', 'mostar_valor', 'qtd_minima', 'qtd_atual'];
        
        if (isset($data['categoria']) && empty($data['categoria'])) $data['categoria'] = null;
        if (isset($data['loja']) && empty($data['loja'])) $data['loja'] = null;

        $query = "UPDATE " . $this->table_name . " SET ";
        $updates = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "$field = :$field";
            }
        }
        $query .= implode(", ", $updates);
        $query .= " WHERE id_produto = :id";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $val = $data[$field];
                if (in_array($field, ['valor_venda', 'valor_promocional', 'valor_custo']) && $val !== null) {
                    // Só limpa se for uma string formatada
                    if (is_string($val) && (strpos($val, ',') !== false || strpos($val, 'R$') !== false)) {
                        $val = str_replace(['R$', ' ', '.'], '', $val);
                        $val = str_replace(',', '.', $val);
                    }
                    $val = (float)$val;
                }
                if ($field === 'ativo' || $field === 'mostar_valor') {
                    $val = filter_var($val, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
                }
                $stmt->bindValue(":$field", $val);
            }
        }
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $this->lastError = null;
        try {
            // Verificar se o produto está em algum orçamento
            $checkQuery = "SELECT COUNT(*) FROM orcamento_item WHERE produto = :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':id', $id);
            $checkStmt->execute();
            
            if ($checkStmt->fetchColumn() > 0) {
                // Se estiver em orçamento, apenas desativa
                $updateQuery = "UPDATE " . $this->table_name . " SET ativo = false WHERE id_produto = :id";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindParam(':id', $id);
                $updateStmt->execute();
                return 'deactivated';
            } else {
                // Se não estiver em orçamento, deleta fisicamente
                $deleteQuery = "DELETE FROM " . $this->table_name . " WHERE id_produto = :id";
                $deleteStmt = $this->db->prepare($deleteQuery);
                $deleteStmt->bindParam(':id', $id);
                return $deleteStmt->execute();
            }
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
?>
