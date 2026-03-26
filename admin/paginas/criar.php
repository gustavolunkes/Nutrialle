<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Nova Página';
$current_module = 'paginas';
$current_page   = 'paginas-criar';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo           = trim($_POST['titulo'] ?? '');
    $slug             = trim($_POST['slug'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords    = trim($_POST['meta_keywords'] ?? '');
    $ativo            = isset($_POST['ativo']) ? 1 : 0;

    if (empty($titulo)) { $error = 'O título é obrigatório.'; }
    elseif (empty($slug)) { $error = 'O slug é obrigatório.'; }
    else {
        try {
            $db  = getDB();
            $chk = $db->prepare("SELECT id FROM paginas WHERE slug = ?");
            $chk->execute([$slug]);
            if ($chk->fetch()) {
                $error = 'Este slug já está em uso.';
            } else {
                $db->prepare("INSERT INTO paginas (titulo, slug, meta_description, meta_keywords, ativo) VALUES (?,?,?,?,?)")
                   ->execute([$titulo, $slug, $meta_description, $meta_keywords, $ativo]);
                $_SESSION['success'] = 'Página criada com sucesso!';
                header('Location: ' . BASE_URL . '/admin/paginas/index.php');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Erro: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
.pg-wrap{padding:28px;max-width:800px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:24px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:30px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg.full{grid-column:1/-1}
.fg label{font-size:13px;font-weight:600;color:#111827}
.req{color:#ef4444}
.fc{padding:11px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s}
.fc:focus{outline:none;border-color:#4f6ef7;background:#fff;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
textarea.fc{resize:vertical;min-height:90px}
.hint{font-size:11px;color:#6b7280}
.slug-preview{display:inline-flex;align-items:center;gap:5px;margin-top:5px;font-size:12px;color:#6b7280;background:#f3f4f6;border-radius:6px;padding:4px 10px;border:1px solid #e5e7eb}
.slug-preview b{color:#4f6ef7}
/* toggle */
.tog-wrap{display:flex;align-items:center;gap:10px}
.tog{position:relative;width:44px;height:24px}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:#10b981}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.tog-label{font-size:13px;font-weight:600;color:#111827}
/* actions */
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:#00071c;color:#fff}
.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}
.btn-ghost:hover{background:#e5e7eb}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid #ef4444;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/paginas/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>📄 Nova Página</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-grid">
                <div class="fg">
                    <label>Título <span class="req">*</span></label>
                    <input type="text" name="titulo" class="fc" id="titulo" value="<?= htmlspecialchars($titulo ?? '') ?>" required placeholder="Ex: Sobre Nós">
                    <span class="hint">Exibido no site e na aba do navegador</span>
                </div>
                <div class="fg">
                    <label>Slug (URL) <span class="req">*</span></label>
                    <input type="text" name="slug" class="fc" id="slug" value="<?= htmlspecialchars($slug ?? '') ?>" required placeholder="sobre-nos">
                    <div class="slug-preview">🔗 <?= rtrim(BASE_URL, '/') ?>/<b id="slug-val"><?= htmlspecialchars($slug ?? '') ?: '...' ?></b></div>
                </div>
                <div class="fg full">
                    <label>Meta Description <small style="font-weight:400;color:#6b7280">(SEO)</small></label>
                    <textarea name="meta_description" class="fc" maxlength="160" placeholder="Descrição para o Google (máx. 160 caracteres)"><?= htmlspecialchars($meta_description ?? '') ?></textarea>
                </div>
                <div class="fg full">
                    <label>Meta Keywords <small style="font-weight:400;color:#6b7280">(SEO)</small></label>
                    <input type="text" name="meta_keywords" class="fc" value="<?= htmlspecialchars($meta_keywords ?? '') ?>" placeholder="palavra1, palavra2, palavra3">
                </div>
                <div class="fg full">
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="ativo" checked><span class="tog-s"></span></label>
                        <span class="tog-label">Página ativa (visível no site)</span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">✅ Criar Página</button>
                <a href="<?= BASE_URL ?>/admin/paginas/index.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</main>

<script>
const tituloEl = document.getElementById('titulo');
const slugEl   = document.getElementById('slug');
const slugVal  = document.getElementById('slug-val');

function toSlug(s) {
    return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
}
tituloEl.addEventListener('input', () => {
    const s = toSlug(tituloEl.value);
    slugEl.value = s;
    slugVal.textContent = s || '...';
});
slugEl.addEventListener('input', () => { slugVal.textContent = slugEl.value || '...'; });
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>