<?php
require_once __DIR__ . '/api/config/db.php';
$database = new Database();
$db = $database->getConnection();
$sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
$stmt = $db->query($sql);
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
