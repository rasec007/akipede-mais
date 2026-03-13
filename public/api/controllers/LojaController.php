<?php
// api/controllers/LojaController.php
require_once __DIR__ . '/../config/db.php';

class LojaController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM loja WHERE id_loja = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM loja WHERE users = ? LIMIT 1");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUrl($url) {
        $stmt = $this->db->prepare("SELECT * FROM loja WHERE url = ? AND status = TRUE");
        $stmt->execute([$url]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getConfiguracoes($id_loja) {
        // No Flutter original, a loja tem cores e logos específicos
        $stmt = $this->db->prepare("SELECT cor, logo, nome, descricao FROM loja WHERE id_loja = ?");
        $stmt->execute([$id_loja]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
