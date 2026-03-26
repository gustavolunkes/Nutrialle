<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Nova Subcategoria';
$current_module = 'menus';
$current_page   = 'menus-lista';

$error = '';
$db    = getDB();

$preselect_cat_id = intval($_GET['categoria_id'] ?? 0);

// Busca todas as categorias com menu
$categorias = $db->query("
    SELECT c.id, c.name AS cat_name, c.menu_id, m.name AS menu_name
    FROM categorias c
    LEFT JOIN menus m ON c.menu_id = m.id
    WHERE c.active = 1
    ORDER BY m.name ASC, c.name ASC
")->fetchAll();

// Mapas para o breadcrumb dinâmico
$cats_map = [];
foreach ($categorias as $c) { $cats_map[$c['id']] = $c; }

$name = $slug = $url = $description = '';
$target = '_self';
$order_position = 0;
$active = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria_id   = intval($_POST['categoria_id'] ?? 0);
    $name           = trim($_POST['name'] ?? '');
    $slug           = trim($_POST['slug'] ?? '');
    $url            = trim($_POST['url'] ?? '');
    $target         = $_POST['target'] ?? '_self';
    $description    = trim($_POST['description'] ?? '');
    $order_position = intval($_POST['order_position'] ?? 0);
    $active         = isset($_POST['active']) ? 1 : 0;

    if (!$categoria_id) { $error = 'Selecione uma categoria.'; }
    elseif (!$name)     { $error = 'O nome é obrigatório.'; }
    elseif (!$slug)     { $error = 'O slug é obrigatório.'; }
    else {
        try {
            $chk = $db->prepare("SELECT id FROM subcategorias WHERE slug = ? AND categoria_id = ?");
            $chk->execute([$slug, $categoria_id]);
            if ($chk->fetch()) {
                $error = 'Slug já em uso nesta categoria.';
            } else {
                $db->prepare("INSERT INTO subcategorias (categoria_id,name,slug,url,target,description,order_position,active) VALUES (?,?,?,?,?,?,?,?)")
                   ->execute([$categoria_id, $name, $slug, $url ?: null, $target, $description, $order_position, $active]);
                $_SESSION['success'] = "Subcategoria <strong>$name</strong> criada com sucesso!";
                header('Location: ' . BASE_URL . '/admin/menus/index.php');
                exit;
            }
        } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
    }
    $preselect_cat_id = $categoria_id;
}

