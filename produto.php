<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

$sku = $_GET['sku'] ?? '';
if (empty($sku)) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path = ltrim(substr($requestUri, strlen($base)), '/');
    $path = strtok($path, '?');
    if (preg_match('#^produto/([A-Z0-9-]+)$#i', $path, $m)) {
        $sku = strtoupper($m[1]);
    }
}

if (empty($sku)) { include __DIR__ . '/404.php'; exit; }

$stmt = $db->prepare("SELECT * FROM produtos WHERE sku = ? AND ativo = 1");
$stmt->execute([strtoupper($sku)]);
$prod = $stmt->fetch();
if (!$prod) { include __DIR__ . '/404.php'; exit; }

// Categorias do produto
$cats_stmt = $db->prepare("
    SELECT cp.nome, cp.slug
    FROM categorias_produtos cp
    JOIN produto_categorias pc ON pc.categoria_produto_id = cp.id
    WHERE pc.produto_id = ? AND cp.ativo = 1
    ORDER BY cp.nome
");
$cats_stmt->execute([$prod['id']]);
$cats = $cats_stmt->fetchAll();

// Produtos relacionados
$sugestoes = [];
if (!empty($cats)) {
    $first_cat = $db->prepare("SELECT id FROM categorias_produtos WHERE slug = ?");
    $first_cat->execute([$cats[0]['slug']]);
    $fc = $first_cat->fetch();
    if ($fc) {
        $sug = $db->prepare("
            SELECT p.* FROM produtos p
            JOIN produto_categorias pc ON pc.produto_id = p.id
            WHERE pc.categoria_produto_id = ? AND p.id != ? AND p.ativo = 1
            ORDER BY RAND() LIMIT 4
        ");
        $sug->execute([$fc['id'], $prod['id']]);
        $sugestoes = $sug->fetchAll();
    }
}

// Monta link WhatsApp
$wpp_link = '';
if (!empty($prod['whatsapp_vendedor'])) {
    $numero  = preg_replace('/\D/', '', $prod['whatsapp_vendedor']);
    $msg     = urlencode('Olá! Tenho interesse no produto: ' . $prod['nome'] . ' (Ref: ' . $prod['sku'] . ')');
    $wpp_link = 'https://wa.me/' . $numero . '?text=' . $msg;
}

$page_title       = htmlspecialchars($prod['nome']);
$meta_description = $prod['meta_description'] ?? '';
$meta_keywords    = $prod['meta_keywords'] ?? '';

include __DIR__ . '/includes/header.php';
?>
<style>
/* ── Layout ── */
.prod-page { max-width: 1100px; margin: 0 auto; padding: 36px 20px 72px; }

/* ── Breadcrumb ── */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 32px;
    font-size: 13px;
    flex-wrap: wrap;
    color: #9ca3af;
}
.breadcrumb a { color: #6b7280; text-decoration: none; transition: color .15s; }
.breadcrumb a:hover { color: #111827; }
.breadcrumb-sep { font-size: 11px; color: #d1d5db; }
.breadcrumb-current { color: #374151; font-weight: 600; }

/* ── Main grid ── */
.prod-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 52px;
    margin-bottom: 52px;
    align-items: start;
}
@media (max-width: 720px) {
    .prod-layout { grid-template-columns: 1fr; gap: 28px; }
}

/* ── Imagem ── */
.prod-img-box { position: sticky; top: 20px; }
@media (max-width: 720px) { .prod-img-box { position: static; } }
.prod-main-img {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 18px;
    border: 1.5px solid #f0f0f2;
    background: #f8f8fb;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.prod-main-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 16px;
}
.prod-img-placeholder-lg {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    color: #c0c0d8;
}
.prod-img-placeholder-lg svg { width: 64px; height: 64px; }
.prod-img-placeholder-lg span { font-size: 13px; font-weight: 500; letter-spacing: .5px; }

/* ── Info ── */
.prod-info { display: flex; flex-direction: column; gap: 20px; }

.prod-cats {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.cat-badge {
    font-size: 12px;
    font-weight: 600;
    color: #3b5bdb;
    background: #eef2ff;
    padding: 4px 12px;
    border-radius: 20px;
    text-decoration: none;
    transition: background .15s;
}
.cat-badge:hover { background: #e0e7ff; }

.dest-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #92400e;
    background: #fef3c7;
    padding: 4px 10px;
    border-radius: 20px;
}

.prod-nome {
    font-size: 28px;
    font-weight: 800;
    color: #00071c;
    line-height: 1.2;
    letter-spacing: -.5px;
    margin: 0;
}

.prod-desc-curta {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.7;
    border-left: 3px solid #e9e9f0;
    padding-left: 16px;
    margin: 0;
}

/* ── Preço ── */
.preco-box {
    background: #f7f8fc;
    border-radius: 14px;
    border: 1.5px solid #ededf5;
    padding: 20px 22px;
}
.preco-label {
    font-size: 11px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 6px;
}
.preco-old {
    font-size: 14px;
    color: #9ca3af;
    text-decoration: line-through;
    margin-bottom: 2px;
}
.preco-val {
    font-size: 30px;
    font-weight: 800;
    color: #00071c;
    letter-spacing: -.5px;
    line-height: 1;
}
.preco-promo {
    font-size: 30px;
    font-weight: 800;
    color: #059669;
    letter-spacing: -.5px;
    line-height: 1;
}
.preco-economia {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 10px;
    font-size: 12px;
    font-weight: 700;
    color: #059669;
    background: #d1fae5;
    padding: 4px 10px;
    border-radius: 20px;
}

/* ── Ações ── */
.prod-actions { display: flex; flex-direction: column; gap: 10px; }

.btn-wpp {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 22px;
    background: #25d366;
    color: #fff;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: background .18s, transform .15s;
    letter-spacing: .1px;
}
.btn-wpp:hover { background: #1ebe5d; transform: translateY(-1px); }
.btn-wpp svg { width: 20px; height: 20px; flex-shrink: 0; }

.btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 18px;
    background: #fff;
    color: #374151;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: 1.5px solid #e5e7eb;
    transition: background .15s;
}
.btn-back:hover { background: #f9fafb; }
.btn-back svg { width: 15px; height: 15px; }

/* ── Descrição ── */
.prod-descricao {
    background: #fff;
    border-radius: 16px;
    border: 1.5px solid #f0f0f2;
    padding: 30px 32px;
    margin-bottom: 36px;
}
.section-title {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 18px;
    padding-bottom: 14px;
    border-bottom: 1.5px solid #f0f0f2;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title svg { width: 16px; height: 16px; color: #9ca3af; }
.prod-descricao-content {
    font-size: 14px;
    color: #374151;
    line-height: 1.85;
    white-space: pre-wrap;
}

/* ── Sugestões ── */
.sugestoes-wrap { margin-top: 8px; }
.sug-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 20px;
}
.sug-card {
    background: #fff;
    border-radius: 14px;
    border: 1.5px solid #f0f0f2;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform .18s, box-shadow .18s, border-color .18s;
}
.sug-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,7,28,.08);
    border-color: #e0e2ef;
}
.sug-img {
    aspect-ratio: 1;
    width: 100%;
    object-fit: cover;
    background: #f8f8fb;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.sug-img img { width: 100%; height: 100%; object-fit: cover; }
.sug-img-placeholder svg { width: 32px; height: 32px; color: #c8c8dc; }
.sug-body { padding: 14px; }
.sug-name { font-size: 13px; font-weight: 700; color: #111827; line-height: 1.3; }
.sug-preco { font-size: 14px; font-weight: 800; color: #00071c; margin-top: 8px; }
.sug-promo { font-size: 14px; font-weight: 800; color: #059669; margin-top: 8px; }
</style>

<div class="prod-page">

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="<?= BASE_URL ?>">Início</a>
        <?php foreach ($cats as $c): ?>
            <span class="breadcrumb-sep">›</span>
            <a href="<?= BASE_URL ?>/produtos/<?= urlencode($c['slug']) ?>"><?= htmlspecialchars($c['nome']) ?></a>
        <?php endforeach; ?>
        <span class="breadcrumb-sep">›</span>
        <span class="breadcrumb-current"><?= htmlspecialchars($prod['nome']) ?></span>
    </nav>

    <!-- Layout principal -->
    <div class="prod-layout">

        <!-- Imagem -->
        <div class="prod-img-box">
            <div class="prod-main-img">
                <?php if (!empty($prod['imagem_principal'])): ?>
                    <img src="<?= htmlspecialchars($prod['imagem_principal']) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                <?php else: ?>
                    <div class="prod-img-placeholder-lg">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            <path d="M16 3H8L6 7h12l-2-4z"/>
                        </svg>
                        <span>Sem imagem</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informações -->
        <div class="prod-info">

            <!-- Badges -->
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <?php if (!empty($cats)): ?>
                    <?php foreach ($cats as $c): ?>
                        <a href="<?= BASE_URL ?>/produtos/<?= urlencode($c['slug']) ?>" class="cat-badge"><?= htmlspecialchars($c['nome']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!empty($prod['destaque'])): ?>
                    <span class="dest-badge">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        Destaque
                    </span>
                <?php endif; ?>
            </div>

            <h1 class="prod-nome"><?= htmlspecialchars($prod['nome']) ?></h1>

            <?php if (!empty($prod['descricao_curta'])): ?>
                <p class="prod-desc-curta"><?= htmlspecialchars($prod['descricao_curta']) ?></p>
            <?php endif; ?>

            <!-- Preço -->
            <?php if (!empty($prod['preco'])): ?>
            <div class="preco-box">
                <div class="preco-label">Preço</div>
                <?php if (!empty($prod['preco_promocional'])): ?>
                    <div class="preco-old">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                    <div class="preco-promo">R$ <?= number_format($prod['preco_promocional'], 2, ',', '.') ?></div>
                    <?php $eco = round((($prod['preco'] - $prod['preco_promocional']) / $prod['preco']) * 100); ?>
                    <div class="preco-economia">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        <?= $eco ?>% de desconto
                    </div>
                <?php else: ?>
                    <div class="preco-val">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Ações -->
            <div class="prod-actions">
                <?php if ($wpp_link): ?>
                <a href="<?= $wpp_link ?>" target="_blank" rel="noopener noreferrer" class="btn-wpp">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.522 5.85L.057 23.776a.5.5 0 0 0 .608.637l6.108-1.598A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.8 9.8 0 0 1-5.032-1.387l-.36-.214-3.733.977.998-3.647-.235-.376A9.818 9.818 0 1 1 12 21.818z"/>
                    </svg>
                    Negociar no WhatsApp
                </a>
                <?php endif; ?>
                <?php if (!empty($cats)): ?>
                    <a href="<?= BASE_URL ?>/produtos/<?= urlencode($cats[0]['slug']) ?>" class="btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 6l-6 6 6 6"/></svg>
                        Voltar à categoria
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>" class="btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 6l-6 6 6 6"/></svg>
                        Início
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Descrição completa -->
    <?php if (!empty($prod['descricao'])): ?>
    <div class="prod-descricao">
        <h2 class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Descrição completa
        </h2>
        <div class="prod-descricao-content"><?= htmlspecialchars($prod['descricao']) ?></div>
    </div>
    <?php endif; ?>

    <!-- Produtos relacionados -->
    <?php if (!empty($sugestoes)): ?>
    <div class="sugestoes-wrap">
        <h2 class="section-title" style="border:none;padding:0;margin-bottom:0">Produtos relacionados</h2>
        <div class="sug-grid">
            <?php foreach ($sugestoes as $s): ?>
            <a href="<?= BASE_URL ?>/produto/<?= urlencode($s['sku']) ?>" class="sug-card">
                <div class="sug-img">
                    <?php if (!empty($s['imagem_principal'])): ?>
                        <img src="<?= htmlspecialchars($s['imagem_principal']) ?>" alt="<?= htmlspecialchars($s['nome']) ?>">
                    <?php else: ?>
                        <div class="sug-img-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                <path d="M16 3H8L6 7h12l-2-4z"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="sug-body">
                    <div class="sug-name"><?= htmlspecialchars($s['nome']) ?></div>
                    <?php if (!empty($s['preco'])): ?>
                        <?php if (!empty($s['preco_promocional'])): ?>
                            <div class="sug-promo">R$ <?= number_format($s['preco_promocional'], 2, ',', '.') ?></div>
                        <?php else: ?>
                            <div class="sug-preco">R$ <?= number_format($s['preco'], 2, ',', '.') ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
</body>
</html>