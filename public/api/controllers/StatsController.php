<?php
// api/controllers/StatsController.php
require_once __DIR__ . '/../config/db.php';

class StatsController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDashboardStats($loja_id) {
        return [
            'top_products' => $this->getTopProducts($loja_id),
            'top_orders' => $this->getTopOrders($loja_id),
            'top_clients' => $this->getTopClients($loja_id),
            'top_partners' => $this->getTopPartners($loja_id),
            'summary' => $this->getSummaryStats($loja_id)
        ];
    }

    private function getTopProducts($loja_id) {
        $query = "
            SELECT p.id_produto, p.nome, p.foto, c.nome as categoria_nome, 
                   COUNT(oi.orcamento) as total_pedidos, 
                   SUM(oi.quantidade) as total_vendidos, 
                   SUM(oi.valor_total) as valor_total
            FROM produto p
            LEFT JOIN categoria c ON p.categoria = c.id_categoria
            INNER JOIN orcamento_item oi ON p.id_produto = oi.produto
            INNER JOIN orcamento o ON oi.orcamento = o.id_orcamento
            WHERE UPPER(o.status) = 'APROVADO' AND o.loja = :loja_id
            GROUP BY p.id_produto, p.nome, p.foto, c.nome
            ORDER BY total_vendidos DESC
            LIMIT 10
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':loja_id', $loja_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTopOrders($loja_id) {
        $query = "
            SELECT o.id_orcamento, o.numero_sequencial, o.dt_criado, 
                   COALESCE(c.nome, o.cliente_nome) as cliente_nome, 
                   o.cliente_fone, o.cliente_cpf_cnpj, o.valor_total, o.status, u.email as parceiro_email
            FROM orcamento o
            LEFT JOIN users u ON o.parceiro = u.id_users
            LEFT JOIN cliente c ON o.cliente_nome::text = c.id_cliente::text
            WHERE o.loja = :loja_id
            ORDER BY o.dt_criado DESC
            LIMIT 10
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':loja_id', $loja_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTopClients($loja_id) {
        $currentYear = date('Y');
        $query = "
            SELECT COALESCE(cl.nome, o.cliente_nome) as nome, o.cliente_fone as fone, o.cliente_cpf_cnpj as email, 
                   COUNT(o.id_orcamento) as total_pedidos, 
                   SUM(o.valor_total) as valor_total,
                   CASE WHEN COALESCE(cl.ativo, true) THEN 'ATIVO' ELSE 'INATIVO' END as status
            FROM orcamento o
            LEFT JOIN cliente cl ON o.cliente_nome::text = cl.id_cliente::text
            WHERE UPPER(o.status) = 'APROVADO' AND o.loja = :loja_id AND EXTRACT(YEAR FROM o.dt_criado) = :year
            GROUP BY cl.nome, o.cliente_nome, o.cliente_fone, o.cliente_cpf_cnpj, cl.ativo
            ORDER BY valor_total DESC
            LIMIT 10
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':loja_id', $loja_id);
        $stmt->bindValue(':year', $currentYear);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTopPartners($loja_id) {
        $query = "
            SELECT c.nome, c.fone, c.email, c.foto,
                   COUNT(o.id_orcamento) as total_pedidos, 
                   SUM(o.valor_total) as valor_total,
                   CASE WHEN c.ativo THEN 'ATIVO' ELSE 'INATIVO' END as status
            FROM orcamento o
            INNER JOIN cliente c ON o.parceiro::text = c.id_cliente::text
            WHERE UPPER(o.status) = 'APROVADO' AND o.loja = :loja_id AND c.perfil = 'Parceiro'
            GROUP BY c.id_cliente, c.nome, c.fone, c.email, c.foto, c.ativo
            ORDER BY valor_total DESC
            LIMIT 10
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':loja_id', $loja_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSummaryStats($loja_id) {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM produto WHERE loja = :loja_id) as produtos,
                (SELECT COUNT(*) FROM cliente WHERE loja = :loja_id) as clientes,
                (SELECT COUNT(*) FROM orcamento WHERE loja = :loja_id) as pedidos,
                (SELECT SUM(valor_total) FROM orcamento WHERE loja = :loja_id AND UPPER(status) = 'APROVADO') as valor_vendas,
                (SELECT COUNT(DISTINCT parceiro) FROM orcamento WHERE loja = :loja_id) as parceiros
            FROM (SELECT 1) as dummy
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':loja_id', $loja_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
