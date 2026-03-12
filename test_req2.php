<?php
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode(['email' => 'teste@teste.com', 'senha' => 'senhaerrada123']),
        'ignore_errors' => true
    ]
]);

$response = file_get_contents('http://localhost:8000/api/auth/login_process.php', false, $context);
echo "RESPONSE FROM SERVER:\n";
echo $response;
