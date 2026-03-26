<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Nova Coluna';
$current_module = 'rodape';
$current_page   = 'rodape-coluna-criar';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $ordem  = (int)($_POST['ordem'] ?? 0);
    $ativo  = isset($_POST['ativo']) ? 1 : 0;

    if (!$titulo) { $error = 'O título é obrigatório.'; }
    else {
        try {
            $db = getDB();
            $db->prepare("INSERT INTO rodape_colunas (titulo, ordem, ativo) VALUES (?, ?, ?)")
               ->execute([$titulo, $ordem, $ativo]);
            $_SESSION['success'] = 'Coluna criada com sucesso!';
            header('Location: ' . BASE_URL . '/admin/rodape/index.php');
            exit;
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
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err);padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
</style>
<main class="content">
<div class="wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>📋 Nova Coluna</h2>
    </div>
    <?php if ($error): ?><div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="card">
        <div class="card-title">Dados da Coluna</div>
        <form method="POST">
            <div class="form-grid">
                <div class="fg full">
                    <label>Título da Coluna <span class="req">*</span></label>
                    <input type="text" name="titulo" class="fc" required value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" placeholder="Ex: Navegação, Serviços, Contato…">
                    <span class="hint">Aparece como cabeçalho da coluna no rodapé</span>
                </div>
                <div class="fg">
                    <label>Ordem</label>
                    <input type="number" name="ordem" class="fc" min="0" value="<?= (int)($_POST['ordem'] ?? 0) ?>">
                    <span class="hint">Menor = aparece primeiro</span>
                </div>
                <div class="fg" style="justify-content:flex-end;padding-bottom:5px">
                    <label>&nbsp;</label>
                    <div class="tog-wrap">
                        <label class="tog"><input type="checkbox" name="ativo" checked><span class="tog-s"></span></label>
                        <span style="font-size:13px;font-weight:600">Coluna ativa (visível no rodapé)</span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-dark">✅ Criar Coluna</button>
                <a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
