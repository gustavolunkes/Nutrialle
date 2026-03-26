<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pagina_id = (int)($_GET['id'] ?? 0);
if (!$pagina_id) {
    $_SESSION['error'] = 'ID inválido.';
    header('Location: ' . BASE_URL . '/admin/paginas/index.php');
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT titulo FROM paginas WHERE id = ?");
    $stmt->execute([$pagina_id]);
    $pagina = $stmt->fetch();

    if (!$pagina) {
        $_SESSION['error'] = 'Página não encontrada.';
    } else {
        $db->prepare("DELETE FROM conteudos WHERE pagina_id = ?")->execute([$pagina_id]);
        $db->prepare("DELETE FROM vinculacoes WHERE pagina_id = ?")->execute([$pagina_id]);
        $db->prepare("DELETE FROM paginas WHERE id = ?")->execute([$pagina_id]);
        $_SESSION['success'] = 'Página "' . htmlspecialchars($pagina['titulo']) . '" excluída com sucesso.';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Erro ao excluir: ' . $e->getMessage();
}

header('Location: ' . BASE_URL . '/admin/paginas/index.php');
exit;