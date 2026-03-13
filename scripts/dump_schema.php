<?php
require 'public/api/config/db.php';
try {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach($tables as $table) {
        echo "Tabela: $table\n";
        $stmt2 = $db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '$table'");
        while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            echo "  - {$row['column_name']} ({$row['data_type']})\n";
        }
        echo "\n";
    }
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage();
}
