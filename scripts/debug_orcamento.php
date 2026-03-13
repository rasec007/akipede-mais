<?php
// scripts/debug_orcamento.php
$dbPath = __DIR__ . '/../public/api/config/db.php';
require_once $dbPath;
$db = (new Database())->getConnection();

echo "--- Estrutura da tabela 'orcamento' (PostgreSQL) ---\n";
$query = "
    SELECT column_name, data_type, is_nullable, column_default
    FROM information_schema.columns
    WHERE table_name = 'orcamento'
    ORDER BY ordinal_position
";
$stmt = $db->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>
