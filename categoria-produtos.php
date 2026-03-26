<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

// O .htaccess passa: categoria-produtos.php?slug=telefone
$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    include __DIR__ . '/404.php';
    exit;
}

// Busca a categoria de produto pelo slug
$stmt = $db->prepare("SELECT * FROM categorias_produtos WHERE slug = ? AND ativo = 1");
$stmt->execute([$slug]);
$cat_produto = $stmt->fetch(); // nome diferente de $cat para não ser sobrescrito pelo header
if (!$cat_produto) {
    include __DIR__ . '/404.php';
    exit;
}

// Filtros e ordenação
$filtro    = trim($_GET['q'] ?? '');
$order     = $_GET['order'] ?? 'nome';
$order_map = [
    'nome'       => 'p.nome ASC',
    'recente'    => 'p.created_at DESC',
    'preco_asc'  => 'p.preco ASC',
    'preco_desc' => 'p.preco DESC',
];
$order_sql = $order_map[$order] ?? 'p.nome ASC';

$where_extra = '';
$params = [$cat_produto['id']];
if ($filtro) {
    $where_extra = ' AND (p.nome LIKE ? OR p.sku LIKE ? OR p.descricao_curta LIKE ?)';
    $params      = array_merge($params, ["%$filtro%", "%$filtro%", "%$filtro%"]);
}

