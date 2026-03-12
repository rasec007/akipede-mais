<?php
// tests/db_test.php
require_once __DIR__ . '/../api/config/db.php';

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "Conexão estabelecida com sucesso!\n";
    
    try {
        $sql = file_get_contents(__DIR__ . '/schema.sql');
        
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // No Postgres, para múltiplos statements com PL/pgSQL, o melhor é rodar em um único bloco ou garantir idempotência
        echo "Executando schema.sql...\n";
        $db->exec($sql);
        
        echo "\n>>> VERIFICAÇÃO DE TABELAS CRIADAS NO SCHEMA 'public': <<<\n";
        
        // Query para listar tabelas
        $query = $db->query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
        $tables = $query->fetchAll(PDO::FETCH_COLUMN);
        
        sort($tables); // Ordenar para facilitar leitura
        
        foreach ($tables as $index => $t) {
            echo ($index + 1) . ". $t\n";
        }
        
        $total = count($tables);
        echo "\nTotal de tabelas encontradas: $total\n";

        if ($total >= 13) {
            echo "✅ SUCESSO! A estrutura está completa.\n";
        } else {
            echo "⚠️ ATENÇÃO: Faltam tabelas! Esperado: 13, Encontrado: $total.\n";
            echo "Execute o comando novamente ou verifique se houve erro acima.\n";
        }

    } catch(PDOException $e) {
        echo "\n❌ ERRO NO SQL: " . $e->getMessage() . "\n";
        echo "DICA: Se for erro de 'Duplicate', eu já tentei corrigir no schema.sql. Rode novamente.\n";
    }
} else {
    echo "Falha na conexão.\n";
}
?>