// Monta JSON para o JS
$cats_json = json_encode($cats_map);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.pg-wrap{padding:28px;max-width:820px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:8px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.breadcrumb{display:flex;align-items:center;gap:6px;margin-bottom:24px;padding:12px 16px;background:#f8f9fa;border-radius:10px;border:1px solid #e5e7eb;font-size:13px;flex-wrap:wrap}
.bc-item{display:flex;align-items:center;gap:5px}
.bc-item .label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-right:2px}
.bc-menu{color:#6b7280}.bc-menu .label{color:#9ca3af}
.bc-cat{color:#1d4ed8;font-weight:600}.bc-cat .label{color:#93c5fd}
.bc-sub{color:#059669;font-weight:700}.bc-sub .label{color:#6ee7b7}
.bc-sep{color:#d1d5db;font-size:16px;font-weight:300}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:30px}
.section-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin:0 0 16px;padding-bottom:8px;border-bottom:1px solid #f3f4f6}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:24px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg.full{grid-column:1/-1}
.fg label{font-size:13px;font-weight:600;color:#111827}
.req{color:#ef4444}
.fc{padding:11px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s,box-shadow .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:#4f6ef7;background:#fff;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
select.fc{cursor:pointer}
textarea.fc{resize:vertical;min-height:80px}
.hint{font-size:11px;color:#9ca3af}
.slug-preview{display:inline-flex;align-items:center;gap:4px;margin-top:4px;font-size:12px;color:#6b7280;background:#f3f4f6;border-radius:6px;padding:3px 9px;border:1px solid #e5e7eb;font-family:monospace}
.slug-preview b{color:#4f6ef7}
.tog-wrap{display:flex;align-items:center;gap:10px}
.tog{position:relative;width:44px;height:24px;flex-shrink:0}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:#10b981}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.tog-label{font-size:13px;font-weight:600;color:#111827}
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:#00071c;color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}.btn-ghost:hover{background:#e5e7eb}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid #ef4444;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.warn-box{background:#fffbeb;color:#92400e;border-left:4px solid #f59e0b;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
/* Optgroup */
select optgroup{font-weight:700;color:#374151}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/menus/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>🔖 Nova Subcategoria</h2>
    </div>

    <!-- BREADCRUMB DINÂMICO: Menu > Categoria > Subcategoria -->
    <div class="breadcrumb">
        <div class="bc-item bc-menu">
            <span class="label">📋 Menu</span>
            <span id="bc-menu-name">...</span>
        </div>
        <span class="bc-sep">›</span>
        <div class="bc-item bc-cat">
            <span class="label">🏷️ Categoria</span>
            <span id="bc-cat-name">...</span>
        </div>
        <span class="bc-sep">›</span>
        <div class="bc-item bc-sub">
            <span class="label">🔖 Subcategoria</span>
            <span id="bc-sub-name">Nova subcategoria...</span>
        </div>
    </div>

    <?php if (empty($categorias)): ?>
        <div class="warn-box">⚠️ Você precisa criar pelo menos uma categoria antes. <a href="<?= BASE_URL ?>/admin/categorias/criar.php" style="color:#92400e;font-weight:600">Criar Categoria agora →</a></div>
    <?php else: ?>

    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <p class="section-title">Localização</p>
        <div class="form-grid">
            <div class="fg full">
                <label>Categoria <span class="req">*</span></label>
                <select id="sel-cat" class="fc">
                    <option value="">— Selecione a categoria pai —</option>
                    <?php
                    // Agrupa por menu para exibir optgroup
                    $by_menu = [];
                    foreach ($categorias as $c) { $by_menu[$c['menu_name']][] = $c; }
                    foreach ($by_menu as $menu_name => $cats):
                    ?>
                    <optgroup label="📋 <?= htmlspecialchars($menu_name) ?>">
                        <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            data-cat="<?= htmlspecialchars($c['cat_name']) ?>"
                            data-menu="<?= htmlspecialchars($c['menu_name']) ?>"
                            <?= $preselect_cat_id == $c['id'] ? 'selected' : '' ?>>
                            🏷️ <?= htmlspecialchars($c['cat_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
                <span class="hint">A subcategoria ficará dentro desta categoria</span>
            </div>
        </div>

        <p class="section-title">Dados da Subcategoria</p>
        <form method="POST" id="form-sub">
            <input type="hidden" name="categoria_id" id="hid-cat">
            <div class="form-grid">
                <div class="fg">
                    <label>Nome <span class="req">*</span></label>
                    <input type="text" name="name" id="inp-name" class="fc" value="<?= htmlspecialchars($name) ?>" required placeholder="Ex: Nossa História">
                </div>
                <div class="fg">
                    <label>Slug (URL) <span class="req">*</span></label>
                    <input type="text" name="slug" id="inp-slug" class="fc" value="<?= htmlspecialchars($slug) ?>" required placeholder="nossa-historia">
                    <div class="slug-preview">🔗 /<b id="slug-val"><?= $slug ?: '...' ?></b></div>
                </div>
                <div class="fg">
                    <label>URL Externa</label>
                    <input type="url" name="url" class="fc" value="<?= htmlspecialchars($url) ?>" placeholder="https://...">
                    <span class="hint">Deixe vazio para vincular uma página interna</span>
                </div>
                <div class="fg">
                    <label>Abrir link em</label>
                    <select name="target" class="fc">
                        <option value="_self" <?= $target == '_self' ? 'selected' : '' ?>>📄 Mesma aba</option>
                        <option value="_blank" <?= $target == '_blank' ? 'selected' : '' ?>>🔗 Nova aba</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Ordem de exibição</label>
                    <input type="number" name="order_position" class="fc" min="0" value="<?= $order_position ?>">
                    <span class="hint">Menor número aparece primeiro</span>
                </div>
                <div class="fg full">
                    <label>Descrição</label>
                    <textarea name="description" class="fc" placeholder="Descrição opcional para SEO..."><?= htmlspecialchars($description) ?></textarea>
                </div>
                <div class="fg full">
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="active" <?= $active ? 'checked' : '' ?>><span class="tog-s"></span></label>
                        <span class="tog-label">Subcategoria ativa (visível no site)</span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">✅ Criar Subcategoria</button>
                <a href="<?= BASE_URL ?>/admin/menus/index.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>

    <?php endif; ?>
</div>
</main>

<script>
const selCat  = document.getElementById('sel-cat');
const hidCat  = document.getElementById('hid-cat');
const inpName = document.getElementById('inp-name');
const inpSlug = document.getElementById('inp-slug');
const slugVal = document.getElementById('slug-val');
const bcMenu  = document.getElementById('bc-menu-name');
const bcCat   = document.getElementById('bc-cat-name');
const bcSub   = document.getElementById('bc-sub-name');

function toSlug(s) {
    return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
}

function syncCat() {
    hidCat.value = selCat.value;
    const opt = selCat.options[selCat.selectedIndex];
    if (opt && selCat.value) {
        bcMenu.textContent = opt.dataset.menu;
        bcCat.textContent  = opt.dataset.cat;
    } else {
        bcMenu.textContent = '...';
        bcCat.textContent  = '...';
    }
}
selCat.addEventListener('change', syncCat);
syncCat(); // inicializa com pré-seleção

inpName.addEventListener('input', () => {
    const s = toSlug(inpName.value);
    inpSlug.value = s;
    slugVal.textContent = s || '...';
    bcSub.textContent = inpName.value || 'Nova subcategoria...';
});
inpSlug.addEventListener('input', () => { slugVal.textContent = inpSlug.value || '...'; });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
