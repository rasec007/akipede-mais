<?php
// scripts/debug_produto_v2.php
$dbPath = __DIR__ . '/../public/api/config/db.php';
require_once $dbPath;
$db = (new Database())->getConnection();

echo "COLUNA | TIPO | NULO | DEFAULT\n";
echo str_repeat("-", 50) . "\n";
$query = "
    SELECT column_name, data_type, is_nullable, column_default
    FROM information_schema.columns
    WHERE table_name = 'produto'
    ORDER BY ordinal_position
";
$stmt = $db->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%s | %s | %s | %s\n", 
        $row['column_name'], 
        $row['data_type'], 
        $row['is_nullable'], 
        $row['column_default']
    );
}
?>
