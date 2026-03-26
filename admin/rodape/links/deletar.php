<?php
// links/deletar.php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$db        = getDB();
$id        = (int)($_GET['id'] ?? 0);
$coluna_id = (int)($_GET['coluna_id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM rodape_links WHERE id=?");
$stmt->execute([$id]);
$link = $stmt->fetch();

if (!$link) {
    $_SESSION['error'] = 'Link não encontrado.';
} else {
    try {
        $db->prepare("DELETE FROM rodape_links WHERE id=?")->execute([$id]);
        $_SESSION['success'] = 'Link "' . $link['label'] . '" removido!';
        $coluna_id = $link['coluna_id'];
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro: ' . $e->getMessage();
    }
}

if ($coluna_id) {
    header('Location: ' . BASE_URL . '/admin/rodape/links/index.php?coluna_id=' . $coluna_id);
} else {
    header('Location: ' . BASE_URL . '/admin/rodape/index.php');
}
exit;
