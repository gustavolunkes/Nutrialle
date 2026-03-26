<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Editar Categoria de Produto';
$current_module = 'produtos';
$current_page   = 'cat-produtos-lista';
$error = '';
$db = getDB();

$cat_id = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM categorias_produtos WHERE id = ?");
$stmt->execute([$cat_id]);
$cat_produto = $stmt->fetch();
if (!$cat_produto) {
    $_SESSION['error'] = 'Categoria não encontrada.';
    header('Location: ' . BASE_URL . '/admin/categorias_produtos/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome             = trim($_POST['nome'] ?? '');
    $slug             = trim($_POST['slug'] ?? '');
    $descricao        = trim($_POST['descricao'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords    = trim($_POST['meta_keywords'] ?? '');
    $imagem           = trim($_POST['imagem'] ?? '');
    $ordem            = intval($_POST['ordem'] ?? 0);
    $ativo            = isset($_POST['ativo']) ? 1 : 0;

    if (!$nome) { $error = 'O nome é obrigatório.'; }
    elseif (!$slug) { $error = 'O slug é obrigatório.'; }
    else {
        $chk = $db->prepare("SELECT id FROM categorias_produtos WHERE slug = ? AND id != ?");
        $chk->execute([$slug, $cat_id]);
        if ($chk->fetch()) { $error = 'Slug já em uso.'; }
        else {
            $db->prepare("UPDATE categorias_produtos SET nome=?,slug=?,descricao=?,imagem=?,meta_description=?,meta_keywords=?,ordem=?,ativo=? WHERE id=?")
               ->execute([$nome, $slug, $descricao, $imagem ?: null, $meta_description, $meta_keywords, $ordem, $ativo, $cat_id]);
            $_SESSION['success'] = "Categoria <strong>$nome</strong> atualizada com sucesso!";
            header('Location: ' . BASE_URL . '/admin/categorias_produtos/index.php');
            exit;
        }
    }
    $cat_produto = array_merge($cat_produto, compact('nome','slug','descricao','imagem','meta_description','meta_keywords','ordem','ativo'));
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--muted:#6b7280}
.pg-wrap{padding:28px;max-width:840px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:24px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.meta-bar{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;padding:12px 16px;background:#fffbeb;border-radius:8px;border:1px solid #fde68a;font-size:12px;color:#92400e}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:28px;margin-bottom:18px}
.section-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin:0 0 14px;padding-bottom:8px;border-bottom:1px solid #f3f4f6}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg.full{grid-column:1/-1}
.fg label{font-size:13px;font-weight:600;color:#111827}
.req{color:var(--err)}
.fc{padding:11px 14px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
textarea.fc{resize:vertical;min-height:80px}
.hint{font-size:11px;color:var(--muted)}
.slug-preview{display:inline-flex;align-items:center;gap:4px;margin-top:4px;font-size:12px;color:var(--muted);background:#f3f4f6;border-radius:6px;padding:3px 10px;border:1px solid var(--border);font-family:monospace}
.slug-preview b{color:var(--accent)}
.tog-wrap{display:flex;align-items:center;gap:10px}
.tog{position:relative;width:44px;height:24px;flex-shrink:0}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:var(--ok)}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.tog-label{font-size:13px;font-weight:600;color:#111827}
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err);padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.upzone{border:2px dashed var(--border);border-radius:10px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;position:relative;transition:all .2s}
.upzone:hover{border-color:var(--accent);background:#eff6ff}
.upzone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.img-prev{max-width:100%;max-height:150px;border-radius:8px;border:2px solid var(--border);object-fit:cover;margin-top:10px;display:block}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/categorias_produtos/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>✏️ Editar Categoria de Produto</h2>
    </div>

    <div class="meta-bar">
        <span>🆔 ID: #<?= $cat_produto['id'] ?></span>
        <span>🔗 URL: /produtos/<?= htmlspecialchars($cat_produto['slug']) ?></span>
        <span>📅 Criado: <?= date('d/m/Y H:i', strtotime($cat_produto['created_at'])) ?></span>
        <span>🔄 Atualizado: <?= date('d/m/Y H:i', strtotime($cat_produto['updated_at'])) ?></span>
    </div>

    <?php if ($error): ?><div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <p class="section-title">Dados da Categoria</p>
        <form method="POST">
            <input type="hidden" name="imagem" id="img-val" value="<?= htmlspecialchars($cat_produto['imagem'] ?? '') ?>">
            <div class="form-grid">
                <div class="fg">
                    <label>Nome <span class="req">*</span></label>
                    <input type="text" name="nome" id="inp-nome" class="fc" value="<?= htmlspecialchars($cat_produto['nome']) ?>" required>
                </div>
                <div class="fg">
                    <label>Slug (URL) <span class="req">*</span></label>
                    <input type="text" name="slug" id="inp-slug" class="fc" value="<?= htmlspecialchars($cat_produto['slug']) ?>" required>
                    <div class="slug-preview">🔗 /produtos/<b id="slug-val"><?= htmlspecialchars($cat_produto['slug']) ?></b></div>
                    <span class="hint">Alterar o slug muda a URL da categoria</span>
                </div>
                <div class="fg full">
                    <label>Descrição</label>
                    <textarea name="descricao" class="fc"><?= htmlspecialchars($cat_produto['descricao'] ?? '') ?></textarea>
                </div>
                <div class="fg full">
                </div>
                <div class="fg">
                    <label>Ordem</label>
                    <input type="number" name="ordem" class="fc" min="0" value="<?= $cat_produto['ordem'] ?>">
                    <span class="hint">Menor número aparece primeiro</span>
                </div>
                <div class="fg">
                    <label>Meta Description</label>
                    <input type="text" name="meta_description" class="fc" value="<?= htmlspecialchars($cat_produto['meta_description'] ?? '') ?>" maxlength="160">
                </div>
                <div class="fg full">
                    <label>Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="fc" value="<?= htmlspecialchars($cat_produto['meta_keywords'] ?? '') ?>">
                </div>
                <div class="fg full">
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="ativo" <?= $cat_produto['ativo'] ? 'checked' : '' ?>><span class="tog-s"></span></label>
                        <span class="tog-label">Categoria ativa (visível no site)</span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">💾 Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/admin/categorias_produtos/index.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</main>
<script>
const UPLOAD_URL = '<?= BASE_URL ?>/admin/paginas/upload.php';
document.getElementById('inp-slug').addEventListener('input', function(){
    document.getElementById('slug-val').textContent = this.value || '...';
});
function uploadImg(e){
    const fd = new FormData(); fd.append('imagem', e.target.files[0]);
    fetch(UPLOAD_URL, {method:'POST', body:fd}).then(r=>r.json()).then(d=>{
        if(d.success){ document.getElementById('img-val').value = d.path;
        const p = document.getElementById('img-prev'); p.src = d.path; p.style.display = 'block'; }
        else alert('Erro no upload');
    });
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>