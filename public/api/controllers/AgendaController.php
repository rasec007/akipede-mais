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
            SELECT 
                oi.quantidade, 
                oi.valor_total, 
                oi.data_inicio, 
                oi.data_fim,
                o.numero_sequencial, 
                o.cliente_nome, 
                u.nome as parceiro
            FROM orcamento_item oi
            JOIN orcamento o ON oi.orcamento = o.id_orcamento
            LEFT JOIN users u ON o.parceiro::text = u.id_users::text
            WHERE oi.produto::text = :produto_id
            AND o.status = 'APROVADO'
            ORDER BY oi.data_inicio ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
