<?php
// redes/deletar.php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM rodape_redes_sociais WHERE id=?");
$stmt->execute([$id]);
$rede = $stmt->fetch();

if (!$rede) {
    $_SESSION['error'] = 'Rede não encontrada.';
} else {
    try {
        $db->prepare("DELETE FROM rodape_redes_sociais WHERE id=?")->execute([$id]);
        $_SESSION['success'] = ucfirst($rede['rede']) . ' removida com sucesso!';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro: ' . $e->getMessage();
    }
}
header('Location: ' . BASE_URL . '/admin/rodape/index.php');
exit;