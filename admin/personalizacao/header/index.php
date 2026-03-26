<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Menu Superior';
$current_module = 'personalizacao';
$current_page   = 'config-header';

$db     = getDB();
$config = $db->query("SELECT * FROM header_config LIMIT 1")->fetch();

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--ok:#10b981;--border:#e5e7eb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:20px}
.card-hd{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand);margin:0}
.grp{padding:6px 20px 14px}
.grp-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);padding:14px 0 8px;border-bottom:1px solid var(--border);margin-bottom:4px}
.cfg-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f3f4f6}
.cfg-row:last-child{border-bottom:none}
.cfg-lbl{font-size:11px;font-weight:700;color:var(--muted);width:180px;flex-shrink:0;text-transform:uppercase;letter-spacing:.3px}
.cfg-val{font-size:13px;color:var(--text);display:flex;align-items:center;gap:8px}
.swatch{width:18px;height:18px;border-radius:50%;border:2px solid rgba(0,0,0,.1);flex-shrink:0}
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-blue{background:#3b82f6;color:#fff}.btn-blue:hover{background:#2563eb}
.btn-sm{padding:5px 11px;font-size:11px}
.empty{text-align:center;padding:40px;color:var(--muted)}
.alert-ok{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.preview-cols{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
.preview-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px}
.preview-wrap{border-radius:8px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.12)}
.ph-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 20px;gap:12px}
.ph-nav{display:flex;gap:12px}
.ph-nav span{font-size:12px;font-weight:500}
.ph-mobile{max-width:200px;margin:0 auto}
.ph-mob-bar{display:flex;align-items:center;justify-content:space-between;padding:10px 14px}
.ph-mob-lines{display:flex;flex-direction:column;gap:4px}
.ph-mob-line{width:18px;height:2px;border-radius:2px}
.ph-mob-menu{display:flex;flex-direction:column}
.ph-mob-item{padding:10px 16px;font-size:12px;font-weight:500;border-bottom:1px solid rgba(0,0,0,.06)}
@media(max-width:600px){.preview-cols{grid-template-columns:1fr}}
</style>

<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h2>⚙️ Menu Superior</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert-ok">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($config): $c = $config; ?>

        

    <div class="card">
        <div class="card-hd">
            <h3>📋 Configurações Atuais</h3>
            <a href="<?= BASE_URL ?>/admin/personalizacao/header/editar.php" class="btn btn-blue btn-sm">✏️ Editar Configurações</a>
        </div>

        <?php
        function swatchRow(string $lbl, string $val): void {
            echo '<div class="cfg-row">';
            echo '<span class="cfg-lbl">' . $lbl . '</span>';
            echo '<span class="cfg-val">';
            if (preg_match('/^#[0-9a-fA-F]{3,6}$/', $val))
                echo '<span class="swatch" style="background:' . $val . '"></span>';
            echo $val . '</span></div>';
        }
        ?>

        <div class="grp">
            <div class="grp-title">🏢 Identidade</div>
            <div class="cfg-row">
                <span class="cfg-lbl">Nome</span>
                <span class="cfg-val"><strong><?= htmlspecialchars($c['nome_empresa']) ?></strong></span>
            </div>
            <div class="cfg-row">
                <span class="cfg-lbl">Logo</span>
                <span class="cfg-val">
                    <?php if (!empty($c['logo_url'])): ?>
                        <img src="<?= htmlspecialchars($c['logo_url']) ?>" style="max-height:28px;border-radius:4px">
                    <?php else: ?>
                        <span style="color:var(--muted)">— Sem logo</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="grp">
            <div class="grp-title">🖥️ Desktop</div>
            <?php swatchRow('Fundo',        $c['cor_fundo']); ?>
            <?php swatchRow('Texto / Logo', $c['cor_texto']); ?>
            <?php swatchRow('Borda',        $c['cor_borda']); ?>
            <?php swatchRow('Links',        $c['cor_nav_link']); ?>
        </div>

        <div class="grp">
            <div class="grp-title">📱 Mobile</div>
            <?php swatchRow('Fundo',        $c['mob_cor_fundo']); ?>
            <?php swatchRow('Texto / Logo', $c['mob_cor_texto']); ?>
            <?php swatchRow('Borda',        $c['mob_cor_borda']); ?>
            <?php swatchRow('Links',        $c['mob_cor_nav_link']); ?>
        </div>

        <div class="grp">
            <div class="grp-title">🕐 Metadados</div>
            <div class="cfg-row">
                <span class="cfg-lbl">Atualizado em</span>
                <span class="cfg-val" style="color:var(--muted);font-size:12px">
                    <?= date('d/m/Y H:i', strtotime($c['updated_at'])) ?>
                </span>
            </div>
        </div>
    </div>

    <?php else: ?>
        <div class="card">
            <div class="empty">
                Nenhuma configuração encontrada.<br>
                <a href="<?= BASE_URL ?>/admin/personalizacao/header/editar.php" class="btn btn-blue" style="margin-top:14px">⚙️ Configurar agora</a>
            </div>
        </div>
    <?php endif; ?>

</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>