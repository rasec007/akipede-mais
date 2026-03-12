<?php
// api/realtime/stream.php

// O servidor PHP embutido (php -S) é single-thread: uma conexão SSE permanente
// bloquearia todas as outras requisições. Bloqueamos o SSE nesse ambiente.
if (php_sapi_name() === 'cli-server') {
    header('Content-Type: application/json');
    http_response_code(503);
    echo json_encode(['error' => 'SSE não disponível no servidor PHP embutido. Use Apache/Nginx em produção.']);
    exit();
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

// Iniciar sessão apenas para verificar autenticação (sem bloquear outras requisições)
session_start();
$isAuth = isset($_SESSION['user']);
session_write_close(); // Libera imediatamente a trava de sessão

if (!$isAuth) {
    http_response_code(401);
    echo "data: {\"error\":\"Não autorizado\"}\n\n";
    exit();
}

// Sem limite de tempo para SSE de longa duração
set_time_limit(0);
ignore_user_abort(false);

require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();

// Configurar o PostgreSQL para LISTEN
$db->exec("LISTEN orcamento_change");

while (true) {
    // Abortar se o cliente desconectou (previne thread presa)
    if (connection_aborted()) {
        break;
    }

    // Verificar se há notificações (timeout de 5 segundos)
    $notification = $db->pgsqlGetNotify(PDO::FETCH_ASSOC, 5000);

    if ($notification) {
        echo "data: " . $notification['payload'] . "\n\n";
    } else {
        // Enviar um "ping" para manter a conexão aberta
        echo ": ping\n\n";
    }

    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}
?>
