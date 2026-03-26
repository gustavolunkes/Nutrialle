<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Produtos';
$current_module = 'produtos';
$current_page   = 'produtos-lista';

$db = getDB();

$filtro_q   = trim($_GET['q']   ?? '');
$filtro_cat = intval($_GET['cat'] ?? 0);
$filtro_status = $_GET['status'] ?? '';

$where  = 'WHERE 1=1';
$params = [];

if ($filtro_q) {
    $where .= ' AND (p.nome LIKE ? OR p.sku LIKE ? OR p.descricao_curta LIKE ?)';
    $params = array_merge($params, ["%$filtro_q%", "%$filtro_q%", "%$filtro_q%"]);
}
if ($filtro_cat) {
    $where .= ' AND EXISTS (SELECT 1 FROM produto_categorias pc2 WHERE pc2.produto_id = p.id AND pc2.categoria_produto_id = ?)';
    $params[] = $filtro_cat;
}
if ($filtro_status === 'ativo')    { $where .= ' AND p.ativo = 1'; }
if ($filtro_status === 'inativo')  { $where .= ' AND p.ativo = 0'; }
if ($filtro_status === 'destaque') { $where .= ' AND p.destaque = 1'; }

$produtos = $db->prepare("
    SELECT p.*,
           GROUP_CONCAT(cp.nome ORDER BY cp.nome SEPARATOR ', ') AS categorias_nomes
    FROM produtos p
    LEFT JOIN produto_categorias pc ON pc.produto_id = p.id
    LEFT JOIN categorias_produtos cp ON cp.id = pc.categoria_produto_id
    $where
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$produtos->execute($params);
$produtos = $produtos->fetchAll();

$categorias = $db->query("SELECT id, nome FROM categorias_produtos WHERE ativo = 1 ORDER BY nome")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.pg-wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.stats{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.stat{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:14px 20px;flex:1;min-width:100px}
.stat-n{font-size:24px;font-weight:800;color:#00071c}
.stat-l{font-size:11px;color:#6b7280;margin-top:2px}
.filters{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
.filters input,.filters select{padding:9px 13px;border:2px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;transition:border-color .2s}
.filters input:focus,.filters select:focus{outline:none;border-color:#4f6ef7}
.filters input{min-width:220px}
.tcard{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden}
table{width:100%;border-collapse:collapse}
thead{background:#f8f9fa}
th{padding:11px 14px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:11px 14px;border-bottom:1px solid #f0f0f0;font-size:13px;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafafa}
.thumb{width:44px;height:44px;border-radius:8px;object-fit:cover;border:2px solid #e5e7eb}
.thumb-ph{width:44px;height:44px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:22px;border:2px solid #e5e7eb}
.sku{font-family:monospace;background:#f3f4f6;color:#4f6ef7;padding:2px 8px;border-radius:5px;font-size:12px;font-weight:700}
.badge{display:inline-flex;align-items:center;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700}
.b-on{background:#ecfdf5;color:#065f46}
.b-off{background:#fef2f2;color:#991b1b}
.b-dest{background:#fef9c3;color:#854d0e}
.b-cat{background:#ede9fe;color:#5b21b6}
.cats-wrap{display:flex;flex-wrap:wrap;gap:3px}
.cat-tag{background:#ede9fe;color:#5b21b6;padding:1px 7px;border-radius:12px;font-size:11px;font-weight:600}
.preco{font-weight:700;color:#111827}
.preco-old{font-size:11px;color:#9ca3af;text-decoration:line-through}
.preco-promo{color:#10b981;font-weight:700}
.acts{display:flex;gap:5px}
.btn{display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-new{padding:9px 18px;font-size:13px}
.btn-dark{background:#00071c;color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-blue{background:#3b82f6;color:#fff}.btn-blue:hover{background:#2563eb}
.btn-red{background:#ef4444;color:#fff}.btn-red:hover{background:#dc2626}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid #10b981}
.empty{text-align:center;padding:60px 20px}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <h2>🛍️ Produtos</h2>
        <a href="<?= BASE_URL ?>/admin/produtos/criar.php" class="btn btn-dark btn-new">➕ Novo Produto</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-ok">✅ <?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php
    $total_ativos    = $db->query("SELECT COUNT(*) FROM produtos WHERE ativo=1")->fetchColumn();
    $total_inativo   = $db->query("SELECT COUNT(*) FROM produtos WHERE ativo=0")->fetchColumn();
    $total_destaque  = $db->query("SELECT COUNT(*) FROM produtos WHERE destaque=1")->fetchColumn();
    $total_geral     = $total_ativos + $total_inativo;
    ?>
    <div class="stats">
        <div class="stat"><div class="stat-n"><?= $total_geral ?></div><div class="stat-l">Total</div></div>
        <div class="stat"><div class="stat-n" style="color:#10b981"><?= $total_ativos ?></div><div class="stat-l">Ativos</div></div>
        <div class="stat"><div class="stat-n" style="color:#ef4444"><?= $total_inativo ?></div><div class="stat-l">Inativos</div></div>
        <div class="stat"><div class="stat-n" style="color:#f59e0b"><?= $total_destaque ?></div><div class="stat-l">Em destaque</div></div>
        <div class="stat"><div class="stat-n" style="color:#8b5cf6"><?= count($categorias) ?></div><div class="stat-l">Categorias</div></div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="filters">
        <input type="text" name="q" value="<?= htmlspecialchars($filtro_q) ?>" placeholder="🔍 Buscar por nome ou SKU...">
        <select name="cat">
            <option value="">Todas as categorias</option>
            <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $filtro_cat == $c['id'] ? 'selected' : '' ?>>📦 <?= htmlspecialchars($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <option value="">Todos os status</option>
            <option value="ativo"    <?= $filtro_status === 'ativo'    ? 'selected' : '' ?>>● Ativos</option>
            <option value="inativo"  <?= $filtro_status === 'inativo'  ? 'selected' : '' ?>>● Inativos</option>
            <option value="destaque" <?= $filtro_status === 'destaque' ? 'selected' : '' ?>>⭐ Destaque</option>
        </select>
        <button type="submit" style="padding:9px 18px;background:#00071c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">Filtrar</button>
        <?php if ($filtro_q || $filtro_cat || $filtro_status): ?>
        <a href="?" style="padding:9px 14px;color:#6b7280;font-size:13px;text-decoration:none">✕ Limpar</a>
        <?php endif; ?>
    </form>

    <div class="tcard">
        <?php if (!empty($produtos)): ?>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>SKU</th>
                    <th>Nome</th>
                    <th>Categorias</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($produtos as $p): ?>
            <tr>
                <td>
                    <?php if ($p['imagem_principal']): ?>
                        <img src="<?= htmlspecialchars($p['imagem_principal']) ?>" class="thumb">
                    <?php else: ?>
                        <div class="thumb-ph">📦</div>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="sku"><?= htmlspecialchars($p['sku']) ?></span>
                    <?php if ($p['destaque']): ?><br><span class="badge b-dest" style="margin-top:3px">⭐</span><?php endif; ?>
                </td>
                <td>
                    <strong><?= htmlspecialchars($p['nome']) ?></strong>
                    <?php if ($p['descricao_curta']): ?>
                        <br><span style="font-size:11px;color:#9ca3af"><?= htmlspecialchars(mb_substr($p['descricao_curta'], 0, 60)) ?><?= mb_strlen($p['descricao_curta']) > 60 ? '…' : '' ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($p['categorias_nomes']): ?>
                    <div class="cats-wrap">
                        <?php foreach (explode(', ', $p['categorias_nomes']) as $cn): ?>
                        <span class="cat-tag">📦 <?= htmlspecialchars($cn) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <span style="color:#9ca3af;font-size:11px">Sem categoria</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($p['preco']): ?>
                        <?php if ($p['preco_promocional']): ?>
                            <div class="preco-old">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
                            <div class="preco-promo">R$ <?= number_format($p['preco_promocional'], 2, ',', '.') ?></div>
                        <?php else: ?>
                            <div class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color:#9ca3af;font-size:11px">—</span>
                    <?php endif; ?>
                </td>
                <td><span class="badge <?= $p['ativo'] ? 'b-on' : 'b-off' ?>"><?= $p['ativo'] ? '● Ativo' : '● Inativo' ?></span></td>
                <td>
                    <div class="acts">
                        <a href="<?= BASE_URL ?>/admin/produtos/editar.php?id=<?= $p['id'] ?>" class="btn btn-blue" title="Editar produto">✏️ Editar</a>
                        <a href="<?= BASE_URL ?>/admin/produtos/deletar.php?id=<?= $p['id'] ?>" class="btn btn-red"
                           onclick="return confirm('Excluir o produto <?= htmlspecialchars(addslashes($p['sku'])) ?>?')" title="Excluir">🗑️</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty">
            <div style="font-size:52px;margin-bottom:12px">🛍️</div>
            <h3 style="margin-bottom:8px;color:#111827">
                <?= ($filtro_q || $filtro_cat || $filtro_status) ? 'Nenhum produto encontrado' : 'Nenhum produto cadastrado' ?>
            </h3>
            <p style="color:#6b7280;margin-bottom:18px;font-size:13px">
                <?= ($filtro_q || $filtro_cat || $filtro_status) ? 'Tente outros filtros.' : 'Cadastre seu primeiro produto.' ?>
            </p>
            <?php if (!$filtro_q && !$filtro_cat && !$filtro_status): ?>
            <a href="<?= BASE_URL ?>/admin/produtos/criar.php" class="btn btn-dark" style="padding:10px 22px;font-size:14px">➕ Criar primeiro produto</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
