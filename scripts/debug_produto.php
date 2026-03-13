<?php
// scripts/debug_produto.php
$dbPath = __DIR__ . '/../public/api/config/db.php';
require_once $dbPath;
$db = (new Database())->getConnection();

echo "--- Estrutura da tabela 'produto' (PostgreSQL) ---\n";
$query = "
    SELECT column_name, data_type, is_nullable, column_default
    FROM information_schema.columns
    WHERE table_name = 'produto'
    ORDER BY ordinal_position
";
$stmt = $db->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

echo "\n--- Amostra de dados ---\n";
$stmt = $db->query("SELECT * FROM produto LIMIT 1");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>
