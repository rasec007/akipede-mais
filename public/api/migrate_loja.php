<?php
require_once __DIR__ . '/../config/db.php';
try {
    $db = (new Database())->getConnection();
    $cols = [
        'cor_tema VARCHAR(20) DEFAULT \'#37c6da\'',
        'descricao TEXT',
        'instagram VARCHAR(255)',
        'facebook VARCHAR(255)',
        'cep VARCHAR(20)',
        'endereco VARCHAR(255)',
        'numero VARCHAR(20)',
        'complemento VARCHAR(100)',
        'bairro VARCHAR(100)',
        'cidade VARCHAR(100)',
        'estado VARCHAR(2)',
        'url VARCHAR(100)'
    ];
    foreach($cols as $col) {
        try {
            $db->exec("ALTER TABLE loja ADD COLUMN $col");
            echo "Added: $col\n";
        } catch(Exception $e) {
            echo "Skipped: $col\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
