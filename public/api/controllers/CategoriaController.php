<?php
// api/controllers/CategoriaController.php
require_once __DIR__ . '/../config/db.php';

class CategoriaController {
    private $db;
    private $table_name = "categoria";

    public function __construct($db) {
        $this->db = $db;
    }

    public function read($loja_id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($loja_id) {
            $query .= " WHERE loja = :loja_id";
        }
        $query .= " ORDER BY nome ASC";

        $stmt = $this->db->prepare($query);
        if ($loja_id) {
            $stmt->bindParam(':loja_id', $loja_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (nome, loja) VALUES (:nome, :loja) RETURNING id_categoria";
        $stmt = $this->db->prepare($query);
        
        $nome = $data['nome'] ?? '';
        $loja = $data['loja'] ?? ($data['id_loja'] ?? null);
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':loja', $loja);
        
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id_categoria'];
        }
        return false;
    }
}
?>
