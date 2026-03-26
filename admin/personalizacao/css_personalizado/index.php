<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'CSS Personalizado';
$current_module = 'personalizacao';
$current_page   = 'css-personalizado';

$db     = getDB();
$error  = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Garante que sempre exista exatamente 1 registro
$css_row = $db->query("SELECT * FROM css_personalizado WHERE id = 1 LIMIT 1")->fetch();
if (!$css_row) {
    $db->exec("INSERT INTO css_personalizado (id, css, ativo) VALUES (1, '', 1)");
    $css_row = $db->query("SELECT * FROM css_personalizado WHERE id = 1 LIMIT 1")->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $css   = $_POST['css']  ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    // Sanitização básica: remove tags <script> e </style> do conteúdo
    $css = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $css);
    $css = str_replace('</style>', '', $css);

    try {
        $db->prepare("UPDATE css_personalizado SET css = :css, ativo = :ativo WHERE id = 1")
           ->execute(['css' => $css, 'ativo' => $ativo]);
        $_SESSION['success'] = 'CSS salvo com sucesso!';
        header('Location: ' . BASE_URL . '/admin/personalizacao/css_personalizado/index.php');
        exit;
    } catch (Exception $e) {
        $error = 'Erro ao salvar: ' . $e->getMessage();
    }
}

