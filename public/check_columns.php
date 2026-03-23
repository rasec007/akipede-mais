<?php
require_once __DIR__ . '/api/config/db.php';
$db = (new Database())->getConnection();

$table = 'orcamento_item';
$stmt = $db->prepare("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = :table ORDER BY ordinal_position");
$stmt->execute(['table' => $table]);
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($columns, JSON_PRETTY_PRINT);
