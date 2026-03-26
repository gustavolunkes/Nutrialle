<?php
/**
 * blog-conteudo.php — Conteúdo individual de um post do Blog
 * Nutrialle — Módulo de Blog
 */
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    include __DIR__ . '/404.php';
    exit;
}

// ── Busca o post ──────────────────────────────────────────────
$stmt = $db->prepare("
    SELECT
        p.*,
        c.nome AS categoria_nome,
        c.slug AS categoria_slug,
        c.cor  AS categoria_cor
    FROM blog_posts p
    LEFT JOIN blog_categorias c ON p.categoria_id = c.id
    WHERE p.slug = ? AND p.ativo = 1
    LIMIT 1
");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    include __DIR__ . '/404.php';
    exit;
}

// ── Incrementa visualizações ──────────────────────────────────
$db->prepare("UPDATE blog_posts SET visualizacoes = visualizacoes + 1 WHERE id = ?")
   ->execute([$post['id']]);

// ── Posts relacionados (mesma categoria, exceto o atual) ──────
$relacionados = [];
if (!empty($post['categoria_id'])) {
    $rel = $db->prepare("
        SELECT id, titulo, slug, imagem_destaque, alt_imagem, tempo_leitura, publicado_em
        FROM blog_posts
        WHERE ativo = 1
          AND categoria_id = ?
          AND id != ?
        ORDER BY publicado_em DESC
        LIMIT 3
    ");
    $rel->execute([$post['categoria_id'], $post['id']]);
    $relacionados = $rel->fetchAll();
}

// ── Posts recentes para sidebar ───────────────────────────────
$recentes = $db->prepare("
    SELECT id, titulo, slug, imagem_destaque, tempo_leitura, publicado_em
    FROM blog_posts
    WHERE ativo = 1 AND id != ?
    ORDER BY publicado_em DESC
    LIMIT 5
");
$recentes->execute([$post['id']]);
$recentes = $recentes->fetchAll();

// ── Categorias para sidebar ───────────────────────────────────
$categorias_side = $db->query("
    SELECT c.nome, c.slug, c.cor, COUNT(p.id) AS total
    FROM blog_categorias c
    LEFT JOIN blog_posts p ON p.categoria_id = c.id AND p.ativo = 1
    WHERE c.ativo = 1
    GROUP BY c.id
    HAVING total > 0
    ORDER BY total DESC, c.nome ASC
")->fetchAll();

// ── Metas da página ───────────────────────────────────────────
$page_title       = $post['titulo'];
$meta_description = !empty($post['meta_description'])
    ? $post['meta_description']
    : (mb_strlen($post['resumo'] ?? '') > 10 ? substr($post['resumo'], 0, 160) : '');
$meta_keywords    = $post['meta_keywords'] ?? '';

// ── Helper: formata data ──────────────────────────────────────
function formatarDataPost(string $data): string {
    $meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
              'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    $ts  = strtotime($data);
    return date('j', $ts) . ' de ' . $meses[(int)date('n', $ts)] . ' de ' . date('Y', $ts);
}

include __DIR__ . '/includes/header.php';
?>

<style>
/* ── Post: conteúdo ───────────────────────────────── */
.post-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px 60px;
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 48px;
    align-items: start;
}

/* ── Breadcrumb ──────────────────────────────────── */
.post-breadcrumb {
    background: #f0f4f9;
    padding: 12px 20px;
    border-bottom: 1px solid #e2eaf3;
}
.post-breadcrumb-inner {
    max-width: 1200px;
    margin: 0 auto;
    font-size: 13px;
    color: #889;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.post-breadcrumb a {
    color: #0057b7;
    text-decoration: none;
    font-weight: 500;
    transition: opacity .2s;
}
.post-breadcrumb a:hover { opacity: .75; }
.post-breadcrumb svg { flex-shrink: 0; }

/* ── Artigo principal ────────────────────────────── */
.post-article {}

.post-cat-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 16px;
    border-radius: 100px;
    font-size: 12.5px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 18px;
    text-decoration: none;
    transition: opacity .2s;
}
.post-cat-badge:hover { opacity: .85; }

.post-titulo {
    font-size: clamp(24px, 4vw, 38px);
    font-weight: 900;
    color: #0d1b2e;
    line-height: 1.25;
    margin-bottom: 18px;
    letter-spacing: -0.5px;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #0057b7;
    font-weight: 600;
    margin-bottom: 28px;
    flex-wrap: wrap;
}
.post-meta span { color: #8899aa; font-weight: 400; }
.post-meta-sep { color: #ccd; }

.post-imagem-destaque {
    width: 100%;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 36px;
    box-shadow: 0 6px 28px rgba(0,87,183,.12);
    aspect-ratio: 16/8;
    background: #dde8f4;
}
.post-imagem-destaque img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* ── Conteúdo do post ────────────────────────────── */
.post-conteudo {
    font-size: 16.5px;
    color: #2a3344;
    line-height: 1.8;
}
.post-conteudo h2 {
    font-size: 24px;
    font-weight: 800;
    color: #0d1b2e;
    margin: 38px 0 16px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0eaf4;
}
.post-conteudo h3 {
    font-size: 20px;
    font-weight: 700;
    color: #0d1b2e;
    margin: 28px 0 12px;
}
.post-conteudo p {
    margin-bottom: 18px;
}
.post-conteudo ul,
.post-conteudo ol {
    padding-left: 24px;
    margin-bottom: 18px;
}
.post-conteudo li {
    margin-bottom: 8px;
    list-style: disc;
}
.post-conteudo ol li { list-style: decimal; }
.post-conteudo strong { color: #0d1b2e; }
.post-conteudo a { color: #0057b7; text-decoration: underline; }
.post-conteudo blockquote {
    border-left: 4px solid #0057b7;
    background: #f0f6ff;
    padding: 16px 22px;
    margin: 24px 0;
    border-radius: 0 8px 8px 0;
    font-style: italic;
    color: #334;
}
.post-conteudo img {
    max-width: 100%;
    border-radius: 10px;
    margin: 20px 0;
}

/* ── Compartilhar ────────────────────────────────── */
.post-share {
    margin-top: 40px;
    padding-top: 28px;
    border-top: 2px solid #eaeff5;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.post-share-label {
    font-size: 13.5px;
    font-weight: 700;
    color: #445;
}
.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
    transition: opacity .2s, transform .2s;
}
.share-btn:hover { opacity: .88; transform: translateY(-1px); }
.share-wa  { background: #25d366; }
.share-fb  { background: #1877f2; }
.share-li  { background: #0a66c2; }

/* ── Posts relacionados ──────────────────────────── */
.relacionados {
    margin-top: 56px;
    padding-top: 36px;
    border-top: 2px solid #eaeff5;
}
.relacionados-titulo {
    font-size: 22px;
    font-weight: 800;
    color: #0d1b2e;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.relacionados-titulo::after {
    content: '';
    flex: 1;
    height: 2px;
    background: linear-gradient(to right, #0057b7, transparent);
    border-radius: 2px;
}
.relacionados-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.rel-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #eaeff5;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    text-decoration: none;
    color: inherit;
    transition: transform .22s, box-shadow .22s;
    display: block;
}
.rel-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,87,183,.12);
}
.rel-card-img {
    aspect-ratio: 16/9;
    overflow: hidden;
    background: #dde8f4;
}
.rel-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s;
}
.rel-card:hover .rel-card-img img { transform: scale(1.05); }
.rel-card-body { padding: 14px 16px 18px; }
.rel-card-data {
    font-size: 11.5px;
    color: #0057b7;
    font-weight: 600;
    margin-bottom: 6px;
}
.rel-card-titulo {
    font-size: 14.5px;
    font-weight: 700;
    color: #1a2535;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Sidebar ─────────────────────────────────────── */
.post-sidebar {
    position: sticky;
    top: 80px;
    display: flex;
    flex-direction: column;
    gap: 28px;
}
.sidebar-card {
    background: #fff;
    border-radius: 14px;
    padding: 22px 22px 24px;
    border: 1px solid #eaeff5;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-top: 20px;
}
.sidebar-titulo {
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #0057b7;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e0eaf4;
}

/* Recentes sidebar */
.sidebar-recentes { display: flex; flex-direction: column; gap: 14px; }
.sidebar-post {
    display: flex;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    transition: opacity .2s;
}
.sidebar-post:hover { opacity: .8; }
.sidebar-post-img {
    width: 68px;
    height: 52px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    background: #dde8f4;
}
.sidebar-post-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.sidebar-post-titulo {
    font-size: 13px;
    font-weight: 600;
    color: #1a2535;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.sidebar-post-data {
    font-size: 11px;
    color: #0057b7;
    font-weight: 600;
    margin-top: 4px;
}

/* Categorias sidebar */
.sidebar-cats { display: flex; flex-direction: column; gap: 8px; }
.sidebar-cat-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 14px;
    border-radius: 8px;
    background: #f4f7fb;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    color: #334;
    transition: background .2s, color .2s;
}
.sidebar-cat-link:hover {
    background: #e0eaf6;
    color: #0057b7;
}
.sidebar-cat-count {
    background: #e0eaf6;
    color: #0057b7;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 100px;
}

/* ── Responsivo ───────────────────────────────────── */
@media (max-width: 1024px) {
    .post-wrapper { grid-template-columns: 1fr; gap: 36px; }
    .post-sidebar { position: static; }
    .relacionados-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .post-wrapper { padding: 24px 16px 48px; }
    .relacionados-grid { grid-template-columns: 1fr; }
    .post-meta { gap: 6px; font-size: 12px; }
}
</style>

<!-- BREADCRUMB -->
<div class="post-breadcrumb">
    <div class="post-breadcrumb-inner">
        <a href="<?= BASE_URL ?>/">Início</a>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <a href="<?= BASE_URL ?>/blog.php">Blog</a>
        <?php if (!empty($post['categoria_nome'])): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
            <a href="<?= BASE_URL ?>/blog.php?categoria=<?= urlencode($post['categoria_slug']) ?>">
                <?= htmlspecialchars($post['categoria_nome']) ?>
            </a>
        <?php endif; ?>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <span><?= htmlspecialchars(mb_strimwidth($post['titulo'], 0, 48, '…')) ?></span>
    </div>
</div>

<div class="post-wrapper">

    <!-- ARTIGO PRINCIPAL -->
    <article class="post-article">

        <!-- Badge de categoria -->
        <?php if (!empty($post['categoria_nome'])): ?>
            <a href="<?= BASE_URL ?>/blog.php?categoria=<?= urlencode($post['categoria_slug']) ?>"
               class="post-cat-badge"
               style="background: <?= htmlspecialchars($post['categoria_cor'] ?? '#0057b7') ?>">
                <?= htmlspecialchars($post['categoria_nome']) ?>
            </a>
        <?php endif; ?>

        <!-- Título -->
        <h1 class="post-titulo"><?= htmlspecialchars($post['titulo']) ?></h1>

        <!-- Meta: data / tempo de leitura / autor -->
        <div class="post-meta">
            <span><?= formatarDataPost($post['publicado_em']) ?></span>
            <span class="post-meta-sep">·</span>
            <?= (int)$post['tempo_leitura'] ?> minutos de leitura
            <?php if (!empty($post['autor'])): ?>
                <span class="post-meta-sep">·</span>
                <span><?= htmlspecialchars($post['autor']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Imagem de destaque -->
        <?php if (!empty($post['imagem_destaque'])): ?>
            <div class="post-imagem-destaque">
                <img src="<?= htmlspecialchars($post['imagem_destaque']) ?>"
                     alt="<?= htmlspecialchars($post['alt_imagem'] ?? $post['titulo']) ?>"
                     loading="eager">
            </div>
        <?php endif; ?>

        <!-- Conteúdo do artigo -->
        <div class="post-conteudo">
            <?= $post['conteudo'] ?>
        </div>

        <!-- Compartilhar -->
        <?php
        $url_encoded = urlencode((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $titulo_encoded = urlencode($post['titulo']);
        ?>
        <div class="post-share">
            <span class="post-share-label">Compartilhar:</span>
            <a href="https://wa.me/?text=<?= $titulo_encoded . '%20' . $url_encoded ?>"
               target="_blank" rel="noopener" class="share-btn share-wa">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url_encoded ?>"
               target="_blank" rel="noopener" class="share-btn share-fb">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                </svg>
                Facebook
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url_encoded ?>&title=<?= $titulo_encoded ?>"
               target="_blank" rel="noopener" class="share-btn share-li">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/>
                    <circle cx="4" cy="4" r="2"/>
                </svg>
                LinkedIn
            </a>
        </div>

        <!-- Posts relacionados -->
        <?php if (!empty($relacionados)): ?>
        <div class="relacionados">
            <h3 class="relacionados-titulo">Artigos Relacionados</h3>
            <div class="relacionados-grid">
                <?php foreach ($relacionados as $rel): ?>
                    <a href="<?= BASE_URL ?>/blog-conteudo.php?slug=<?= urlencode($rel['slug']) ?>" class="rel-card">
                        <div class="rel-card-img">
                            <?php if (!empty($rel['imagem_destaque'])): ?>
                                <img src="<?= htmlspecialchars($rel['imagem_destaque']) ?>"
                                     alt="<?= htmlspecialchars($rel['alt_imagem'] ?? $rel['titulo']) ?>"
                                     loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="rel-card-body">
                            <div class="rel-card-data"><?= formatarDataPost($rel['publicado_em']) ?></div>
                            <div class="rel-card-titulo"><?= htmlspecialchars($rel['titulo']) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </article>

    <!-- SIDEBAR -->
    <aside class="post-sidebar">

        <!-- Categorias -->
        <?php if (!empty($categorias_side)): ?>
        <div class="sidebar-card">
            <div class="sidebar-titulo">Categorias</div>
            <div class="sidebar-cats">
                <?php foreach ($categorias_side as $cat): ?>
                    <a href="<?= BASE_URL ?>/blog.php?categoria=<?= urlencode($cat['slug']) ?>"
                       class="sidebar-cat-link">
                        <?= htmlspecialchars($cat['nome']) ?>
                        <span class="sidebar-cat-count"><?= $cat['total'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Posts recentes -->
        <?php if (!empty($recentes)): ?>
        <div class="sidebar-card">
            <div class="sidebar-titulo">Artigos Recentes</div>
            <div class="sidebar-recentes">
                <?php foreach ($recentes as $rec): ?>
                    <a href="<?= BASE_URL ?>/blog-conteudo.php?slug=<?= urlencode($rec['slug']) ?>" class="sidebar-post">
                        <div class="sidebar-post-img">
                            <?php if (!empty($rec['imagem_destaque'])): ?>
                                <img src="<?= htmlspecialchars($rec['imagem_destaque']) ?>" alt="" loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="sidebar-post-titulo"><?= htmlspecialchars($rec['titulo']) ?></div>
                            <div class="sidebar-post-data"><?= formatarDataPost($rec['publicado_em']) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Voltar ao blog -->
        <a href="<?= BASE_URL ?>/blog.php"
           style="display:flex;align-items:center;justify-content:center;gap:8px;
                  padding:14px;background:#f0f6ff;border-radius:12px;
                  color:#0057b7;font-weight:700;font-size:14px;text-decoration:none;
                  border:2px solid #c5d8f0;transition:background .2s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Voltar ao Blog
        </a>

    </aside>

</div><!-- /.post-wrapper -->

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
</body>
</html>