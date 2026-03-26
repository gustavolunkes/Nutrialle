<?php
session_start();

require_once __DIR__ . '/../config/database.php';

// Remove a sessão do banco de dados
if (isset($_SESSION['user_id'])) {
    try {
        $db = getDB();
        $session_id = session_id();
        // Usa 'id' ao invés de 'session_id'
        $stmt = $db->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->execute([$session_id]);
    } catch (Exception $e) {
        // Log do erro se necessário
    }
}

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destroi o cookie de sessão
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destroi a sessão
session_destroy();

// Redireciona para o login
header('Location: login.php');
exit;
?>