$v = [
    'css'   => $_POST['css']   ?? $css_row['css'],
    'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : (int)$css_row['ativo'],
];

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
function hce($s){ return htmlspecialchars($s, ENT_QUOTES,'UTF-8'); }
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--warn:#f59e0b;--border:#e5e7eb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 16px;border:none;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.btn-sm{padding:5px 11px;font-size:11px}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:20px}
.card-hd{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand);margin:0}
.card-body{padding:24px}
.alert-ok{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.alert-warn{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#fffbeb;color:#92400e;border-left:4px solid var(--warn)}
.editor-wrap{position:relative;border:2px solid var(--border);border-radius:10px;overflow:hidden;background:#1e1e2e;font-family:'Courier New',Courier,monospace}
.editor-wrap:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-s)}
.editor-header{display:flex;align-items:center;gap:8px;padding:8px 14px;background:#16162a;border-bottom:1px solid rgba(255,255,255,.07)}
.editor-dot{width:10px;height:10px;border-radius:50%}
.editor-lang{margin-left:auto;font-size:11px;font-weight:700;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.8px}
#cssEditor{width:100%;min-height:360px;padding:16px;background:transparent;color:#cdd6f4;font-size:13px;font-family:'Courier New',Courier,monospace;line-height:1.7;border:none;outline:none;resize:vertical;box-sizing:border-box;tab-size:2}
.char-count{font-size:11px;color:var(--muted);text-align:right;margin-top:4px}
.fg{display:flex;flex-direction:column;gap:5px}
.fg label{font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px}
.form-actions{display:flex;gap:10px;margin-top:4px;padding-top:4px;align-items:center}
.toggle-wrap{display:flex;align-items:center;gap:10px}
.toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.slider{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:.3s}
.slider::before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.toggle input:checked + .slider{background:var(--ok)}
.toggle input:checked + .slider::before{transform:translateX(20px)}
.info-row{display:flex;gap:20px;margin-bottom:20px;flex-wrap:wrap}
.info-chip{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;background:#f3f4f6;color:var(--muted);display:flex;align-items:center;gap:6px}
.info-chip.active{background:#d1fae5;color:#065f46}
</style>

<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h2>💅 CSS Personalizado</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert-ok">✅ <?= hce($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= hce($error) ?></div>
    <?php endif; ?>

    <div class="alert-warn">
        ⚠️ <strong>Atenção:</strong> o CSS inserido aqui é incluído diretamente no <code>&lt;head&gt;</code> de todas as páginas do site. 
        Use apenas regras CSS válidas. Tags <code>&lt;script&gt;</code> são removidas automaticamente.
    </div>

    <!-- Status info -->
    <div class="info-row">
        <div class="info-chip <?= $css_row['ativo'] ? 'active' : '' ?>">
            <?= $css_row['ativo'] ? '● CSS Ativo' : '○ CSS Inativo' ?>
        </div>
        <div class="info-chip">
            📝 <?= strlen($css_row['css']) ?> caracteres
        </div>
        <div class="info-chip">
            🕐 Atualizado: <?= date('d/m/Y H:i', strtotime($css_row['updated_at'])) ?>
        </div>
    </div>

    <form method="POST">
        <div class="card">
            <div class="card-hd">
                <h3>📝 Editor CSS</h3>
                <div class="toggle-wrap">
                    <label class="toggle">
                        <input type="checkbox" name="ativo" value="1" <?= $v['ativo'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <span style="font-size:13px;color:var(--text)">Incluir no site</span>
                </div>
            </div>
            <div class="card-body">
                <div class="editor-wrap">
                    <div class="editor-header">
                        <span class="editor-dot" style="background:#ff5f56"></span>
                        <span class="editor-dot" style="background:#ffbd2e"></span>
                        <span class="editor-dot" style="background:#27c93f"></span>
                        <span class="editor-lang">CSS</span>
                    </div>
                    <textarea id="cssEditor" name="css"
                              placeholder="/* Escreva seu CSS personalizado aqui */&#10;&#10;/* Exemplo: */&#10;body { font-family: 'Inter', sans-serif; }&#10;.btn-primary { border-radius: 50px; }"
                              oninput="updateCount()"><?= hce($v['css']) ?></textarea>
                </div>
                <div class="char-count" id="charCount"><?= strlen($v['css']) ?> caracteres</div>

                <!-- Snippets rápidos -->
                <div style="margin-top:16px">
                    <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px">⚡ Inserir snippet:</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px">
                        <button type="button" class="btn btn-ghost btn-sm" onclick="insertSnippet('/* === Cores Globais === */\n:root {\n  --cor-primaria: #4f6ef7;\n  --cor-secundaria: #10b981;\n}\n')">Variáveis de Cor</button>
                        <button type="button" class="btn btn-ghost btn-sm" onclick="insertSnippet('@import url(\'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap\');\nbody { font-family: \'Inter\', sans-serif; }\n')">Google Font</button>
                        <button type="button" class="btn btn-ghost btn-sm" onclick="insertSnippet('/* === Animação Fade In === */\n@keyframes fadeIn {\n  from { opacity: 0; transform: translateY(16px); }\n  to   { opacity: 1; transform: translateY(0); }\n}\n.fade-in { animation: fadeIn .5s ease both; }\n')">Fade In</button>
                        <button type="button" class="btn btn-ghost btn-sm" onclick="insertSnippet('/* === Botão arredondado === */\n.btn, button {\n  border-radius: 50px !important;\n}\n')">Botões Arredondados</button>
                        <button type="button" class="btn btn-ghost btn-sm" onclick="insertSnippet('/* === Ocultar elemento === */\n.meu-elemento {\n  display: none !important;\n}\n')">Ocultar Elemento</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-dark">💾 Salvar CSS</button>
            <?php if (!empty($v['css'])): ?>
                <button type="button" class="btn btn-ghost" onclick="if(confirm('Limpar todo o CSS?')){document.getElementById('cssEditor').value='';updateCount();}">🗑️ Limpar tudo</button>
            <?php endif; ?>
            <span style="font-size:12px;color:var(--muted);margin-left:auto">O CSS é inserido no &lt;head&gt; de todas as páginas.</span>
        </div>
    </form>

</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script>
function updateCount() {
    var len = document.getElementById('cssEditor').value.length;
    document.getElementById('charCount').textContent = len + ' caracteres';
}

function insertSnippet(snippet) {
    var ta = document.getElementById('cssEditor');
    var start = ta.selectionStart;
    var end   = ta.selectionEnd;
    var val   = ta.value;
    ta.value = val.substring(0, start) + '\n' + snippet + '\n' + val.substring(end);
    ta.selectionStart = ta.selectionEnd = start + snippet.length + 2;
    ta.focus();
    updateCount();
}

// Tab key support no editor
document.getElementById('cssEditor').addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        var start = this.selectionStart;
        var end   = this.selectionEnd;
        this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
        this.selectionStart = this.selectionEnd = start + 2;
    }
});
</script>
