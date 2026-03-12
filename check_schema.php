<?php
require 'public/api/config/db.php';
try {
    $db = (new Database())->getConnection();
    
    $stmt = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'loja'");
    echo "Colunas LOJA:\n";
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

    $stmt2 = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'produto'");
    echo "\nColunas PRODUTO:\n";
    print_r($stmt2->fetchAll(PDO::FETCH_COLUMN));

} catch(Exception $e) {
    echo "Erro: " . $e->getMessage();
}
