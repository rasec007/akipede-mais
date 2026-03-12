<?php
// api/controllers/PedidoController.php
require_once __DIR__ . '/../config/db.php';

class PedidoController {
    private $db;
    private $table_name = "pedido";

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
}
?>
