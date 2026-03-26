<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Editar Menu Superior';
$current_module = 'personalizacao';
$current_page   = 'config-header';

$db     = getDB();
$config = $db->query("SELECT * FROM header_config LIMIT 1")->fetch();
$error  = '';

define('LOGO_UPLOAD_DIR', __DIR__ . '/../../../assets/uploads/logo_header/');
define('LOGO_UPLOAD_URL', BASE_URL . '/assets/uploads/logo_header/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'nome_empresa'     => trim($_POST['nome_empresa']     ?? ''),
        'cor_fundo'        => trim($_POST['cor_fundo']        ?? '#00071c'),
        'cor_texto'        => trim($_POST['cor_texto']        ?? '#ffffff'),
        'cor_borda'        => trim($_POST['cor_borda']        ?? '#1e2a3a'),
        'cor_nav_link'     => trim($_POST['cor_nav_link']     ?? '#ffffff'),
        'mob_cor_fundo'       => trim($_POST['mob_cor_fundo']       ?? '#111827'),
        'mob_cor_texto'       => trim($_POST['mob_cor_texto']       ?? '#ffffff'),
        'mob_cor_borda'       => trim($_POST['mob_cor_borda']       ?? '#1e2a3a'),
        'mob_cor_nav_link'    => trim($_POST['mob_cor_nav_link']    ?? '#ffffff'),
        'mob_cor_menu_aberto' => trim($_POST['mob_cor_menu_aberto'] ?? '#1a2235'),
    ];

    // Valida e normaliza cores: aceita #RGB e #RRGGBB
    foreach ($fields as $k => $val) {
        if (str_starts_with($k, 'cor_') || str_starts_with($k, 'mob_cor_')) {
            if (!preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $val)) {
                $error = "Cor inválida no campo «{$k}»: {$val}";
                break;
            }
        }
    }

    $logo_url = $config['logo_url'] ?? null;

    if (!$error) {
        if (!empty($_POST['remover_logo']) && $logo_url) {
            $arq = LOGO_UPLOAD_DIR . basename($logo_url);
            if (file_exists($arq)) unlink($arq);
            $logo_url = null;
        }

        if (!empty($_FILES['logo_file']['name'])) {
            $file = $_FILES['logo_file'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp','svg'])) {
                $error = 'Formato inválido. Use: JPG, PNG, GIF, WEBP ou SVG.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'Arquivo muito grande. Máximo: 2MB.';
            } elseif ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Erro no upload. Código: ' . $file['error'];
            } else {
                if (!is_dir(LOGO_UPLOAD_DIR)) mkdir(LOGO_UPLOAD_DIR, 0755, true);
                if ($logo_url) { $a = LOGO_UPLOAD_DIR . basename($logo_url); if (file_exists($a)) unlink($a); }
                $nome_arq = 'logo_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], LOGO_UPLOAD_DIR . $nome_arq)) {
                    $logo_url = LOGO_UPLOAD_URL . $nome_arq;
                } else {
                    $error = 'Falha ao mover o arquivo. Verifique as permissões.';
                }
            }
        }
    }

    if (!$fields['nome_empresa']) $error = $error ?: 'O nome da empresa é obrigatório.';

    if (!$error) {
        try {
            $fields['logo_url'] = $logo_url;
            if ($config) {
                $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($fields)));
                $fields['id'] = $config['id'];
                $db->prepare("UPDATE header_config SET $set WHERE id = :id")->execute($fields);
            } else {
                $cols = implode(', ', array_keys($fields));
                $vals = ':' . implode(', :', array_keys($fields));
                $db->prepare("INSERT INTO header_config ($cols) VALUES ($vals)")->execute($fields);
            }
            $_SESSION['success'] = 'Configurações salvas com sucesso!';
            header('Location: ' . BASE_URL . '/admin/personalizacao/header/index.php');
            exit;
        } catch (Exception $e) {
            $error = 'Erro ao salvar: ' . $e->getMessage();
        }
    }

    $config = $db->query("SELECT * FROM header_config LIMIT 1")->fetch();
}

