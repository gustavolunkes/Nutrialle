<?php
// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'Usuário';
$user_role = $_SESSION['user_role'] ?? 'viewer';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Wire Stack' ?> - Wire Stack</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/dashboard.css">
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?= BASE_URL ?>/<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>