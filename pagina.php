<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/render_layout.php';

$db = getDB();

// Pega o slug da URL (suporta slugs compostos: menu/categoria/subcategoria)
// O Apache com a flag [NE] preserva as barras; caso contrário, remontamos do REQUEST_URI
$slug = $_GET['slug'] ?? '';

// Fallback: se o slug vier vazio ou sem barras, tenta extrair do REQUEST_URI
if (empty($slug) || !str_contains($slug, '/')) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base       = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path       = ltrim(substr($requestUri, strlen($base)), '/');
    // Remove query string residual
    $path = strtok($path, '?');
    if (!empty($path) && $path !== 'pagina.php') {
        $slug = rtrim($path, '/');
    }
}

if (empty($slug)) {
    include __DIR__ . '/404.php';
    exit;
}

// Busca a página pelo slug
$stmt = $db->prepare("SELECT * FROM paginas WHERE slug = ? AND ativo = 1");
$stmt->execute([$slug]);
$pagina = $stmt->fetch();

if (!$pagina) {
    include __DIR__ . '/404.php';
    exit;
}

$page_title       = $pagina['titulo'];
$meta_description = $pagina['meta_description'];
$meta_keywords    = $pagina['meta_keywords'];

include __DIR__ . '/includes/header.php';
?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <?php render_page_contents($pagina['id'], $db); ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
</body>
</html>