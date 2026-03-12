<?php
// api/utils/upload.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$type = $_POST['type'] ?? 'produtos'; // 'produtos' ou 'usuarios'
$targetDir = "../../public/storage-akipede/" . $type . "/";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Nenhum arquivo enviado']);
    exit;
}

$file = $_FILES['file'];
$fileName = time() . '_' . basename($file['name']);
$targetFile = $targetDir . $fileName;
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// Validar se é imagem
$check = getimagesize($file['tmp_name']);
if($check === false) {
    http_response_code(400);
    echo json_encode(['error' => 'O arquivo não é uma imagem']);
    exit;
}

// Mover arquivo
if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    // Retornar o caminho relativo para ser salvo no banco
    $publicPath = "storage-akipede/" . $type . "/" . $fileName;
    echo json_encode(['success' => true, 'path' => $publicPath]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao mover o arquivo']);
}
