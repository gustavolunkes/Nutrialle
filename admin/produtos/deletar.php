<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$db = getDB();
$pid = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$pid]);
$p = $stmt->fetch();
if (!$p) { $_SESSION['error'] = 'Produto não encontrado.'; header('Location: ' . BASE_URL . '/admin/produtos/index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $db->prepare("DELETE FROM produto_categorias WHERE produto_id = ?")->execute([$pid]);
    $db->prepare("DELETE FROM produtos WHERE id = ?")->execute([$pid]);
    $_SESSION['success'] = 'Produto <strong>' . htmlspecialchars($p['sku']) . '</strong> excluído.';
    header('Location: ' . BASE_URL . '/admin/produtos/index.php'); exit;
}

$page_title = 'Excluir Produto'; $current_module = 'produtos'; $current_page = 'produtos-lista';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.wrap{padding:28px;max-width:500px}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:28px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-red{background:#ef4444;color:#fff}.btn-red:hover{background:#dc2626}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}.btn-ghost:hover{background:#e5e7eb}
</style>
<main class="content">
<div class="wrap">
    <div class="card">
        <div style="text-align:center;margin-bottom:20px">
            <div style="font-size:48px">🗑️</div>
            <h2 style="color:#00071c;margin:10px 0 4px">Excluir Produto</h2>
            <p style="color:#6b7280;font-size:13px">Esta ação não pode ser desfeita</p>
        </div>
        <div style="background:#fef2f2;border-radius:8px;padding:16px;margin-bottom:20px;border:1px solid #fecaca">
            <strong style="color:#991b1b"><?= htmlspecialchars($p['sku']) ?> — <?= htmlspecialchars($p['nome']) ?></strong><br>
            <span style="font-size:12px;color:#b91c1c">Os vínculos com categorias serão removidos.</span>
        </div>
        <form method="POST" style="display:flex;gap:10px;justify-content:center">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="btn btn-red">🗑️ Confirmar Exclusão</button>
            <a href="<?= BASE_URL ?>/admin/produtos/index.php" class="btn btn-ghost">Cancelar</a>
        </form>
    </div>
</div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
