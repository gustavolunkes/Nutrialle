<?php
// colunas/deletar.php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM rodape_colunas WHERE id=?");
$stmt->execute([$id]);
$coluna = $stmt->fetch();

if (!$coluna) {
    $_SESSION['error'] = 'Coluna não encontrada.';
} else {
    try {
        // Remove os links da coluna antes
        $db->prepare("DELETE FROM rodape_links WHERE coluna_id=?")->execute([$id]);
        $db->prepare("DELETE FROM rodape_colunas WHERE id=?")->execute([$id]);
        $_SESSION['success'] = 'Coluna "' . $coluna['titulo'] . '" e seus links foram removidos!';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro: ' . $e->getMessage();
    }
}

header('Location: ' . BASE_URL . '/admin/rodape/index.php');
exit;
