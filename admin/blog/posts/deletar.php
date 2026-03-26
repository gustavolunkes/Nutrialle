<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$db = getDB();
$id = intval($_GET['id'] ?? 0);

$stmt = $db->prepare("
    SELECT p.*, c.nome as categoria_nome
    FROM blog_posts p
    LEFT JOIN blog_categorias c ON p.categoria_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    $_SESSION['error'] = 'Post não encontrado.';
    header('Location: ' . BASE_URL . '/admin/blog/posts/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        $db->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
        $_SESSION['success'] = 'Post excluído com sucesso!';
        header('Location: ' . BASE_URL . '/admin/blog/posts/index.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao excluir: ' . $e->getMessage();
        header('Location: ' . BASE_URL . '/admin/blog/posts/index.php');
        exit;
    }
}

$page_title     = 'Excluir Post do Blog';
$current_module = 'blog';
$current_page   = 'blog-posts';

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
.confirm-container{background:#fff;padding:40px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.05);max-width:520px;text-align:center}
.confirm-icon{font-size:80px;margin-bottom:20px}
.confirm-container h3{color:#00071c;margin-bottom:15px}
.confirm-container p{color:#7f8c8d;margin-bottom:10px;line-height:1.6}
.info-box{background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;text-align:left}
.info-box p{margin:8px 0;font-size:14px}
.post-title-preview{font-size:15px;font-weight:700;color:#00071c;margin-bottom:4px}
.warning{background:#fff3cd;color:#856404;padding:15px;border-radius:8px;margin:20px 0;font-size:14px;border-left:4px solid #ffc107}
.form-actions{display:flex;gap:10px;justify-content:center;margin-top:30px}
.btn-danger{padding:12px 24px;background:#e74c3c;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;transition:all .3s}
.btn-danger:hover{background:#c0392b;transform:translateY(-2px)}
.btn-secondary{padding:12px 24px;background:#95a5a6;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;text-decoration:none;display:inline-block;transition:all .3s}
.btn-secondary:hover{background:#7f8c8d}
.badge-destaque{background:#fff3cd;color:#856404;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;display:inline-block;margin-top:4px}
</style>

<main class="content">
    <div class="confirm-container">
        <div class="confirm-icon">⚠️</div>
        <h3>Confirmar Exclusão</h3>
        <p>Você tem certeza que deseja excluir este post?</p>

        <div class="info-box">
            <p class="post-title-preview"><?= $post['destaque'] ? '⭐ ' : '' ?><?= htmlspecialchars($post['titulo']) ?></p>
            <?php if ($post['destaque']): ?>
                <span class="badge-destaque">⭐ Post em destaque</span><br><br>
            <?php endif; ?>
            <p><strong>🔗 Slug:</strong> <?= htmlspecialchars($post['slug']) ?></p>
            <p><strong>🏷️ Categoria:</strong> <?= $post['categoria_nome'] ? htmlspecialchars($post['categoria_nome']) : '<em>Sem categoria</em>' ?></p>
            <p><strong>✍️ Autor:</strong> <?= htmlspecialchars($post['autor']) ?></p>
            <p><strong>📅 Publicado em:</strong> <?= date('d/m/Y H:i', strtotime($post['publicado_em'])) ?></p>
            <p><strong>👁️ Visualizações:</strong> <?= number_format($post['visualizacoes']) ?></p>
            <p><strong>✅ Status:</strong> <?= $post['ativo'] ? 'Publicado' : 'Rascunho' ?></p>
        </div>

        <div class="warning">
            <strong>⚠️ Atenção!</strong> Esta ação não pode ser desfeita. O post será permanentemente removido.
        </div>

        <form method="POST">
            <div class="form-actions">
                <button type="submit" name="confirm" class="btn-danger">🗑️ Sim, Excluir Post</button>
                <a href="<?= BASE_URL ?>/admin/blog/posts/index.php" class="btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
