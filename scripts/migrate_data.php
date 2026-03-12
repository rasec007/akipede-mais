<?php
// scripts/migrate_data.php
require_once __DIR__ . '/../api/config/db.php';

// Configurações do Supabase (Origem)
$supabase_url = "https://divagjstanomoryzlizm.supabase.co/rest/v1/";
$supabase_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImRpdmFnanN0YW5vbW9yeXpsaXptIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2OTM2ODU0NywiZXhwIjoyMDg0OTQ0NTQ3fQ.FBmUjxn5Deb_ePikjESp7hTGc2B2Vel-0CuzID3VLEc";

function fetchSupabase($table) {
    global $supabase_url, $supabase_key;
    $ch = curl_init($supabase_url . $table);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key",
        "Authorization: Bearer $supabase_key"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function isValidUuid($uuid) {
    return is_string($uuid) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid) === 1;
}

$db = (new Database())->getConnection();
if (!$db) die("Falha na conexão com Postgres.\n");

// Mapeamento de tabelas e ordens (Crucial para chaves estrangeiras)
$tables = [
    'users', 'plano', 'loja', 'categoria', 'cliente', 
    'produto', 'orcamento', 'orcamento_item', 'pedido', 
    'produto_pedido', 'carrinho', 'produto_carrinho', 'agenda_produto'
];

// Colunas que são UUIDs (para validação)
$uuid_columns = [
    'id_users', 'id_plano', 'id_loja', 'id_categoria', 'id_cliente', 
    'id_produto', 'id_orcamento', 'id_item', 'id_pedido', 
    'id_produto_pedido', 'id_carrinho', 'id_produto_carrinho', 'id_agenda_produto',
    'loja', 'users', 'plano', 'categoria', 'parceiro', 'orcamento', 'produto', 'pedido', 'carrinho', 'user', 'user_comprador'
];

// Mapeamento de nomes de colunas do Supabase (CamelCase) para Postgres (SnakeCase)
$column_mapping = [
    'carrinho' => [
        'userComprador' => 'user_comprador',
        'urlLoja' => 'url_loja'
    ]
];

foreach ($tables as $table) {
    echo "\n>>> Migrando tabela: $table...\n";
    $data = fetchSupabase($table);
    
    if ($table === 'cliente') {
        echo "DEBUG CLIENTE: Supabase retornou " . (is_array($data) ? count($data) : 'NAO-ARRAY') . " registros.\n";
        if (!is_array($data)) var_dump($data);
    }
    if (empty($data) || isset($data['message'])) {
        echo "Aviso: Sem dados para $table (ou erro: " . ($data['message'] ?? 'vazio') . ")\n";
        continue;
    }

    foreach ($data as $row) {
        $clean_row = [];
        
        foreach ($row as $key => $value) {
            // 1. Mapear nome da coluna se necessário
            $dest_key = $key;
            if (isset($column_mapping[$table][$key])) {
                $dest_key = $column_mapping[$table][$key];
            }

            // 2. Tratar Booleanos (Postgres não aceita "" ou nulo em NOT NULL boolean)
            if (is_bool($value)) {
                $clean_row[$dest_key] = $value ? 'true' : 'false';
            } elseif ($value === "" || $value === null) {
                // Tentar inferir se é boolean pelo nome do campo ou deixar null se o banco permitir
                if (in_array($key, ['ativo', 'status', 'mostar_valor'])) {
                    $clean_row[$dest_key] = 'false';
                } else {
                    $clean_row[$dest_key] = null;
                }
            } else {
                $clean_row[$dest_key] = $value;
            }

            // 3. Validar UUIDs
            if (in_array($dest_key, $uuid_columns)) {
                if ($clean_row[$dest_key] !== null && !isValidUuid($clean_row[$dest_key])) {
                    $clean_row[$dest_key] = null; // Evita erro de sintaxe UUID
                }
            }
        }

        // Montar SQL
        $cols = array_keys($clean_row);
        // Escapar colunas reservadas (como "user")
        $escaped_cols = array_map(function($c) { return "\"$c\""; }, $cols);
        
        $placeholders = ":" . implode(", :", $cols);
        $sql = "INSERT INTO $table (" . implode(", ", $escaped_cols) . ") VALUES ($placeholders) ON CONFLICT DO NOTHING";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($clean_row);
        } catch (Exception $e) {
            echo "Erro em $table: " . $e->getMessage() . " | SQL: $sql\n";
        }
    }
    echo "Fim da migracao de $table: " . count($data) . " registros.\n";
}

echo "\n✅ Migração concluída com sucesso!";
?>
