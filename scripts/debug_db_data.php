<?php
require_once __DIR__ . '/../public/api/config/db.php';
$db = (new Database())->getConnection();

echo "USUARIOS:\n";
$users = $db->query("SELECT id_users, nome, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
print_r($users);

echo "\nLOJAS:\n";
$lojas = $db->query("SELECT id_loja, nome, users FROM loja")->fetchAll(PDO::FETCH_ASSOC);
print_r($lojas);

echo "\nCATEGORIAS:\n";
$cats = $db->query("SELECT id_categoria, nome, loja FROM categoria")->fetchAll(PDO::FETCH_ASSOC);
print_r($cats);
