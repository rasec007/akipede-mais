<?php
// api/controllers/ClienteController.php
require_once __DIR__ . '/../config/db.php';

class ClienteController {
    private $db;
    private $table_name = "cliente";

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
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, apelido, fone, email, foto, loja, uuid_api) 
                  VALUES (:nome, :apelido, :fone, :email, :foto, :loja, :uuid_api)";
        $stmt = $this->db->prepare($query);
        foreach ($data as $key => &$val) {
            $stmt->bindParam(":$key", $val);
        }
        return $stmt->execute();
    }

    public function update($id, $data) {
        $fields = "";
        foreach ($data as $key => $val) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");
        $query = "UPDATE " . $this->table_name . " SET $fields WHERE id_cliente = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        foreach ($data as $key => &$val) {
            $stmt->bindParam(":$key", $val);
        }
        return $stmt->execute();
    }
}
?>
