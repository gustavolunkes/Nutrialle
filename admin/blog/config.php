<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Configurações do Blog';
$current_module = 'blog';
$current_page   = 'blog-config';

$error = '';
$db    = getDB();

$config = $db->query("SELECT * FROM blog_config WHERE id = 1")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_titulo    = trim($_POST['hero_titulo']    ?? '');
    $hero_subtitulo = trim($_POST['hero_subtitulo'] ?? '');
    $hero_cor_inicio = trim($_POST['hero_cor_inicio'] ?? '#003d85');
    $hero_cor_meio  = trim($_POST['hero_cor_meio']  ?? '#0057b7');
    $hero_cor_fim   = trim($_POST['hero_cor_fim']   ?? '#1a7fe0');
    $page_title_val = trim($_POST['page_title']     ?? '');
    $meta_desc      = trim($_POST['meta_description'] ?? '');
    $meta_kw        = trim($_POST['meta_keywords']    ?? '');

    if (!$hero_titulo) { $error = 'O título do hero é obrigatório.'; }
    else {
        try {
            $db->prepare("UPDATE blog_config SET hero_titulo=?, hero_subtitulo=?, hero_cor_inicio=?, hero_cor_meio=?, hero_cor_fim=?, page_title=?, meta_description=?, meta_keywords=? WHERE id=1")
               ->execute([$hero_titulo, $hero_subtitulo, $hero_cor_inicio, $hero_cor_meio, $hero_cor_fim, $page_title_val, $meta_desc, $meta_kw]);
            $_SESSION['success'] = 'Configurações do blog atualizadas com sucesso!';
            header('Location: ' . BASE_URL . '/admin/blog/config.php');
            exit;
        } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<style>
.pg-wrap{padding:28px;max-width:820px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:24px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:30px;margin-bottom:20px}
.section-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin:0 0 16px;padding-bottom:8px;border-bottom:1px solid #f3f4f6}
.fg{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
.fg label{font-size:13px;font-weight:600;color:#111827}
.fc{padding:11px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s,box-shadow .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:#4f6ef7;background:#fff;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
textarea.fc{resize:vertical}
.hint{font-size:11px;color:#9ca3af}
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:#00071c;color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}.btn-ghost:hover{background:#e5e7eb}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid #ef4444;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-success{background:#d4edda;color:#155724;border-left:4px solid #28a745;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.char-count{font-size:11px;text-align:right;color:#9ca3af;margin-top:2px}
.char-count.warn{color:#f59e0b}
.char-count.over{color:#ef4444}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <h2>⚙️ Configurações do Blog</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <p class="section-title">Hero / Cabeçalho do Blog</p>
        <form method="POST">
            <div class="fg">
                <label>Título do Hero</label>
                <input type="text" name="hero_titulo" class="fc" value="<?= htmlspecialchars($config['hero_titulo'] ?? '') ?>" placeholder="Blog Nutrialle">
            </div>
            <div class="fg">
                <label>Subtítulo do Hero</label>
                <textarea name="hero_subtitulo" class="fc" rows="2" placeholder="Artigos, dicas e novidades..."><?= htmlspecialchars($config['hero_subtitulo'] ?? '') ?></textarea>
            </div>
            <div class="fg">
                <label>Cor Inicial do Gradiente</label>
                <input type="color" name="hero_cor_inicio" class="fc" value="<?= htmlspecialchars($config['hero_cor_inicio'] ?? '#003d85') ?>" style="height:50px;">
                <span class="hint">Cor do início do gradiente do hero</span>
            </div>
            <div class="fg">
                <label>Cor do Meio do Gradiente</label>
                <input type="color" name="hero_cor_meio" class="fc" value="<?= htmlspecialchars($config['hero_cor_meio'] ?? '#0057b7') ?>" style="height:50px;">
                <span class="hint">Cor do meio do gradiente do hero</span>
            </div>
            <div class="fg">
                <label>Cor Final do Gradiente</label>
                <input type="color" name="hero_cor_fim" class="fc" value="<?= htmlspecialchars($config['hero_cor_fim'] ?? '#1a7fe0') ?>" style="height:50px;">
                <span class="hint">Cor do fim do gradiente do hero</span>
            </div>
            <div class="fg">
                <label>Título da Página (aba do navegador)</label>
                <input type="text" name="page_title" class="fc" value="<?= htmlspecialchars($config['page_title'] ?? '') ?>" placeholder="Blog">
            </div>

            <p class="section-title" style="margin-top:24px">SEO da Página de Blog</p>
            <div class="fg">
                <label>Meta Description</label>
                <textarea name="meta_description" class="fc" rows="2" id="inp-metadesc" placeholder="Descrição para mecanismos de busca (até 160 caracteres)"><?= htmlspecialchars($config['meta_description'] ?? '') ?></textarea>
                <div class="char-count" id="cc-metadesc">0 / 160</div>
            </div>
            <div class="fg">
                <label>Meta Keywords</label>
                <input type="text" name="meta_keywords" class="fc" value="<?= htmlspecialchars($config['meta_keywords'] ?? '') ?>" placeholder="blog, nutrição animal, silagem">
                <span class="hint">Separe com vírgulas</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-dark">💾 Salvar Configurações</button>
            </div>
        </form>
    </div>
</div>
</main>

<script>
const inpMeta = document.getElementById('inp-metadesc');
const ccMeta  = document.getElementById('cc-metadesc');
function updateCharCount(el, counter, max) {
    const len = el.value.length;
    counter.textContent = len + ' / ' + max;
    counter.className = 'char-count' + (len > max ? ' over' : len > max * 0.85 ? ' warn' : '');
}
inpMeta.addEventListener('input', () => updateCharCount(inpMeta, ccMeta, 160));
updateCharCount(inpMeta, ccMeta, 160);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
