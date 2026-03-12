<?php
// api/config/db.php

class Database {
    private $host = "easypanel2.c2net.com.br";
    private $port = "5435";
    private $db_name = "akipede_orcamento";
    private $username = "akipede_orcamento";
    private $password = "R@sec007k9.,.,";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET client_encoding TO 'UTF8'");
        } catch(PDOException $exception) {
            // Não dar echo aqui para não quebrar o JSON das APIs
            error_log("Erro de conexão: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
