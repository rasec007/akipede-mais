<?php
require_once __DIR__ . '/api/config/db.php';
$db = (new Database())->getConnection();

function getColumns($db, $table) {
    $stmt = $db->prepare("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = :table ORDER BY ordinal_position");
    $stmt->execute(['table' => $table]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Listar tabelas
$stmt = $db->query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

$result = [];
foreach ($tables as $table) {
    $result[$table] = getColumns($db, $table);
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
