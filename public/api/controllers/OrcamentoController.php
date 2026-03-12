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

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (parceiro, cliente_nome, cliente_cpf_cnpj, cliente_fone, loja, status, validade, valor_total, observacoes, desconto, mes, ano, data_orcamento) 
                  VALUES (:parceiro, :cliente_nome, :cliente_cpf_cnpj, :cliente_fone, :loja, :status, :validade, :valor_total, :observacoes, :desconto, :mes, :ano, :data_orcamento)";
        
        $stmt = $this->db->prepare($query);
        
        // No PostgreSQL, o INSERT disparará o pg_notify via TRIGGER que criamos no schema.sql
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}
?>
