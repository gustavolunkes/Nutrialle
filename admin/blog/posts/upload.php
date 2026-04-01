<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

require_once __DIR__ . '/../../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['imagem'])) {
    echo json_encode(['success' => false, 'error' => 'Nenhum arquivo enviado']);
    exit;
}

$file    = $_FILES['imagem'];
$maxSize = 5 * 1024 * 1024; // 5MB
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Erro no upload (código ' . $file['error'] . ')']);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'error' => 'Arquivo muito grande. Máx 5MB.']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if (!in_array($mime, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Tipo não permitido. Use JPG, PNG, WEBP ou GIF.']);
    exit;
}

$ext = match($mime) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    default      => 'jpg',
};

$uploadDir = __DIR__ . '/../../../assets/uploads/blog/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = uniqid('blog_', true) . '.' . $ext;
$dest     = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success' => false, 'error' => 'Falha ao salvar o arquivo no servidor.']);
    exit;
}

$path = BASE_URL . '/assets/uploads/blog/' . $filename;
echo json_encode(['success' => true, 'path' => $path]);
