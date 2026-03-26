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

    public function update($id, $data) {
        try {
            // Validate URL uniqueness if url is provided
            if (!empty($data['url'])) {
                $stmtCheck = $this->db->prepare("SELECT id_loja FROM loja WHERE url = :url AND id_loja != :id");
                $stmtCheck->execute([':url' => $data['url'], ':id' => $id]);
                if ($stmtCheck->rowCount() > 0) {
                    throw new Exception("URL já está em uso por outra loja.");
                }
            }

            $query = "UPDATE loja SET 
                        nome = :nome,
                        cnpj = :cnpj,
                        whatsapp = :whatsapp,
                        cor_tema = :cor_tema,
                        descricao = :descricao,
                        instagram = :instagram,
                        facebook = :facebook,
                        cep = :cep,
                        endereco = :endereco,
                        numero = :numero,
                        complemento = :complemento,
                        bairro = :bairro,
                        cidade = :cidade,
                        estado = :estado,
                        url = :url
                      WHERE id_loja = :id";
                      
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':nome' => $data['nome'] ?? null,
                ':cnpj' => $data['cnpj'] ?? null,
                ':whatsapp' => $data['whatsapp'] ?? null,
                ':cor_tema' => $data['cor_tema'] ?? '#37c6da',
                ':descricao' => $data['descricao'] ?? null,
                ':instagram' => $data['instagram'] ?? null,
                ':facebook' => $data['facebook'] ?? null,
                ':cep' => $data['cep'] ?? null,
                ':endereco' => $data['endereco'] ?? null,
                ':numero' => $data['numero'] ?? null,
                ':complemento' => $data['complemento'] ?? null,
                ':bairro' => $data['bairro'] ?? null,
                ':cidade' => $data['cidade'] ?? null,
                ':estado' => $data['estado'] ?? null,
                ':url' => $data['url'] ?? null,
                ':id' => $id
            ]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>
