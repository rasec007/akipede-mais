<?php
require 'public/api/config/db.php';
$db = (new Database())->getConnection();

$emailToTest = 'rasec007@gmail.com';

echo "Testando email: '$emailToTest'\n";

$query = "SELECT id_users as id, nome, email, senha, foto, perfil FROM users WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $emailToTest);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "USUÁRIO ENCONTRADO!\n";
    print_r($user);
} else {
    echo "USUÁRIO NÃO ENCONTRADO PELA QUERY DO LOGIN.\n";
}
