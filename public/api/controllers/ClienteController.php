<?php
// api/controllers/ClienteController.php
require_once __DIR__ . '/../config/db.php';

class ClienteController {
    private $db;
    private $table_name = "cliente";

    private $lastError = "";

    public function __construct($db) {
        $this->db = $db;
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function read($loja_id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($loja_id) {
            $query .= " WHERE loja = :loja_id";
        }
        $query .= " ORDER BY nome ASC";

        $stmt = $this->db->prepare($query);
        if ($loja_id) {
            $stmt->bindParam(':loja_id', $loja_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                      (nome, apelido, email, fone, cpf_cnpj, foto, loja, perfil, ativo, obs, logradouro, num, complemento, bairro, cidade, estado, cep) 
                      VALUES (:nome, :apelido, :email, :fone, :cpf_cnpj, :foto, :loja, :perfil, :ativo, :obs, :logradouro, :num, :complemento, :bairro, :cidade, :estado, :cep)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(':nome', $data['nome'] ?? '');
            $stmt->bindValue(':apelido', $data['apelido'] ?? null);
            $stmt->bindValue(':email', $data['email'] ?? null);
            $stmt->bindValue(':fone', $data['fone'] ?? null);
            $stmt->bindValue(':cpf_cnpj', $data['cpf_cnpj'] ?? ($data['cpf'] ?? null));
            $stmt->bindValue(':foto', $data['foto'] ?? null);
            $stmt->bindValue(':loja', $data['loja'] ?? null);
            $stmt->bindValue(':perfil', $data['perfil'] ?? 'Usuário');
            $stmt->bindValue(':ativo', true, PDO::PARAM_BOOL);
            
            $stmt->bindValue(':obs', $data['obs'] ?? null);
            $stmt->bindValue(':logradouro', $data['logradouro'] ?? null);
            $stmt->bindValue(':num', $data['num'] ?? null);
            $stmt->bindValue(':complemento', $data['complemento'] ?? null);
            $stmt->bindValue(':bairro', $data['bairro'] ?? null);
            $stmt->bindValue(':cidade', $data['cidade'] ?? null);
            $stmt->bindValue(':estado', $data['estado'] ?? null);
            $stmt->bindValue(':cep', $data['cep'] ?? null);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET nome = :nome, apelido = :apelido, email = :email, fone = :fone, 
                          cpf_cnpj = :cpf_cnpj, foto = :foto, perfil = :perfil, ativo = :ativo,
                          obs = :obs, logradouro = :logradouro, num = :num, complemento = :complemento,
                          bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep
                      WHERE id_cliente = :id";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':nome', $data['nome'] ?? '');
            $stmt->bindValue(':apelido', $data['apelido'] ?? null);
            $stmt->bindValue(':email', $data['email'] ?? null);
            $stmt->bindValue(':fone', $data['fone'] ?? null);
            $stmt->bindValue(':cpf_cnpj', $data['cpf_cnpj'] ?? ($data['cpf'] ?? null));
            $stmt->bindValue(':foto', $data['foto'] ?? null);
            $stmt->bindValue(':perfil', $data['perfil'] ?? 'Usuário');
            $stmt->bindValue(':ativo', $data['ativo'] ?? true, PDO::PARAM_BOOL);
            
            $stmt->bindValue(':obs', $data['obs'] ?? null);
            $stmt->bindValue(':logradouro', $data['logradouro'] ?? null);
            $stmt->bindValue(':num', $data['num'] ?? null);
            $stmt->bindValue(':complemento', $data['complemento'] ?? null);
            $stmt->bindValue(':bairro', $data['bairro'] ?? null);
            $stmt->bindValue(':cidade', $data['cidade'] ?? null);
            $stmt->bindValue(':estado', $data['estado'] ?? null);
            $stmt->bindValue(':cep', $data['cep'] ?? null);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id_cliente = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
?>
