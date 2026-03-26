<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Cores do Rodapé';
$current_module = 'rodape';
$current_page   = 'rodape-cores';

$db    = getDB();
$temas = $db->query("SELECT * FROM rodape_cores ORDER BY ativo DESC, id ASC")->fetchAll();
$tema  = $db->query("SELECT * FROM rodape_cores WHERE ativo = 1 LIMIT 1")->fetch();

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'ativar') {
        $tema_id = (int)($_POST['tema_id'] ?? 0);
        try {
            $db->exec("UPDATE rodape_cores SET ativo=0");
            $db->prepare("UPDATE rodape_cores SET ativo=1 WHERE id=?")->execute([$tema_id]);
            $success = 'Tema ativado com sucesso!';
        } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
        $temas = $db->query("SELECT * FROM rodape_cores ORDER BY ativo DESC, id ASC")->fetchAll();
        $tema  = $db->query("SELECT * FROM rodape_cores WHERE ativo = 1 LIMIT 1")->fetch();

    } elseif ($action === 'salvar') {
        $tid             = (int)($_POST['id'] ?? 0);
        $nome_tema       = trim($_POST['nome_tema']       ?? '');
        $cor_fundo       = trim($_POST['cor_fundo']       ?? '#0a0f1e');
        $cor_texto       = trim($_POST['cor_texto']       ?? '#ffffff');
        $cor_texto_suave = trim($_POST['cor_texto_suave'] ?? '#8892a4');
        $cor_link        = trim($_POST['cor_link']        ?? '#ffffff');
        $cor_link_hover  = trim($_POST['cor_link_hover']  ?? '#e91e8c');
        $cor_divisor     = trim($_POST['cor_divisor']     ?? '#e91e8c');
        $cor_linha       = trim($_POST['cor_linha']       ?? '#1e2a3a');

        if (!$nome_tema) { $error = 'Nome do tema é obrigatório.'; }
        else {
            try {
                if ($tid) {
                    $db->prepare("UPDATE rodape_cores SET nome_tema=?,cor_fundo=?,cor_texto=?,cor_texto_suave=?,cor_link=?,cor_link_hover=?,cor_divisor=?,cor_linha=? WHERE id=?")
                       ->execute([$nome_tema,$cor_fundo,$cor_texto,$cor_texto_suave,$cor_link,$cor_link_hover,$cor_divisor,$cor_linha,$tid]);
                    $success = 'Tema atualizado!';
                } else {
                    $db->prepare("INSERT INTO rodape_cores (nome_tema,cor_fundo,cor_texto,cor_texto_suave,cor_link,cor_link_hover,cor_divisor,cor_linha,ativo) VALUES (?,?,?,?,?,?,?,?,0)")
                       ->execute([$nome_tema,$cor_fundo,$cor_texto,$cor_texto_suave,$cor_link,$cor_link_hover,$cor_divisor,$cor_linha]);
                    $success = 'Tema criado!';
                }
                $temas = $db->query("SELECT * FROM rodape_cores ORDER BY ativo DESC, id ASC")->fetchAll();
                $tema  = $db->query("SELECT * FROM rodape_cores WHERE ativo = 1 LIMIT 1")->fetch();
            } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
        }

    } elseif ($action === 'deletar') {
        $tema_id = (int)($_POST['tema_id'] ?? 0);
        try {
            $t = $db->prepare("SELECT * FROM rodape_cores WHERE id=?");
            $t->execute([$tema_id]);
            $t = $t->fetch();
            if ($t && $t['ativo']) { $error = 'Não é possível deletar o tema ativo.'; }
            else {
                $db->prepare("DELETE FROM rodape_cores WHERE id=?")->execute([$tema_id]);
                $success = 'Tema removido.';
            }
        } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
        $temas = $db->query("SELECT * FROM rodape_cores ORDER BY ativo DESC, id ASC")->fetchAll();
        $tema  = $db->query("SELECT * FROM rodape_cores WHERE ativo = 1 LIMIT 1")->fetch();
    }
}

