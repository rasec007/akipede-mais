<?php
// api/controllers/OrcamentoController.php
require_once __DIR__ . '/../config/db.php';

class OrcamentoController {
    private $db;
    private $table_name = "orcamento";

    public function __construct($db) {
        $this->db = $db;
    }

    public function read($loja_id = null) {
        $query = "SELECT o.*, 
                         c.nome AS cliente_nome_real,
                         o.cliente_nome AS cliente_id,
                         u.nome AS parceiro_nome
                  FROM " . $this->table_name . " o
                  LEFT JOIN cliente c ON o.cliente_nome::text = c.id_cliente::text
                  LEFT JOIN users u ON o.parceiro::text = u.id_users::text";
        if ($loja_id) {
            $query .= " WHERE o.loja = :loja_id";
        }
        $query .= " ORDER BY o.dt_criado DESC";

        $stmt = $this->db->prepare($query);
        if ($loja_id) {
            $stmt->bindParam(':loja_id', $loja_id);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Substituir cliente_nome pelo nome real do cliente
        foreach ($results as &$row) {
            if (!empty($row['cliente_nome_real'])) {
                $row['cliente_nome'] = $row['cliente_nome_real'];
            }
            unset($row['cliente_nome_real']);
        }
        
        return $results;
    }

    public function create($data) {
        try {
            // Extrair itens para inserção posterior
            $itens = $data['itens'] ?? [];
            unset($data['itens']);

            $query = "INSERT INTO " . $this->table_name . " 
                      (numero_sequencial, parceiro, cliente_nome, cliente_cpf_cnpj, cliente_fone, loja, status, validade, valor_total, observacoes, desconto, mes, ano, data_orcamento, data_inicio, data_fim) 
                      VALUES (:numero_sequencial, :parceiro, :cliente_nome, :cliente_cpf_cnpj, :cliente_fone, :loja, :status, :validade, :valor_total, :observacoes, :desconto, :mes, :ano, :data_orcamento, :data_inicio, :data_fim)
                      RETURNING id_orcamento";
            
            $stmt = $this->db->prepare($query);

            if ($stmt->execute($data)) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $orcamento_id = $result['id_orcamento'];

                // Inserir os itens na tabela orcamento_item
                foreach ($itens as $item) {
                    $queryItem = "INSERT INTO orcamento_item 
                                  (orcamento, produto, quantidade, valor_unitario, valor_total, data_inicio, data_fim) 
                                  VALUES (:orcamento, :produto, :quantidade, :valor_unitario, :valor_total, :data_inicio, :data_fim)";
                    $stmtItem = $this->db->prepare($queryItem);
                    
                    $valor_total_item = (float)$item['quantidade'] * (float)$item['valor_unitario'];
                    $paramsItem = [
                        ':orcamento' => $orcamento_id,
                        ':produto' => $item['produto_id'],
                        ':quantidade' => $item['quantidade'],
                        ':valor_unitario' => $item['valor_unitario'],
                        ':valor_total' => $valor_total_item,
                        ':data_inicio' => $data['data_inicio'],
                        ':data_fim' => $data['data_fim']
                    ];
                    $stmtItem->execute($paramsItem);
                }

                return $orcamento_id;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getDisponibilidade($produto_id, $inicio, $fim) {
        try {
            // Adicionar 24h ao fim
            $fimDate = new DateTime($fim);
            $fimDate->modify('+24 hours');
            $fimPlus24 = $fimDate->format('Y-m-d H:i:s');
            
            // Query para somar quantidades em orcamento_item que sobrepõem o período
            // Sobreposição: (start1 <= end2) AND (end1 >= start2)
            $query = "SELECT SUM(quantidade) as total_reservado 
                      FROM orcamento_item 
                      WHERE produto::text = :produto_id 
                      AND status = 'APROVADO'
                      AND (data_inicio <= :fim_24 AND data_fim >= :inicio)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':produto_id', $produto_id);
            $stmt->bindParam(':inicio', $inicio);
            $stmt->bindParam(':fim_24', $fimPlus24);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalReservado = (float)($row['total_reservado'] ?? 0);
            
            // Buscar a qtd_atual do produto
            $queryProd = "SELECT qtd_atual FROM produto WHERE id_produto::text = :produto_id";
            $stmtProd = $this->db->prepare($queryProd);
            $stmtProd->bindParam(':produto_id', $produto_id);
            $stmtProd->execute();
            $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);
            $qtdAtual = (float)($prod['qtd_atual'] ?? 0);
            
            // Cálculo do usuário: Somar reservas - Qtd Atual
            return $totalReservado - $qtdAtual;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getItens($orcamento_id) {
        $query = "SELECT oi.*, p.nome as nome_produto FROM orcamento_item oi JOIN produto p ON oi.produto = p.id_produto WHERE oi.orcamento = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $orcamento_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id_orcamento = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $this->db->prepare("DELETE FROM orcamento_item WHERE orcamento = :id")->execute([':id' => $id]);
        $stmt = $this->db->prepare("DELETE FROM " . $this->table_name . " WHERE id_orcamento = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function update($id, $data) {
        try {
            $itens = $data['itens'] ?? [];
            
            $query = "UPDATE " . $this->table_name . " 
                      SET cliente_nome = :cliente_nome, validade = :validade, valor_total = :valor_total, observacoes = :observacoes, desconto = :desconto, data_inicio = :data_inicio, data_fim = :data_fim 
                      WHERE id_orcamento = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':cliente_nome' => $data['cliente_nome'],
                ':validade' => !empty($data['validade']) ? $data['validade'] : null,
                ':valor_total' => $data['valor_total'],
                ':observacoes' => $data['observacoes'],
                ':desconto' => $data['desconto'] ?? 0,
                ':data_inicio' => !empty($data['data_inicio']) ? $data['data_inicio'] : null,
                ':data_fim' => !empty($data['data_fim']) ? $data['data_fim'] : null,
                ':id' => $id
            ]);

            // Delete and re-insert items
            $this->db->prepare("DELETE FROM orcamento_item WHERE orcamento = :id")->execute([':id' => $id]);

            foreach ($itens as $item) {
                $queryItem = "INSERT INTO orcamento_item 
                              (orcamento, produto, quantidade, valor_unitario, valor_total, data_inicio, data_fim) 
                              VALUES (:orcamento, :produto, :quantidade, :valor_unitario, :valor_total, :data_inicio, :data_fim)";
                $stmtItem = $this->db->prepare($queryItem);
                $stmtItem->execute([
                    ':orcamento' => $id,
                    ':produto' => $item['produto_id'],
                    ':quantidade' => $item['quantidade'],
                    ':valor_unitario' => $item['valor_unitario'],
                    ':valor_total' => $item['quantidade'] * $item['valor_unitario'],
                    ':data_inicio' => !empty($data['data_inicio']) ? $data['data_inicio'] : null,
                    ':data_fim' => !empty($data['data_fim']) ? $data['data_fim'] : null
                ]);
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>
