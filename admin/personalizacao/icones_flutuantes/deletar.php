<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $db->prepare("DELETE FROM icones_flutuantes WHERE id = ?")->execute([$id]);
    $_SESSION['success'] = 'Ícone excluído com sucesso!';
} else {
    $_SESSION['success'] = 'ID inválido.';
}

header('Location: ' . BASE_URL . '/admin/personalizacao/icones_flutuantes/index.php');
exit;
