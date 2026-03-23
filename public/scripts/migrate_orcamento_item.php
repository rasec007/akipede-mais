<?php
require_once __DIR__ . '/../api/config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "Iniciando migração de orcamento_item...\n";

    $sql = "
        ALTER TABLE orcamento_item ADD COLUMN IF NOT EXISTS dt_criado TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP;
        ALTER TABLE orcamento_item ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'Ativo';
        ALTER TABLE orcamento_item ADD COLUMN IF NOT EXISTS obs TEXT;
    ";

    $db->exec($sql);
    echo "Colunas adicionadas com sucesso (ou já existiam).\n";

} catch (Exception $e) {
    echo "ERRO NA MIGRAÇÃO: " . $e->getMessage() . "\n";
}
