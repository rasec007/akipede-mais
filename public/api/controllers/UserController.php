<?php
// api/controllers/UserController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/NotificationService.php';

class UserController {
    private $db;
    private $table_name = "users";

    public function __construct($db) {
        $this->db = $db;
    }

    public function read($loja_id = null) {
        $query = "SELECT id, email, nome, foto, perfil FROM " . $this->table_name;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (email, senha, nome, foto, perfil) VALUES (:email, :senha, :nome, :foto, :perfil)";
        $stmt = $this->db->prepare($query);
        
        $rawData = $data; // Mantém cópia para notificação
        if (isset($data['senha'])) {
            $rawData['raw_password'] = $data['senha'];
            $data['senha'] = password_hash($data['senha'], PASSWORD_BCRYPT);
        }

        foreach ($data as $key => &$val) {
            $stmt->bindParam(":$key", $val);
        }
        
        $success = $stmt->execute();
        if ($success) {
            NotificationService::sendWelcome($rawData, $rawData['perfil'] ?? 'user');
        }
        return $success;
    }
}