// Tema selecionado para editar
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM rodape_cores WHERE id=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--bg:#f4f6fb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden}
.card-hd{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand)}
.card-body{padding:22px}
.tcard{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:20px}
table{width:100%;border-collapse:collapse}
thead{background:#f8f9fa}
th{padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}
td{padding:11px 16px;border-bottom:1px solid #f0f0f0;font-size:13px;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafafa}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-on{background:#ecfdf5;color:#065f46}.badge-off{background:#fef2f2;color:#991b1b}
.acts{display:flex;gap:6px}
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540}
.btn-blue{background:#3b82f6;color:#fff}.btn-blue:hover{background:#2563eb}
.btn-red{background:var(--err);color:#fff}.btn-red:hover{background:#dc2626}
.btn-ok{background:var(--ok);color:#fff}.btn-ok:hover{background:#059669}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.btn-sm{padding:5px 11px;font-size:11px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.fg{display:flex;flex-direction:column;gap:5px}
.fg.full{grid-column:1/-1}
.fg label{font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px}
.fc{padding:9px 12px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-s)}
.color-row{display:flex;align-items:center;gap:8px}
.color-row input[type=color]{width:38px;height:38px;border:2px solid var(--border);border-radius:8px;cursor:pointer;padding:2px;background:#fafafa}
.color-row input[type=text]{flex:1}
.swatches{display:flex;gap:5px;flex-wrap:wrap}
.swatch{width:22px;height:22px;border-radius:50%;border:2px solid rgba(0,0,0,.12);flex-shrink:0}
.form-actions{display:flex;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid #f0f0f0}
.preview-bar{border-radius:10px;overflow:hidden;margin-bottom:20px;box-shadow:0 4px 20px rgba(0,0,0,.18)}
.pb-body{padding:24px;display:flex;gap:20px;flex-wrap:wrap}
.pb-bottom{padding:12px 24px;font-size:12px;text-align:center}
@media(max-width:750px){.g2{grid-template-columns:1fr}}
</style>
<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h2>🎨 Cores do Rodapé</h2>
        <div style="display:flex;gap:8px">
            <a href="<?= BASE_URL ?>/admin/rodape/index.php" class="btn btn-ghost">← Voltar</a>
        </div>
    </div>

    <?php if ($error): ?><div class="alert alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-ok">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>

    <?php
    $bg   = $tema['cor_fundo']        ?? '#0a0f1e';
    $txt  = $tema['cor_texto']        ?? '#ffffff';
    $soft = $tema['cor_texto_suave']  ?? '#8892a4';
    $div  = $tema['cor_divisor']      ?? '#e91e8c';
    ?>
    <?php if ($tema): ?>
    <div class="preview-bar">
        <div class="pb-body" style="background:<?= $bg ?>">
            <div style="flex:1;min-width:180px">
                <div style="font-size:16px;font-weight:800;color:<?= $txt ?>;margin-bottom:6px">Sua Empresa</div>
                <div style="width:32px;height:3px;background:<?= $div ?>;border-radius:2px;margin-bottom:10px"></div>
                <div style="font-size:12px;color:<?= $soft ?>;line-height:1.6">Texto de descrição / tagline aparece aqui.</div>
            </div>
            <div style="flex:1;min-width:140px">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:<?= $txt ?>;border-bottom:2px solid <?= $div ?>;padding-bottom:8px;margin-bottom:12px">Navegação</div>
                <?php foreach (['Início','Serviços','Contato','Blog'] as $item): ?>
                    <div style="font-size:12px;color:<?= $soft ?>;margin-bottom:7px"><?= $item ?></div>
                <?php endforeach; ?>
            </div>
            <div style="flex:1;min-width:140px">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:<?= $txt ?>;border-bottom:2px solid <?= $div ?>;padding-bottom:8px;margin-bottom:12px">Redes Sociais</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <?php foreach (['f','ig','in','yt'] as $icon): ?>
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.13);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:<?= $txt ?>"><?= $icon ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="pb-bottom" style="background:<?= $bg ?>;color:<?= $soft ?>;border-top:1px solid rgba(255,255,255,.07)">
            © 2025 Sua Empresa. Todos os direitos reservados. — Tema ativo: <strong style="color:<?= $txt ?>"><?= htmlspecialchars($tema['nome_tema']) ?></strong>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista de Temas -->
    <div class="tcard">
        <div class="card-hd">
            <h3>🎨 Temas Disponíveis</h3>
        </div>
        <?php if ($temas): ?>
        <table>
            <thead><tr><th>#</th><th>Nome</th><th>Cores do Rodapé</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($temas as $t): ?>
            <tr>
                <td style="color:var(--muted);font-size:12px">#<?= $t['id'] ?></td>
                <td><strong><?= htmlspecialchars($t['nome_tema']) ?></strong></td>
                <td>
                    <div class="swatches">
                        <?php foreach (['cor_fundo','cor_texto','cor_texto_suave','cor_link','cor_divisor','cor_linha'] as $ck): ?>
                            <?php if (!empty($t[$ck])): ?>
                            <span class="swatch" style="background:<?= htmlspecialchars($t[$ck]) ?>" title="<?= str_replace('cor_','',$ck) ?>: <?= $t[$ck] ?>"></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </td>
                <td><span class="badge badge-<?= $t['ativo']?'on':'off' ?>"><?= $t['ativo']?'● Ativo':'● Inativo' ?></span></td>
                <td>
                    <div class="acts">
                        <a href="?editar=<?= $t['id'] ?>" class="btn btn-blue btn-sm">✏️</a>
                        <?php if (!$t['ativo']): ?>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="ativar">
                                <input type="hidden" name="tema_id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-ok btn-sm">✅ Ativar</button>
                            </form>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Excluir este tema?')">
                                <input type="hidden" name="action" value="deletar">
                                <input type="hidden" name="tema_id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-red btn-sm">🗑️</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div style="text-align:center;padding:40px;color:var(--muted)">Nenhum tema cadastrado. <a href="?editar=novo" class="btn btn-dark" style="margin-left:10px">➕ Criar tema</a></div>
        <?php endif; ?>
    </div>

    <!-- Formulário de criação/edição -->
    <?php if (isset($_GET['editar'])): ?>
    <?php
    $isNovo = ($_GET['editar'] === 'novo');
    $ed = $editando ?? [
        'id'=>'','nome_tema'=>'',
        'cor_fundo'=>'#0a0f1e','cor_texto'=>'#ffffff','cor_texto_suave'=>'#8892a4',
        'cor_link'=>'#ffffff','cor_link_hover'=>'#e91e8c',
        'cor_divisor'=>'#e91e8c','cor_linha'=>'#1e2a3a'
    ];
    ?>
    <div class="card">
        <div class="card-hd">
            <h3><?= $isNovo ? '➕ Novo Tema' : '✏️ Editar Tema: '.htmlspecialchars($ed['nome_tema']) ?></h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="salvar">
                <input type="hidden" name="id" value="<?= $ed['id'] ?>">
                <div class="form-grid">
                    <div class="fg full">
                        <label>Nome do Tema</label>
                        <input type="text" name="nome_tema" class="fc" required value="<?= htmlspecialchars($ed['nome_tema']) ?>" placeholder="Ex: Escuro Padrão, Azul Elegante…">
                    </div>
                    <?php
                    $campos = [
                        'cor_fundo'        => 'Fundo do Rodapé',
                        'cor_texto'        => 'Texto Principal',
                        'cor_texto_suave'  => 'Texto Suave',
                        'cor_link'         => 'Cor dos Links',
                        'cor_link_hover'   => 'Link ao Passar o Mouse',
                        'cor_divisor'      => 'Cor Divisor / Destaque',
                        'cor_linha'        => 'Cor Linha Separadora',
                    ];
                    foreach ($campos as $campo => $label):
                        $val = htmlspecialchars($ed[$campo] ?? '#000000');
                    ?>
                    <div class="fg">
                        <label><?= $label ?></label>
                        <div class="color-row">
                            <input type="color" value="<?= $val ?>" oninput="document.getElementById('txt_<?= $campo ?>').value=this.value">
                            <input type="text" id="txt_<?= $campo ?>" name="<?= $campo ?>" class="fc" value="<?= $val ?>" placeholder="#000000" pattern="^#[0-9a-fA-F]{3,6}$">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-dark">💾 <?= $isNovo ? 'Criar Tema' : 'Salvar Alterações' ?></button>
                    <a href="<?= BASE_URL ?>/admin/rodape/cores.php" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>