<?php
// Testar quais produtos pertencem ao usuario 'rasec007@gmail.com'
require 'public/api/config/db.php';

try {
    $db = (new Database())->getConnection();
    
    // Pegar o ID do usuario
    $stmtUser = $db->query("SELECT id_users FROM users WHERE email = 'rasec007@gmail.com'");
    $userId = $stmtUser->fetchColumn();
    
    if (!$userId) {
        die("Usuário não encontrado.");
    }
    
    echo "ID do Usuário: $userId\n";
    
    // Consulta otimizada
    $query = "
        SELECT p.nome, p.ativo, l.nome as loja_nome 
        FROM produto p
        INNER JOIN loja l ON p.loja = l.id_loja
        WHERE l.users = :user_id
        ORDER BY p.dt_criado DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Produtos encontrados (" . count($produtos) . "):\n";
    foreach ($produtos as $p) {
        echo "- " . $p['nome'] . " (Ativo: " . $p['ativo'] . ") - Loja: " . $p['loja_nome'] . "\n";
    }
    
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage();
}
