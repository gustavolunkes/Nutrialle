<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Editar Categoria do Blog';
$current_module = 'blog';
$current_page   = 'blog-categorias';

$error = '';
$db    = getDB();

$id   = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM blog_categorias WHERE id = ?");
$stmt->execute([$id]);
$cat  = $stmt->fetch();

if (!$cat) {
    $_SESSION['error'] = 'Categoria não encontrada.';
    header('Location: ' . BASE_URL . '/admin/blog/categorias/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']  ?? '');
    $slug  = trim($_POST['slug']  ?? '');
    $cor   = trim($_POST['cor']   ?? '#0057b7');
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if (!$nome) { $error = 'O nome é obrigatório.'; }
    elseif (!$slug) { $error = 'O slug é obrigatório.'; }
    else {
        try {
            $chk = $db->prepare("SELECT id FROM blog_categorias WHERE slug = ? AND id != ?");
            $chk->execute([$slug, $id]);
            if ($chk->fetch()) {
                $error = 'Este slug já está em uso.';
            } else {
                $db->prepare("UPDATE blog_categorias SET nome=?, slug=?, cor=?, ativo=? WHERE id=?")
                   ->execute([$nome, $slug, $cor, $ativo, $id]);
                $_SESSION['success'] = "Categoria <strong>$nome</strong> atualizada com sucesso!";
                header('Location: ' . BASE_URL . '/admin/blog/categorias/index.php');
                exit;
            }
        } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
    }
    $cat = array_merge($cat, compact('nome','slug','cor','ativo'));
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
.pg-wrap{padding:28px;max-width:820px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:8px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.meta-bar{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;padding:12px 16px;background:#fffbeb;border-radius:8px;border:1px solid #fde68a;font-size:12px;color:#92400e}
.meta-bar span{display:flex;align-items:center;gap:5px}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:30px}
.section-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin:0 0 16px;padding-bottom:8px;border-bottom:1px solid #f3f4f6}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:24px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg.full{grid-column:1/-1}
.fg label{font-size:13px;font-weight:600;color:#111827}
.req{color:#ef4444}
.fc{padding:11px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s,box-shadow .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:#4f6ef7;background:#fff;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
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
.color-row{display:flex;align-items:center;gap:12px}
.color-preview{width:40px;height:40px;border-radius:8px;border:2px solid #e5e7eb;flex-shrink:0}
input[type="color"].fc{padding:4px 8px;height:44px;cursor:pointer}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/blog/categorias/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>✏️ Editar Categoria do Blog</h2>
    </div>

    <div class="meta-bar">
        <span>🆔 ID: #<?= $cat['id'] ?></span>
        <span>📅 Criado em: <?= date('d/m/Y H:i', strtotime($cat['created_at'])) ?></span>
    </div>

    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <p class="section-title">Dados da Categoria</p>
        <form method="POST">
            <div class="form-grid">
                <div class="fg">
                    <label>Nome <span class="req">*</span></label>
                    <input type="text" name="nome" id="inp-nome" class="fc" value="<?= htmlspecialchars($cat['nome']) ?>" required placeholder="Ex: Nutrição Animal">
                </div>
                <div class="fg">
                    <label>Slug (URL) <span class="req">*</span></label>
                    <input type="text" name="slug" id="inp-slug" class="fc" value="<?= htmlspecialchars($cat['slug']) ?>" required placeholder="nutricao-animal">
                    <div class="slug-preview">🔗 blog/<b id="slug-val"><?= htmlspecialchars($cat['slug']) ?></b></div>
                </div>
                <div class="fg full">
                    <label>Cor da Categoria</label>
                    <div class="color-row">
                        <input type="color" name="cor" id="inp-cor" class="fc" value="<?= htmlspecialchars($cat['cor']) ?>" style="width:100px">
                        <div class="color-preview" id="color-preview" style="background:<?= htmlspecialchars($cat['cor']) ?>"></div>
                        <span class="hint">Cor usada para identificar a categoria nos cards do blog</span>
                    </div>
                </div>
                <div class="fg full">
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="ativo" <?= $cat['ativo'] ? 'checked' : '' ?>><span class="tog-s"></span></label>
                        <span class="tog-label">Categoria ativa (visível no blog)</span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">💾 Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/admin/blog/categorias/index.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</main>

<script>
const inpSlug = document.getElementById('inp-slug');
const slugVal = document.getElementById('slug-val');
const inpCor  = document.getElementById('inp-cor');
const preview = document.getElementById('color-preview');
inpSlug.addEventListener('input', () => { slugVal.textContent = inpSlug.value || '...'; });
inpCor.addEventListener('input', () => { preview.style.background = inpCor.value; });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
