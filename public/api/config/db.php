<?php
// api/config/db.php

class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: "easypanel2.c2net.com.br";
        $this->port = getenv('DB_PORT') ?: "5435";
        $this->db_name = getenv('DB_NAME') ?: "akipede_orcamento";
        $this->username = getenv('DB_USER') ?: "akipede_orcamento";
        $this->password = getenv('DB_PASS') ?: "R@sec007k9.,.,";
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET client_encoding TO 'UTF8'");
        } catch(PDOException $exception) {
            error_log("Erro de conexão: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
