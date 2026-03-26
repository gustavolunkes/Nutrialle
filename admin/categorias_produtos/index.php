<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Categorias de Produtos';
$current_module = 'produtos';
$current_page   = 'cat-produtos-lista';

$db = getDB();

$cats = $db->query("
    SELECT cp.*, COUNT(pc.produto_id) AS total_produtos
    FROM categorias_produtos cp
    LEFT JOIN produto_categorias pc ON pc.categoria_produto_id = cp.id
    GROUP BY cp.id
    ORDER BY cp.ordem ASC, cp.nome ASC
")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.pg-wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.stats{display:flex;gap:14px;margin-bottom:20px;flex-wrap:wrap}
.stat{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:16px 22px;flex:1;min-width:110px}
.stat-n{font-size:26px;font-weight:800;color:#00071c}
.stat-l{font-size:12px;color:#6b7280;margin-top:2px}
.tcard{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden}
table{width:100%;border-collapse:collapse}
thead{background:#f8f9fa}
th{padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:12px 16px;border-bottom:1px solid #f0f0f0;font-size:13px;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafafa}
.thumb{width:42px;height:42px;border-radius:7px;object-fit:cover;border:2px solid #e5e7eb}
.thumb-ph{width:42px;height:42px;border-radius:7px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:20px;border:2px solid #e5e7eb}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.b-on{background:#ecfdf5;color:#065f46}
.b-off{background:#fef2f2;color:#991b1b}
.b-prod{background:#ede9fe;color:#5b21b6}
code{background:#f3f4f6;padding:2px 7px;border-radius:5px;font-size:12px;color:#4f6ef7;font-family:monospace}
.acts{display:flex;gap:6px;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:#00071c;color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-blue{background:#3b82f6;color:#fff}.btn-blue:hover{background:#2563eb}
.btn-red{background:#ef4444;color:#fff}.btn-red:hover{background:#dc2626}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid #10b981}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid #ef4444}
.empty{text-align:center;padding:60px 20px}
.url-hint{font-size:11px;color:#9ca3af;margin-top:3px}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <h2>📦 Categorias de Produtos</h2>
        <a href="<?= BASE_URL ?>/admin/categorias_produtos/criar.php" class="btn btn-dark">➕ Nova Categoria</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-ok">✅ <?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-err">⚠️ <?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php $ativas = count(array_filter($cats, fn($c) => $c['ativo'])); ?>
    <div class="stats">
        <div class="stat"><div class="stat-n"><?= count($cats) ?></div><div class="stat-l">Total</div></div>
        <div class="stat"><div class="stat-n" style="color:#10b981"><?= $ativas ?></div><div class="stat-l">Ativas</div></div>
        <div class="stat"><div class="stat-n" style="color:#8b5cf6"><?= count($cats) - $ativas ?></div><div class="stat-l">Inativas</div></div>
    </div>

    <div class="tcard">
        <?php if (!empty($cats)): ?>
        <table>
            <thead>
                <tr><th>Img</th><th>Nome</th><th>URL</th><th>Produtos</th><th>Ordem</th><th>Status</th><th>Ações</th></tr>
            </thead>
            <tbody>
            <?php foreach ($cats as $c): ?>
                <tr>
                    <td>
                        <?php if ($c['imagem']): ?>
                            <img src="<?= htmlspecialchars($c['imagem']) ?>" class="thumb">
                        <?php else: ?>
                            <div class="thumb-ph">📦</div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                    <td>
                        <code>/produtos/<?= htmlspecialchars($c['slug']) ?></code>
                        <div class="url-hint">Cole este link no menu de navegação</div>
                    </td>
                    <td><span class="badge b-prod">🛍️ <?= $c['total_produtos'] ?></span></td>
                    <td style="color:#6b7280"><?= $c['ordem'] ?></td>
                    <td><span class="badge <?= $c['ativo'] ? 'b-on' : 'b-off' ?>"><?= $c['ativo'] ? '● Ativa' : '● Inativa' ?></span></td>
                    <td>
                        <div class="acts">
                            <a href="<?= BASE_URL ?>/admin/categorias_produtos/editar.php?id=<?= $c['id'] ?>" class="btn btn-blue">✏️ Editar</a>
                            <a href="<?= BASE_URL ?>/admin/categorias_produtos/deletar.php?id=<?= $c['id'] ?>" class="btn btn-red"
                               onclick="return confirm('Excluir a categoria \'<?= htmlspecialchars(addslashes($c['nome'])) ?>\'?\n\nOs produtos serão mantidos.')">🗑️</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty">
            <div style="font-size:52px;margin-bottom:12px">📦</div>
            <h3 style="margin-bottom:8px;color:#111827">Nenhuma categoria de produto</h3>
            <p style="color:#6b7280;margin-bottom:18px;font-size:13px">Crie categorias para organizar e exibir seus produtos no site</p>
            <a href="<?= BASE_URL ?>/admin/categorias_produtos/criar.php" class="btn btn-dark">➕ Criar primeira categoria</a>
        </div>
        <?php endif; ?>
    </div>
</div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>