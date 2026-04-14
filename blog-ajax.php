<?php
/**
 * blog-ajax.php — Retorna cards + paginação + total via AJAX
 * Nutrialle — Módulo de Blog
 */
if (!isset($_GET['ajax'])) {
    http_response_code(403);
    exit('Acesso negado.');
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

$db = getDB();

$por_pagina   = 9;
$pagina_atual = max(1, (int)($_GET['pagina'] ?? 1));
$offset       = ($pagina_atual - 1) * $por_pagina;
$cat_slug     = trim($_GET['categoria'] ?? '');

$where_cat   = '';
$bind_params = [];
$cat_nome    = '';

if (!empty($cat_slug)) {
    $cat_stmt = $db->prepare("SELECT id, nome FROM blog_categorias WHERE slug = ? AND ativo = 1");
    $cat_stmt->execute([$cat_slug]);
    $cat_filtro = $cat_stmt->fetch();
    if ($cat_filtro) {
        $where_cat              = 'AND p.categoria_id = :cat_id';
        $bind_params[':cat_id'] = $cat_filtro['id'];
        $cat_nome               = $cat_filtro['nome'];
    }
}

// Total filtrado (para label)
$count_stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts p WHERE p.ativo = 1 $where_cat");
$count_stmt->execute($bind_params);
$total_posts   = (int)$count_stmt->fetchColumn();
$total_paginas = (int)ceil($total_posts / $por_pagina);

// Total geral sem filtro — para manter o badge "Todos" sempre correto
$total_geral = (int)$db->query("SELECT COUNT(*) FROM blog_posts WHERE ativo = 1")->fetchColumn();

// Posts
$sql = "
    SELECT
        p.id, p.titulo, p.slug, p.resumo, p.imagem_destaque,
        p.alt_imagem, p.tempo_leitura, p.publicado_em, p.autor, p.destaque,
        c.nome AS categoria_nome, c.slug AS categoria_slug, c.cor AS categoria_cor
    FROM blog_posts p
    LEFT JOIN blog_categorias c ON p.categoria_id = c.id
    WHERE p.ativo = 1 $where_cat
    ORDER BY p.destaque DESC, p.publicado_em DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $db->prepare($sql);
foreach ($bind_params as $key => $val) $stmt->bindValue($key, $val);
$stmt->bindValue(':limit',  $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// ── Helper: data pt-BR ────────────────────────────────────────
function formatarDataAjax(string $data): string {
    $meses = ['','janeiro','fevereiro','março','abril','maio','junho',
              'julho','agosto','setembro','outubro','novembro','dezembro'];
    $ts = strtotime($data);
    return date('j', $ts) . ' de ' . $meses[(int)date('n', $ts)] . ' de ' . date('Y', $ts);
}

// ── Monta HTML dos cards ──────────────────────────────────────
ob_start();

if (empty($posts)): ?>
<div class="blog-empty">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#0057b7" stroke-width="1.5">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
    </svg>
    <h3>Nenhum artigo encontrado</h3>
    <p>Tente outra categoria ou volte em breve.</p>
</div>
<?php else:
    foreach ($posts as $post):
        $img_src  = !empty($post['imagem_destaque']) ? htmlspecialchars($post['imagem_destaque']) : '';
        $cat_cor  = htmlspecialchars($post['categoria_cor'] ?? '#0057b7');
        $post_url = BASE_URL . '/blog-conteudo.php?slug=' . urlencode($post['slug']);
?>
<a href="<?= $post_url ?>" class="blog-card">
    <div class="blog-card-img">
        <?php if ($img_src): ?>
            <img src="<?= $img_src ?>"
                 alt="<?= htmlspecialchars($post['alt_imagem'] ?? $post['titulo']) ?>"
                 loading="lazy">
        <?php else: ?>
            <div class="blog-card-img-placeholder">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#0057b7" stroke-width="1.2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
        <?php endif; ?>
        <?php if (!empty($post['categoria_nome'])): ?>
            <span class="blog-card-cat" style="background:<?= $cat_cor ?>">
                <?= htmlspecialchars($post['categoria_nome']) ?>
            </span>
        <?php endif; ?>
    </div>
    <div class="blog-card-body">
        <div class="blog-card-meta">
            <?= formatarDataAjax($post['publicado_em']) ?>
            <span>/</span>
            <?= (int)$post['tempo_leitura'] ?> minutos de leitura
        </div>
        <h2 class="blog-card-titulo"><?= htmlspecialchars($post['titulo']) ?></h2>
        <?php if (!empty($post['resumo'])): ?>
            <p class="blog-card-resumo"><?= htmlspecialchars($post['resumo']) ?></p>
        <?php endif; ?>
        <div class="blog-card-footer">
            <span class="blog-card-leitura">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <?= (int)$post['tempo_leitura'] ?> min
            </span>
            <span class="blog-card-ler">
                Ler artigo
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </span>
        </div>
    </div>
</a>
<?php
    endforeach;
endif;

$cards_html = ob_get_clean();

// ── Monta HTML da paginação ───────────────────────────────────
ob_start();

if ($total_paginas > 1):
    for ($p = 1; $p <= $total_paginas; $p++):
        if ($total_paginas > 7) {
            $show = ($p === 1 || $p === $total_paginas || abs($p - $pagina_atual) <= 2);
            if (!$show) {
                if ($p === 2 || $p === $total_paginas - 1) {
                    echo '<span class="pag-btn pag-desativado">…</span>';
                }
                continue;
            }
        }
        $cls = $p === $pagina_atual ? 'pag-btn pag-atual' : 'pag-btn';
        echo '<button class="' . $cls . '" data-pagina="' . $p . '">' . $p . '</button>';
    endfor;
endif;

$pag_html = ob_get_clean();

// ── Texto do total ────────────────────────────────────────────
$s     = $total_posts !== 1;
$label = '<strong>' . $total_posts . '</strong> artigo' . ($s ? 's' : '') . ' encontrado' . ($s ? 's' : '');
if (!empty($cat_nome)) {
    $label = 'Categoria: <strong>' . htmlspecialchars($cat_nome) . '</strong> — ' . $label;
}

echo json_encode([
    'cards'         => $cards_html,
    'paginacao'     => $pag_html,
    'total_label'   => $label,
    'total_geral'   => $total_geral,   // <-- novo: para atualizar badge "Todos"
    'total_paginas' => $total_paginas,
    'pagina_atual'  => $pagina_atual,
], JSON_UNESCAPED_UNICODE);
