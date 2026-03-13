<?php
$url = 'http://localhost:8000/api/index.php/categorias';
$data = ['nome' => 'Teste Categoria ' . time(), 'loja' => '35f29d20-0097-4d7a-b286-9a25b3952f9c'];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "POST Categorias: " . $result . "\n\n";

$get = file_get_contents($url);
echo "GET Categorias: " . $get . "\n";
