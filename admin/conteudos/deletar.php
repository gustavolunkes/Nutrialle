<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$db = getDB();

// Pega o ID do conteúdo
$conteudo_id = $_GET['id'] ?? 0;

// Busca o conteúdo
$stmt = $db->prepare("SELECT * FROM conteudos WHERE id = ?");
$stmt->execute([$conteudo_id]);
$conteudo = $stmt->fetch();

if (!$conteudo) {
    $_SESSION['error'] = 'Conteúdo não encontrado';
    header('Location: ' . BASE_URL . '/admin/paginas/index.php');
    exit;
}

$pagina_id = $conteudo['pagina_id'];

try {
    // Deleta o conteúdo
    $stmt = $db->prepare("DELETE FROM conteudos WHERE id = ?");
    $stmt->execute([$conteudo_id]);
    
    // Reorganiza a ordem dos conteúdos restantes
    $stmt = $db->prepare("
        SELECT id FROM conteudos 
        WHERE pagina_id = ? 
        ORDER BY ordem ASC
    ");
    $stmt->execute([$pagina_id]);
    $conteudos = $stmt->fetchAll();
    
    $ordem = 1;
    foreach ($conteudos as $c) {
        $stmt = $db->prepare("UPDATE conteudos SET ordem = ? WHERE id = ?");
        $stmt->execute([$ordem, $c['id']]);
        $ordem++;
    }
    
    $_SESSION['success'] = 'Conteúdo deletado com sucesso!';
} catch (Exception $e) {
    $_SESSION['error'] = 'Erro ao deletar conteúdo: ' . $e->getMessage();
}

header('Location: ' . BASE_URL . '/admin/conteudos/index.php?pagina_id=' . $pagina_id);
exit;
