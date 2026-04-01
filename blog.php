<?php
/**
 * blog.php — Listagem de posts do Blog
 * Nutrialle — Módulo de Blog
 */
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

// ── Configuração do hero ──────────────────────────────────────
$blog_cfg = $db->query("SELECT * FROM blog_config WHERE id = 1 LIMIT 1")->fetch();
$hero_titulo    = $blog_cfg['hero_titulo']    ?? 'Blog Nutrialle';
$hero_subtitulo = $blog_cfg['hero_subtitulo'] ?? '';
$hero_cor_inicio = $blog_cfg['hero_cor_inicio'] ?? '#003d85';
$hero_cor_meio  = $blog_cfg['hero_cor_meio']  ?? '#0057b7';
$hero_cor_fim   = $blog_cfg['hero_cor_fim']   ?? '#1a7fe0';

$page_title       = $blog_cfg['page_title']       ?? 'Blog';
$meta_description = $blog_cfg['meta_description'] ?? '';
$meta_keywords    = $blog_cfg['meta_keywords']    ?? '';

// ── Paginação ────────────────────────────────────────────────
$por_pagina  = 9;
$pagina_atual = max(1, (int)($_GET['pagina'] ?? 1));
$offset       = ($pagina_atual - 1) * $por_pagina;

// ── Filtro por categoria ─────────────────────────────────────
$cat_slug    = $_GET['categoria'] ?? '';
$where_cat   = '';
$bind_params = [];

if (!empty($cat_slug)) {
    $cat_stmt = $db->prepare("SELECT id, nome FROM blog_categorias WHERE slug = ? AND ativo = 1");
    $cat_stmt->execute([$cat_slug]);
    $cat_filtro = $cat_stmt->fetch();
    if ($cat_filtro) {
        $where_cat   = 'AND p.categoria_id = :cat_id';
        $bind_params[':cat_id'] = $cat_filtro['id'];
    }
}

// ── Total de posts ────────────────────────────────────────────
$count_sql = "SELECT COUNT(*) FROM blog_posts p WHERE p.ativo = 1 $where_cat";
$count_stmt = $db->prepare($count_sql);
$count_stmt->execute($bind_params);
$total_posts  = (int)$count_stmt->fetchColumn();
$total_paginas = (int)ceil($total_posts / $por_pagina);

// ── Busca posts ───────────────────────────────────────────────
$sql = "
    SELECT
        p.id, p.titulo, p.slug, p.resumo, p.imagem_destaque,
        p.alt_imagem, p.tempo_leitura, p.publicado_em, p.autor, p.destaque,
        c.nome AS categoria_nome, c.slug AS categoria_slug, c.cor AS categoria_cor
    FROM blog_posts p
    LEFT JOIN blog_categorias c ON p.categoria_id = c.id
    WHERE p.ativo = 1
    $where_cat
    ORDER BY p.destaque DESC, p.publicado_em DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $db->prepare($sql);
foreach ($bind_params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit',  $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// ── Categorias para o filtro ──────────────────────────────────
$categorias = $db->query("
    SELECT c.id, c.nome, c.slug, c.cor,
           COUNT(p.id) AS total
    FROM blog_categorias c
    LEFT JOIN blog_posts p ON p.categoria_id = c.id AND p.ativo = 1
    WHERE c.ativo = 1
    GROUP BY c.id
    HAVING total > 0
    ORDER BY c.nome ASC
")->fetchAll();

// ── Helper: formata data em pt-BR ─────────────────────────────
function formatarData(string $data): string {
    $meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
              'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    $ts  = strtotime($data);
    $dia = date('j', $ts);
    $mes = $meses[(int)date('n', $ts)];
    $ano = date('Y', $ts);
    return "$dia de $mes de $ano";
}

include __DIR__ . '/includes/header.php';
?>

<style>
/* ── Blog: listagem ───────────────────────────────── */
.blog-hero {
    background: linear-gradient(135deg, <?= htmlspecialchars($hero_cor_inicio) ?> 0%, <?= htmlspecialchars($hero_cor_meio) ?> 60%, <?= htmlspecialchars($hero_cor_fim) ?> 100%);
    padding: 60px 20px 50px;
    text-align: center;
    color: #fff;
    position: relative;
}
.blog-hero .home-btn {
    display: inline-block;
    margin-top: 20px;
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s;
}
.blog-hero .home-btn:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
}
.blog-hero h1 {
    font-size: clamp(28px, 5vw, 44px);
    font-weight: 800;
    letter-spacing: -0.5px;
    margin-bottom: 10px;
}
.blog-hero p {
    font-size: 17px;
    opacity: 0.88;
    max-width: 520px;
    margin: 0 auto;
}

/* ── Filtro de categorias ─────────────────────────── */
.blog-filtros {
    background: #fff;
    border-bottom: 1px solid #e8edf3;
    padding: 0 20px;
    position: sticky;
    top: var(--header-height, 0px);
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.blog-filtros-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
    padding: 14px 0;
}
.blog-filtros-inner::-webkit-scrollbar { display: none; }

.filtro-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 18px;
    border-radius: 100px;
    font-size: 13.5px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    text-decoration: none;
    border: 2px solid transparent;
    transition: all .2s;
    background: #f0f4f8;
    color: #445;
}
.filtro-btn:hover {
    background: #e0eaf6;
    color: #0057b7;
}
.filtro-btn.ativo {
    background: #0057b7;
    color: #fff;
    border-color: #0057b7;
}
.filtro-btn .filtro-count {
    background: rgba(255,255,255,0.3);
    padding: 1px 7px;
    border-radius: 100px;
    font-size: 11px;
}
.filtro-btn:not(.ativo) .filtro-count {
    background: rgba(0,0,0,0.08);
}

