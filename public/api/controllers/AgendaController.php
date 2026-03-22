<?php
// api/controllers/AgendaController.php
require_once __DIR__ . '/../config/db.php';

class AgendaController {
    private $db;
    private $table_name = "agenda_produto";

    public function __construct($db) {
        $this->db = $db;
    }

    public function getByProduto($produto_id) {
        $query = "
            SELECT a.*, o.cliente_nome, o.quantidade 
            FROM " . $this->table_name . " a
            LEFT JOIN orcamento_item o ON a.orcamento = o.orcamento AND o.produto = a.produto
            WHERE a.produto = :produto_id
            ORDER BY a.data_inicio ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
