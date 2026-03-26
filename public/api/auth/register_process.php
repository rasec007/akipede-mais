<?php
// public/api/auth/register_process.php
session_start();
error_reporting(0);
ini_set('display_errors', 0);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/NotificationService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$nome = trim((string)($data['nome'] ?? ''));
$nomeLoja = trim((string)($data['nome_loja'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$cpf = trim((string)($data['cpf'] ?? ''));
$celular = trim((string)($data['celular'] ?? ''));
$apelido = trim((string)($data['apelido'] ?? ''));
$senha = (string)($data['senha'] ?? '');
$senha_confirma = (string)($data['senha_confirma'] ?? '');

if (empty($nome) || empty($nomeLoja) || empty($email) || empty($cpf) || empty($celular) || empty($senha) || empty($senha_confirma)) {
    http_response_code(400);
    echo json_encode(['error' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

if ($senha !== $senha_confirma) {
    http_response_code(400);
    echo json_encode(['error' => 'As senhas não coincidem.']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $db->beginTransaction();

    // 1. Verifica se e-mail já existe
    $queryVerify = "SELECT id_users FROM users WHERE email = :email LIMIT 1";
    $stmtVerify = $db->prepare($queryVerify);
    $stmtVerify->bindParam(':email', $email);
    $stmtVerify->execute();
    
    if ($stmtVerify->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(409); // Conflict
        echo json_encode(['error' => 'Este e-mail já está cadastrado.']);
        $db->rollBack();
        exit;
    }

    // 2. Insere usuário logista
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $perfil = 'lojista';
    
    $queryUser = "INSERT INTO users (nome, email, cpf, fone, apelido, senha, perfil, ativo) 
                  VALUES (:nome, :email, :cpf, :fone, :apelido, :senha, :perfil, true) RETURNING id_users";
    $stmtUser = $db->prepare($queryUser);
    $stmtUser->bindParam(':nome', $nome);
    $stmtUser->bindParam(':email', $email);
    $stmtUser->bindParam(':cpf', $cpf);
    $stmtUser->bindParam(':fone', $celular);
    $stmtUser->bindParam(':apelido', $apelido);
    $stmtUser->bindParam(':senha', $senhaHash);
    $stmtUser->bindParam(':perfil', $perfil);
    $stmtUser->execute();

    $userResult = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $idUsers = $userResult['id_users'];

    // 3. Insere a Loja do Logista
    $queryLoja = "INSERT INTO loja (nome, email, fone, users, status) 
                  VALUES (:nome, :email, :fone, :users, true) RETURNING id_loja";
    $stmtLoja = $db->prepare($queryLoja);
    $stmtLoja->bindParam(':nome', $nomeLoja);
    $stmtLoja->bindParam(':email', $email);
    $stmtLoja->bindParam(':fone', $celular);
    $stmtLoja->bindParam(':users', $idUsers);
    $stmtLoja->execute();

    $db->commit();

    // Enviar Notificação
    $notifData = $data;
    $notifData['raw_password'] = $senha; // Senha original antes do hash
    $notifData['fone'] = $celular;
    NotificationService::sendWelcome($notifData, 'lojista');

    echo json_encode([
        'success' => true, 
        'message' => 'Conta criada com sucesso!'
    ]);

} catch (PDOException $e) {
    if(isset($db)) $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor. Tente novamente mais tarde.']);
    // echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]); // Somente dev
}
