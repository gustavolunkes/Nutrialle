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
$subcategoria_id = $_GET['id'] ?? 0;

// Busca a subcategoria com informações da categoria e menu
$stmt = $db->prepare("
    SELECT 
        s.*,
        c.name as categoria_name,
        m.name as menu_name
    FROM subcategorias s
    LEFT JOIN categorias c ON s.categoria_id = c.id
    LEFT JOIN menus m ON c.menu_id = m.id
    WHERE s.id = ?
");
$stmt->execute([$subcategoria_id]);
$subcategoria = $stmt->fetch();

if (!$subcategoria) {
    $_SESSION['error'] = 'Subcategoria não encontrada';
    header('Location: index.php');
    exit;
}

// Se confirmou a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        // TODO: Quando criar vinculação com páginas, adicionar verificação aqui
        // Verifica se existem páginas vinculadas a esta subcategoria
        // $stmt = $db->prepare("SELECT COUNT(*) as total FROM vinculacoes WHERE tipo_vinculo = 'subcategoria' AND vinculo_id = ?");
        // $stmt->execute([$subcategoria_id]);
        // $result = $stmt->fetch();
        // 
        // if ($result['total'] > 0) {
        //     $_SESSION['error'] = 'Não é possível excluir esta subcategoria pois existem ' . $result['total'] . ' página(s) vinculada(s) a ela';
        //     header('Location: index.php');
        //     exit;
        // }
        
        // Deleta a subcategoria
        $stmt = $db->prepare("DELETE FROM subcategorias WHERE id = ?");
        $stmt->execute([$subcategoria_id]);
        
        $_SESSION['success'] = 'Subcategoria deletada com sucesso!';
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao deletar subcategoria: ' . $e->getMessage();
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Deletar Subcategoria';
$current_module = 'menus';
$current_page = 'subcategorias-deletar';

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
    
    .subcategoria-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .subcategoria-info strong {
        color: #00071c;
    }
    
    .subcategoria-info p {
        margin: 8px 0;
        text-align: left;
    }
    
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        margin: 10px 0;
        flex-wrap: wrap;
    }
    
    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-menu {
        background: #00071c;
        color: white;
    }
    
    .badge-categoria {
        background: #3498db;
        color: white;
    }
    
    .badge-subcategoria {
        background: #9b59b6;
        color: white;
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
        <p>Você tem certeza que deseja excluir esta subcategoria?</p>
        
        <div class="breadcrumb">
            <span class="badge badge-menu">📋 <?= htmlspecialchars($subcategoria['menu_name']) ?></span>
            <span>→</span>
            <span class="badge badge-categoria">🏷️ <?= htmlspecialchars($subcategoria['categoria_name']) ?></span>
            <span>→</span>
            <span class="badge badge-subcategoria">🔖 <?= htmlspecialchars($subcategoria['name']) ?></span>
        </div>
        
        <div class="subcategoria-info">
            <p><strong>🔖 Nome:</strong> <?= htmlspecialchars($subcategoria['name']) ?></p>
            <p><strong>🔗 Slug:</strong> /<?= htmlspecialchars($subcategoria['slug']) ?></p>
            <?php if ($subcategoria['url']): ?>
                <p><strong>🌐 URL:</strong> <?= htmlspecialchars($subcategoria['url']) ?></p>
            <?php endif; ?>
            <?php if ($subcategoria['description']): ?>
                <p><strong>📝 Descrição:</strong> <?= htmlspecialchars($subcategoria['description']) ?></p>
            <?php endif; ?>
            <p><strong>📊 Ordem:</strong> <?= $subcategoria['order_position'] ?></p>
            <p><strong>✅ Status:</strong> <?= $subcategoria['active'] ? 'Ativa' : 'Inativa' ?></p>
        </div>
        
        <div class="warning">
            <strong>⚠️ Atenção!</strong> Esta ação não pode ser desfeita.<br>
            Se houver páginas vinculadas a esta subcategoria, a exclusão não será permitida.
        </div>
        
        <form method="POST">
            <div class="form-actions">
                <button type="submit" name="confirm" class="btn-danger">
                    🗑️ Sim, Deletar Subcategoria
                </button>
                <a href="index.php" class="btn-secondary">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>