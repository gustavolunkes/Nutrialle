<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Links da Coluna';
$current_module = 'rodape';
$current_page   = 'rodape-links';

$db        = getDB();
$coluna_id = (int)($_GET['coluna_id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM rodape_colunas WHERE id=?");
$stmt->execute([$coluna_id]);
$coluna = $stmt->fetch();

if (!$coluna) {
    $_SESSION['error'] = 'Coluna não encontrada.';
    header('Location: ' . BASE_URL . '/admin/rodape/index.php');
    exit;
}

$links = $db->prepare("SELECT * FROM rodape_links WHERE coluna_id=? ORDER BY ordem ASC");
$links->execute([$coluna_id]);
$links = $links->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--bg:#f4f6fb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.pg-actions{display:flex;gap:8px;flex-wrap:wrap}
.breadcrumb{font-size:12px;color:var(--muted);margin-bottom:18px}
.breadcrumb a{color:var(--accent);text-decoration:none}
.breadcrumb a:hover{text-decoration:underline}
.tcard{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden}
.card-hd{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand)}
table{width:100%;border-collapse:collapse}
thead{background:#f8f9fa}
th{padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:11px 16px;border-bottom:1px solid #f0f0f0;font-size:13px;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafafa}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-on{background:#ecfdf5;color:#065f46}.badge-off{background:#fef2f2;color:#991b1b}
.badge-blue{background:#eff6ff;color:#1d4ed8}
code{background:#f3f4f6;padding:2px 7px;border-radius:5px;font-size:12px;color:var(--accent);font-family:monospace}
.acts{display:flex;gap:6px}
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-blue{background:#3b82f6;color:#fff}.btn-blue:hover{background:#2563eb}
.btn-red{background:var(--err);color:#fff}.btn-red:hover{background:#dc2626}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.btn-sm{padding:5px 11px;font-size:11px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.empty{text-align:center;padding:48px 20px;color:var(--muted);font-size:13px}
.col-info{background:var(--brand);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:24px;margin-bottom:22px}
.ci-label{font-size:10px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.5px}
.ci-val{font-size:13px;font-weight:700;color:#fff}
</style>
<main class="content">
<div class="wrap">

    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/admin/rodape/index.php">🦶 Rodapé</a> › 
        <a href="<?= BASE_URL ?>/admin/rodape/colunas/editar.php?id=<?= $coluna['id'] ?>"><?= htmlspecialchars($coluna['titulo']) ?></a> › 
        Links
    </div>

    <div class="pg-header">
        <h2>🔗 Links — <?= htmlspecialchars($coluna['titulo']) ?></h2>
        <div class="pg-actions">
            <a href="<?= BASE_URL ?>/admin/rodape/colunas/editar.php?id=<?= $coluna['id'] ?>" class="btn btn-ghost">✏️ Editar Coluna</a>
            <a href="<?= BASE_URL ?>/admin/rodape/links/criar.php?coluna_id=<?= $coluna['id'] ?>" class="btn btn-dark">➕ Novo Link</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-ok">✅ <?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-err">⚠️ <?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="col-info">
        <div><div class="ci-label">Coluna</div><div class="ci-val"><?= htmlspecialchars($coluna['titulo']) ?></div></div>
        <div><div class="ci-label">Status</div><div class="ci-val"><?= $coluna['ativo'] ? '● Ativa' : '● Inativa' ?></div></div>
        <div><div class="ci-label">Total de Links</div><div class="ci-val"><?= count($links) ?></div></div>
        <div><div class="ci-label">Ordem</div><div class="ci-val"><?= $coluna['ordem'] ?></div></div>
    </div>

    <div class="tcard">
        <div class="card-hd">
            <h3>🔗 Links da Coluna</h3>
            <a href="<?= BASE_URL ?>/admin/rodape/links/criar.php?coluna_id=<?= $coluna['id'] ?>" class="btn btn-dark btn-sm">➕ Novo Link</a>
        </div>
        <?php if ($links): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Label</th>
                    <th>URL</th>
                    <th>Ordem</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($links as $link): ?>
            <tr>
                <td style="color:var(--muted);font-size:12px">#<?= $link['id'] ?></td>
                <td><strong><?= htmlspecialchars($link['label']) ?></strong></td>
                <td><code><?= htmlspecialchars(mb_substr($link['url'], 0, 45)) ?><?= strlen($link['url']) > 45 ? '…' : '' ?></code></td>
                <td><span class="badge badge-blue">📍 <?= $link['ordem'] ?></span></td>
                <td><span class="badge badge-<?= $link['ativo'] ? 'on' : 'off' ?>"><?= $link['ativo'] ? '● On' : '● Off' ?></span></td>
                <td>
                    <div class="acts">
                        <a href="<?= BASE_URL ?>/admin/rodape/links/editar.php?id=<?= $link['id'] ?>" class="btn btn-blue btn-sm">✏️</a>
                        <a href="<?= BASE_URL ?>/admin/rodape/links/deletar.php?id=<?= $link['id'] ?>&coluna_id=<?= $coluna['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Remover este link?')">🗑️</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">
                <div style="font-size:40px;margin-bottom:10px">🔗</div>
                Nenhum link nesta coluna ainda.
                <br><a href="<?= BASE_URL ?>/admin/rodape/links/criar.php?coluna_id=<?= $coluna['id'] ?>" class="btn btn-dark" style="margin-top:12px">➕ Criar primeiro link</a>
            </div>
        <?php endif; ?>
    </div>

    <div style="margin-top:16px">
        <a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost">← Voltar ao Rodapé</a>
    </div>

</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
