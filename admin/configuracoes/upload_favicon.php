<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['favicon'])) {
    echo json_encode(['success' => false, 'error' => 'Nenhum arquivo enviado']);
    exit;
}

$file    = $_FILES['favicon'];
$maxSize = 1 * 1024 * 1024; // 1MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Erro no upload (código ' . $file['error'] . ')']);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'error' => 'Arquivo muito grande. Máximo: 1MB.']);
    exit;
}

// Valida extensão — apenas .ico permitido
$origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($origExt !== 'ico') {
    echo json_encode(['success' => false, 'error' => 'Apenas arquivos .ico são aceitos.']);
    exit;
}

// Valida tipo MIME real
$finfo       = new finfo(FILEINFO_MIME_TYPE);
$mime        = $finfo->file($file['tmp_name']);
$allowedMime = ['image/x-icon', 'image/vnd.microsoft.icon'];

// Alguns sistemas retornam application/octet-stream para .ico — aceita junto
if (!in_array($mime, $allowedMime) && $mime !== 'application/octet-stream') {
    echo json_encode(['success' => false, 'error' => 'Arquivo inválido. Envie um favicon.ico legítimo.']);
    exit;
}

// Destino: raiz do projeto
$dest = __DIR__ . '/../../favicon.ico';

// Remove o anterior se existir
if (file_exists($dest)) {
    @unlink($dest);
}

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success' => false, 'error' => 'Falha ao salvar o arquivo no servidor.']);
    exit;
}

$path = BASE_URL . '/favicon.ico';

echo json_encode([
    'success'  => true,
    'path'     => $path,
    'filename' => 'favicon.ico',
]);