<?php
// includes/header.php — Cabeçalho do site público
// Requer: $db, $page_title
// Opcional: $meta_description, $meta_keywords

// ── Configurações globais do site ────────────────────────────
$_site_cfg = $db->query("SELECT * FROM configuracoes_site WHERE id = 1 LIMIT 1")->fetch();
if (!$_site_cfg) $_site_cfg = [];

// Meta description: usa a específica da página, ou a global como fallback
if (empty($meta_description) && !empty($_site_cfg['meta_descricao'])) {
    $meta_description = $_site_cfg['meta_descricao'];
}
if (empty($meta_keywords) && !empty($_site_cfg['meta_keywords'])) {
    $meta_keywords = $_site_cfg['meta_keywords'];
}

// Nome do site
$_site_name_display = SITE_NAME;
$_sep_titulo        = ' - ';

// Robots
$_robots_index  = isset($_site_cfg['robots_index'])  ? (int)$_site_cfg['robots_index']  : 1;
$_robots_follow = isset($_site_cfg['robots_follow']) ? (int)$_site_cfg['robots_follow'] : 1;
$_robots_meta   = ($_robots_index ? 'index' : 'noindex') . ', ' . ($_robots_follow ? 'follow' : 'nofollow');

// Google Analytics
$_ga_id = !empty($_site_cfg['google_analytics']) ? preg_replace('/[^A-Za-z0-9\-]/', '', $_site_cfg['google_analytics']) : '';

// Open Graph
$_og_titulo    = !empty($_site_cfg['og_titulo'])    ? $_site_cfg['og_titulo']    : $_site_name_display;
$_og_descricao = !empty($_site_cfg['og_descricao']) ? $_site_cfg['og_descricao'] : ($meta_description ?? '');
$_og_imagem    = $_site_cfg['og_imagem'] ?? '';
$_favicon_url  = !empty($_site_cfg['favicon_url'])  ? $_site_cfg['favicon_url']  : null;

// ── Configurações visuais do header ──────────────────────────
$_hdr_cfg = $db->query("SELECT * FROM header_config WHERE ativo = 1 LIMIT 1")->fetch();

$_hc = [
    'nome_empresa'     => $_hdr_cfg['nome_empresa']     ?? 'Minha Empresa',
    'logo_url'         => $_hdr_cfg['logo_url']         ?? null,
    // Desktop
    'cor_fundo'        => $_hdr_cfg['cor_fundo']        ?? '#00071c',
    'cor_texto'        => $_hdr_cfg['cor_texto']        ?? '#ffffff',
    'cor_borda'        => $_hdr_cfg['cor_borda']        ?? '#1e2a3a',
    'cor_nav_link'     => $_hdr_cfg['cor_nav_link']     ?? '#ffffff',
    // Mobile
    'mob_cor_fundo'       => $_hdr_cfg['mob_cor_fundo']       ?? '#111827',
    'mob_cor_texto'       => $_hdr_cfg['mob_cor_texto']       ?? '#ffffff',
    'mob_cor_borda'       => $_hdr_cfg['mob_cor_borda']       ?? '#1e2a3a',
    'mob_cor_nav_link'    => $_hdr_cfg['mob_cor_nav_link']    ?? '#ffffff',
    'mob_cor_menu_aberto' => $_hdr_cfg['mob_cor_menu_aberto'] ?? '#1a2235',
];

