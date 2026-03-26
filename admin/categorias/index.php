<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Configurações da página
$page_title = 'Categorias';
$current_module = 'categorias';
$current_page = 'categorias-lista';

$db = getDB();

// Busca todas as categorias com informações do menu
$stmt = $db->query("
    SELECT c.*, m.name as menu_name 
    FROM categorias c
    LEFT JOIN menus m ON c.menu_id = m.id
    ORDER BY m.name ASC, c.order_position ASC, c.name ASC
");
$categorias = $stmt->fetchAll();

// Inclui o header e sidebar
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<style>
    .table-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .table-header h3 {
        color: #00071c;
        font-size: 20px;
    }
    
    .btn-add {
        padding: 10px 20px;
        background: #00071c;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 7, 28, 0.3);
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background: #f8f9fa;
    }
    
    th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #00071c;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    td {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        font-size: 14px;
    }
    
    tbody tr:hover {
        background: #f8f9fa;
    }
    
    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-active {
        background: #d4edda;
        color: #155724;
    }
    
    .badge-inactive {
        background: #fee;
        color: #c33;
    }
    
    .badge-menu {
        background: #00071c;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .table-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-icon {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-edit {
        background: #3498db;
        color: white;
    }
    
    .btn-edit:hover {
        background: #2980b9;
    }
    
    .btn-delete {
        background: #e74c3c;
        color: white;
    }
    
    .btn-delete:hover {
        background: #c0392b;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }
    
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .alert-error {
        background: #fee;
        color: #c33;
        border-left: 4px solid #c33;
    }
    
    .order-badge {
        background: #00071c;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }
</style>

<!-- CONTENT -->
<main class="content">
    <div class="table-container">
        <div class="table-header">
            <h3>🏷️ Lista de Categorias</h3>
            <a href="<?= BASE_URL ?>/admin/categorias/criar.php" class="btn-add">
                ➕ Nova Categoria
            </a>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (count($categorias) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Ordem</th>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Menu</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><span class="order-badge"><?= $categoria['order_position'] ?></span></td>
                            <td><strong><?= htmlspecialchars($categoria['name']) ?></strong></td>
                            <td><code>/<?= htmlspecialchars($categoria['slug']) ?></code></td>
                            <td>
                                <span class="badge-menu">
                                    📋 <?= htmlspecialchars($categoria['menu_name']) ?>
                                </span>
                            </td>
                            <td><?= $categoria['description'] ? substr(htmlspecialchars($categoria['description']), 0, 50) . '...' : '<em>Sem descrição</em>' ?></td>
                            <td>
                                <span class="badge badge-<?= $categoria['active'] ? 'active' : 'inactive' ?>">
                                    <?= $categoria['active'] ? 'Ativa' : 'Inativa' ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= BASE_URL ?>/admin/categorias/editar.php?id=<?= $categoria['id'] ?>" class="btn-icon btn-edit">
                                        ✏️ Editar
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/categorias/deletar.php?id=<?= $categoria['id'] ?>" class="btn-icon btn-delete">
                                        🗑️ Excluir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 60px;">🏷️</div>
                <h3>Nenhuma categoria encontrada</h3>
                <p>Comece adicionando a primeira categoria</p>
                <a href="<?= BASE_URL ?>/admin/categorias/criar.php" class="btn-add" style="margin-top: 20px;">
                    ➕ Criar Primeira Categoria
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>