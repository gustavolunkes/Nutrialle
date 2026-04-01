<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$db = getDB();
$user_id = $_GET['id'] ?? 0;

// Não permite deletar a si mesmo
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = 'Você não pode deletar seu próprio usuário';
    header('Location: index.php');
    exit;
}

// Busca o usuário
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'Usuário não encontrado';
    header('Location: index.php');
    exit;
}

// 🚫 Não permite deletar usuário master
if ($user['user_master'] === 'S') {
    $_SESSION['error'] = 'Usuários master não podem ser deletados';
    header('Location: index.php');
    exit;
}

// Se confirmou a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        // Deleta o usuário
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $_SESSION['success'] = 'Usuário deletado com sucesso!';
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao deletar usuário: ' . $e->getMessage();
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Deletar Usuário';
$current_module = 'usuarios';
$current_page = 'usuarios-deletar';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<style>
    .confirm-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 500px;
        text-align: center;
    }
    
    .confirm-icon {
        font-size: 80px;
        margin-bottom: 20px;
    }
    
    .confirm-container h3 {
        color: #00071c;
        margin-bottom: 15px;
    }
    
    .confirm-container p {
        color: #7f8c8d;
        margin-bottom: 10px;
        line-height: 1.6;
    }
    
    .user-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .user-info strong {
        color: #00071c;
    }
    
    .warning {
        background: #fff3cd;
        color: #856404;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        font-size: 14px;
        border-left: 4px solid #ffc107;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 30px;
    }
    
    .btn-danger {
        padding: 12px 24px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-danger:hover {
        background: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
    }
    
    .btn-secondary {
        padding: 12px 24px;
        background: #95a5a6;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background: #7f8c8d;
    }
</style>

<main class="content">
    <div class="confirm-container">
        <div class="confirm-icon">⚠️</div>
        <h3>Confirmar Exclusão</h3>
        <p>Você tem certeza que deseja excluir este usuário?</p>
        
        <div class="user-info">
            <p><strong>Nome:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Tipo:</strong> <?= ucfirst($user['role']) ?></p>
        </div>
        
        <div class="warning">
            <strong>⚠️ Atenção!</strong> Esta ação não pode ser desfeita.
        </div>
        
        <form method="POST">
            <div class="form-actions">
                <button type="submit" name="confirm" class="btn-danger">
                    🗑️ Sim, Deletar Usuário
                </button>
                <a href="index.php" class="btn-secondary">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>