$defaults = [
    'nome_empresa'     => '',
    'cor_fundo'        => '#00071c', 'cor_texto'        => '#ffffff',
    'cor_borda'        => '#1e2a3a', 'cor_nav_link'     => '#ffffff',
    'mob_cor_fundo'       => '#111827', 'mob_cor_texto'       => '#ffffff',
    'mob_cor_borda'       => '#1e2a3a', 'mob_cor_nav_link'    => '#ffffff',
    'mob_cor_menu_aberto' => '#1a2235',
];
$v = ['logo_url' => $config['logo_url'] ?? ''];
foreach ($defaults as $k => $d) $v[$k] = $_POST[$k] ?? $config[$k] ?? $d;

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';

function hce($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

/**
 * Renderiza um campo de cor com picker + input texto sincronizados.
 * O input[type=color] fica ANTES do input[type=text] no DOM.
 */
function colorField(string $campo, string $label, array $v): void {
    $val = hce($v[$campo]);
    echo '<div class="fg">';
    echo '<label>' . $label . '</label>';
    echo '<div class="color-row">';
    echo '<input type="color" id="clr_' . $campo . '" value="' . $val . '" oninput="syncColorPicker(this,\'' . $campo . '\')">';
    echo '<input type="text" id="txt_' . $campo . '" name="' . $campo . '" class="fc" value="' . $val . '" maxlength="7" placeholder="#000000" oninput="syncColorText(this,\'' . $campo . '\')">';
    echo '</div></div>';
}
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:20px}
.card-hd{padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand);margin:0}
.card-body{padding:24px}
/* Tabs de contexto (form) */
.ctx-tabs{display:flex;border-radius:8px 8px 0 0;overflow:hidden;border:1px solid var(--border);border-bottom:none}
.ctx-tab{flex:1;padding:10px;text-align:center;font-size:13px;font-weight:600;cursor:pointer;background:#f3f4f6;color:var(--muted);border:none;transition:background .2s}
.ctx-tab.active{background:#fff;color:var(--brand)}
.ctx-panel{display:none;border:1px solid var(--border);border-radius:0 0 10px 10px;padding:20px;margin-bottom:20px}
.ctx-panel.active{display:block}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{display:flex;flex-direction:column;gap:5px}
.fg label{font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px}
.fc{padding:9px 12px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-s)}
.color-row{display:flex;align-items:center;gap:8px}
.color-row input[type=color]{width:40px;height:40px;border:2px solid var(--border);border-radius:8px;cursor:pointer;padding:2px;background:#fafafa;flex-shrink:0}
.color-row input[type=text]{flex:1}
.form-actions{display:flex;gap:10px;margin-top:4px;padding-top:16px}
.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 16px;border:none;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.alert-err{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
/* Preview */
.pv-tabs{display:flex;border-radius:8px 8px 0 0;overflow:hidden;border:1px solid var(--border);border-bottom:none}
.pvtab{flex:1;padding:9px;text-align:center;font-size:12px;font-weight:600;cursor:pointer;background:#f3f4f6;color:var(--muted);border:none;transition:background .2s}
.pvtab.active{background:#fff;color:var(--brand)}
.pv-panel{display:none;border:1px solid var(--border);border-radius:0 0 10px 10px;overflow:hidden;margin-bottom:20px}
.pv-panel.active{display:block}
.pv-bar{display:flex;align-items:center;justify-content:space-between;padding:14px 28px;gap:20px}
.pv-nav{display:flex;gap:20px}
.pv-nav span{font-size:13px;font-weight:500}
.pv-mobile{max-width:280px;margin:0 auto}
.pv-mob-bar{display:flex;align-items:center;justify-content:space-between;padding:13px 16px}
.pv-mob-lines{display:flex;flex-direction:column;gap:5px}
.pv-mob-line{width:20px;height:3px;border-radius:3px}
.pv-mob-menu{display:flex;flex-direction:column}
.pv-mob-item{padding:14px 20px;font-weight:500;font-size:14px;border-bottom:1px solid rgba(0,0,0,.06)}
/* Logo */
.logo-atual{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.remover-logo{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--err);cursor:pointer;font-weight:600}
.fc-file{padding:7px 10px;cursor:pointer}
.fc-file::file-selector-button{padding:5px 12px;border:none;border-radius:6px;background:var(--brand);color:#fff;font-size:12px;font-weight:600;cursor:pointer;margin-right:10px}
.hint{font-size:11px;color:var(--muted);margin-top:2px}
@media(max-width:700px){.form-grid{grid-template-columns:1fr}}
</style>

<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h2>✏️ Editar Menu Superior</h2>
        <a href="<?= BASE_URL ?>/admin/personalizacao/header/index.php" class="btn btn-ghost">← Voltar</a>
    </div>

    <?php if ($error): ?><div class="alert-err">⚠️ <?= hce($error) ?></div><?php endif; ?>


    <!-- FORMULÁRIO -->
    <form method="POST" enctype="multipart/form-data">

        <div class="card">
            <div class="card-hd"><h3>🏢 Identidade</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="fg">
                        <label>Nome da Empresa</label>
                        <input type="text" name="nome_empresa" class="fc" required
                               value="<?= hce($v['nome_empresa']) ?>" id="inputNome">
                        <span class="hint">Exibido quando não há logo.</span>
                    </div>
                    <div class="fg">
                        <label>Logo</label>
                        <?php if (!empty($v['logo_url'])): ?>
                            <div class="logo-atual">
                                <img src="<?= hce($v['logo_url']) ?>" id="thumbLogo"
                                     style="max-height:40px;border-radius:6px;border:1px solid var(--border);padding:4px">
                                <label class="remover-logo">
                                    <input type="checkbox" name="remover_logo" value="1" id="chkRemover">
                                    Remover logo
                                </label>
                            </div>
                        <?php else: ?>
                            <img id="thumbLogo" src="" style="display:none;max-height:40px;border-radius:6px;border:1px solid var(--border);padding:4px;margin-bottom:6px">
                        <?php endif; ?>
                        <input type="file" name="logo_file" class="fc fc-file" id="inputLogo"
                               accept=".jpg,.jpeg,.png,.gif,.webp,.svg">
                        <span class="hint">JPG, PNG, WEBP, SVG ou GIF — máx. 2MB.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Desktop / Mobile para cores -->
        <div class="ctx-tabs">
            <button type="button" class="ctx-tab active" onclick="switchCtxTab('dsk',this)">🖥️ Cores Desktop</button>
            <button type="button" class="ctx-tab" onclick="switchCtxTab('mob',this)">📱 Cores Mobile</button>
        </div>

        <div class="ctx-panel active" id="ctx-dsk">
            <div class="form-grid">
                <?php colorField('cor_fundo',    'Fundo do Header', $v); ?>
                <?php colorField('cor_texto',    'Logo / Texto',    $v); ?>
                <?php colorField('cor_borda',    'Linha de Borda',  $v); ?>
                <?php colorField('cor_nav_link', 'Links do Menu',   $v); ?>
            </div>
        </div>
            <div class="pv-panel active" id="pvp-desk">
        <div id="pvBar" class="pv-bar" style="background:<?= $v['cor_fundo'] ?>;border-bottom:2px solid <?= $v['cor_borda'] ?>">
            <div id="pvLogo" style="font-size:17px;font-weight:800;color:<?= $v['cor_texto'] ?>">
                <?php if (!empty($v['logo_url'])): ?>
                    <img src="<?= hce($v['logo_url']) ?>" style="max-height:32px;display:block" onerror="this.style.display='none'">
                <?php else: ?>
                    <?= hce($v['nome_empresa']) ?>
                <?php endif; ?>
            </div>
            <div class="pv-nav">
                <?php foreach (['Início','Serviços','Blog','Contato'] as $i): ?>
                    <span class="pv-navlink" style="color:<?= $v['cor_nav_link'] ?>"><?= $i ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

        <div class="ctx-panel" id="ctx-mob">
            <div class="form-grid">
                <?php colorField('mob_cor_fundo',       'Fundo da Barra (fechado)',   $v); ?>
                <?php colorField('mob_cor_texto',       'Logo / Texto',               $v); ?>
                <?php colorField('mob_cor_borda',       'Linha de Borda',             $v); ?>
                <?php colorField('mob_cor_nav_link',    'Links do Menu',              $v); ?>
                <?php colorField('mob_cor_menu_aberto', 'Fundo do Menu (aberto)',     $v); ?>
            </div>
        </div>

        <div class="pv-panel" id="pvp-mob">
            <div class="pv-mobile">
                <div id="pvMobBar" class="pv-mob-bar" style="background:<?= $v['mob_cor_fundo'] ?>;border-bottom:2px solid <?= $v['mob_cor_borda'] ?>">
                    <div id="pvMobLogo" style="font-size:14px;font-weight:800;color:<?= $v['mob_cor_texto'] ?>">
                        <?= !empty($v['logo_url']) ? '<img src="'.hce($v['logo_url']).'" style="max-height:26px">' : hce($v['nome_empresa']) ?>
                    </div>
                    <div class="pv-mob-lines">
                        <span class="pv-mob-line pv-hambline" style="background:<?= $v['mob_cor_texto'] ?>"></span>
                        <span class="pv-mob-line pv-hambline" style="background:<?= $v['mob_cor_texto'] ?>"></span>
                        <span class="pv-mob-line pv-hambline" style="background:<?= $v['mob_cor_texto'] ?>"></span>
                    </div>
                </div>
                <!-- Sidebar aberta usa mob_cor_menu_aberto -->
                <div id="pvMobMenu" class="pv-mob-menu" style="background:<?= $v['mob_cor_menu_aberto'] ?>">
                    <?php foreach (['Início','Serviços','Blog','Contato'] as $i): ?>
                        <div class="pv-mob-item pv-mob-navlink" style="color:<?= $v['mob_cor_nav_link'] ?>"><?= $i ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-dark">💾 Salvar Configurações</button>
            <a href="<?= BASE_URL ?>/admin/personalizacao/header/index.php" class="btn btn-ghost">Cancelar</a>
        </div>

    </form>

</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
/* ===== Navegação entre abas ===== */
function switchPvTab(tab, btn) {
    document.querySelectorAll('.pv-panel').forEach(function(p){ p.classList.remove('active'); });
    document.querySelectorAll('.pvtab').forEach(function(b){ b.classList.remove('active'); });
    document.getElementById('pvp-' + tab).classList.add('active');
    btn.classList.add('active');
}

function switchCtxTab(tab, btn) {
    document.querySelectorAll('.ctx-panel').forEach(function(p){ p.classList.remove('active'); });
    document.querySelectorAll('.ctx-tab').forEach(function(b){ b.classList.remove('active'); });
    document.getElementById('ctx-' + tab).classList.add('active');
    btn.classList.add('active');
    // Sincroniza preview com a aba do form
    var pvTab = (tab === 'dsk') ? 'desk' : 'mob';
    var pvBtn = document.querySelectorAll('.pvtab')[tab === 'dsk' ? 0 : 1];
    switchPvTab(pvTab, pvBtn);
}

/* ===== Mapeamento campo → atualização do preview ===== */
var previewMap = {
    cor_fundo:        function(v){ document.getElementById('pvBar').style.background = v; },
    cor_texto:        function(v){ document.getElementById('pvLogo').style.color = v; },
    cor_borda:        function(v){ document.getElementById('pvBar').style.borderBottom = '2px solid ' + v; },
    cor_nav_link:     function(v){ document.querySelectorAll('.pv-navlink').forEach(function(el){ el.style.color = v; }); },

    mob_cor_fundo:    function(v){
        document.getElementById('pvMobBar').style.background  = v;
    },
    mob_cor_texto:    function(v){
        document.getElementById('pvMobLogo').style.color = v;
        document.querySelectorAll('.pv-hambline').forEach(function(el){ el.style.background = v; });
    },
    mob_cor_borda:    function(v){ document.getElementById('pvMobBar').style.borderBottom = '2px solid ' + v; },
    mob_cor_nav_link: function(v){ document.querySelectorAll('.pv-mob-navlink').forEach(function(el){ el.style.color = v; }); },
    mob_cor_menu_aberto: function(v){ document.getElementById('pvMobMenu').style.background = v; },
};

/**
 * Chamado quando o COLOR PICKER muda.
 * Atualiza o campo de texto correspondente e o preview.
 */
function syncColorPicker(picker, campo) {
    var val = picker.value; // sempre #rrggbb válido
    document.getElementById('txt_' + campo).value = val;
    if (previewMap[campo]) previewMap[campo](val);
}

/**
 * Chamado quando o campo de TEXTO muda.
 * Atualiza o picker apenas quando o hex for válido, e sempre atualiza o preview.
 */
function syncColorText(input, campo) {
    var val = input.value.trim();
    // Normaliza: adiciona # se esqueceu
    if (/^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/.test(val)) val = '#' + val;
    if (/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/.test(val)) {
        // Expande #abc → #aabbcc para compatibilidade com input[type=color]
        var normalized = val;
        if (val.length === 4) {
            normalized = '#' + val[1]+val[1] + val[2]+val[2] + val[3]+val[3];
        }
        document.getElementById('clr_' + campo).value = normalized;
        if (previewMap[campo]) previewMap[campo](val);
    }
    // Garante que o valor do campo name=campo seja sempre o que o usuário digitou
    input.value = input.value; // mantém o valor original para o POST
}

/* ===== Nome da empresa no preview ===== */
document.getElementById('inputNome').addEventListener('input', function () {
    var nome = this.value || 'Sua Empresa';
    ['pvLogo','pvMobLogo'].forEach(function(id) {
        var el = document.getElementById(id);
        if (!el.querySelector('img')) el.textContent = nome;
    });
});

/* ===== Preview de logo ao fazer upload ===== */
document.getElementById('inputLogo').addEventListener('change', function () {
    if (!this.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var src = e.target.result;
        var th = document.getElementById('thumbLogo');
        th.src = src;
        th.style.display = 'block';
        document.getElementById('pvLogo').innerHTML    = '<img src="' + src + '" style="max-height:32px;display:block">';
        document.getElementById('pvMobLogo').innerHTML = '<img src="' + src + '" style="max-height:26px">';
    };
    reader.readAsDataURL(this.files[0]);
});

/* ===== Remover logo ===== */
var chkRemover = document.getElementById('chkRemover');
if (chkRemover) {
    chkRemover.addEventListener('change', function () {
        var nome = document.getElementById('inputNome').value || 'Sua Empresa';
        var th   = document.getElementById('thumbLogo');
        if (this.checked) {
            document.getElementById('pvLogo').textContent    = nome;
            document.getElementById('pvMobLogo').textContent = nome;
            th.style.opacity = '0.3';
        } else {
            th.style.opacity = '1';
            document.getElementById('pvLogo').innerHTML    = '<img src="' + th.src + '" style="max-height:32px;display:block">';
            document.getElementById('pvMobLogo').innerHTML = '<img src="' + th.src + '" style="max-height:26px">';
        }
    });
}
</script>   