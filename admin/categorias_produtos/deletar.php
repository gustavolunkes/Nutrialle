<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
$db=getDB(); $id=intval($_GET['id']??0);
$stmt=$db->prepare("SELECT * FROM categorias_produtos WHERE id=?"); $stmt->execute([$id]); $cat=$stmt->fetch();
if(!$cat){$_SESSION['error']='Categoria não encontrada.';header('Location: '.BASE_URL.'/admin/categorias_produtos/index.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['confirm'])){
    $db->prepare("DELETE FROM produto_categorias WHERE categoria_produto_id=?")->execute([$id]);
    $db->prepare("DELETE FROM categorias_produtos WHERE id=?")->execute([$id]);
    $_SESSION['success']='Categoria excluída. Produtos mantidos intactos.';
    header('Location: '.BASE_URL.'/admin/categorias_produtos/index.php'); exit;
}
$page_title='Excluir Categoria de Produto'; $current_module='produtos'; $current_page='cat-produtos-lista';
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.wrap{padding:28px;max-width:520px}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:36px;text-align:center}
.info-box{background:#f8f9fa;border-radius:8px;padding:16px;margin:16px 0;text-align:left;font-size:13px;border:1px solid #e5e7eb}
.warn{background:#fffbeb;color:#92400e;border-left:4px solid #f59e0b;padding:12px 16px;border-radius:8px;font-size:13px;margin:16px 0}
.acts{display:flex;gap:10px;justify-content:center;margin-top:24px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-red{background:#ef4444;color:#fff}.btn-red:hover{background:#dc2626}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}.btn-ghost:hover{background:#e5e7eb}
</style>
<main class="content">
<div class="wrap"><div class="card">
    <div style="font-size:52px;margin-bottom:12px">⚠️</div>
    <h3 style="font-size:18px;font-weight:700;color:#00071c;margin-bottom:8px">Confirmar Exclusão</h3>
    <p style="font-size:13px;color:#6b7280">Esta ação não pode ser desfeita.</p>
    <div class="info-box">
        <p><strong>📦 Nome:</strong> <?= htmlspecialchars($cat['nome']) ?></p>
        <p><strong>🔗 Slug:</strong> /<?= htmlspecialchars($cat['slug']) ?></p>
    </div>
    <div class="warn">⚠️ Os vínculos no menu serão removidos. Os produtos associados serão mantidos, mas desvinculados desta categoria.</div>
    <form method="POST"><div class="acts">
        <button type="submit" name="confirm" class="btn btn-red">🗑️ Sim, excluir</button>
        <a href="<?= BASE_URL ?>/admin/categorias_produtos/index.php" class="btn btn-ghost">Cancelar</a>
    </div></form>
</div></div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
