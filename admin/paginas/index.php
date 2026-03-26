<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Páginas';
$current_module = 'paginas';
$current_page   = 'paginas-lista';

$db = getDB();
$paginas = $db->query("
    SELECT p.*, COUNT(c.id) as total_conteudos
    FROM paginas p
    LEFT JOIN conteudos c ON c.pagina_id = p.id
    GROUP BY p.id
    ORDER BY p.titulo ASC
")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.pg-wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.stats{display:flex;gap:14px;margin-bottom:22px;flex-wrap:wrap}
.stat{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:16px 22px;flex:1;min-width:110px}
.stat-n{font-size:26px;font-weight:800;color:#00071c}
.stat-l{font-size:12px;color:#6b7280;margin-top:2px}
.tcard{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden}
table{width:100%;border-collapse:collapse}
thead{background:#f8f9fa}
th{padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:13px 16px;border-bottom:1px solid #f0f0f0;font-size:13px;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafafa}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-on{background:#ecfdf5;color:#065f46}
.badge-off{background:#fef2f2;color:#991b1b}
.badge-ct{background:#ede9fe;color:#5b21b6}
code{background:#f3f4f6;padding:2px 7px;border-radius:5px;font-size:12px;color:#4f6ef7;font-family:monospace}
.acts{display:flex;gap:6px}
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:#00071c;color:#fff}
.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-blue{background:#3b82f6;color:#fff}
.btn-blue:hover{background:#2563eb}
.btn-red{background:#ef4444;color:#fff}
.btn-red:hover{background:#dc2626}
.btn-purple{background:#8b5cf6;color:#fff}
.btn-purple:hover{background:#7c3aed}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid #10b981}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid #ef4444}
.empty{text-align:center;padding:60px 20px}
.empty-i{font-size:52px;margin-bottom:12px}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <h2>📄 Páginas do Site</h2>
        <a href="<?= BASE_URL ?>/admin/paginas/criar.php" class="btn btn-dark">➕ Nova Página</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-ok">✅ <?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-err">⚠️ <?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php
    $ativas   = count(array_filter($paginas, fn($p) => $p['ativo']));
    $inativas = count($paginas) - $ativas;
    $blocos   = array_sum(array_column($paginas, 'total_conteudos'));
    ?>
    <div class="stats">
        <div class="stat"><div class="stat-n"><?= count($paginas) ?></div><div class="stat-l">Total de páginas</div></div>
        <div class="stat"><div class="stat-n" style="color:#10b981"><?= $ativas ?></div><div class="stat-l">Ativas</div></div>
        <div class="stat"><div class="stat-n" style="color:#ef4444"><?= $inativas ?></div><div class="stat-l">Inativas</div></div>
        <div class="stat"><div class="stat-n" style="color:#8b5cf6"><?= $blocos ?></div><div class="stat-l">Blocos de conteúdo</div></div>
    </div>

    <div class="tcard">
        <?php if (count($paginas)): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Título</th><th>Slug</th><th>Blocos</th><th>Status</th><th>Criado em</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($paginas as $p): ?>
                <tr>
                    <td style="color:#9ca3af;font-size:12px">#<?= $p['id'] ?></td>
                    <td><strong><?= htmlspecialchars($p['titulo']) ?></strong></td>
                    <td><code>/<?= htmlspecialchars($p['slug']) ?></code></td>
                    <td><span class="badge badge-ct">🧩 <?= $p['total_conteudos'] ?></span></td>
                    <td><span class="badge badge-<?= $p['ativo'] ? 'on' : 'off' ?>"><?= $p['ativo'] ? '● Ativa' : '● Inativa' ?></span></td>
                    <td style="color:#9ca3af;font-size:12px"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                    <td>
                        <div class="acts">
                            <a href="<?= BASE_URL ?>/admin/paginas/editar.php?id=<?= $p['id'] ?>" class="btn btn-blue">✏️ Editar</a>
                            <a href="<?= BASE_URL ?>/admin/paginas/editar.php?id=<?= $p['id'] ?>&tab=conteudos" class="btn btn-purple">🧩 Conteúdos</a>
                            <a href="<?= BASE_URL ?>/admin/paginas/deletar.php?id=<?= $p['id'] ?>" class="btn btn-red"
                               onclick="return confirm('Excluir a página e todos os seus conteúdos?')">🗑️</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty">
            <div class="empty-i">📄</div>
            <h3>Nenhuma página ainda</h3>
            <p style="color:#6b7280;margin:8px 0 18px">Crie sua primeira página para começar</p>
            <a href="<?= BASE_URL ?>/admin/paginas/criar.php" class="btn btn-dark">➕ Criar primeira página</a>
        </div>
        <?php endif; ?>
    </div>
</div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>