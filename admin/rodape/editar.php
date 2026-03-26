<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Configurações do Rodapé';
$current_module = 'rodape';
$current_page   = 'rodape-editar';

$db = getDB();
$config = $db->query("SELECT * FROM rodape_config LIMIT 1")->fetch();
if (!$config) {
    $db->exec("INSERT INTO rodape_config (nome_empresa,descricao,copyright_texto) VALUES ('','','')");
    $config = $db->query("SELECT * FROM rodape_config LIMIT 1")->fetch();
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_empresa    = trim($_POST['nome_empresa'] ?? '');
    $descricao       = trim($_POST['descricao'] ?? '');
    $copyright_texto = trim($_POST['copyright_texto'] ?? '');
    $logo_url        = trim($_POST['logo_url'] ?? '');
    $ativo           = isset($_POST['ativo']) ? 1 : 0;

    if (empty($nome_empresa)) { $error = 'Nome da empresa é obrigatório.'; }
    else {
        if (!empty($_FILES['logo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp','svg'])) { $error = 'Formato inválido. Use JPG, PNG, WEBP ou SVG.'; }
            elseif ($_FILES['logo']['size'] > 2*1024*1024) { $error = 'Logo deve ter no máximo 2MB.'; }
            else {
                $dir = __DIR__ . '/../../assets/uploads/rodape/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $fname = 'logo_'.time().'.'.$ext;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $dir.$fname))
                    $logo_url = BASE_URL.'/assets/uploads/rodape/'.$fname;
                else $error = 'Falha ao salvar imagem.';
            }
        }
        if (!$error) {
            $db->prepare("UPDATE rodape_config SET nome_empresa=?,descricao=?,copyright_texto=?,logo_url=?,ativo=? WHERE id=?")
               ->execute([$nome_empresa,$descricao,$copyright_texto,$logo_url?:null,$ativo,$config['id']]);
            $success = 'Configurações salvas!';
            $config  = $db->query("SELECT * FROM rodape_config LIMIT 1")->fetch();
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--bg:#f4f6fb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px;max-width:860px}
.info-strip{display:flex;align-items:center;background:var(--brand);border-radius:var(--r);margin-bottom:24px;overflow:hidden}
.is-item{padding:16px 22px;border-right:1px solid rgba(255,255,255,.1)}
.is-item:last-child{border-right:none;margin-left:auto}
.is-label{font-size:10px;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.6px;margin-bottom:3px}
.is-val{font-size:13px;font-weight:700;color:#fff}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.btn-sm{padding:6px 13px;font-size:12px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);padding:28px}
.card-title{font-size:15px;font-weight:700;color:var(--brand);margin-bottom:22px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg.full{grid-column:1/-1}
.fg label{font-size:13px;font-weight:600;color:var(--text)}
.req{color:var(--err)}
.fc{padding:11px 14px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-s)}
textarea.fc{resize:vertical;min-height:90px}
.hint{font-size:11px;color:var(--muted)}
.tog-wrap{display:flex;align-items:center;gap:10px}
.tog{position:relative;width:44px;height:24px}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:var(--ok)}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.upzone{border:2px dashed var(--border);border-radius:10px;padding:22px;text-align:center;cursor:pointer;transition:all .2s;background:#fafafa;position:relative}
.upzone:hover{border-color:var(--accent);background:var(--accent-s)}
.upzone input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
</style>
<main class="content">
<div class="wrap">
    <div class="info-strip">
        <div class="is-item"><div class="is-label">Módulo</div><div class="is-val">🦶 Rodapé</div></div>
        <div class="is-item"><div class="is-label">Empresa</div><div class="is-val"><?= htmlspecialchars($config['nome_empresa']?:'—') ?></div></div>
        <div class="is-item"><div class="is-label">Atualizado</div><div class="is-val"><?= date('d/m/Y H:i',strtotime($config['updated_at'])) ?></div></div>
        <div class="is-item"><a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost btn-sm">← Voltar</a></div>
    </div>

    <?php if($error):?><div class="alert alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if($success):?><div class="alert alert-ok">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-title">⚙️ Configurações Gerais do Rodapé</div>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="fg">
                    <label>Nome da Empresa <span class="req">*</span></label>
                    <input type="text" name="nome_empresa" class="fc" required value="<?= htmlspecialchars($config['nome_empresa']) ?>" placeholder="Ex: Wire Stack">
                    <span class="hint">Exibido quando não há logo</span>
                </div>
                <div class="fg">
                    <label>Texto de Copyright</label>
                    <input type="text" name="copyright_texto" class="fc" value="<?= htmlspecialchars($config['copyright_texto']) ?>" placeholder="© 2025 Wire Stack. Todos os direitos reservados.">
                </div>
                <div class="fg full">
                    <label>Descrição / Tagline</label>
                    <textarea name="descricao" class="fc"><?= htmlspecialchars($config['descricao']) ?></textarea>
                    <span class="hint">Aparece abaixo do logo, no canto esquerdo do rodapé</span>
                </div>
                <div class="fg full">
                    <label>URL da Logo <small style="font-weight:400;color:var(--muted)">(ou faça upload abaixo)</small></label>
                    <input type="text" name="logo_url" class="fc" value="<?= htmlspecialchars($config['logo_url']??'') ?>" placeholder="https://...">
                    <?php if ($config['logo_url']): ?>
                        <img src="<?= htmlspecialchars($config['logo_url']) ?>" style="max-height:46px;border-radius:6px;border:2px solid var(--border);margin-top:8px" alt="logo atual">
                    <?php endif; ?>
                </div>
                <div class="fg full">
                    <label>Upload de Logo</label>
                    <div class="upzone">
                        <input type="file" name="logo" accept="image/*">
                        <div style="font-size:26px;margin-bottom:6px">🖼️</div>
                        <div style="font-size:13px;color:var(--muted)">Arraste ou <strong style="color:var(--accent)">clique para selecionar</strong></div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:3px">PNG, JPG, SVG, WEBP — máx. 2MB</div>
                    </div>
                </div>
                <!-- <div class="fg full">
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="ativo" <?= $config['ativo']?'checked':'' ?>><span class="tog-s"></span></label>
                        <span style="font-size:13px;font-weight:600">Rodapé ativo (visível no site)</span>
                    </div>
                </div> -->
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">💾 Salvar Configurações</button>
                <a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>