<?php
require 'public/api/config/db.php';
require 'public/api/controllers/ProdutoController.php';

try {
    $db = (new Database())->getConnection();
    $controller = new ProdutoController($db);
    $produtos = $controller->read();
    
    echo "Total de produtos no banco: " . count($produtos) . "\n";
    print_r($produtos);

} catch(Exception $e) {
    echo "Erro: " . $e->getMessage();
}
