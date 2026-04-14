<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Editar Produto';
$current_module = 'produtos';
$current_page   = 'produtos-lista';
$error = '';
$db = getDB();

$pid = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$pid]);
$p = $stmt->fetch();
if (!$p) { $_SESSION['error'] = 'Produto não encontrado.'; header('Location: ' . BASE_URL . '/admin/produtos/index.php'); exit; }

$categorias = $db->query("SELECT id, nome FROM categorias_produtos WHERE ativo = 1 ORDER BY nome")->fetchAll();

$cats_stmt = $db->prepare("SELECT categoria_produto_id FROM produto_categorias WHERE produto_id = ?");
$cats_stmt->execute([$pid]);
$cats_sel = $cats_stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku               = strtoupper(trim($_POST['sku'] ?? ''));
    $nome              = trim($_POST['nome'] ?? '');
    $descricao_curta   = trim($_POST['descricao_curta'] ?? '');
    $descricao         = trim($_POST['descricao'] ?? '');
    $preco             = trim($_POST['preco'] ?? '');
    $preco_promocional = trim($_POST['preco_promocional'] ?? '');
    $whatsapp_vendedor = preg_replace('/\D/', '', trim($_POST['whatsapp_vendedor'] ?? ''));
    $remover_imagem    = isset($_POST['remover_imagem']);
    $imagem_principal  = $remover_imagem ? '' : trim($_POST['imagem_principal'] ?? '');
    $meta_description  = trim($_POST['meta_description'] ?? '');
    $meta_keywords     = trim($_POST['meta_keywords'] ?? '');
    $destaque          = isset($_POST['destaque']) ? 1 : 0;
    $ativo             = isset($_POST['ativo']) ? 1 : 0;
    $cats_sel          = array_map('intval', $_POST['categorias'] ?? []);

    if (!$sku)  { $error = 'O SKU é obrigatório.'; }
    elseif (!$nome) { $error = 'O nome é obrigatório.'; }
    else {
        $chk = $db->prepare("SELECT id FROM produtos WHERE sku = ? AND id != ?");
        $chk->execute([$sku, $pid]);
        if ($chk->fetch()) { $error = 'Este SKU já está em uso por outro produto.'; }
        else {
            try {
                $db->prepare("UPDATE produtos SET sku=?,nome=?,descricao_curta=?,descricao=?,preco=?,preco_promocional=?,whatsapp_vendedor=?,imagem_principal=?,meta_description=?,meta_keywords=?,destaque=?,ativo=? WHERE id=?")
                   ->execute([$sku, $nome, $descricao_curta, $descricao,
                              $preco ?: null, $preco_promocional ?: null,
                              $whatsapp_vendedor ?: null,
                              $imagem_principal ?: null,
                              $meta_description, $meta_keywords, $destaque, $ativo, $pid]);
                $db->prepare("DELETE FROM produto_categorias WHERE produto_id = ?")->execute([$pid]);
                foreach ($cats_sel as $cid) {
                    if ($cid) $db->prepare("INSERT IGNORE INTO produto_categorias (produto_id, categoria_produto_id) VALUES (?,?)")->execute([$pid, $cid]);
                }
                $_SESSION['success'] = "Produto <strong>$nome</strong> atualizado com sucesso!";
                header('Location: ' . BASE_URL . '/admin/produtos/index.php');
                exit;
            } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
        }
    }
    $p = array_merge($p, compact('sku','nome','descricao_curta','descricao','preco','preco_promocional','whatsapp_vendedor','imagem_principal','meta_description','meta_keywords','destaque','ativo'));
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--muted:#6b7280}
.pg-wrap{padding:28px;max-width:900px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:8px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.meta-bar{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;padding:12px 16px;background:#fffbeb;border-radius:8px;border:1px solid #fde68a;font-size:12px;color:#92400e}
.meta-bar span{display:flex;align-items:center;gap:5px}
.sku-badge{font-family:monospace;background:#ede9fe;color:#5b21b6;padding:4px 12px;border-radius:8px;font-size:14px;font-weight:700;letter-spacing:1px}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:28px;margin-bottom:18px}
.section-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin:0 0 16px;padding-bottom:8px;border-bottom:1px solid #f3f4f6}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fg{display:flex;flex-direction:column;gap:6px}
.fg.full{grid-column:1/-1}
.fg label{font-size:13px;font-weight:600;color:#111827}
.req{color:var(--err)}
.fc{padding:11px 14px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
textarea.fc{resize:vertical;min-height:90px}
.hint{font-size:11px;color:var(--muted)}
.img-prev{max-width:100%;max-height:160px;border-radius:8px;border:2px solid var(--border);object-fit:cover;margin-top:10px;display:block}
.upzone{border:2px dashed var(--border);border-radius:10px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;position:relative;transition:all .2s}
.upzone:hover{border-color:var(--accent);background:#eff6ff}
.upzone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.cats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;margin-top:4px}
.cat-check{display:flex;align-items:center;gap:8px;padding:9px 12px;border:2px solid var(--border);border-radius:8px;cursor:pointer;transition:all .15s;background:#fafafa}
.cat-check:hover{border-color:var(--accent);background:#eff6ff}
.cat-check input{width:16px;height:16px;cursor:pointer;accent-color:var(--accent)}
.cat-check.checked{border-color:var(--accent);background:#eff6ff}
.cat-check span{font-size:13px;font-weight:500;color:#374151}
.tog-wrap{display:flex;align-items:center;gap:10px}
.tog{position:relative;width:44px;height:24px;flex-shrink:0}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:var(--ok)}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.tog-label{font-size:13px;font-weight:600;color:#111827}
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err);padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.preco-wrap{position:relative}
.preco-wrap span{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;font-weight:600;pointer-events:none}
.preco-wrap input{padding-left:34px}
.wpp-wrap{position:relative}
.wpp-wrap svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#25d366;pointer-events:none}
.wpp-wrap input{padding-left:38px}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/produtos/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>✏️ Editar Produto</h2>
        <span class="sku-badge"><?= htmlspecialchars($p['sku']) ?></span>
    </div>

    <div class="meta-bar">
        <span>🆔 ID: #<?= $p['id'] ?></span>
        <span>🔗 URL: /produto/<?= htmlspecialchars($p['sku']) ?></span>
        <span>📅 Criado: <?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></span>
        <span>🔄 Atualizado: <?= date('d/m/Y H:i', strtotime($p['updated_at'])) ?></span>
        <a href="<?= BASE_URL ?>/produto/<?= urlencode($p['sku']) ?>" target="_blank" style="color:#92400e;font-weight:700;margin-left:auto">👁️ Ver no site →</a>
    </div>

    <?php if ($error): ?><div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
    <div class="card">
        <p class="section-title">Identificação</p>
        <div class="form-grid">
            <div class="fg">
                <label>SKU <span class="req">*</span></label>
                <input type="text" name="sku" id="inp-sku" class="fc" value="<?= htmlspecialchars($p['sku']) ?>" required style="text-transform:uppercase;font-family:monospace;font-weight:700;letter-spacing:1px">
                <span class="hint">URL do produto: /produto/<strong id="sku-url-prev"><?= htmlspecialchars($p['sku']) ?></strong></span>
            </div>
            <div class="fg">
                <label>Nome do Produto <span class="req">*</span></label>
                <input type="text" name="nome" class="fc" value="<?= htmlspecialchars($p['nome']) ?>" required placeholder="Nome do produto">
            </div>
            <div class="fg full">
                <label>Descrição Curta</label>
                <input type="text" name="descricao_curta" class="fc" value="<?= htmlspecialchars($p['descricao_curta'] ?? '') ?>" maxlength="120" placeholder="Resumo exibido na listagem">
            </div>
        </div>
    </div>

    <div class="card">
        <p class="section-title">Descrição Completa</p>
        <textarea name="descricao" class="fc" style="min-height:160px" placeholder="Descrição detalhada..."><?= htmlspecialchars($p['descricao'] ?? '') ?></textarea>
    </div>

    <div class="card">
        <p class="section-title">Preço, Vendedor & Imagem</p>
        <div class="form-grid">
            <div class="fg">
                <label>Preço</label>
                <div class="preco-wrap"><span>R$</span>
                    <input type="number" name="preco" class="fc" value="<?= htmlspecialchars($p['preco'] ?? '') ?>" min="0" step="0.01" placeholder="0,00">
                </div>
            </div>
            <div class="fg">
                <label>Preço Promocional</label>
                <div class="preco-wrap"><span>R$</span>
                    <input type="number" name="preco_promocional" class="fc" value="<?= htmlspecialchars($p['preco_promocional'] ?? '') ?>" min="0" step="0.01" placeholder="0,00">
                </div>
            </div>
            <div class="fg full">
                <label>WhatsApp do Vendedor</label>
                <div class="wpp-wrap">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.522 5.85L.057 23.776a.5.5 0 0 0 .608.637l6.108-1.598A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.8 9.8 0 0 1-5.032-1.387l-.36-.214-3.733.977.998-3.647-.235-.376A9.818 9.818 0 1 1 12 21.818z"/>
                    </svg>
                    <input type="text" name="whatsapp_vendedor" class="fc" value="<?= htmlspecialchars($p['whatsapp_vendedor'] ?? '') ?>" placeholder="5544999999999" maxlength="20" inputmode="numeric">
                </div>
                <span class="hint">Código do país + DDD + número, somente dígitos. Ex: <strong>5544999999999</strong>. Deixe vazio para não exibir o botão.</span>
            </div>
            <div class="fg full">
                <label>Imagem Principal</label>
                <?php if (!empty($p['imagem_principal'])): ?>
                    <div id="img-atual" style="margin-bottom:8px;display:flex;align-items:center;gap:10px;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:10px;background:#fafafa">
                        <img src="<?= htmlspecialchars($p['imagem_principal']) ?>" style="width:56px;height:56px;object-fit:cover;border-radius:8px;border:1.5px solid #e5e7eb;flex-shrink:0">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:2px">Imagem atual</div>
                            <div style="font-size:11px;color:#9ca3af;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars(basename($p['imagem_principal'])) ?></div>
                        </div>
                        <button type="button" onclick="removerImagem()" style="display:flex;align-items:center;gap:5px;padding:6px 12px;background:#fef2f2;color:#991b1b;border:1.5px solid #fecaca;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;transition:background .15s" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            Remover imagem
                        </button>
                    </div>
                    <input type="hidden" name="remover_imagem" id="inp-remover" value="" disabled>
                <?php endif; ?>
                <div class="upzone">
                    <input type="file" accept="image/*" onchange="uploadImg(event)">
                    <div style="font-size:26px;margin-bottom:6px">🖼️</div>
                    <div style="font-size:13px;color:#6b7280">Clique ou arraste para substituir</div>
                </div>
                <input type="hidden" name="imagem_principal" id="img-val" value="<?= htmlspecialchars($p['imagem_principal'] ?? '') ?>">
                <img id="img-prev" class="img-prev" style="display:none">
            </div>
        </div>
    </div>

    <div class="card">
        <p class="section-title">Categorias de Produto</p>
        <p style="font-size:13px;color:#6b7280;margin-bottom:12px">Este produto aparecerá em todas as categorias marcadas:</p>
        <?php if (empty($categorias)): ?>
            <div style="background:#fffbeb;padding:12px 16px;border-radius:8px;border-left:4px solid #f59e0b;font-size:13px;color:#92400e">⚠️ Nenhuma categoria de produto ativa.</div>
        <?php else: ?>
        <div class="cats-grid">
            <?php foreach ($categorias as $c): ?>
            <label class="cat-check <?= in_array($c['id'], $cats_sel) ? 'checked' : '' ?>">
                <input type="checkbox" name="categorias[]" value="<?= $c['id'] ?>" <?= in_array($c['id'], $cats_sel) ? 'checked' : '' ?> onchange="this.closest('.cat-check').classList.toggle('checked',this.checked)">
                <span>📦 <?= htmlspecialchars($c['nome']) ?></span>
            </label>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <p class="section-title">SEO & Opções</p>
        <div class="form-grid">
            <div class="fg full">
                <label>Meta Description</label>
                <input type="text" name="meta_description" class="fc" value="<?= htmlspecialchars($p['meta_description'] ?? '') ?>" maxlength="160" placeholder="Para o Google (máx. 160 chars)">
            </div>
            <div class="fg full">
                <label>Meta Keywords</label>
                <input type="text" name="meta_keywords" class="fc" value="<?= htmlspecialchars($p['meta_keywords'] ?? '') ?>" placeholder="palavra1, palavra2">
            </div>
            <div class="fg full" style="display:flex;gap:24px;flex-wrap:wrap">
                <div class="tog-wrap">
                    <label class="tog"><input type="checkbox" name="destaque" <?= $p['destaque'] ? 'checked' : '' ?>><span class="tog-s"></span></label>
                    <span class="tog-label">⭐ Produto em destaque</span>
                </div>
                <div class="tog-wrap">
                    <label class="tog"><input type="checkbox" name="ativo" <?= $p['ativo'] ? 'checked' : '' ?>><span class="tog-s"></span></label>
                    <span class="tog-label">Produto ativo (visível no site)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-dark">💾 Salvar Alterações</button>
        <a href="<?= BASE_URL ?>/admin/produtos/index.php" class="btn btn-ghost">Cancelar</a>
    </div>
    </form>
</div>
</main>

<script>
const UPLOAD_URL = '<?= BASE_URL ?>/admin/paginas/upload.php';

document.getElementById('inp-sku').addEventListener('input', function(){
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9-]/g,'');
    document.getElementById('sku-url-prev').textContent = this.value || '...';
});

function uploadImg(e){
    const fd=new FormData(); fd.append('imagem',e.target.files[0]);
    fetch(UPLOAD_URL,{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
        if(d.success){ document.getElementById('img-val').value=d.path;
        const p=document.getElementById('img-prev'); p.src=d.path; p.style.display='block'; }
        else alert('Erro no upload');
    });
}

function removerImagem(){
    if(!confirm('Remover a imagem deste produto?')) return;
    document.getElementById('img-atual').style.opacity = '0.4';
    document.getElementById('img-atual').style.pointerEvents = 'none';
    const inp = document.getElementById('inp-remover');
    inp.disabled = false;
    inp.value = '1';
    // Limpa qualquer novo upload pendente também
    document.getElementById('img-val').value = '';
    document.getElementById('img-prev').style.display = 'none';
}

document.querySelector('[name=whatsapp_vendedor]').addEventListener('input', function(){
    this.value = this.value.replace(/\D/g,'');
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>