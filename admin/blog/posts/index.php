<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Posts do Blog';
$current_module = 'blog';
$current_page   = 'blog-posts';

$db = getDB();

$stmt = $db->query("
    SELECT p.*, c.nome as categoria_nome, c.cor as categoria_cor
    FROM blog_posts p
    LEFT JOIN blog_categorias c ON p.categoria_id = c.id
    ORDER BY p.publicado_em DESC
");
$posts = $stmt->fetchAll();

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
th{padding:12px 15px;text-align:left;font-weight:600;color:#00071c;font-size:12px;text-transform:uppercase;letter-spacing:.5px}
td{padding:12px 15px;border-bottom:1px solid #e0e0e0;font-size:13px;vertical-align:middle}
tbody tr:hover{background:#f8f9fa}
.badge{padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-active{background:#d4edda;color:#155724}
.badge-inactive{background:#fee;color:#c33}
.badge-destaque{background:#fff3cd;color:#856404}
.table-actions{display:flex;gap:8px}
.btn-icon{padding:5px 10px;border:none;border-radius:6px;cursor:pointer;font-size:11px;transition:all .3s;text-decoration:none;display:inline-block}
.btn-edit{background:#3498db;color:#fff}.btn-edit:hover{background:#2980b9}
.btn-delete{background:#e74c3c;color:#fff}.btn-delete:hover{background:#c0392b}
.empty-state{text-align:center;padding:60px 20px;color:#95a5a6}
.alert{padding:15px;border-radius:8px;margin-bottom:20px;font-size:14px}
.alert-success{background:#d4edda;color:#155724;border-left:4px solid #28a745}
.alert-error{background:#fee;color:#c33;border-left:4px solid #c33}
.cat-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;color:#fff}
.post-title{font-weight:600;color:#00071c;max-width:280px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.views{display:inline-flex;align-items:center;gap:4px;color:#6b7280;font-size:12px}
.tempo{display:inline-flex;align-items:center;gap:4px;color:#6b7280;font-size:12px}
</style>

<main class="content">
    <div class="table-container">
        <div class="table-header">
            <h3>📝 Posts do Blog</h3>
            <a href="<?= BASE_URL ?>/admin/blog/posts/criar.php" class="btn-add">➕ Novo Post</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (count($posts) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Autor</th>
                        <th>Tempo</th>
                        <th>Views</th>
                        <th>Publicado em</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><strong>#<?= $post['id'] ?></strong></td>
                            <td>
                                <span class="post-title" title="<?= htmlspecialchars($post['titulo']) ?>">
                                    <?= $post['destaque'] ? '⭐ ' : '' ?><?= htmlspecialchars($post['titulo']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($post['categoria_nome']): ?>
                                    <span class="cat-badge" style="background:<?= htmlspecialchars($post['categoria_cor'] ?? '#999') ?>">
                                        <?= htmlspecialchars($post['categoria_nome']) ?>
                                    </span>
                                <?php else: ?>
                                    <em style="color:#ccc">Sem categoria</em>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($post['autor']) ?></td>
                            <td><span class="tempo">⏱️ <?= $post['tempo_leitura'] ?>min</span></td>
                            <td><span class="views">👁️ <?= number_format($post['visualizacoes']) ?></span></td>
                            <td><?= date('d/m/Y', strtotime($post['publicado_em'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $post['ativo'] ? 'active' : 'inactive' ?>">
                                    <?= $post['ativo'] ? 'Publicado' : 'Rascunho' ?>
                                </span>
                                <?php if ($post['destaque']): ?>
                                    <span class="badge badge-destaque">⭐ Destaque</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= BASE_URL ?>/admin/blog/posts/editar.php?id=<?= $post['id'] ?>" class="btn-icon btn-edit">✏️ Editar</a>
                                    <a href="<?= BASE_URL ?>/admin/blog/posts/deletar.php?id=<?= $post['id'] ?>" class="btn-icon btn-delete">🗑️</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size:60px">📝</div>
                <h3>Nenhum post encontrado</h3>
                <p>Comece criando o primeiro artigo do blog</p>
                <a href="<?= BASE_URL ?>/admin/blog/posts/criar.php" class="btn-add" style="margin-top:20px">➕ Criar Primeiro Post</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
