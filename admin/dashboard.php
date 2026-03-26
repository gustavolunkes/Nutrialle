<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Configurações da página
$page_title = 'Dashboard';
$current_module = 'dashboard';
$current_page = 'dashboard';

$db = getDB();

// Total de usuários ativos
$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE active = 1");
$stats = $stmt->fetch();
$total_users = $stats['total'];

// Total de sessões ativas
$stmt = $db->query("SELECT COUNT(*) as total FROM sessions");
$stats_sessions = $stmt->fetch();
$total_sessions = $stats_sessions['total'];

// Total de tentativas de login nas últimas 24h
$stmt = $db->query("SELECT COUNT(*) as total FROM login_attempts WHERE attempted_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stats_attempts = $stmt->fetch();
$total_attempts = $stats_attempts['total'];

// Inclui o header e sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- CONTENT -->
<main class="content">
    <div class="cards">
        <div class="card">
            <div class="card-icon blue">👥</div>
            <div class="card-title">Total de Usuários</div>
            <div class="card-value"><?= $total_users ?></div>
        </div>
        
        <div class="card">
            <div class="card-icon green">🔒</div>
            <div class="card-title">Sessões Ativas</div>
            <div class="card-value"><?= $total_sessions ?></div>
        </div>
        
        <div class="card">
            <div class="card-icon orange">⚠️</div>
            <div class="card-title">Tentativas de Login</div>
            <div class="card-value"><?= $total_attempts ?></div>
            <div class="card-subtitle">Últimas 24 horas</div>
        </div>
    </div>
    
    <div class="welcome-section">
        <h3>👋 Bem-vindo, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h3>
        <p>Este é o seu painel administrativo do Wire Stack. Aqui você pode gerenciar usuários, monitorar sessões ativas e acompanhar a segurança do sistema.</p>
        <p>Use este sistema como base para criar sites institucionais personalizados para seus clientes.</p>
        <a href="usuarios/criar.php" class="btn">➕ Criar Novo Usuário</a>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>