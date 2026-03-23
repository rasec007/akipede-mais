<?php
require_once __DIR__ . '/api/config/db.php';
$database = new Database();
$db = $database->getConnection();

function getCols($db, $table) {
    echo "Cols for $table:\n";
    $sql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '$table'";
    $stmt = $db->query($sql);
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
}

getCols($db, 'orcamento_item');
getCols($db, 'agenda_produto');
