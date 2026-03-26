<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Menus';
$current_module = 'menus';
$current_page   = 'menus-lista';

$db = getDB();

$menus      = $db->query("SELECT * FROM menus ORDER BY order_position ASC, name ASC")->fetchAll();
$categorias = $db->query("SELECT c.*, m.name as menu_name FROM categorias c LEFT JOIN menus m ON c.menu_id = m.id ORDER BY c.order_position ASC, c.name ASC")->fetchAll();
$subcats    = $db->query("SELECT s.*, c.name as categoria_name FROM subcategorias s LEFT JOIN categorias c ON s.categoria_id = c.id ORDER BY s.order_position ASC, s.name ASC")->fetchAll();

$cats_by_menu = [];
foreach ($categorias as $c) { $cats_by_menu[$c['menu_id']][] = $c; }

$subs_by_cat = [];
foreach ($subcats as $s) { $subs_by_cat[$s['categoria_id']][] = $s; }

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.mn-wrap{padding:28px}
.mn-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
.mn-top h2{font-size:20px;font-weight:700;color:#00071c}
.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 15px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .15s;white-space:nowrap}
.btn-dark{background:#00071c;color:#fff}.btn-dark:hover{background:#1a2540}
.btn-blue{background:#3b82f6;color:#fff}.btn-blue:hover{background:#2563eb}
.btn-green{background:#10b981;color:#fff}.btn-green:hover{background:#059669}
.btn-edit{background:#e8f4fd;color:#1d6fa5;border:1px solid #bde0f7}.btn-edit:hover{background:#bde0f7}
.btn-del{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}.btn-del:hover{background:#fecaca}
.btn-sm{padding:4px 10px;font-size:12px;border-radius:6px}
.alert{padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:13px}
.alert-s{background:#d1fae5;color:#065f46;border-left:4px solid #10b981}
.alert-e{background:#fef2f2;color:#991b1b;border-left:4px solid #ef4444}
.empty{text-align:center;padding:60px 20px;color:#9ca3af}
.empty .ico{font-size:56px;display:block;margin-bottom:12px}
/* Tree */
.tree{display:flex;flex-direction:column;gap:12px}
.menu-card{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.07);border:1px solid #e5e7eb;overflow:hidden}
.menu-hd{display:flex;align-items:center;gap:12px;padding:15px 20px;cursor:pointer;user-select:none;transition:background .15s}
.menu-hd:hover{background:#f9fafb}
.toggler{font-size:10px;color:#9ca3af;transition:transform .2s;flex-shrink:0}
.toggler.open{transform:rotate(90deg)}
.m-order{background:#00071c;color:#fff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:5px;flex-shrink:0}
.m-name{font-size:15px;font-weight:700;color:#00071c;flex:1}
.m-slug{font-size:12px;color:#aaa;font-family:monospace}
.badge{padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;flex-shrink:0}
.b-on{background:#d1fae5;color:#065f46}
.b-off{background:#fee2e2;color:#991b1b}
.m-acts{display:flex;gap:6px;flex-shrink:0}
/* body */
.menu-bd{border-top:1px solid #f3f4f6;background:#f8f9fa}
.sec-hd{display:flex;align-items:center;justify-content:space-between;padding:11px 20px 8px 44px}
.sec-lbl{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px}
.cats-list{padding:0 16px 16px 44px;display:flex;flex-direction:column;gap:7px}
.no-item{padding:12px 20px 14px 44px;font-size:13px;color:#bbb;font-style:italic}
/* cat card */
.cat-card{background:#fff;border:1px solid #e5e7eb;border-radius:9px;overflow:hidden}
.cat-hd{display:flex;align-items:center;gap:9px;padding:10px 13px;cursor:pointer;user-select:none;transition:background .15s}
.cat-hd:hover{background:#f9fafb}
.c-name{font-size:13px;font-weight:600;color:#111;flex:1}
.c-slug{font-size:11px;color:#ccc;font-family:monospace}
.cat-bd{border-top:1px solid #f3f4f6;background:#f9fafb}
.sub-hd{display:flex;align-items:center;justify-content:space-between;padding:8px 13px 4px 28px}
.sub-lbl{font-size:10px;font-weight:700;color:#bbb;text-transform:uppercase;letter-spacing:.5px}
.subs-list{padding:4px 10px 10px 28px;display:flex;flex-direction:column;gap:5px}
.no-sub{padding:6px 12px 10px 28px;font-size:12px;color:#ccc;font-style:italic}
.sub-row{display:flex;align-items:center;gap:8px;padding:7px 11px;background:#fff;border:1px solid #e5e7eb;border-radius:7px;font-size:12px}
.s-name{flex:1;font-weight:600;color:#111}
.s-slug{color:#ccc;font-family:monospace;font-size:11px}
</style>

<main class="content">
<div class="mn-wrap">
    <div class="mn-top">
        <h2>📋 Menus</h2>
        <a href="<?= BASE_URL ?>/admin/menus/criar.php" class="btn btn-dark">➕ Novo Menu</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-s">✅ <?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-e">⚠️ <?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($menus)): ?>
        <div class="empty">
            <span class="ico">📋</span>
            <h3 style="color:#374151;font-size:18px;margin-bottom:8px">Nenhum menu cadastrado</h3>
            <p style="font-size:14px">Crie o primeiro menu para organizar a navegação do site.</p>
            <a href="<?= BASE_URL ?>/admin/menus/criar.php" class="btn btn-dark" style="margin-top:18px">➕ Criar Primeiro Menu</a>
        </div>
    <?php else: ?>

    <div class="tree">
    <?php foreach ($menus as $menu): ?>
    <?php $menu_cats = $cats_by_menu[$menu['id']] ?? []; ?>

    <div class="menu-card">
        <!-- MENU ROW -->
        <div class="menu-hd" onclick="tog('m','<?= $menu['id'] ?>')">
            <span class="toggler" id="tg-m-<?= $menu['id'] ?>">▶</span>
            <span class="m-order"><?= $menu['order_position'] ?></span>
            <span class="m-name"><?= htmlspecialchars($menu['name']) ?></span>
            <span class="m-slug">/<?= htmlspecialchars($menu['slug']) ?></span>
            <span class="badge <?= $menu['active'] ? 'b-on':'b-off' ?>"><?= $menu['active'] ? 'Ativo':'Inativo' ?></span>
            <div class="m-acts" onclick="event.stopPropagation()">
                <a href="<?= BASE_URL ?>/admin/menus/editar.php?id=<?= $menu['id'] ?>" class="btn btn-edit btn-sm">✏️ Editar</a>
                <a href="<?= BASE_URL ?>/admin/menus/deletar.php?id=<?= $menu['id'] ?>" class="btn btn-del btn-sm">🗑️</a>
            </div>
        </div>

        <!-- CATEGORIAS -->
        <div class="menu-bd" id="bd-m-<?= $menu['id'] ?>" style="display:none">
            <div class="sec-hd">
                <span class="sec-lbl">📂 Categorias (<?= count($menu_cats) ?>)</span>
                <a href="<?= BASE_URL ?>/admin/categorias/criar.php?menu_id=<?= $menu['id'] ?>" class="btn btn-blue btn-sm">➕ Nova Categoria</a>
            </div>

            <?php if (empty($menu_cats)): ?>
                <div class="no-item">Nenhuma categoria — clique em "Nova Categoria" para adicionar.</div>
            <?php else: ?>
            <div class="cats-list">
            <?php foreach ($menu_cats as $cat): ?>
            <?php $cat_subs = $subs_by_cat[$cat['id']] ?? []; ?>
            <div class="cat-card">
                <!-- CATEGORIA ROW -->
                <div class="cat-hd" onclick="tog('c','<?= $cat['id'] ?>')">
                    <span class="toggler" id="tg-c-<?= $cat['id'] ?>">▶</span>
                    <span class="c-name"><?= htmlspecialchars($cat['name']) ?></span>
                    <span class="c-slug">/<?= htmlspecialchars($cat['slug']) ?></span>
                    <span class="badge <?= $cat['active'] ? 'b-on':'b-off' ?>" style="font-size:10px;padding:2px 8px"><?= $cat['active'] ? 'Ativa':'Inativa' ?></span>
                    <div onclick="event.stopPropagation()" style="display:flex;gap:5px">
                        <a href="<?= BASE_URL ?>/admin/categorias/editar.php?id=<?= $cat['id'] ?>" class="btn btn-edit btn-sm">✏️</a>
                        <a href="<?= BASE_URL ?>/admin/categorias/deletar.php?id=<?= $cat['id'] ?>" class="btn btn-del btn-sm">🗑️</a>
                    </div>
                </div>

                <!-- SUBCATEGORIAS -->
                <div class="cat-bd" id="bd-c-<?= $cat['id'] ?>" style="display:none">
                    <div class="sub-hd">
                        <span class="sub-lbl">🔖 Subcategorias (<?= count($cat_subs) ?>)</span>
                        <a href="<?= BASE_URL ?>/admin/subcategorias/criar.php?categoria_id=<?= $cat['id'] ?>" class="btn btn-green btn-sm">➕ Nova Subcategoria</a>
                    </div>

                    <?php if (empty($cat_subs)): ?>
                        <div class="no-sub">Nenhuma subcategoria nesta categoria.</div>
                    <?php else: ?>
                    <div class="subs-list">
                    <?php foreach ($cat_subs as $sub): ?>
                        <div class="sub-row">
                            <span class="s-name"><?= htmlspecialchars($sub['name']) ?></span>
                            <span class="s-slug">/<?= htmlspecialchars($sub['slug']) ?></span>
                            <span class="badge <?= $sub['active'] ? 'b-on':'b-off' ?>" style="font-size:10px;padding:2px 8px"><?= $sub['active'] ? 'Ativa':'Inativa' ?></span>
                            <div style="display:flex;gap:5px">
                                <a href="<?= BASE_URL ?>/admin/subcategorias/editar.php?id=<?= $sub['id'] ?>" class="btn btn-edit btn-sm">✏️</a>
                                <a href="<?= BASE_URL ?>/admin/subcategorias/deletar.php?id=<?= $sub['id'] ?>" class="btn btn-del btn-sm">🗑️</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>
</main>

<script>
function tog(type, id) {
    const bd  = document.getElementById('bd-' + type + '-' + id);
    const tg  = document.getElementById('tg-' + type + '-' + id);
    const isOpen = bd.style.display !== 'none';
    bd.style.display = isOpen ? 'none' : 'block';
    tg.classList.toggle('open', !isOpen);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
