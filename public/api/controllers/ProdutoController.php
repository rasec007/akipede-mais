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
        // Query altamente performática que junta os produtos com a loja pertinente ao usuário logado
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

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, loja, descricao, valor_venda, valor_promocional, valor_custo, ativo, cod_produto, foto, categoria, agendamento, mostar_valor, qtd_minima, qtd_atual) 
                  VALUES (:nome, :loja, :descricao, :valor_venda, :valor_promocional, :valor_custo, :ativo, :cod_produto, :foto, :categoria, :agendamento, :mostar_valor, :qtd_minima, :qtd_atual)";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => &$val) {
            $stmt->bindParam(":$key", $val);
        }
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
