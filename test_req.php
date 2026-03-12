<?php
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode(['email' => 'teste@teste.com', 'senha' => 'senhaerrada123'])
    ]
]);

$response = @file_get_contents('http://localhost:8000/api/auth/login_process.php', false, $context);

if ($response === false) {
    echo "FALHA NA REQUISIÇÃO:\n";
    print_r($http_response_header);
} else {
    echo "STATUS CODE:\n";
    print_r($http_response_header[0]);
    echo "\n\nBODY (RAW):\n";
    echo $response;
}
