<?php
require_once __DIR__ . '/../public/api/config/db.php';
try {
    $db = (new Database())->getConnection();
    $res = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    foreach($res->fetchAll(PDO::FETCH_COLUMN) as $t) {
        echo "Table: $t\n";
        $cols = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = '$t'")->fetchAll(PDO::FETCH_COLUMN);
        print_r($cols);
        echo "-------------------\n";
    }
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage();
}
