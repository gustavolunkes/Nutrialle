<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$db = getDB();
$id = intval($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM blog_categorias WHERE id = ?");
$stmt->execute([$id]);
$cat = $stmt->fetch();

if (!$cat) {
    $_SESSION['error'] = 'Categoria não encontrada.';
    header('Location: ' . BASE_URL . '/admin/blog/categorias/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        $chk = $db->prepare("SELECT COUNT(*) as total FROM blog_posts WHERE categoria_id = ?");
        $chk->execute([$id]);
        $result = $chk->fetch();

        if ($result['total'] > 0) {
            $_SESSION['error'] = "Não é possível excluir: existem {$result['total']} post(s) vinculado(s) a esta categoria.";
            header('Location: ' . BASE_URL . '/admin/blog/categorias/index.php');
            exit;
        }

        $db->prepare("DELETE FROM blog_categorias WHERE id = ?")->execute([$id]);
        $_SESSION['success'] = 'Categoria excluída com sucesso!';
        header('Location: ' . BASE_URL . '/admin/blog/categorias/index.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao excluir: ' . $e->getMessage();
        header('Location: ' . BASE_URL . '/admin/blog/categorias/index.php');
        exit;
    }
}

// Conta posts vinculados
$stmtPosts = $db->prepare("SELECT COUNT(*) as total FROM blog_posts WHERE categoria_id = ?");
$stmtPosts->execute([$id]);
$totalPosts = $stmtPosts->fetch()['total'];

$page_title     = 'Excluir Categoria do Blog';
$current_module = 'blog';
$current_page   = 'blog-categorias';

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
.confirm-container{background:#fff;padding:40px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.05);max-width:500px;text-align:center}
.confirm-icon{font-size:80px;margin-bottom:20px}
.confirm-container h3{color:#00071c;margin-bottom:15px}
.confirm-container p{color:#7f8c8d;margin-bottom:10px;line-height:1.6}
.info-box{background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;text-align:left}
.info-box p{margin:8px 0;font-size:14px}
.color-dot{display:inline-block;width:12px;height:12px;border-radius:50%;vertical-align:middle;margin-right:4px;border:1px solid rgba(0,0,0,.1)}
.warning{background:#fff3cd;color:#856404;padding:15px;border-radius:8px;margin:20px 0;font-size:14px;border-left:4px solid #ffc107}
.danger{background:#fef2f2;color:#991b1b;padding:15px;border-radius:8px;margin:20px 0;font-size:14px;border-left:4px solid #ef4444}
.form-actions{display:flex;gap:10px;justify-content:center;margin-top:30px}
.btn-danger{padding:12px 24px;background:#e74c3c;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;transition:all .3s}
.btn-danger:hover{background:#c0392b;transform:translateY(-2px)}
.btn-secondary{padding:12px 24px;background:#95a5a6;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;text-decoration:none;display:inline-block;transition:all .3s}
.btn-secondary:hover{background:#7f8c8d}
</style>

<main class="content">
    <div class="confirm-container">
        <div class="confirm-icon">⚠️</div>
        <h3>Confirmar Exclusão</h3>
        <p>Você tem certeza que deseja excluir esta categoria?</p>

        <div class="info-box">
            <p><strong>🏷️ Nome:</strong> <?= htmlspecialchars($cat['nome']) ?></p>
            <p><strong>🔗 Slug:</strong> <?= htmlspecialchars($cat['slug']) ?></p>
            <p><strong>🎨 Cor:</strong>
                <span class="color-dot" style="background:<?= htmlspecialchars($cat['cor']) ?>"></span>
                <?= htmlspecialchars($cat['cor']) ?>
            </p>
            <p><strong>📝 Posts vinculados:</strong> <?= $totalPosts ?></p>
            <p><strong>✅ Status:</strong> <?= $cat['ativo'] ? 'Ativa' : 'Inativa' ?></p>
        </div>

        <?php if ($totalPosts > 0): ?>
            <div class="danger">
                <strong>🚫 Não é possível excluir!</strong><br>
                Esta categoria possui <strong><?= $totalPosts ?> post(s)</strong> vinculado(s). Remova ou altere a categoria dos posts antes de excluí-la.
            </div>
            <div class="form-actions">
                <a href="<?= BASE_URL ?>/admin/blog/categorias/index.php" class="btn-secondary">← Voltar</a>
            </div>
        <?php else: ?>
            <div class="warning">
                <strong>⚠️ Atenção!</strong> Esta ação não pode ser desfeita.
            </div>
            <form method="POST">
                <div class="form-actions">
                    <button type="submit" name="confirm" class="btn-danger">🗑️ Sim, Excluir</button>
                    <a href="<?= BASE_URL ?>/admin/blog/categorias/index.php" class="btn-secondary">❌ Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