$stmt = $db->prepare("
    SELECT p.*
    FROM produtos p
    JOIN produto_categorias pc ON pc.produto_id = p.id
    WHERE pc.categoria_produto_id = ? AND p.ativo = 1
    $where_extra
    ORDER BY $order_sql
");
$stmt->execute($params);
$produtos = $stmt->fetchAll();

$page_title       = $cat_produto['nome'];
$meta_description = $cat_produto['meta_description'] ?? '';
$meta_keywords    = $cat_produto['meta_keywords'] ?? '';
$cat_url          = BASE_URL . '/produtos/' . $cat_produto['slug'];

include __DIR__ . '/includes/header.php';
?>
<style>
.cat-hero{background:linear-gradient(135deg,#00071c 0%,#1a2540 100%);color:#fff;padding:48px 24px 36px;text-align:center}   
.cat-hero img{width:80px;height:80px;border-radius:12px;object-fit:cover;margin-bottom:16px;border:3px solid rgba(255,255,255,.2)}
.cat-hero h1{font-size:28px;font-weight:800;margin:0 0 8px}
.cat-hero p{font-size:14px;opacity:.75;max-width:600px;margin:0 auto}
.cat-body{max-width:1200px;margin:0 auto;padding:32px 20px}
.cat-toolbar{display:flex;gap:12px;margin-bottom:28px;flex-wrap:wrap;align-items:center}
.search-wrap{flex:1;min-width:200px;position:relative}
.search-wrap input{width:100%;padding:11px 14px 11px 38px;border:2px solid #e5e7eb;border-radius:9px;font-size:14px;font-family:inherit;background:#fff;box-sizing:border-box;transition:border-color .2s}
.search-wrap input:focus{outline:none;border-color:#4f6ef7;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
.search-wrap::before{content:'🔍';position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none}
.order-sel{padding:11px 14px;border:2px solid #e5e7eb;border-radius:9px;font-size:13px;font-family:inherit;background:#fff;cursor:pointer}
.order-sel:focus{outline:none;border-color:#4f6ef7}
.count-label{font-size:13px;color:#6b7280;white-space:nowrap}
.prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px}
.prod-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.07);overflow:hidden;transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column}
.prod-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.12)}
.prod-img{width:100%;aspect-ratio:1;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:48px;text-decoration:none;overflow:hidden}
.prod-img img{width:100%;height:100%;object-fit:cover}
.prod-body{padding:16px;flex:1;display:flex;flex-direction:column;gap:8px}
.prod-sku{font-family:monospace;font-size:11px;font-weight:700;color:#8b5cf6;background:#ede9fe;padding:2px 8px;border-radius:5px;display:inline-block}
.prod-name{font-size:15px;font-weight:700;color:#111827;line-height:1.35}
.prod-desc{font-size:12px;color:#6b7280;line-height:1.5;flex:1}
.prod-preco{margin-top:auto}
.preco-old{font-size:12px;color:#9ca3af;text-decoration:line-through}
.preco-val{font-size:17px;font-weight:800;color:#00071c}
.preco-promo{font-size:17px;font-weight:800;color:#10b981}
.prod-foot{padding:0 16px 16px}
.btn-detalhe{display:block;text-align:center;padding:9px;background:#00071c;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:background .18s}
.btn-detalhe:hover{background:#1a2540}
.badge-dest{display:inline-flex;align-items:center;gap:3px;background:#fef9c3;color:#854d0e;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;margin-bottom:4px}
.empty{text-align:center;padding:80px 20px;color:#9ca3af}
</style>

<div class="cat-hero">
    <?php if (!empty($cat_produto['imagem'])): ?>
        <img src="<?= htmlspecialchars($cat_produto['imagem']) ?>" alt="<?= htmlspecialchars($cat_produto['nome']) ?>">
    <?php endif; ?>
    <h1><?= htmlspecialchars($cat_produto['nome']) ?></h1>
    <?php if (!empty($cat_produto['descricao'])): ?>
        <p><?= htmlspecialchars($cat_produto['descricao']) ?></p>
    <?php endif; ?>
</div>

<div class="cat-body">
    <form method="GET" action="<?= $cat_url ?>" class="cat-toolbar">
        <div class="search-wrap">
            <input type="text" name="q" value="<?= htmlspecialchars($filtro) ?>" placeholder="Buscar produto nesta categoria...">
        </div>
        <select name="order" class="order-sel" onchange="this.form.submit()">
            <option value="nome"       <?= $order === 'nome'       ? 'selected' : '' ?>>A-Z</option>
            <option value="recente"    <?= $order === 'recente'    ? 'selected' : '' ?>>Mais recentes</option>
            <option value="preco_asc"  <?= $order === 'preco_asc'  ? 'selected' : '' ?>>Menor preço</option>
            <option value="preco_desc" <?= $order === 'preco_desc' ? 'selected' : '' ?>>Maior preço</option>
        </select>
        <span class="count-label"><?= count($produtos) ?> produto<?= count($produtos) !== 1 ? 's' : '' ?></span>
    </form>

    <?php if (empty($produtos)): ?>
        <div class="empty">
            <div style="font-size:56px;margin-bottom:12px">🔍</div>
            <h3 style="color:#374151;margin-bottom:8px">
                <?= $filtro ? 'Nenhum resultado encontrado' : 'Nenhum produto nesta categoria' ?>
            </h3>
            <?php if ($filtro): ?>
                <p style="font-size:13px;margin-bottom:16px">Tente outros termos de busca.</p>
                <a href="<?= $cat_url ?>" style="color:#4f6ef7;font-size:13px;font-weight:600">← Ver todos</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
    <div class="prod-grid">
        <?php foreach ($produtos as $prod): ?>
        <div class="prod-card">
            <a href="<?= BASE_URL ?>/produto/<?= urlencode($prod['sku']) ?>" class="prod-img">
                <?php if (!empty($prod['imagem_principal'])): ?>
                    <img src="<?= htmlspecialchars($prod['imagem_principal']) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                <?php else: ?>
                    📦
                <?php endif; ?>
            </a>
            <div class="prod-body">
                <?php if (!empty($prod['destaque'])): ?>
                    <span class="badge-dest">⭐ Destaque</span>
                <?php endif; ?>
                <span class="prod-sku"><?= htmlspecialchars($prod['sku']) ?></span>
                <div class="prod-name"><?= htmlspecialchars($prod['nome']) ?></div>
                <?php if (!empty($prod['descricao_curta'])): ?>
                    <div class="prod-desc"><?= htmlspecialchars($prod['descricao_curta']) ?></div>
                <?php endif; ?>
                <?php if (!empty($prod['preco'])): ?>
                <div class="prod-preco">
                    <?php if (!empty($prod['preco_promocional'])): ?>
                        <div class="preco-old">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                        <div class="preco-promo">R$ <?= number_format($prod['preco_promocional'], 2, ',', '.') ?></div>
                    <?php else: ?>
                        <div class="preco-val">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="prod-foot">
                <a href="<?= BASE_URL ?>/produto/<?= urlencode($prod['sku']) ?>" class="btn-detalhe">Ver detalhes →</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
</body>
</html>
