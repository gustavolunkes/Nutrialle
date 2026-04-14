<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    include __DIR__ . '/404.php';
    exit;
}

$stmt = $db->prepare("SELECT * FROM categorias_produtos WHERE slug = ? AND ativo = 1");
$stmt->execute([$slug]);
$cat_produto = $stmt->fetch();
if (!$cat_produto) {
    include __DIR__ . '/404.php';
    exit;
}

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
    $where_extra = ' AND (p.nome LIKE ? OR p.descricao_curta LIKE ?)';
    $params      = array_merge($params, ["%$filtro%", "%$filtro%"]);
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
/* ── Hero ── */
.cat-hero {
    background: #00071c;
    color: #fff;
    padding: 56px 24px 44px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cat-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 120%, rgba(79,110,247,.18) 0%, transparent 70%);
    pointer-events: none;
}
.cat-hero-icon {
    width: 72px;
    height: 72px;
    border-radius: 16px;
    object-fit: cover;
    margin-bottom: 20px;
    border: 2px solid rgba(255,255,255,.15);
}
.cat-hero h1 {
    font-size: 30px;
    font-weight: 800;
    margin: 0 0 10px;
    letter-spacing: -.5px;
}
.cat-hero p {
    font-size: 14px;
    opacity: .6;
    max-width: 520px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ── Body ── */
.cat-body {
    max-width: 1200px;
    margin: 0 auto;
    padding: 36px 20px 60px;
}

/* ── Toolbar ── */
.cat-toolbar {
    display: flex;
    gap: 10px;
    margin-bottom: 32px;
    flex-wrap: wrap;
    align-items: center;
}
.search-wrap {
    flex: 1;
    min-width: 200px;
    position: relative;
}
.search-wrap svg {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    width: 15px;
    height: 15px;
    color: #9ca3af;
    pointer-events: none;
}
.search-wrap input {
    width: 100%;
    padding: 11px 14px 11px 40px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    box-sizing: border-box;
    transition: border-color .2s, box-shadow .2s;
    color: #111827;
}
.search-wrap input:focus {
    outline: none;
    border-color: #4f6ef7;
    box-shadow: 0 0 0 3px rgba(79,110,247,.1);
}
.order-sel {
    padding: 11px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 13px;
    font-family: inherit;
    background: #fff;
    cursor: pointer;
    color: #374151;
}
.order-sel:focus { outline: none; border-color: #4f6ef7; }
.count-label {
    font-size: 13px;
    color: #9ca3af;
    white-space: nowrap;
    padding: 0 4px;
}

/* ── Grid ── */
.prod-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 22px;
}

/* ── Card ── */
.prod-card {
    background: #fff;
    border-radius: 16px;
    border: 1.5px solid #f0f0f2;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .2s, box-shadow .2s, border-color .2s;
}
.prod-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,7,28,.1);
    border-color: #e0e2ef;
}
.prod-img-wrap {
    width: 100%;
    aspect-ratio: 1;
    background: #f8f8fb;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    overflow: hidden;
    position: relative;
}
.prod-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .35s;
}
.prod-card:hover .prod-img-wrap img { transform: scale(1.04); }
.prod-img-placeholder {
    width: 56px;
    height: 56px;
    background: #ececf3;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.prod-img-placeholder svg {
    width: 28px;
    height: 28px;
    color: #b0b0c8;
}
.badge-dest {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #fef3c7;
    color: #92400e;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
    letter-spacing: .2px;
}
.prod-body {
    padding: 18px 18px 8px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.prod-name {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    line-height: 1.35;
}
.prod-desc {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.55;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.prod-preco {
    margin-top: 10px;
}
.preco-old {
    font-size: 12px;
    color: #9ca3af;
    text-decoration: line-through;
    margin-bottom: 1px;
}
.preco-val {
    font-size: 18px;
    font-weight: 800;
    color: #00071c;
    letter-spacing: -.3px;
}
.preco-promo {
    font-size: 18px;
    font-weight: 800;
    color: #059669;
    letter-spacing: -.3px;
}
.preco-economia {
    display: inline-block;
    margin-top: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #059669;
    background: #d1fae5;
    padding: 2px 8px;
    border-radius: 20px;
}
.prod-foot {
    padding: 12px 18px 18px;
}
.btn-detalhe {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px;
    background: #00071c;
    color: #fff;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background .18s;
}
.btn-detalhe:hover { background: #1a2540; }
.btn-detalhe svg { width: 14px; height: 14px; }

/* ── Empty ── */
.empty {
    text-align: center;
    padding: 80px 20px;
    color: #9ca3af;
}
.empty-icon {
    width: 56px;
    height: 56px;
    background: #f3f4f6;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}
.empty-icon svg { width: 26px; height: 26px; color: #d1d5db; }
</style>

<div class="cat-hero">
    <?php if (!empty($cat_produto['imagem'])): ?>
        <img class="cat-hero-icon" src="<?= htmlspecialchars($cat_produto['imagem']) ?>" alt="<?= htmlspecialchars($cat_produto['nome']) ?>">
    <?php endif; ?>
    <h1><?= htmlspecialchars($cat_produto['nome']) ?></h1>
    <?php if (!empty($cat_produto['descricao'])): ?>
        <p><?= htmlspecialchars($cat_produto['descricao']) ?></p>
    <?php endif; ?>
</div>

<div class="cat-body">
    <form method="GET" action="<?= $cat_url ?>" class="cat-toolbar">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" name="q" value="<?= htmlspecialchars($filtro) ?>" placeholder="Buscar produto...">
        </div>
        <select name="order" class="order-sel" onchange="this.form.submit()">
            <option value="nome"       <?= $order === 'nome'       ? 'selected' : '' ?>>A–Z</option>
            <option value="recente"    <?= $order === 'recente'    ? 'selected' : '' ?>>Mais recentes</option>
            <option value="preco_asc"  <?= $order === 'preco_asc'  ? 'selected' : '' ?>>Menor preço</option>
            <option value="preco_desc" <?= $order === 'preco_desc' ? 'selected' : '' ?>>Maior preço</option>
        </select>
        <span class="count-label"><?= count($produtos) ?> produto<?= count($produtos) !== 1 ? 's' : '' ?></span>
    </form>

    <?php if (empty($produtos)): ?>
        <div class="empty">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#374151;margin:0 0 8px">
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
            <a href="<?= BASE_URL ?>/produto/<?= urlencode($prod['sku']) ?>" class="prod-img-wrap">
                <?php if (!empty($prod['imagem_principal'])): ?>
                    <img src="<?= htmlspecialchars($prod['imagem_principal']) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                <?php else: ?>
                    <div class="prod-img-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            <path d="M16 3H8L6 7h12l-2-4z"/>
                        </svg>
                    </div>
                <?php endif; ?>
                <?php if (!empty($prod['destaque'])): ?>
                    <span class="badge-dest">★ Destaque</span>
                <?php endif; ?>
            </a>

            <div class="prod-body">
                <div class="prod-name"><?= htmlspecialchars($prod['nome']) ?></div>
                <?php if (!empty($prod['descricao_curta'])): ?>
                    <div class="prod-desc"><?= htmlspecialchars($prod['descricao_curta']) ?></div>
                <?php endif; ?>
                <?php if (!empty($prod['preco'])): ?>
                <div class="prod-preco">
                    <?php if (!empty($prod['preco_promocional'])): ?>
                        <div class="preco-old">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                        <div class="preco-promo">R$ <?= number_format($prod['preco_promocional'], 2, ',', '.') ?></div>
                        <?php $eco = round((($prod['preco'] - $prod['preco_promocional']) / $prod['preco']) * 100); ?>
                        <span class="preco-economia">–<?= $eco ?>% de desconto</span>
                    <?php else: ?>
                        <div class="preco-val">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="prod-foot">
                <a href="<?= BASE_URL ?>/produto/<?= urlencode($prod['sku']) ?>" class="btn-detalhe">
                    Ver detalhes
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                </a>
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