<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Editar Rede Social';
$current_module = 'rodape';
$current_page   = 'rodape-rede-editar';

$db   = getDB();
$id   = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM rodape_redes_sociais WHERE id=?");
$stmt->execute([$id]);
$rede = $stmt->fetch();

if (!$rede) {
    $_SESSION['error'] = 'Rede social não encontrada.';
    header('Location: ' . BASE_URL . '/admin/rodape/index.php');
    exit;
}

$redesOpcoes = ['facebook','instagram','x','linkedin','youtube','tiktok','pinterest','whatsapp','telegram','github'];
$redesIcons  = ['facebook'=>'📘','instagram'=>'📸','x'=>'🐦','linkedin'=>'💼','youtube'=>'▶️','tiktok'=>'🎵','pinterest'=>'📌','whatsapp'=>'💬','telegram'=>'✈️','github'=>'🐙'];

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redesNome = trim($_POST['rede'] ?? '');
    $url       = trim($_POST['url'] ?? '');
    $ordem     = (int)($_POST['ordem'] ?? 0);
    $ativo     = isset($_POST['ativo']) ? 1 : 0;

    if (!$redesNome || !$url) { $error = 'Rede e URL são obrigatórios.'; }
    else {
        try {
            $db->prepare("UPDATE rodape_redes_sociais SET rede=?, url=?, ordem=?, ativo=? WHERE id=?")
               ->execute([$redesNome, $url, $ordem, $ativo, $id]);
            $success = 'Rede social atualizada!';
            $stmt->execute([$id]);
            $rede = $stmt->fetch();
        } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--bg:#f4f6fb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px;max-width:620px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:24px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);padding:28px}
.card-title{font-size:15px;font-weight:700;color:var(--brand);margin-bottom:22px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg.full{grid-column:1/-1}
.fg label{font-size:13px;font-weight:600;color:var(--text)}
.req{color:var(--err)}
.fc{padding:11px 14px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-s)}
.hint{font-size:11px;color:var(--muted)}
.rede-picker{display:grid;grid-template-columns:repeat(5,1fr);gap:8px}
.rede-opt{border:2px solid var(--border);border-radius:10px;padding:10px 6px;cursor:pointer;text-align:center;transition:all .18s;background:#fafafa;position:relative}
.rede-opt:hover{border-color:var(--accent);background:var(--accent-s)}
.rede-opt.sel{border-color:var(--accent);background:var(--accent-s)}
.rede-opt input{display:none}
.rede-icon{font-size:22px;margin-bottom:3px}
.rede-name{font-size:11px;font-weight:700;color:var(--text)}
.tog-wrap{display:flex;align-items:center;gap:10px}
.tog{position:relative;width:44px;height:24px}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:var(--ok)}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
</style>
<main class="content">
<div class="wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>📱 Editar Rede Social</h2>
    </div>
    <?php if ($error): ?><div class="alert alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-ok">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>
    <div class="card">
        <div class="card-title">Editando: <?= ucfirst($rede['rede']) ?> #<?= $rede['id'] ?></div>
        <form method="POST">
            <div class="form-grid">
                <div class="fg full">
                    <label>Rede Social <span class="req">*</span></label>
                    <div class="rede-picker">
                        <?php foreach ($redesOpcoes as $r): $sel = ($rede['rede'] === $r); ?>
                        <label class="rede-opt <?= $sel ? 'sel' : '' ?>" onclick="selRede(this)">
                            <input type="radio" name="rede" value="<?= $r ?>" <?= $sel ? 'checked' : '' ?> required>
                            <div class="rede-icon"><?= $redesIcons[$r] ?? '🔗' ?></div>
                            <div class="rede-name"><?= ucfirst($r) ?></div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="fg full">
                    <label>URL do Perfil <span class="req">*</span></label>
                    <input type="url" name="url" class="fc" required value="<?= htmlspecialchars($rede['url']) ?>" placeholder="https://...">
                    <span class="hint">URL completa com https://</span>
                </div>
                <div class="fg">
                    <label>Ordem</label>
                    <input type="number" name="ordem" class="fc" min="0" value="<?= (int)$rede['ordem'] ?>">
                    <span class="hint">Menor = aparece primeiro</span>
                </div>
                <div class="fg" style="justify-content:flex-end;padding-bottom:5px">
                    <label>&nbsp;</label>
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="ativo" <?= $rede['ativo'] ? 'checked' : '' ?>><span class="tog-s"></span></label>
                        <span style="font-size:13px;font-weight:600">Ativa (visível no rodapé)</span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">💾 Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</main>
<script>
function selRede(el) {
    document.querySelectorAll('.rede-opt').forEach(o => o.classList.remove('sel'));
    el.classList.add('sel');
    el.querySelector('input').checked = true;
}
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
