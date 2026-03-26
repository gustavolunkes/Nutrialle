<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    try {
        $db->exec("
            UPDATE header_config SET
                nome_empresa     = 'Minha Empresa',
                logo_url         = NULL,
                cor_fundo        = '#00071c',
                cor_texto        = '#ffffff',
                cor_borda        = '#1e2a3a',
                cor_nav_link     = '#ffffff',
                mob_cor_fundo    = '#111827',
                mob_cor_texto    = '#ffffff',
                mob_cor_borda    = '#1e2a3a',
                mob_cor_nav_link = '#ffffff'
            WHERE id = (SELECT id FROM (SELECT id FROM header_config LIMIT 1) t)
        ");
        $_SESSION['success'] = 'Configurações resetadas para o padrão.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao resetar: ' . $e->getMessage();
    }
}

header('Location: ' . BASE_URL . '/admin/personalizacao/header/index.php');
exit;