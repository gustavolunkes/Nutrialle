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
$categoria_id = $_GET['id'] ?? 0;

// Busca a categoria com informações do menu
$stmt = $db->prepare("
    SELECT c.*, m.name as menu_name 
    FROM categorias c
    LEFT JOIN menus m ON c.menu_id = m.id
    WHERE c.id = ?
");
$stmt->execute([$categoria_id]);
$categoria = $stmt->fetch();

if (!$categoria) {
    $_SESSION['error'] = 'Categoria não encontrada';
    header('Location: ' . BASE_URL . '/admin/menus/index.php');
    exit;
}

// Se confirmou a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        // TODO: Quando criar a tabela subcategorias, descomentar esta validação
        // Verifica se existem subcategorias vinculadas a esta categoria
        // $stmt = $db->prepare("SELECT COUNT(*) as total FROM subcategorias WHERE categoria_id = ?");
        // $stmt->execute([$categoria_id]);
        // $result = $stmt->fetch();
        // 
        // if ($result['total'] > 0) {
        //     $_SESSION['error'] = 'Não é possível excluir esta categoria pois existem ' . $result['total'] . ' subcategoria(s) vinculada(s) a ela';
        //     header('Location: ' . BASE_URL . '/admin/menus/index.php');
        //     exit;
        // }
        
        // Deleta a categoria
        $stmt = $db->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$categoria_id]);
        
        $_SESSION['success'] = 'Categoria deletada com sucesso!';
        header('Location: ' . BASE_URL . '/admin/menus/index.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao deletar categoria: ' . $e->getMessage();
        header('Location: ' . BASE_URL . '/admin/menus/index.php');
        exit;
    }
}

$page_title = 'Deletar Categoria';
$current_module = 'menus';
$current_page = 'categorias-deletar';

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
    
    .categoria-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .categoria-info strong {
        color: #00071c;
    }
    
    .categoria-info p {
        margin: 8px 0;
        text-align: left;
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
        <p>Você tem certeza que deseja excluir esta categoria?</p>
        
        <div class="categoria-info">
            <p><strong>🏷️ Nome:</strong> <?= htmlspecialchars($categoria['name']) ?></p>
            <p><strong>🔗 Slug:</strong> /<?= htmlspecialchars($categoria['slug']) ?></p>
            <p><strong>📋 Menu:</strong> <?= htmlspecialchars($categoria['menu_name']) ?></p>
            <?php if ($categoria['description']): ?>
                <p><strong>📝 Descrição:</strong> <?= htmlspecialchars($categoria['description']) ?></p>
            <?php endif; ?>
            <p><strong>📊 Ordem:</strong> <?= $categoria['order_position'] ?></p>
            <p><strong>✅ Status:</strong> <?= $categoria['active'] ? 'Ativa' : 'Inativa' ?></p>
        </div>
        
        <div class="warning">
            <strong>⚠️ Atenção!</strong> Esta ação não pode ser desfeita.<br>
            Se houver subcategorias vinculadas a esta categoria, a exclusão não será permitida.
        </div>
        
        <form method="POST">
            <div class="form-actions">
                <button type="submit" name="confirm" class="btn-danger">
                    🗑️ Sim, Deletar Categoria
                </button>
                <a href="<?= BASE_URL ?>/admin/categorias/index.php" class="btn-secondary">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
