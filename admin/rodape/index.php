<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Rodapé';
$current_module = 'rodape';
$current_page   = 'rodape-index';

$db = getDB();

$config  = $db->query("SELECT * FROM rodape_config LIMIT 1")->fetch();
$colunas = $db->query("
    SELECT c.*, COUNT(l.id) as total_links
    FROM rodape_colunas c
    LEFT JOIN rodape_links l ON l.coluna_id = c.id
    GROUP BY c.id ORDER BY c.ordem ASC
")->fetchAll();
$redes = $db->query("SELECT * FROM rodape_redes_sociais ORDER BY ordem ASC")->fetchAll();
$tema  = $db->query("SELECT * FROM rodape_cores WHERE ativo = 1 LIMIT 1")->fetch();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;
      --border:#e5e7eb;--bg:#f4f6fb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.pg-actions{display:flex;gap:8px;flex-wrap:wrap}
.stats{display:flex;gap:14px;margin-bottom:22px;flex-wrap:wrap}
.stat{background:var(--card);border-radius:10px;box-shadow:var(--sh);padding:16px 22px;flex:1;min-width:110px}
.stat-n{font-size:26px;font-weight:800;color:var(--brand)}
.stat-l{font-size:12px;color:var(--muted);margin-top:2px}
.footer-preview{border-radius:var(--r);overflow:hidden;margin-bottom:22px;box-shadow:0 4px 20px rgba(0,0,0,.18)}
.fp-body{display:flex;gap:32px;padding:36px 32px;flex-wrap:wrap}
.fp-col{flex:1;min-width:160px}
.fp-brand{flex:1.4;min-width:200px}
.fp-col-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid}
.fp-link{display:block;font-size:13px;margin-bottom:8px}
.fp-soc-row{display:flex;gap:8px;margin-top:6px}
.fp-soc{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800}
.fp-bar{padding:14px 32px;font-size:12px;text-align:center}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden}
.card-hd{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand)}
.tcard{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:18px}
table{width:100%;border-collapse:collapse}
thead{background:#f8f9fa}
th{padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:11px 16px;border-bottom:1px solid #f0f0f0;font-size:13px;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafafa}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-on{background:#ecfdf5;color:#065f46}.badge-off{background:#fef2f2;color:#991b1b}
.badge-blue{background:#eff6ff;color:#1d4ed8}.badge-purple{background:#ede9fe;color:#5b21b6}
code{background:#f3f4f6;padding:2px 7px;border-radius:5px;font-size:12px;color:var(--accent);font-family:monospace}
.acts{display:flex;gap:6px}
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-blue{background:#3b82f6;color:#fff}.btn-blue:hover{background:#2563eb}
.btn-red{background:var(--err);color:#fff}.btn-red:hover{background:#dc2626}
.btn-purple{background:#8b5cf6;color:#fff}.btn-purple:hover{background:#7c3aed}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.btn-sm{padding:5px 11px;font-size:11px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.cfg-row{display:flex;gap:10px;align-items:center;padding:9px 20px;border-bottom:1px solid var(--border)}
.cfg-row:last-child{border-bottom:none}
.cfg-lbl{font-size:11px;font-weight:700;color:var(--muted);width:120px;flex-shrink:0;text-transform:uppercase;letter-spacing:.4px}
.cfg-val{font-size:13px;color:var(--text)}
.swatch{width:18px;height:18px;border-radius:50%;border:2px solid rgba(0,0,0,.1);flex-shrink:0;display:inline-block}
.swatches{display:flex;gap:4px;flex-wrap:wrap}
.empty{text-align:center;padding:36px 20px;color:var(--muted);font-size:13px}
@media(max-width:750px){.g2{grid-template-columns:1fr}.fp-body{padding:24px 20px}}
</style>

<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h1>Rodapé do Site</h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-ok">✅ <?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-err">⚠️ <?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>


    <!-- PREVIEW LIVE DO RODAPÉ -->
    <?php
    $bg    = $tema['cor_fundo']       ?? '#0a0f1e';
    $txt   = $tema['cor_texto']       ?? '#ffffff';
    $soft  = $tema['cor_texto_suave'] ?? '#8892a4';
    $div   = $tema['cor_divisor']     ?? '#e91e8c';
    ?>


    <!-- GRID: CONFIG + CORES -->
    <div class="g2">
        <div class="card">
            <div class="card-hd">
                <h3>⚙️ Configurações Gerais</h3>
                <a href="<?= BASE_URL ?>/admin/rodape/editar.php" class="btn btn-blue btn-sm">✏️ Editar</a>
            </div>
            <?php if ($config): ?>
                <div class="cfg-row"><span class="cfg-lbl">Empresa</span><strong class="cfg-val"><?= htmlspecialchars($config['nome_empresa']) ?></strong></div>
                <div class="cfg-row"><span class="cfg-lbl">Logo</span><span class="cfg-val"><?= $config['logo_url'] ? '<img src="'.htmlspecialchars($config['logo_url']).'" style="max-height:26px;border-radius:4px">' : '<span style="color:var(--muted)">—</span>' ?></span></div>
                <div class="cfg-row"><span class="cfg-lbl">Copyright</span><span class="cfg-val" style="font-size:12px"><?= htmlspecialchars(mb_substr($config['copyright_texto'],0,60)) ?></span></div>
                <div class="cfg-row"><span class="cfg-lbl">Status</span><span class="badge badge-<?= $config['ativo']?'on':'off' ?>"><?= $config['ativo']?'● Ativo':'● Inativo' ?></span></div>
                <div class="cfg-row"><span class="cfg-lbl">Atualizado</span><span class="cfg-val" style="font-size:12px;color:var(--muted)"><?= date('d/m/Y H:i', strtotime($config['updated_at'])) ?></span></div>
            <?php else: ?>
                <div class="empty">Sem configuração.<br><a href="<?= BASE_URL ?>/admin/rodape/editar.php" class="btn btn-blue" style="margin-top:10px">Configurar →</a></div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-hd">
                <h3>🎨 Tema de Cores</h3>
                <a href="<?= BASE_URL ?>/admin/rodape/cores.php" class="btn btn-purple btn-sm">🎨 Gerenciar</a>
            </div>
            <?php if ($tema): ?>
                <div class="cfg-row"><span class="cfg-lbl">Tema</span><span class="cfg-val"><strong><?= htmlspecialchars($tema['nome_tema']) ?></strong></span></div>
                <div class="cfg-row"><span class="cfg-lbl">Fundo</span><span class="cfg-val" style="display:flex;align-items:center;gap:6px"><span class="swatch" style="background:<?= $tema['cor_fundo'] ?>"></span><?= $tema['cor_fundo'] ?></span></div>
                <div class="cfg-row"><span class="cfg-lbl">Divisor</span><span class="cfg-val" style="display:flex;align-items:center;gap:6px"><span class="swatch" style="background:<?= $tema['cor_divisor'] ?>"></span><?= $tema['cor_divisor'] ?></span></div>
                <div class="cfg-row">
                    <span class="cfg-lbl">Todas</span>
                    <div class="swatches">
                        <?php foreach (['cor_fundo','cor_texto','cor_texto_suave','cor_link','cor_link_hover','cor_divisor','cor_linha'] as $ck): ?>
                            <span class="swatch" style="background:<?= $tema[$ck] ?>" title="<?= str_replace('cor_','',$ck) ?>: <?= $tema[$ck] ?>"></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="cfg-row"><span class="cfg-lbl">Atualizado</span><span class="cfg-val" style="font-size:12px;color:var(--muted)"><?= date('d/m/Y H:i', strtotime($tema['updated_at'])) ?></span></div>
            <?php else: ?>
                <div class="empty">Nenhum tema ativo.<br><a href="<?= BASE_URL ?>/admin/rodape/cores.php" class="btn btn-purple" style="margin-top:10px">🎨 Criar tema</a></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- REDES SOCIAIS -->
    <div class="tcard">
        <div class="card-hd" style="padding:14px 20px">
            <h3>📱 Redes Sociais</h3>
            <a href="<?= BASE_URL ?>/admin/rodape/redes/criar.php" class="btn btn-dark btn-sm">➕ Nova</a>
        </div>
        <?php if ($redes):
            $ri=['facebook'=>'📘','instagram'=>'📸','x'=>'🐦','linkedin'=>'💼','youtube'=>'▶️','tiktok'=>'🎵','whatsapp'=>'💬','telegram'=>'✈️','github'=>'🐙']; ?>
        <table>
            <thead><tr><th>#</th><th>Rede</th><th>URL</th><th>Ordem</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($redes as $r): ?>
            <tr>
                <td style="color:var(--muted);font-size:12px">#<?= $r['id'] ?></td>
                <td><strong><?= ($ri[$r['rede']]??'🔗').' '.ucfirst($r['rede']) ?></strong></td>
                <td><code><?= htmlspecialchars(mb_substr($r['url'],0,38)) ?>…</code></td>
                <td><?= $r['ordem'] ?></td>
                <td><span class="badge badge-<?= $r['ativo']?'on':'off' ?>"><?= $r['ativo']?'● On':'● Off' ?></span></td>
                <td>
                    <div class="acts">
                        <a href="<?= BASE_URL ?>/admin/rodape/redes/editar.php?id=<?= $r['id'] ?>" class="btn btn-blue btn-sm">✏️</a>
                        <a href="<?= BASE_URL ?>/admin/rodape/redes/deletar.php?id=<?= $r['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Remover?')">🗑️</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">Nenhuma rede social.</div>
        <?php endif; ?>
    </div>

    <!-- COLUNAS -->
    <div class="tcard">
        <div class="card-hd" style="padding:14px 20px">
            <h3>📋 Colunas do Rodapé</h3>
            <a href="<?= BASE_URL ?>/admin/rodape/colunas/criar.php" class="btn btn-dark btn-sm">➕ Nova Coluna</a>
        </div>
        <?php if ($colunas): ?>
        <table>
            <thead><tr><th>#</th><th>Título</th><th>Ordem</th><th>Links</th><th>Status</th><th>Criado em</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($colunas as $col): ?>
            <tr>
                <td style="color:var(--muted);font-size:12px">#<?= $col['id'] ?></td>
                <td><strong><?= htmlspecialchars($col['titulo']) ?></strong></td>
                <td><span class="badge badge-blue">📍 <?= $col['ordem'] ?></span></td>
                <td><span class="badge badge-purple">🔗 <?= $col['total_links'] ?></span></td>
                <td><span class="badge badge-<?= $col['ativo']?'on':'off' ?>"><?= $col['ativo']?'● Ativa':'● Inativa' ?></span></td>
                <td style="color:var(--muted);font-size:12px"><?= date('d/m/Y', strtotime($col['created_at'])) ?></td>
                <td>
                    <div class="acts">
                        <a href="<?= BASE_URL ?>/admin/rodape/colunas/editar.php?id=<?= $col['id'] ?>" class="btn btn-blue btn-sm">✏️ Editar</a>
                        <a href="<?= BASE_URL ?>/admin/rodape/links/index.php?coluna_id=<?= $col['id'] ?>" class="btn btn-ghost btn-sm">🔗 Links</a>
                        <a href="<?= BASE_URL ?>/admin/rodape/colunas/deletar.php?id=<?= $col['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Excluir coluna e todos os links?')">🗑️</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">
                <div style="font-size:40px;margin-bottom:10px">📋</div>
                Nenhuma coluna ainda.
                <br><a href="<?= BASE_URL ?>/admin/rodape/colunas/criar.php" class="btn btn-dark" style="margin-top:12px">➕ Criar primeira coluna</a>
            </div>
        <?php endif; ?>
    </div>

    <h2>Previa</h2><br>
    
    <div class="footer-preview">
        <div class="fp-body" style="background:<?= $bg ?>">
            <div class="fp-brand">
                <?php if ($config && $config['logo_url']): ?>
                    <img src="<?= htmlspecialchars($config['logo_url']) ?>" style="max-height:34px;margin-bottom:8px;display:block">
                <?php else: ?>
                    <div style="font-size:15px;font-weight:800;color:<?= $txt ?>;margin-bottom:6px"><?= htmlspecialchars($config['nome_empresa'] ?? 'Wire Stack') ?></div>
                <?php endif; ?>
                <div style="width:34px;height:3px;background:<?= $div ?>;margin-bottom:12px;border-radius:2px"></div>
                <p style="font-size:12px;color:<?= $soft ?>;line-height:1.65;margin:0;max-width:220px"><?= htmlspecialchars(mb_substr($config['descricao'] ?? '', 0, 120)) ?>…</p>
            </div>
            <?php foreach ($colunas as $col): if (!$col['ativo']) continue;
                $cls = $db->prepare("SELECT * FROM rodape_links WHERE coluna_id=? AND ativo=1 ORDER BY ordem LIMIT 6");
                $cls->execute([$col['id']]); $cls = $cls->fetchAll();
            ?>
            <div class="fp-col">
                <div class="fp-col-title" style="color:<?= $txt ?>;border-color:<?= $div ?>"><?= htmlspecialchars($col['titulo']) ?></div>
                <?php foreach ($cls as $l): ?>
                    <div class="fp-link" style="color:<?= $soft ?>"><?= htmlspecialchars($l['label']) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
            <div class="fp-col">
                <div class="fp-col-title" style="color:<?= $txt ?>;border-color:<?= $div ?>">Redes Sociais</div>
                <div class="fp-soc-row">
                    <?php $ri=['facebook'=>'f','instagram'=>'ig','x'=>'𝕏','linkedin'=>'in','youtube'=>'yt','tiktok'=>'tk','whatsapp'=>'wa'];
                    foreach ($redes as $r): if (!$r['ativo']) continue; ?>
                        <div class="fp-soc" style="background:rgba(255,255,255,.13);color:<?= $txt ?>"><?= $ri[$r['rede']] ?? strtoupper(substr($r['rede'],0,1)) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="fp-bar" style="background:<?= $bg ?>;color:<?= $soft ?>;border-top:1px solid rgba(255,255,255,.07)">
            <?= htmlspecialchars($config['copyright_texto'] ?? '') ?>
        </div>
    </div>

</div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>