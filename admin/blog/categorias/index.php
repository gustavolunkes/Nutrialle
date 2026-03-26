<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Categorias do Blog';
$current_module = 'blog';
$current_page   = 'blog-categorias';

$db = getDB();

$stmt = $db->query("SELECT * FROM blog_categorias ORDER BY nome ASC");
$categorias = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
.table-container{background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
.table-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px}
.table-header h3{color:#00071c;font-size:20px}
.btn-add{padding:10px 20px;background:#00071c;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px;transition:all .3s;display:inline-flex;align-items:center;gap:8px}
.btn-add:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,7,28,.3)}
table{width:100%;border-collapse:collapse}
thead{background:#f8f9fa}
th{padding:15px;text-align:left;font-weight:600;color:#00071c;font-size:13px;text-transform:uppercase;letter-spacing:.5px}
td{padding:15px;border-bottom:1px solid #e0e0e0;font-size:14px}
tbody tr:hover{background:#f8f9fa}
.badge{padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.badge-active{background:#d4edda;color:#155724}
.badge-inactive{background:#fee;color:#c33}
.table-actions{display:flex;gap:10px}
.btn-icon{padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-size:12px;transition:all .3s;text-decoration:none;display:inline-block}
.btn-edit{background:#3498db;color:#fff}.btn-edit:hover{background:#2980b9}
.btn-delete{background:#e74c3c;color:#fff}.btn-delete:hover{background:#c0392b}
.empty-state{text-align:center;padding:60px 20px;color:#95a5a6}
.alert{padding:15px;border-radius:8px;margin-bottom:20px;font-size:14px}
.alert-success{background:#d4edda;color:#155724;border-left:4px solid #28a745}
.alert-error{background:#fee;color:#c33;border-left:4px solid #c33}
.color-dot{display:inline-block;width:16px;height:16px;border-radius:50%;vertical-align:middle;margin-right:6px;border:1px solid rgba(0,0,0,.1)}
</style>

<main class="content">
    <div class="table-container">
        <div class="table-header">
            <h3>🏷️ Categorias do Blog</h3>
            <a href="<?= BASE_URL ?>/admin/blog/categorias/criar.php" class="btn-add">➕ Nova Categoria</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (count($categorias) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Cor</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $cat): ?>
                        <tr>
                            <td><strong>#<?= $cat['id'] ?></strong></td>
                            <td><strong><?= htmlspecialchars($cat['nome']) ?></strong></td>
                            <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                            <td>
                                <span class="color-dot" style="background:<?= htmlspecialchars($cat['cor']) ?>"></span>
                                <code><?= htmlspecialchars($cat['cor']) ?></code>
                            </td>
                            <td>
                                <span class="badge badge-<?= $cat['ativo'] ? 'active' : 'inactive' ?>">
                                    <?= $cat['ativo'] ? 'Ativa' : 'Inativa' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($cat['created_at'])) ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= BASE_URL ?>/admin/blog/categorias/editar.php?id=<?= $cat['id'] ?>" class="btn-icon btn-edit">✏️ Editar</a>
                                    <a href="<?= BASE_URL ?>/admin/blog/categorias/deletar.php?id=<?= $cat['id'] ?>" class="btn-icon btn-delete">🗑️ Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size:60px">🏷️</div>
                <h3>Nenhuma categoria encontrada</h3>
                <p>Comece adicionando a primeira categoria do blog</p>
                <a href="<?= BASE_URL ?>/admin/blog/categorias/criar.php" class="btn-add" style="margin-top:20px">➕ Criar Primeira Categoria</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
