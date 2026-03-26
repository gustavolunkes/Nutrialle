<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Ícones Flutuantes';
$current_module = 'personalizacao';
$current_page   = 'icones-flutuantes';

$db    = getDB();
$error = '';

// Garante que os dois registros fixos existam
$db->exec("INSERT IGNORE INTO icones_flutuantes (tipo, ativo, numero, mensagem, cor_fundo, cor_icone, posicao)
           VALUES ('whatsapp', 0, '', 'Olá! Vim pelo site e gostaria de mais informações.', '#25d366', '#ffffff', 'bottom-right')");
$db->exec("INSERT IGNORE INTO icones_flutuantes (tipo, ativo, cor_fundo, cor_icone, posicao)
           VALUES ('topo', 0, '#374151', '#ffffff', 'bottom-right')");

// Busca os dois
$wa  = $db->query("SELECT * FROM icones_flutuantes WHERE tipo = 'whatsapp' LIMIT 1")->fetch();
$top = $db->query("SELECT * FROM icones_flutuantes WHERE tipo = 'topo'     LIMIT 1")->fetch();

// ── Processar POST ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';

    if ($tipo === 'whatsapp') {
        $numero   = preg_replace('/\D/', '', trim($_POST['numero']   ?? ''));
        $mensagem = trim($_POST['mensagem'] ?? '');
        $cor_f    = trim($_POST['cor_fundo'] ?? '#25d366');
        $cor_i    = trim($_POST['cor_icone'] ?? '#ffffff');
        $posicao  = in_array($_POST['posicao'] ?? '', ['bottom-right','bottom-left']) ? $_POST['posicao'] : 'bottom-right';
        $ativo    = isset($_POST['ativo_wa']) ? 1 : 0;

        if ($ativo && empty($numero)) {
            $error = 'Para ativar o WhatsApp, informe o número.';
        } elseif (!preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $cor_f)) {
            $error = 'Cor de fundo do WhatsApp inválida.';
        } elseif (!preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $cor_i)) {
            $error = 'Cor do ícone do WhatsApp inválida.';
        } else {
            $db->prepare("UPDATE icones_flutuantes SET ativo=:ativo, numero=:numero, mensagem=:mensagem,
                          cor_fundo=:cor_fundo, cor_icone=:cor_icone, posicao=:posicao
                          WHERE tipo='whatsapp'")
               ->execute(['ativo'=>$ativo,'numero'=>$numero,'mensagem'=>$mensagem,
                          'cor_fundo'=>$cor_f,'cor_icone'=>$cor_i,'posicao'=>$posicao]);
            $_SESSION['success'] = 'WhatsApp salvo com sucesso!';
            header('Location: ' . BASE_URL . '/admin/personalizacao/icones_flutuantes/index.php');
            exit;
        }

    } elseif ($tipo === 'topo') {
        $cor_f   = trim($_POST['cor_fundo'] ?? '#374151');
        $cor_i   = trim($_POST['cor_icone'] ?? '#ffffff');
        $posicao = in_array($_POST['posicao'] ?? '', ['bottom-right','bottom-left']) ? $_POST['posicao'] : 'bottom-right';
        $ativo   = isset($_POST['ativo_topo']) ? 1 : 0;

        if (!preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $cor_f)) {
            $error = 'Cor de fundo do botão Topo inválida.';
        } elseif (!preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $cor_i)) {
            $error = 'Cor do ícone do botão Topo inválida.';
        } else {
            $db->prepare("UPDATE icones_flutuantes SET ativo=:ativo, cor_fundo=:cor_fundo,
                          cor_icone=:cor_icone, posicao=:posicao WHERE tipo='topo'")
               ->execute(['ativo'=>$ativo,'cor_fundo'=>$cor_f,'cor_icone'=>$cor_i,'posicao'=>$posicao]);
            $_SESSION['success'] = 'Botão Topo salvo com sucesso!';
            header('Location: ' . BASE_URL . '/admin/personalizacao/icones_flutuantes/index.php');
            exit;
        }
    }

    // Re-fetch após erro
    $wa  = $db->query("SELECT * FROM icones_flutuantes WHERE tipo = 'whatsapp' LIMIT 1")->fetch();
    $top = $db->query("SELECT * FROM icones_flutuantes WHERE tipo = 'topo'     LIMIT 1")->fetch();
}

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
function hce($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.cards-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:860px){.cards-row{grid-template-columns:1fr}}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden}
.card-hd{padding:16px 20px;border-bottom:1px solid var(--border);background:#fafafa;display:flex;align-items:center;justify-content:space-between;gap:12px}
.card-hd-left{display:flex;align-items:center;gap:10px}
.card-hd h3{font-size:15px;font-weight:700;color:var(--brand);margin:0}
.card-body{padding:22px}
.toggle-wrap{display:flex;align-items:center;gap:8px}
.toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.slider{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:.3s}
.slider::before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.toggle input:checked + .slider{background:var(--ok)}
.toggle input:checked + .slider::before{transform:translateX(20px)}
.badge-status{font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px}
.badge-on{background:#d1fae5;color:#065f46}
.badge-off{background:#f3f4f6;color:var(--muted)}
.fg{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
.fg:last-child{margin-bottom:0}
.fg label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
.fc{padding:9px 12px;border:2px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fafafa;transition:border-color .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-s)}
.color-row{display:flex;align-items:center;gap:8px}
.color-row input[type=color]{width:38px;height:38px;border:2px solid var(--border);border-radius:8px;cursor:pointer;padding:2px;background:#fafafa;flex-shrink:0}
.color-row input[type=text]{flex:1}
.hint{font-size:11px;color:var(--muted);margin-top:2px}
.preview-area{display:flex;align-items:center;gap:14px;padding:16px;background:#f1f5f9;border-radius:10px;margin-bottom:18px}
.preview-ball{width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(0,0,0,.2);flex-shrink:0;transition:transform .2s;cursor:default}
.preview-ball:hover{transform:scale(1.08)}
.preview-info strong{display:block;font-size:13px;color:var(--text);margin-bottom:2px}
.preview-info{font-size:12px;color:var(--muted);line-height:1.5}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:10px 18px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s;width:100%;margin-top:8px}
.btn-wa{background:#25d366;color:#fff}.btn-wa:hover{background:#1ebe5d}
.btn-topo{background:var(--brand);color:#fff}.btn-topo:hover{background:#1a2540}
.alert-ok{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.divider{height:1px;background:var(--border);margin:16px 0}
.tip{font-size:11px;color:#92400e;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:6px;padding:9px 12px;margin-bottom:14px;line-height:1.5}
</style>

<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h2>🔘 Ícones Flutuantes</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert-ok">✅ <?= hce($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= hce($error) ?></div>
    <?php endif; ?>

    <div class="cards-row">

        <!-- ══════════════ WHATSAPP ══════════════ -->
        <div class="card">
            <div class="card-hd">
                <div class="card-hd-left">
                    <span style="font-size:22px">💬</span>
                    <h3>WhatsApp</h3>
                </div>
                <span class="badge-status <?= $wa['ativo'] ? 'badge-on' : 'badge-off' ?>" id="badgeWa">
                    <?= $wa['ativo'] ? '● Ativo' : '○ Inativo' ?>
                </span>
            </div>
            <div class="card-body">

                <div class="preview-area">
                    <div class="preview-ball" id="waBall"
                         style="background:<?= hce($wa['cor_fundo']) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             id="waSvg"
                             style="width:24px;height:24px;fill:<?= hce($wa['cor_icone']) ?>">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div class="preview-info">
                        <strong>Pré-visualização</strong>
                        Botão fixo no canto da tela.<br>Abre conversa no WhatsApp.
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="tipo" value="whatsapp">

                    <div class="fg">
                        <label>Status</label>
                        <div class="toggle-wrap">
                            <label class="toggle">
                                <input type="checkbox" name="ativo_wa" value="1" id="togWa"
                                       <?= $wa['ativo'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                            <span style="font-size:13px;color:var(--text)" id="togWaLabel">
                                <?= $wa['ativo'] ? 'Exibindo no site' : 'Oculto no site' ?>
                            </span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="fg">
                        <label>Número do WhatsApp</label>
                        <input type="text" name="numero" class="fc"
                               value="<?= hce($wa['numero'] ?? '') ?>"
                               placeholder="5511999999999">
                        <span class="hint">Somente números com DDI. Ex: <strong>5511999999999</strong></span>
                    </div>

                    <div class="fg">
                        <label>Mensagem pré-preenchida <span style="font-weight:400">(opcional)</span></label>
                        <textarea name="mensagem" class="fc" rows="2"
                                  placeholder="Olá! Vim pelo site..."><?= hce($wa['mensagem'] ?? '') ?></textarea>
                    </div>

                    <div class="fg">
                        <label>Posição na tela</label>
                        <select name="posicao" class="fc">
                            <option value="bottom-right" <?= ($wa['posicao']==='bottom-right')?'selected':'' ?>>Inferior Direito</option>
                            <option value="bottom-left"  <?= ($wa['posicao']==='bottom-left') ?'selected':'' ?>>Inferior Esquerdo</option>
                        </select>
                    </div>

                    <div class="fg">
                        <label>Cor de Fundo</label>
                        <div class="color-row">
                            <input type="color" id="waClrF" value="<?= hce($wa['cor_fundo']) ?>"
                                   oninput="syncClr('wa','F',this.value)">
                            <input type="text" id="waTxtF" name="cor_fundo" class="fc"
                                   value="<?= hce($wa['cor_fundo']) ?>" maxlength="7"
                                   oninput="syncTxt('wa','F',this.value)">
                        </div>
                    </div>

                    <div class="fg">
                        <label>Cor do Ícone</label>
                        <div class="color-row">
                            <input type="color" id="waClrI" value="<?= hce($wa['cor_icone']) ?>"
                                   oninput="syncClr('wa','I',this.value)">
                            <input type="text" id="waTxtI" name="cor_icone" class="fc"
                                   value="<?= hce($wa['cor_icone']) ?>" maxlength="7"
                                   oninput="syncTxt('wa','I',this.value)">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-wa">💾 Salvar WhatsApp</button>
                </form>
            </div>
        </div>

        <!-- ══════════════ TOPO ══════════════ -->
        <div class="card">
            <div class="card-hd">
                <div class="card-hd-left">
                    <span style="font-size:22px">⬆️</span>
                    <h3>Subir ao Topo</h3>
                </div>
                <span class="badge-status <?= $top['ativo'] ? 'badge-on' : 'badge-off' ?>" id="badgeTopo">
                    <?= $top['ativo'] ? '● Ativo' : '○ Inativo' ?>
                </span>
            </div>
            <div class="card-body">

                <div class="preview-area">
                    <div class="preview-ball" id="topoBall"
                         style="background:<?= hce($top['cor_fundo']) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             id="topoSvg"
                             style="width:22px;height:22px;fill:<?= hce($top['cor_icone']) ?>">
                            <path d="M12 4l-8 8h5v8h6v-8h5z"/>
                        </svg>
                    </div>
                    <div class="preview-info">
                        <strong>Pré-visualização</strong>
                        Aparece após rolar 300px.<br>Leva o visitante ao topo da página.
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="tipo" value="topo">

                    <div class="fg">
                        <label>Status</label>
                        <div class="toggle-wrap">
                            <label class="toggle">
                                <input type="checkbox" name="ativo_topo" value="1" id="togTopo"
                                       <?= $top['ativo'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                            <span style="font-size:13px;color:var(--text)" id="togTopoLabel">
                                <?= $top['ativo'] ? 'Exibindo no site' : 'Oculto no site' ?>
                            </span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="tip">
                        💡 O botão aparece automaticamente após o visitante rolar <strong>300px</strong> para baixo e some ao voltar ao topo.
                    </div>

                    <div class="fg">
                        <label>Posição na tela</label>
                        <select name="posicao" class="fc">
                            <option value="bottom-right" <?= ($top['posicao']==='bottom-right')?'selected':'' ?>>Inferior Direito</option>
                            <option value="bottom-left"  <?= ($top['posicao']==='bottom-left') ?'selected':'' ?>>Inferior Esquerdo</option>
                        </select>
                    </div>

                    <div class="fg">
                        <label>Cor de Fundo</label>
                        <div class="color-row">
                            <input type="color" id="topoClrF" value="<?= hce($top['cor_fundo']) ?>"
                                   oninput="syncClr('topo','F',this.value)">
                            <input type="text" id="topoTxtF" name="cor_fundo" class="fc"
                                   value="<?= hce($top['cor_fundo']) ?>" maxlength="7"
                                   oninput="syncTxt('topo','F',this.value)">
                        </div>
                    </div>

                    <div class="fg">
                        <label>Cor do Ícone</label>
                        <div class="color-row">
                            <input type="color" id="topoClrI" value="<?= hce($top['cor_icone']) ?>"
                                   oninput="syncClr('topo','I',this.value)">
                            <input type="text" id="topoTxtI" name="cor_icone" class="fc"
                                   value="<?= hce($top['cor_icone']) ?>" maxlength="7"
                                   oninput="syncTxt('topo','I',this.value)">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-topo">💾 Salvar Botão Topo</button>
                </form>
            </div>
        </div>

    </div><!-- /.cards-row -->
</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
function syncClr(prefix, side, val) {
    document.getElementById(prefix + 'Txt' + side).value = val;
    applyPreview(prefix, side, val);
}
function syncTxt(prefix, side, val) {
    val = val.trim();
    if (/^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/.test(val)) val = '#' + val;
    if (/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/.test(val)) {
        var n = val.length === 4 ? '#'+val[1]+val[1]+val[2]+val[2]+val[3]+val[3] : val;
        document.getElementById(prefix + 'Clr' + side).value = n;
        applyPreview(prefix, side, val);
    }
}
function applyPreview(prefix, side, val) {
    if (prefix === 'wa') {
        if (side === 'F') document.getElementById('waBall').style.background = val;
        if (side === 'I') document.getElementById('waSvg').style.fill = val;
    } else {
        if (side === 'F') document.getElementById('topoBall').style.background = val;
        if (side === 'I') document.getElementById('topoSvg').style.fill = val;
    }
}
document.getElementById('togWa').addEventListener('change', function () {
    document.getElementById('togWaLabel').textContent = this.checked ? 'Exibindo no site' : 'Oculto no site';
});
document.getElementById('togTopo').addEventListener('change', function () {
    document.getElementById('togTopoLabel').textContent = this.checked ? 'Exibindo no site' : 'Oculto no site';
});
</script>