/* Dropdown para mobile */
.blog-filtros-mobile {
    display: none;
    max-width: 1200px;
    margin: 0 auto;
    padding: 14px 0;
}
.blog-filtros-mobile select {
    width: 100%;
    max-width: 300px;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    cursor: pointer;
    transition: border-color .2s;
}
.blog-filtros-mobile select:focus {
    outline: none;
    border-color: #0057b7;
    box-shadow: 0 0 0 3px rgba(0,87,183,.1);
}
.blog-filtros-mobile option {
    padding: 8px;
}

/* ── Grid de posts ────────────────────────────────── */
.blog-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 48px 20px 60px;
}

.blog-header-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 10px;
}
.blog-total-txt {
    font-size: 14px;
    color: #667;
}
.blog-total-txt strong {
    color: #0057b7;
}

.blog-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
}

/* ── Card de post ─────────────────────────────────── */
.blog-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    transition: transform .25s, box-shadow .25s;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    border: 1px solid #eaeff5;
}
.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 32px rgba(0,87,183,.13);
    border-color: #c5d8f0;
}

.blog-card-img {
    position: relative;
    width: 100%;
    aspect-ratio: 16/9;
    overflow: hidden;
    background: #dde8f4;
}
.blog-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
    display: block;
}
.blog-card:hover .blog-card-img img {
    transform: scale(1.05);
}
.blog-card-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #dde8f4, #c5d8f0);
}
.blog-card-img-placeholder svg {
    opacity: 0.35;
}

.blog-card-cat {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 11.5px;
    font-weight: 700;
    color: #fff;
    letter-spacing: .3px;
    text-shadow: 0 1px 3px rgba(0,0,0,.2);
}

.blog-card-body {
    padding: 20px 22px 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blog-card-meta {
    font-size: 12.5px;
    color: #0057b7;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.blog-card-meta span {
    color: #8899aa;
    font-weight: 400;
}

.blog-card-titulo {
    font-size: 17px;
    font-weight: 800;
    color: #1a2535;
    line-height: 1.4;
    margin-bottom: 10px;
    flex: 1;
}

.blog-card-resumo {
    font-size: 13.5px;
    color: #556;
    line-height: 1.65;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 16px;
}

.blog-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid #eaeef4;
    margin-top: auto;
}
.blog-card-leitura {
    font-size: 12px;
    color: #8899aa;
    display: flex;
    align-items: center;
    gap: 5px;
}
.blog-card-ler {
    font-size: 13px;
    font-weight: 700;
    color: #ff7200;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: gap .2s;
}
.blog-card:hover .blog-card-ler {
    gap: 8px;
}

