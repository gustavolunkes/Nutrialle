<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/render_layout.php';

$db = getDB();

$page_title       = 'Home';
$meta_description = '';
$meta_keywords    = '';

include __DIR__ . '/includes/header.php';
?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <?php render_home_contents($db); ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
</body>
</html>