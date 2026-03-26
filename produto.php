<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

// Pega o SKU da URL: /produto/PRD-ABC123
$sku = $_GET['sku'] ?? '';
if (empty($sku)) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path = ltrim(substr($requestUri, strlen($base)), '/');
    $path = strtok($path, '?');
    // Espera formato: produto/SKU
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
$cats = $db->prepare("
    SELECT cp.nome, cp.slug
    FROM categorias_produtos cp
    JOIN produto_categorias pc ON pc.categoria_produto_id = cp.id
    WHERE pc.produto_id = ? AND cp.ativo = 1
    ORDER BY cp.nome
");
$cats->execute([$prod['id']]);
$cats = $cats->fetchAll();

// Outros produtos da mesma categoria (sugestões)
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

$page_title       = htmlspecialchars($prod['nome']);
$meta_description = $prod['meta_description'] ?? '';
$meta_keywords    = $prod['meta_keywords'] ?? '';

include __DIR__ . '/includes/header.php';
?>
<style>
.prod-page{max-width:1100px;margin:0 auto;padding:32px 20px}
/* Breadcrumb */
.breadcrumb{display:flex;align-items:center;gap:6px;margin-bottom:24px;font-size:13px;flex-wrap:wrap}
.breadcrumb a{color:#4f6ef7;text-decoration:none}.breadcrumb a:hover{text-decoration:underline}
.breadcrumb span{color:#9ca3af}
/* Layout principal */
.prod-layout{display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-bottom:48px}
@media(max-width:700px){.prod-layout{grid-template-columns:1fr}}
/* Imagem */
.prod-img-box{position:sticky;top:20px}
.prod-main-img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:14px;border:2px solid #e5e7eb;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:80px}
.prod-main-img img{width:100%;height:100%;object-fit:cover;border-radius:12px}
/* Info */
.prod-info{display:flex;flex-direction:column;gap:16px}
.prod-badges{display:flex;gap:6px;flex-wrap:wrap}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.b-sku{background:#ede9fe;color:#5b21b6;font-family:monospace;font-size:13px;padding:5px 12px;border-radius:8px;letter-spacing:1px}
.b-dest{background:#fef9c3;color:#854d0e}
.b-cat{background:#e0f2fe;color:#0369a1;font-size:12px}
.prod-nome{font-size:26px;font-weight:800;color:#00071c;line-height:1.2}
.prod-desc-curta{font-size:14px;color:#6b7280;line-height:1.6;border-left:3px solid #e5e7eb;padding-left:14px}
/* Preço */
.preco-box{padding:16px 20px;background:#f8f9fa;border-radius:12px;border:1px solid #e5e7eb}
.preco-old{font-size:14px;color:#9ca3af;text-decoration:line-through;margin-bottom:2px}
.preco-val{font-size:28px;font-weight:800;color:#00071c}
.preco-promo{font-size:28px;font-weight:800;color:#10b981}
.preco-economia{font-size:12px;color:#10b981;font-weight:600;margin-top:4px}
/* Ações */
.prod-actions{display:flex;gap:10px;flex-wrap:wrap}
.btn-contato{display:inline-flex;align-items:center;gap:7px;padding:12px 22px;background:#00071c;color:#fff;border-radius:9px;font-size:14px;font-weight:700;text-decoration:none;transition:all .18s}
.btn-contato:hover{background:#1a2540;transform:translateY(-1px)}
.btn-back{display:inline-flex;align-items:center;gap:7px;padding:12px 18px;background:#f3f4f6;color:#374151;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;border:2px solid #e5e7eb;transition:all .18s}
.btn-back:hover{background:#e5e7eb}
/* Descrição completa */
.prod-descricao{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.07);padding:28px;margin-bottom:32px}
.prod-descricao h2{font-size:16px;font-weight:700;color:#00071c;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f0f0f0}
.prod-descricao-content{font-size:14px;color:#374151;line-height:1.8;white-space:pre-wrap}
/* Sugestões */
.sugestoes-wrap{margin-top:8px}
.sugestoes-wrap h2{font-size:18px;font-weight:700;color:#00071c;margin-bottom:16px}
.sug-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px}
.sug-card{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden;transition:transform .18s;text-decoration:none;color:inherit;display:block}
.sug-card:hover{transform:translateY(-2px)}
.sug-img{aspect-ratio:1;width:100%;object-fit:cover;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:32px}
.sug-img img{width:100%;height:100%;object-fit:cover}
.sug-body{padding:12px}
.sug-sku{font-size:10px;font-weight:700;color:#8b5cf6;font-family:monospace;background:#ede9fe;padding:1px 6px;border-radius:4px}
.sug-name{font-size:13px;font-weight:700;color:#111827;margin-top:4px;line-height:1.3}
.sug-preco{font-size:13px;font-weight:700;color:#00071c;margin-top:6px}
.sug-promo{font-size:13px;font-weight:700;color:#10b981;margin-top:6px}
</style>

<div class="prod-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>">Início</a>
        <?php foreach ($cats as $c): ?>
            <span>›</span>
            <a href="<?= BASE_URL ?>/produtos/<?= urlencode($c['slug']) ?>"><?= htmlspecialchars($c['nome']) ?></a>
        <?php endforeach; ?>
        <span>›</span>
        <span style="color:#374151"><?= htmlspecialchars($prod['nome']) ?></span>
    </div>

    <!-- Layout produto -->
    <div class="prod-layout">
        <!-- Imagem -->
        <div class="prod-img-box">
            <div class="prod-main-img">
                <?php if ($prod['imagem_principal']): ?>
                    <img src="<?= htmlspecialchars($prod['imagem_principal']) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                <?php else: ?>
                    📦
                <?php endif; ?>
            </div>
        </div>

        <!-- Informações -->
        <div class="prod-info">
            <div class="prod-badges">
                <span class="b-sku"><?= htmlspecialchars($prod['sku']) ?></span>
                <?php if ($prod['destaque']): ?>
                    <span class="badge b-dest">⭐ Destaque</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($cats)): ?>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
                <?php foreach ($cats as $c): ?>
                    <a href="<?= BASE_URL ?>/produtos/<?= urlencode($c['slug']) ?>" class="badge b-cat">📦 <?= htmlspecialchars($c['nome']) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h1 class="prod-nome"><?= htmlspecialchars($prod['nome']) ?></h1>

            <?php if ($prod['descricao_curta']): ?>
                <p class="prod-desc-curta"><?= htmlspecialchars($prod['descricao_curta']) ?></p>
            <?php endif; ?>

            <?php if ($prod['preco']): ?>
            <div class="preco-box">
                <?php if ($prod['preco_promocional']): ?>
                    <div class="preco-old">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                    <div class="preco-promo">R$ <?= number_format($prod['preco_promocional'], 2, ',', '.') ?></div>
                    <?php
                    $economia = (($prod['preco'] - $prod['preco_promocional']) / $prod['preco']) * 100;
                    ?>
                    <div class="preco-economia">✔ <?= round($economia) ?>% de desconto</div>
                <?php else: ?>
                    <div class="preco-val">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="prod-actions">
                <?php if (!empty($cats)): ?>
                    <a href="<?= BASE_URL ?>/produtos/<?= urlencode($cats[0]['slug']) ?>" class="btn-back">← Voltar à categoria</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>" class="btn-back">← Início</a>
                <?php endif; ?>
            </div>

            <div style="font-size:11px;color:#d1d5db;margin-top:8px">Ref: #<?= $prod['id'] ?> · <?= strtoupper($prod['sku']) ?></div>
        </div>
    </div>

    <!-- Descrição completa -->
    <?php if ($prod['descricao']): ?>
    <div class="prod-descricao">
        <h2>📋 Descrição Completa</h2>
        <div class="prod-descricao-content"><?= htmlspecialchars($prod['descricao']) ?></div>
    </div>
    <?php endif; ?>

    <!-- Sugestões -->
    <?php if (!empty($sugestoes)): ?>
    <div class="sugestoes-wrap">
        <h2>Produtos relacionados</h2>
        <div class="sug-grid">
            <?php foreach ($sugestoes as $s): ?>
            <a href="<?= BASE_URL ?>/produto/<?= urlencode($s['sku']) ?>" class="sug-card">
                <div class="sug-img">
                    <?php if ($s['imagem_principal']): ?>
                        <img src="<?= htmlspecialchars($s['imagem_principal']) ?>" alt="<?= htmlspecialchars($s['nome']) ?>">
                    <?php else: ?>📦<?php endif; ?>
                </div>
                <div class="sug-body">
                    <span class="sug-sku"><?= htmlspecialchars($s['sku']) ?></span>
                    <div class="sug-name"><?= htmlspecialchars($s['nome']) ?></div>
                    <?php if ($s['preco']): ?>
                        <?php if ($s['preco_promocional']): ?>
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