/* ── Empty state ──────────────────────────────────── */
.blog-empty {
    text-align: center;
    padding: 80px 20px;
    color: #889;
    grid-column: 1 / -1;
}
.blog-empty svg { opacity: .3; margin-bottom: 16px; }
.blog-empty h3 { font-size: 20px; color: #556; margin-bottom: 8px; }

/* ── Paginação ────────────────────────────────────── */
.blog-paginacao {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 48px;
    flex-wrap: wrap;
}
.pag-btn {
    min-width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 2px solid #e0e8f0;
    background: #fff;
    color: #445;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    text-decoration: none;
    cursor: pointer;
    transition: all .2s;
}
.pag-btn:hover:not(.pag-atual):not(.pag-desativado) {
    border-color: #0057b7;
    color: #0057b7;
}
.pag-btn.pag-atual {
    background: #0057b7;
    border-color: #0057b7;
    color: #fff;
    cursor: default;
}
.pag-btn.pag-desativado {
    opacity: 0.38;
    cursor: default;
    pointer-events: none;
}

/* ── Responsivo ───────────────────────────────────── */
@media (max-width: 1024px) {
    .blog-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .blog-filtros-inner { display: none; }
    .blog-filtros-mobile { display: flex; justify-content: center; }
    .blog-filtros { padding: 0 16px; }
}
@media (max-width: 640px) {
    .blog-grid { grid-template-columns: 1fr; gap: 20px; }
    .blog-hero { padding: 40px 20px 36px; }
    .blog-section { padding: 32px 16px 48px; }
}
</style>

<!-- HERO DO BLOG -->
<div class="blog-hero">
    <h1><?= htmlspecialchars($hero_titulo) ?></h1>
    <?php if (!empty($hero_subtitulo)): ?>
        <p><?= htmlspecialchars($hero_subtitulo) ?></p>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/" class="home-btn">← Voltar para Home</a>
</div>

<!-- FILTRO DE CATEGORIAS -->
<?php if (!empty($categorias)): ?>
<div class="blog-filtros">
    <div class="blog-filtros-inner">
        <button class="filtro-btn <?= empty($cat_slug) ? 'ativo' : '' ?>"
                data-slug=""
                data-cor="">
            Todos
            <span class="filtro-count"><?= $total_posts ?></span>
        </button>
        <?php foreach ($categorias as $cat):
            $cor = htmlspecialchars($cat['cor'] ?? '#0057b7');
            $ativo = $cat_slug === $cat['slug'];
        ?>
            <button class="filtro-btn <?= $ativo ? 'ativo' : '' ?>"
                    data-slug="<?= htmlspecialchars($cat['slug']) ?>"
                    data-cor="<?= $cor ?>"
                    <?= $ativo ? 'style="background:' . $cor . ';border-color:' . $cor . '"' : '' ?>>
                <?= htmlspecialchars($cat['nome']) ?>
                <span class="filtro-count"><?= $cat['total'] ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Dropdown para mobile -->
    <div class="blog-filtros-mobile">
        <select id="categoria-mobile" onchange="filtrarCategoriaMobile(this.value)">
            <option value="">Todos (<?= $total_posts ?> artigos)</option>
            <?php foreach ($categorias as $cat):
                $ativo = $cat_slug === $cat['slug'];
            ?>
                <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= $ativo ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nome']) ?> (<?= $cat['total'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?php endif; ?>

<!-- GRID DE POSTS -->
<main class="blog-section">

    <div class="blog-header-info">
        <p class="blog-total-txt" id="blog-total-txt">
            <?php if (!empty($cat_filtro)): ?>
                Categoria: <strong><?= htmlspecialchars($cat_filtro['nome']) ?></strong> —
            <?php endif; ?>
            <strong><?= $total_posts ?></strong> artigo<?= $total_posts !== 1 ? 's' : '' ?> encontrado<?= $total_posts !== 1 ? 's' : '' ?>
        </p>
    </div>

    <div class="blog-grid" id="blog-grid">

        <?php if (empty($posts)): ?>
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
        <?php else: ?>

            <?php foreach ($posts as $post):
                $img_src    = !empty($post['imagem_destaque']) ? htmlspecialchars($post['imagem_destaque']) : '';
                $cat_cor    = htmlspecialchars($post['categoria_cor'] ?? '#0057b7');
                $post_url   = BASE_URL . '/blog-conteudo.php?slug=' . urlencode($post['slug']);
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
                        <span class="blog-card-cat" style="background: <?= $cat_cor ?>">
                            <?= htmlspecialchars($post['categoria_nome']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        <?= formatarData($post['publicado_em']) ?>
                        <span>/</span>
                        <?= (int)$post['tempo_leitura'] ?> minutos de leitura
                    </div>

                    <h2 class="blog-card-titulo">
                        <?= htmlspecialchars($post['titulo']) ?>
                    </h2>

                    <?php if (!empty($post['resumo'])): ?>
                        <p class="blog-card-resumo">
                            <?= htmlspecialchars($post['resumo']) ?>
                        </p>
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
            <?php endforeach; ?>

        <?php endif; ?>

    </div><!-- /.blog-grid -->

    <!-- PAGINAÇÃO -->
    <div class="blog-paginacao" id="blog-paginacao">
        <?php if ($total_paginas > 1):
            for ($p = 1; $p <= $total_paginas; $p++):
                if ($total_paginas > 7) {
                    $show = ($p === 1 || $p === $total_paginas || abs($p - $pagina_atual) <= 2);
                    if (!$show) {
                        if ($p === 2 || $p === $total_paginas - 1) echo '<span class="pag-btn pag-desativado">…</span>';
                        continue;
                    }
                }
                $cls = $p === $pagina_atual ? 'pag-btn pag-atual' : 'pag-btn';
                echo '<button class="' . $cls . '" data-pagina="' . $p . '">' . $p . '</button>';
            endfor;
        endif; ?>
    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
<script>
(function () {
    // ── Header height ──────────────────────────────────────────
    function setHeaderHeight() {
        var h = document.querySelector('.header');
        if (h) document.documentElement.style.setProperty('--header-height', h.offsetHeight + 'px');
    }
    setHeaderHeight();
    window.addEventListener('resize', setHeaderHeight);

    // ── Estado ────────────────────────────────────────────────
    var catAtual  = '<?= addslashes($cat_slug) ?>';
    var pagAtual  = 1;
    var carregando = false;

    var grid     = document.getElementById('blog-grid');
    var totalTxt = document.getElementById('blog-total-txt');
    var pagDiv   = document.getElementById('blog-paginacao');
    var ajaxUrl  = '<?= BASE_URL ?>/blog-ajax.php';

    // ── Busca posts via AJAX ──────────────────────────────────
    function buscarPosts(cat, pagina) {
        if (carregando) return;
        carregando = true;

        // Feedback visual no grid
        grid.style.opacity = '0.4';
        grid.style.pointerEvents = 'none';

        var url = ajaxUrl + '?ajax=1&pagina=' + pagina;
        if (cat) url += '&categoria=' + encodeURIComponent(cat);

        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                // Atualiza grid
                grid.innerHTML = data.cards;
                grid.style.opacity = '1';
                grid.style.pointerEvents = '';

                // Atualiza total
                totalTxt.innerHTML = '<strong>' + data.total_label.replace(/<strong>/g, '<strong>') + '</strong>';
                totalTxt.innerHTML = data.total_label;

                // Atualiza paginação
                pagDiv.innerHTML = data.paginacao;
                bindPaginacao();

                // Atualiza URL sem reload
                var novaUrl = window.location.pathname;
                var params = [];
                if (cat)    params.push('categoria=' + encodeURIComponent(cat));
                if (pagina > 1) params.push('pagina=' + pagina);
                if (params.length) novaUrl += '?' + params.join('&');
                history.pushState({ cat: cat, pagina: pagina }, '', novaUrl);

                catAtual = cat;
                pagAtual = pagina;
                carregando = false;
            })
            .catch(function () {
                grid.style.opacity = '1';
                grid.style.pointerEvents = '';
                carregando = false;
            });
    }

    // ── Bind nos botões de filtro ─────────────────────────────
    document.querySelectorAll('.filtro-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var slug = this.dataset.slug;
            var cor  = this.dataset.cor;

            // Ativo visual
            document.querySelectorAll('.filtro-btn').forEach(function (b) {
                b.classList.remove('ativo');
                b.style.background = '';
                b.style.borderColor = '';
            });
            this.classList.add('ativo');
            if (cor) {
                this.style.background  = cor;
                this.style.borderColor = cor;
            }

            buscarPosts(slug, 1);
        });
    });

    // ── Bind nos botões de paginação ──────────────────────────
    function bindPaginacao() {
        pagDiv.querySelectorAll('.pag-btn[data-pagina]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var p = parseInt(this.dataset.pagina, 10);
                buscarPosts(catAtual, p);
                window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
            });
        });
    }
    bindPaginacao();

    // ── Suporte ao botão Voltar do navegador ──────────────────
    window.addEventListener('popstate', function (e) {
        var state = e.state || { cat: '', pagina: 1 };
        buscarPosts(state.cat || '', state.pagina || 1);
    });

    // ── Função para dropdown mobile ───────────────────────────
    window.filtrarCategoriaMobile = function(slug) {
        var btn = document.querySelector('.filtro-btn[data-slug="' + slug + '"]');
        if (btn) {
            btn.click();
        } else {
            // Para "Todos"
            document.querySelector('.filtro-btn[data-slug=""]').click();
        }
    };

})();
</script>
</body>
</html>