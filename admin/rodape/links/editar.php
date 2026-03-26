<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Editar Link';
$current_module = 'rodape';
$current_page   = 'rodape-link-editar';

$db   = getDB();
$id   = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT l.*, c.titulo as coluna_titulo FROM rodape_links l JOIN rodape_colunas c ON c.id=l.coluna_id WHERE l.id=?");
$stmt->execute([$id]);
$link = $stmt->fetch();

if (!$link) {
    $_SESSION['error'] = 'Link não encontrado.';
    header('Location: ' . BASE_URL . '/admin/rodape/index.php');
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label        = trim($_POST['label'] ?? '');
    $url          = trim($_POST['url'] ?? '');
    $ordem        = (int)($_POST['ordem'] ?? 0);
    $ativo        = isset($_POST['ativo']) ? 1 : 0;
    $target_blank = isset($_POST['target_blank']) ? 1 : 0;

    if (!$label || !$url) { $error = 'Label e URL são obrigatórios.'; }
    else {
        try {
            $db->prepare("UPDATE rodape_links SET label=?, url=?, ordem=?, ativo=?, target_blank=? WHERE id=?")
               ->execute([$label, $url, $ordem, $ativo, $target_blank, $id]);
            $success = 'Link atualizado!';
            $stmt->execute([$id]);
            $link = $stmt->fetch();
        } catch (Exception $e) {
            try {
                $db->prepare("UPDATE rodape_links SET label=?, url=?, ordem=?, ativo=? WHERE id=?")
                   ->execute([$label, $url, $ordem, $ativo, $id]);
                $success = 'Link atualizado!';
                $stmt->execute([$id]);
                $link = $stmt->fetch();
            } catch (Exception $e2) {
                $error = 'Erro: ' . $e2->getMessage();
            }
        }
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
.breadcrumb{font-size:12px;color:var(--muted);margin-bottom:18px}
.breadcrumb a{color:var(--accent);text-decoration:none}.breadcrumb a:hover{text-decoration:underline}
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
.tog-wrap{display:flex;align-items:center;gap:10px;margin-bottom:4px}
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
.tag-coluna{display:inline-block;background:var(--accent-s);color:var(--accent);border-radius:6px;padding:4px 12px;font-size:12px;font-weight:700;margin-bottom:20px}
</style>
<main class="content">
<div class="wrap">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/admin/rodape/index.php">🦶 Rodapé</a> ›
        <a href="<?= BASE_URL ?>/admin/rodape/links/index.php?coluna_id=<?= $link['coluna_id'] ?>"><?= htmlspecialchars($link['coluna_titulo']) ?></a> ›
        Editar Link
    </div>
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/rodape/links/index.php?coluna_id=<?= $link['coluna_id'] ?>" class="btn btn-ghost">← Voltar</a>
        <h2>✏️ Editar Link</h2>
    </div>
    <div class="tag-coluna">📋 Coluna: <?= htmlspecialchars($link['coluna_titulo']) ?></div>
    <?php if ($error): ?><div class="alert alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-ok">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>
    <div class="card">
        <div class="card-title">Dados do Link #<?= $link['id'] ?></div>
        <form method="POST">
            <div class="form-grid">
                <div class="fg full">
                    <label>Texto do Link (Label) <span class="req">*</span></label>
                    <input type="text" name="label" class="fc" required value="<?= htmlspecialchars($link['label']) ?>" placeholder="Ex: Política de Privacidade">
                </div>
                <div class="fg full">
                    <label>URL <span class="req">*</span></label>
                    <input type="text" name="url" class="fc" required value="<?= htmlspecialchars($link['url']) ?>" placeholder="Ex: /politica-privacidade ou https://...">
                    <span class="hint">URL relativa ou absoluta</span>
                </div>
                <div class="fg">
                    <label>Ordem</label>
                    <input type="number" name="ordem" class="fc" min="0" value="<?= (int)$link['ordem'] ?>">
                    <span class="hint">Menor = aparece primeiro</span>
                </div>
                <div class="fg" style="justify-content:flex-end">
                    <label>&nbsp;</label>
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="ativo" <?= $link['ativo'] ? 'checked' : '' ?>><span class="tog-s"></span></label>
                        <span style="font-size:13px;font-weight:600">Link ativo</span>
                    </div>
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="target_blank" <?= ($link['target_blank'] ?? 0) ? 'checked' : '' ?>><span class="tog-s"></span></label>
                        <span style="font-size:13px;font-weight:600">Abrir em nova aba</span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">💾 Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/admin/rodape/links/index.php?coluna_id=<?= $link['coluna_id'] ?>" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
