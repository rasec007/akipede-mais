<?php
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/json\r\n" . 
                    "Cookie: PHPSESSID=t1qrt8e122c02mtmqgd1bjhmvl\r\n", // Sessao dummy ou simulando 
        'ignore_errors' => true
    ]
]);

$response = file_get_contents('http://localhost:8000/api/index.php/produtos', false, $context);
echo "STATUS CODE:\n";
print_r($http_response_header[0]);
echo "\n\nBODY (RAW):\n";
echo substr($response, 0, 500) . "..."; // Mostrar apenas o inicio
