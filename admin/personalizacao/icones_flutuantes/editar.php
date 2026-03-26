<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Editar Ícone Flutuante';
$current_module = 'personalizacao';
$current_page   = 'icones-flutuantes';

$db    = getDB();
$id    = (int)($_GET['id'] ?? 0);
$icone = $db->prepare("SELECT * FROM icones_flutuantes WHERE id = ?")->execute([$id]) ? null : null;
$stmt  = $db->prepare("SELECT * FROM icones_flutuantes WHERE id = ?");
$stmt->execute([$id]);
$icone = $stmt->fetch();

if (!$icone) {
    $_SESSION['success'] = 'Ícone não encontrado.';
    header('Location: ' . BASE_URL . '/admin/personalizacao/icones_flutuantes/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'label'     => trim($_POST['label']     ?? ''),
        'icone'     => trim($_POST['icone']      ?? ''),
        'url'       => trim($_POST['url']        ?? ''),
        'target'    => in_array($_POST['target'] ?? '', ['_self','_blank']) ? $_POST['target'] : '_blank',
        'cor_fundo' => trim($_POST['cor_fundo']  ?? '#25d366'),
        'cor_icone' => trim($_POST['cor_icone']  ?? '#ffffff'),
        'posicao'   => in_array($_POST['posicao'] ?? '', ['bottom-right','bottom-left','top-right','top-left'])
                       ? $_POST['posicao'] : 'bottom-right',
        'ordem'     => (int)($_POST['ordem'] ?? 0),
        'ativo'     => isset($_POST['ativo']) ? 1 : 0,
        'id'        => $id,
    ];

    if (!$fields['label']) $error = 'O label é obrigatório.';
    elseif (!$fields['icone']) $error = 'O ícone é obrigatório.';
    elseif (!filter_var($fields['url'], FILTER_VALIDATE_URL)) $error = 'URL inválida.';
    elseif (!preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $fields['cor_fundo'])) $error = 'Cor de fundo inválida.';
    elseif (!preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $fields['cor_icone'])) $error = 'Cor do ícone inválida.';

    if (!$error) {
        try {
            $db->prepare("
                UPDATE icones_flutuantes
                SET label=:label, icone=:icone, url=:url, target=:target,
                    cor_fundo=:cor_fundo, cor_icone=:cor_icone,
                    posicao=:posicao, ordem=:ordem, ativo=:ativo
                WHERE id=:id
            ")->execute($fields);
            $_SESSION['success'] = 'Ícone atualizado com sucesso!';
            header('Location: ' . BASE_URL . '/admin/personalizacao/icones_flutuantes/index.php');
            exit;
        } catch (Exception $e) {
            $error = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

$v = [
    'label'     => $_POST['label']     ?? $icone['label'],
    'icone'     => $_POST['icone']     ?? $icone['icone'],
    'url'       => $_POST['url']       ?? $icone['url'],
    'target'    => $_POST['target']    ?? $icone['target'],
    'cor_fundo' => $_POST['cor_fundo'] ?? $icone['cor_fundo'],
    'cor_icone' => $_POST['cor_icone'] ?? $icone['cor_icone'],
    'posicao'   => $_POST['posicao']   ?? $icone['posicao'],
    'ordem'     => $_POST['ordem']     ?? $icone['ordem'],
    'ativo'     => isset($_POST['ativo']) ? (int)$_POST['ativo'] : (int)$icone['ativo'],
];

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
function hce($s){ return htmlspecialchars($s, ENT_QUOTES,'UTF-8'); }
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 16px;border:none;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:20px}
.card-hd{padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand);margin:0}
.card-body{padding:24px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{display:flex;flex-direction:column;gap:5px}
.fg label{font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px}
.fc{padding:9px 12px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-s)}
.color-row{display:flex;align-items:center;gap:8px}
.color-row input[type=color]{width:40px;height:40px;border:2px solid var(--border);border-radius:8px;cursor:pointer;padding:2px;background:#fafafa;flex-shrink:0}
.color-row input[type=text]{flex:1}
.form-actions{display:flex;gap:10px;margin-top:4px;padding-top:16px}
.alert-err{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.toggle-wrap{display:flex;align-items:center;gap:10px}
.toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.slider{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:.3s}
.slider::before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.toggle input:checked + .slider{background:var(--ok)}
.toggle input:checked + .slider::before{transform:translateX(20px)}
.preview-ball{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 4px 14px rgba(0,0,0,.25);flex-shrink:0}
.preview-section{display:flex;align-items:center;gap:16px;padding:20px;background:#f8fafc;border-radius:10px}
.preview-label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px}
.hint{font-size:11px;color:var(--muted);margin-top:2px}
.emoji-grid{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
.emoji-btn{font-size:20px;padding:4px 6px;border:2px solid transparent;border-radius:6px;cursor:pointer;background:#f3f4f6;transition:.15s}
.emoji-btn:hover,.emoji-btn.sel{border-color:var(--accent);background:#e0e7ff}
@media(max-width:700px){.form-grid{grid-template-columns:1fr}}
</style>

<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h2>✏️ Editar Ícone Flutuante</h2>
        <a href="<?= BASE_URL ?>/admin/personalizacao/icones_flutuantes/index.php" class="btn btn-ghost">← Voltar</a>
    </div>

    <?php if ($error): ?><div class="alert-err">⚠️ <?= hce($error) ?></div><?php endif; ?>

    <form method="POST">

        <div class="card">
            <div class="card-hd"><h3>👁️ Preview</h3></div>
            <div class="card-body">
                <div class="preview-section">
                    <div class="preview-ball" id="previewBall"
                         style="background:<?= hce($v['cor_fundo']) ?>;color:<?= hce($v['cor_icone']) ?>">
                        <span id="previewIcone"><?= hce($v['icone']) ?></span>
                    </div>
                    <div>
                        <div class="preview-label">Pré-visualização</div>
                        <div style="font-size:13px;color:var(--muted)">Aparência do botão flutuante no site</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-hd"><h3>📝 Informações</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="fg">
                        <label>Label / Tooltip *</label>
                        <input type="text" name="label" class="fc" required value="<?= hce($v['label']) ?>">
                    </div>
                    <div class="fg">
                        <label>URL de Destino *</label>
                        <input type="url" name="url" class="fc" required value="<?= hce($v['url']) ?>">
                    </div>
                    <div class="fg">
                        <label>Abrir em</label>
                        <select name="target" class="fc">
                            <option value="_blank" <?= $v['target']==='_blank'?'selected':'' ?>>Nova aba (_blank)</option>
                            <option value="_self"  <?= $v['target']==='_self' ?'selected':'' ?>>Mesma aba (_self)</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Posição na tela</label>
                        <select name="posicao" class="fc">
                            <?php foreach (['bottom-right'=>'Inferior Direito','bottom-left'=>'Inferior Esquerdo','top-right'=>'Superior Direito','top-left'=>'Superior Esquerdo'] as $k=>$lbl): ?>
                                <option value="<?= $k ?>" <?= $v['posicao']===$k?'selected':'' ?>><?= $lbl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Ordem</label>
                        <input type="number" name="ordem" class="fc" min="0" value="<?= (int)$v['ordem'] ?>">
                    </div>
                    <div class="fg" style="justify-content:flex-end">
                        <label>Status</label>
                        <div class="toggle-wrap" style="margin-top:6px">
                            <label class="toggle">
                                <input type="checkbox" name="ativo" value="1" <?= $v['ativo']?'checked':'' ?>>
                                <span class="slider"></span>
                            </label>
                            <span style="font-size:13px;color:var(--text)">Ativo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-hd"><h3>😀 Ícone</h3></div>
            <div class="card-body">
                <div class="fg">
                    <label>Emoji / Caractere *</label>
                    <input type="text" name="icone" id="inputIcone" class="fc" required
                           value="<?= hce($v['icone']) ?>" maxlength="10"
                           oninput="document.getElementById('previewIcone').textContent=this.value">
                    <span class="hint">Sugestões:</span>
                    <div class="emoji-grid">
                        <?php foreach (['💬','📞','📧','📍','🔗','💡','📲','🎧','🛒','❓','⭐','🔔','📣','💼'] as $e): ?>
                            <button type="button" class="emoji-btn <?= $v['icone']===$e?'sel':'' ?>"
                                    onclick="pickEmoji('<?= $e ?>')"><?= $e ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-hd"><h3>🎨 Cores</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="fg">
                        <label>Cor de Fundo</label>
                        <div class="color-row">
                            <input type="color" id="clr_cor_fundo" value="<?= hce($v['cor_fundo']) ?>"
                                   oninput="syncClr(this,'cor_fundo')">
                            <input type="text" id="txt_cor_fundo" name="cor_fundo" class="fc"
                                   value="<?= hce($v['cor_fundo']) ?>" maxlength="7"
                                   oninput="syncTxt(this,'cor_fundo')">
                        </div>
                    </div>
                    <div class="fg">
                        <label>Cor do Ícone</label>
                        <div class="color-row">
                            <input type="color" id="clr_cor_icone" value="<?= hce($v['cor_icone']) ?>"
                                   oninput="syncClr(this,'cor_icone')">
                            <input type="text" id="txt_cor_icone" name="cor_icone" class="fc"
                                   value="<?= hce($v['cor_icone']) ?>" maxlength="7"
                                   oninput="syncTxt(this,'cor_icone')">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-dark">💾 Salvar Alterações</button>
            <a href="<?= BASE_URL ?>/admin/personalizacao/icones_flutuantes/index.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script>
function syncClr(picker, campo) {
    var v = picker.value;
    document.getElementById('txt_' + campo).value = v;
    applyColor(campo, v);
}
function syncTxt(input, campo) {
    var v = input.value.trim();
    if (/^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/.test(v)) v = '#' + v;
    if (/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/.test(v)) {
        var n = v.length === 4 ? '#'+v[1]+v[1]+v[2]+v[2]+v[3]+v[3] : v;
        document.getElementById('clr_' + campo).value = n;
        applyColor(campo, v);
    }
}
function applyColor(campo, v) {
    var ball = document.getElementById('previewBall');
    if (campo === 'cor_fundo') ball.style.background = v;
    if (campo === 'cor_icone') ball.style.color = v;
}
function pickEmoji(e) {
    document.getElementById('inputIcone').value = e;
    document.getElementById('previewIcone').textContent = e;
    document.querySelectorAll('.emoji-btn').forEach(function(b){ b.classList.remove('sel'); });
    event.target.classList.add('sel');
}
</script>