// ── Menus / Categorias / Subcategorias ───────────────────────
$_hdr_stmt = $db->query("
    SELECT
        m.id as menu_id, m.name as menu_name, m.slug as menu_slug,
        m.url as menu_url, m.target as menu_target,
        c.id as categoria_id, c.name as categoria_name, c.slug as categoria_slug,
        c.url as categoria_url, c.target as categoria_target,
        s.id as subcategoria_id, s.name as subcategoria_name, s.slug as subcategoria_slug,
        s.url as subcategoria_url, s.target as subcategoria_target
    FROM menus m
    LEFT JOIN categorias c ON m.id = c.menu_id AND c.active = 1
    LEFT JOIN subcategorias s ON c.id = s.categoria_id AND s.active = 1
    WHERE m.active = 1
    ORDER BY m.order_position ASC, m.name ASC,
             c.order_position ASC, c.name ASC,
             s.order_position ASC, s.name ASC
");

$_hdr_result = $_hdr_stmt->fetchAll();
$menus = [];
foreach ($_hdr_result as $row) {
    $mid = $row['menu_id'];
    if (!isset($menus[$mid])) {
        $menus[$mid] = [
            'id' => $mid, 'name' => $row['menu_name'], 'slug' => $row['menu_slug'],
            'url' => $row['menu_url'], 'target' => $row['menu_target'], 'categorias' => []
        ];
    }
    if ($row['categoria_id']) {
        $cid = $row['categoria_id'];
        if (!isset($menus[$mid]['categorias'][$cid])) {
            $menus[$mid]['categorias'][$cid] = [
                'id' => $cid, 'name' => $row['categoria_name'], 'slug' => $row['categoria_slug'],
                'url' => $row['categoria_url'], 'target' => $row['categoria_target'], 'subcategorias' => []
            ];
        }
        if ($row['subcategoria_id']) {
            $menus[$mid]['categorias'][$cid]['subcategorias'][] = [
                'id' => $row['subcategoria_id'], 'name' => $row['subcategoria_name'],
                'slug' => $row['subcategoria_slug'], 'url' => $row['subcategoria_url'],
                'target' => $row['subcategoria_target']
            ];
        }
    }
}

function _hce(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _hce($page_title) ?><?= _hce($_sep_titulo) ?><?= _hce($_site_name_display) ?></title>

    <!-- SEO Meta -->
    <meta name="robots" content="<?= _hce($_robots_meta) ?>">
    <?php if (!empty($meta_description)): ?>
        <meta name="description" content="<?= _hce($meta_description) ?>">
    <?php endif; ?>
    <?php if (!empty($meta_keywords)): ?>
        <meta name="keywords" content="<?= _hce($meta_keywords) ?>">
    <?php endif; ?>
    <?php if (!empty($_site_cfg['meta_autor'])): ?>
        <meta name="author" content="<?= _hce($_site_cfg['meta_autor']) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <?php if (!empty($_favicon_url)): ?>
        <link rel="icon" href="<?= _hce($_favicon_url) ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?= _hce(BASE_URL) ?>/">
    <meta property="og:title"       content="<?= _hce($_og_titulo) ?>">
    <meta property="og:description" content="<?= _hce($_og_descricao) ?>">
    <?php if (!empty($_og_imagem)): ?>
        <meta property="og:image" content="<?= _hce($_og_imagem) ?>">
    <?php endif; ?>

    <!-- Google Analytics (GA4) -->
    <?php if (!empty($_ga_id)): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= _hce($_ga_id) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= _hce($_ga_id) ?>');
        </script>
    <?php endif; ?>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

    <!-- Cores dinâmicas do header (banco de dados) -->
    <style>
        :root {
            /* ── Desktop ── */
            --hdr-bg:       <?= _hce($_hc['cor_fundo']) ?>;
            --hdr-text:     <?= _hce($_hc['cor_texto']) ?>;
            --hdr-border:   <?= _hce($_hc['cor_borda']) ?>;
            --hdr-nav-link: <?= _hce($_hc['cor_nav_link']) ?>;

            /* ── Mobile ── valores concretos, sem referência a outra variável */
            --mob-bg:           <?= _hce($_hc['mob_cor_fundo']) ?>;
            --mob-text:         <?= _hce($_hc['mob_cor_texto']) ?>;
            --mob-border:       <?= _hce($_hc['mob_cor_borda']) ?>;
            --mob-nav-link:     <?= _hce($_hc['mob_cor_nav_link']) ?>;
            --mob-menu-aberto:  <?= _hce($_hc['mob_cor_menu_aberto']) ?>;
        }
    </style>

    <?php
    // ── CSS Personalizado (banco de dados) ─────────────────────
    $_css_row = $db->query("SELECT css FROM css_personalizado WHERE id = 1 AND ativo = 1 LIMIT 1")->fetch();
    if (!empty($_css_row['css'])):
    ?>
    <style id="css-personalizado">
        <?= $_css_row['css'] ?>
    </style>
    <?php endif; ?>
</head>
<body>

<?php
// ── Ícones Flutuantes ───────────────────────────────────────────
$_fi_wa   = $db->query("SELECT * FROM icones_flutuantes WHERE tipo='whatsapp' AND ativo=1 LIMIT 1")->fetch();
$_fi_topo = $db->query("SELECT * FROM icones_flutuantes WHERE tipo='topo'     AND ativo=1 LIMIT 1")->fetch();

if ($_fi_wa || $_fi_topo):
?>
<style>
/* ── Base do botão flutuante ── */
.fi-btn {
    position: fixed;
    bottom: 24px;
    z-index: 9998;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(0,0,0,.28);
    cursor: pointer;
    transition: transform .2s, box-shadow .2s, opacity .3s;
    text-decoration: none;
    border: none;
    outline: none;
}
.fi-btn:hover {
    transform: scale(1.12);
    box-shadow: 0 6px 22px rgba(0,0,0,.36);
}
/* Tooltip */
.fi-btn::after {
    content: attr(data-tip);
    position: absolute;
    white-space: nowrap;
    background: rgba(0,0,0,.72);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    pointer-events: none;
    opacity: 0;
    transition: opacity .2s;
    font-family: inherit;
}
.fi-btn:hover::after { opacity: 1; }
/* Posições */
.fi-br { bottom: 24px; right: 24px; }
.fi-bl { bottom: 24px; left:  24px; }
/* Tooltip lado */
.fi-br::after { right: calc(100% + 10px); }
.fi-bl::after { left:  calc(100% + 10px); }
/* Empilhar quando ambos estão no mesmo canto */
.fi-btn.fi-offset { bottom: 86px; } /* sobe quando há outro botão abaixo */
/* Botão topo: oculto até rolar */
#fi-topo { opacity: 0; pointer-events: none; }
#fi-topo.visible { opacity: 1; pointer-events: all; }
@media(max-width:480px) {
    .fi-btn { width: 44px; height: 44px; }
    .fi-br { bottom: 16px; right: 16px; }
    .fi-bl { bottom: 16px; left:  16px; }
    .fi-btn.fi-offset { bottom: 70px; }
}
</style>

<?php
// Detecta se ambos estão no mesmo canto (para empilhar)
$_fi_wa_pos   = $_fi_wa   ? _hce($_fi_wa['posicao'])   : '';
$_fi_topo_pos = $_fi_topo ? _hce($_fi_topo['posicao']) : '';
$_mesmo_canto = ($_fi_wa && $_fi_topo && $_fi_wa_pos === $_fi_topo_pos);

// WhatsApp
if ($_fi_wa):
    $_wa_url   = 'https://wa.me/' . preg_replace('/\D/','',$_fi_wa['numero']);
    if (!empty($_fi_wa['mensagem'])) $_wa_url .= '?text=' . rawurlencode($_fi_wa['mensagem']);
    $_wa_map   = ['bottom-right' => 'br', 'bottom-left' => 'bl'];
    $_wa_cls   = 'fi-btn fi-' . ($_wa_map[$_fi_wa['posicao']] ?? 'br') . ' ' . ($_mesmo_canto ? 'fi-offset' : '');
?>
<a id="fi-whatsapp"
   href="<?= _hce($_wa_url) ?>"
   target="_blank"
   rel="noopener noreferrer"
   class="<?= trim($_wa_cls) ?>"
   style="background:<?= _hce($_fi_wa['cor_fundo']) ?>"
   data-tip="WhatsApp"
   aria-label="WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
         style="width:26px;height:26px;fill:<?= _hce($_fi_wa['cor_icone']) ?>">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
</a>
<?php endif; ?>

<?php if ($_fi_topo):
    $_topo_map = ['bottom-right' => 'br', 'bottom-left' => 'bl'];
    $_topo_cls = 'fi-btn fi-' . ($_topo_map[$_fi_topo['posicao']] ?? 'br');
?>
<button id="fi-topo"
        class="<?= trim($_topo_cls) ?>"
        style="background:<?= _hce($_fi_topo['cor_fundo']) ?>"
        data-tip="Voltar ao topo"
        aria-label="Voltar ao topo"
        onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
         style="width:22px;height:22px;fill:<?= _hce($_fi_topo['cor_icone']) ?>">
        <path d="M12 4l-8 8h5v8h6v-8h5z"/>
    </svg>
</button>
<script>
(function(){
    var btn = document.getElementById('fi-topo');
    if(!btn) return;
    window.addEventListener('scroll', function(){
        if(window.scrollY > 300) btn.classList.add('visible');
        else btn.classList.remove('visible');
    }, {passive:true});
})();
</script>
<?php endif; ?>

<?php endif; // $_fi_wa || $_fi_topo ?>

<header class="header">
    <div class="header-container">

        <div class="logo">
            <a href="<?= BASE_URL ?>/">
                <?php if (!empty($_hc['logo_url'])): ?>
                    <img src="<?= _hce($_hc['logo_url']) ?>"
                         alt="<?= _hce($_hc['nome_empresa']) ?>"
                         style="max-height:40px;display:block">
                <?php else: ?>
                    <?= _hce($_hc['nome_empresa']) ?>
                <?php endif; ?>
            </a>
        </div>

        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Abrir menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav" id="mainNav">
            <ul>
                <?php foreach ($menus as $menu): ?>
                    <?php
                    $hasCats = count($menu['categorias']) > 0;
                    if ($hasCats) {
                        $menuLink = '#'; $menuClickable = false;
                    } elseif (!empty($menu['url'])) {
                        $menuLink = _hce($menu['url']); $menuClickable = true;
                    } else {
                        $menuLink = BASE_URL . '/' . _hce($menu['slug']); $menuClickable = true;
                    }
                    ?>
                    <li class="<?= $hasCats ? 'has-submenu' : '' ?>">
                        <?php if ($menuClickable): ?>
                            <a href="<?= $menuLink ?>" target="<?= _hce($menu['target']) ?>"><?= _hce($menu['name']) ?></a>
                        <?php else: ?>
                            <a href="#" onclick="return false;"><?= _hce($menu['name']) ?></a>
                        <?php endif; ?>

                        <?php if ($hasCats): ?>
                            <ul class="dropdown">
                                <?php foreach ($menu['categorias'] as $cat): ?>
                                    <?php
                                    $hasSub = count($cat['subcategorias']) > 0;
                                    if ($hasSub) {
                                        $catLink = '#'; $catClickable = false;
                                    } elseif (!empty($cat['url'])) {
                                        $catLink = _hce($cat['url']); $catClickable = true;
                                    } else {
                                        $catLink = BASE_URL . '/' . _hce($menu['slug']) . '/' . _hce($cat['slug']); $catClickable = true;
                                    }
                                    ?>
                                    <li class="<?= $hasSub ? 'has-submenu' : '' ?>">
                                        <?php if ($catClickable): ?>
                                            <a href="<?= $catLink ?>" target="<?= _hce($cat['target']) ?>"><?= _hce($cat['name']) ?></a>
                                        <?php else: ?>
                                            <a href="#" onclick="return false;"><?= _hce($cat['name']) ?></a>
                                        <?php endif; ?>

                                        <?php if ($hasSub): ?>
                                            <ul class="dropdown-sub">
                                                <?php foreach ($cat['subcategorias'] as $sub): ?>
                                                    <?php
                                                    $subLink = !empty($sub['url'])
                                                        ? _hce($sub['url'])
                                                        : BASE_URL . '/' . _hce($menu['slug']) . '/' . _hce($cat['slug']) . '/' . _hce($sub['slug']);
                                                    ?>
                                                    <li>
                                                        <a href="<?= $subLink ?>" target="<?= _hce($sub['target']) ?>"><?= _hce($sub['name']) ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

    </div>
</header>

<div class="mobile-overlay" id="mobileOverlay"></div>