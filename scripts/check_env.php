<?php
// scripts/check_env.php
echo "--- Diagnostico do Ambiente PHP ---\n";
echo "Versao PHP: " . PHP_VERSION . "\n";
echo "Arquivo php.ini carregado: " . php_ini_loaded_file() . "\n";
echo "Diretorio de extensoes: " . ini_get('extension_dir') . "\n";

echo "\n--- Extensoes Carregadas ---\n";
$extensions = get_loaded_extensions();
echo "Total: " . count($extensions) . "\n";
echo "Postgres (pdo_pgsql): " . (in_array('pdo_pgsql', $extensions) ? "✅ CARREGADO" : "❌ NAO ENCONTRADO") . "\n";
echo "Postgres (pgsql): " . (in_array('pgsql', $extensions) ? "✅ CARREGADO" : "❌ NAO ENCONTRADO") . "\n";

if (!in_array('pdo_pgsql', $extensions)) {
    echo "\n--- Dica de Solucao ---\n";
    echo "1. Abra o arquivo: " . php_ini_loaded_file() . "\n";
    echo "2. Procure pela linha: ;extension=pdo_pgsql\n";
    echo "3. Remova o ';' do inicio.\n";
    echo "4. Salve e REINICIE o Apache no XAMPP Control Panel.\n";
}
?>
