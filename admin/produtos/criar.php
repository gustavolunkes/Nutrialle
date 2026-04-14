<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Novo Produto';
$current_module = 'produtos';
$current_page   = 'produtos-criar';
$error = '';
$db = getDB();

$categorias = $db->query("SELECT id, nome FROM categorias_produtos WHERE ativo = 1 ORDER BY nome")->fetchAll();

function gerarSKU(PDO $db): string {
    do {
        $sku = 'PRD-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $chk = $db->prepare("SELECT id FROM produtos WHERE sku = ?");
        $chk->execute([$sku]);
    } while ($chk->fetch());
    return $sku;
}
$sku_sugerido = gerarSKU($db);

$nome = $sku = $descricao_curta = $descricao = $imagem_principal = '';
$preco = $preco_promocional = $whatsapp_vendedor = '';
$meta_description = $meta_keywords = '';
$destaque = 0; $ativo = 1;
$cats_sel = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku               = strtoupper(trim($_POST['sku'] ?? ''));
    $nome              = trim($_POST['nome'] ?? '');
    $descricao_curta   = trim($_POST['descricao_curta'] ?? '');
    $descricao         = trim($_POST['descricao'] ?? '');
    $preco             = trim($_POST['preco'] ?? '');
    $preco_promocional = trim($_POST['preco_promocional'] ?? '');
    $whatsapp_vendedor = preg_replace('/\D/', '', trim($_POST['whatsapp_vendedor'] ?? ''));
    $imagem_principal  = trim($_POST['imagem_principal'] ?? '');
    $meta_description  = trim($_POST['meta_description'] ?? '');
    $meta_keywords     = trim($_POST['meta_keywords'] ?? '');
    $destaque          = isset($_POST['destaque']) ? 1 : 0;
    $ativo             = isset($_POST['ativo']) ? 1 : 0;
    $cats_sel          = array_map('intval', $_POST['categorias'] ?? []);

    if (!$sku)  { $error = 'O SKU é obrigatório.'; }
    elseif (!$nome) { $error = 'O nome é obrigatório.'; }
    else {
        $chk = $db->prepare("SELECT id FROM produtos WHERE sku = ?");
        $chk->execute([$sku]);
        if ($chk->fetch()) { $error = 'Este SKU já está em uso.'; }
        else {
            try {
                $db->prepare("INSERT INTO produtos (sku,nome,descricao_curta,descricao,preco,preco_promocional,whatsapp_vendedor,imagem_principal,meta_description,meta_keywords,destaque,ativo)
                              VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
                   ->execute([$sku, $nome, $descricao_curta, $descricao,
                              $preco ?: null, $preco_promocional ?: null,
                              $whatsapp_vendedor ?: null,
                              $imagem_principal ?: null,
                              $meta_description, $meta_keywords, $destaque, $ativo]);
                $pid = $db->lastInsertId();
                foreach ($cats_sel as $cid) {
                    if ($cid) {
                        $db->prepare("INSERT IGNORE INTO produto_categorias (produto_id, categoria_produto_id) VALUES (?,?)")
                           ->execute([$pid, $cid]);
                    }
                }
                $_SESSION['success'] = "Produto <strong>$nome</strong> (SKU: $sku) criado com sucesso!";
                header('Location: ' . BASE_URL . '/admin/produtos/index.php');
                exit;
            } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--ok:#10b981;--err:#ef4444;--border:#e5e7eb;--muted:#6b7280}
.pg-wrap{padding:28px;max-width:900px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:24px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
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
.sku-box{display:flex;gap:8px;align-items:flex-start}
.sku-box input{flex:1}
.btn-gen{padding:11px 14px;background:#f3f4f6;border:2px solid var(--border);border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;color:#374151;white-space:nowrap;transition:all .15s}
.btn-gen:hover{background:var(--border)}
.upzone{border:2px dashed var(--border);border-radius:10px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;position:relative;transition:all .2s}
.upzone:hover{border-color:var(--accent);background:#eff6ff}
.upzone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.img-prev{max-width:100%;max-height:160px;border-radius:8px;border:2px solid var(--border);object-fit:cover;margin-top:10px;display:block}
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
        <h2>🛍️ Novo Produto</h2>
    </div>

    <?php if ($error): ?><div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
    <!-- IDENTIFICAÇÃO -->
    <div class="card">
        <p class="section-title">Identificação</p>
        <div class="form-grid">
            <div class="fg">
                <label>SKU <span class="req">*</span></label>
                <div class="sku-box">
                    <input type="text" name="sku" id="inp-sku" class="fc" value="<?= htmlspecialchars($sku ?: $sku_sugerido) ?>" required placeholder="PRD-ABC123" style="text-transform:uppercase">
                    <button type="button" class="btn-gen" onclick="gerarSku()">🔄 Gerar</button>
                </div>
                <span class="hint">Código único do produto. URL: /produto/<strong id="sku-url-prev"><?= htmlspecialchars($sku ?: $sku_sugerido) ?></strong></span>
            </div>
            <div class="fg">
                <label>Nome do Produto <span class="req">*</span></label>
                <input type="text" name="nome" class="fc" value="<?= htmlspecialchars($nome) ?>" required placeholder="Ex: Parafuso Sextavado M8">
            </div>
            <div class="fg full">
                <label>Descrição Curta</label>
                <input type="text" name="descricao_curta" class="fc" value="<?= htmlspecialchars($descricao_curta) ?>" placeholder="Resumo exibido na listagem (máx. 120 chars)" maxlength="120">
                <span class="hint">Aparece nos cards da listagem de categoria</span>
            </div>
        </div>
    </div>

    <!-- DESCRIÇÃO -->
    <div class="card">
        <p class="section-title">Descrição Completa</p>
        <textarea name="descricao" class="fc" style="min-height:160px" placeholder="Descrição detalhada do produto. Aparece na página de detalhe."><?= htmlspecialchars($descricao) ?></textarea>
    </div>

    <!-- PREÇO, WHATSAPP & IMAGEM -->
    <div class="card">
        <p class="section-title">Preço, Vendedor & Imagem</p>
        <div class="form-grid">
            <div class="fg">
                <label>Preço</label>
                <div class="preco-wrap"><span>R$</span>
                    <input type="number" name="preco" class="fc" value="<?= htmlspecialchars($preco) ?>" min="0" step="0.01" placeholder="0,00">
                </div>
            </div>
            <div class="fg">
                <label>Preço Promocional</label>
                <div class="preco-wrap"><span>R$</span>
                    <input type="number" name="preco_promocional" class="fc" value="<?= htmlspecialchars($preco_promocional) ?>" min="0" step="0.01" placeholder="0,00">
                </div>
                <span class="hint">Deixe vazio se não houver promoção</span>
            </div>
            <div class="fg full">
                <label>WhatsApp do Vendedor</label>
                <div class="wpp-wrap">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.522 5.85L.057 23.776a.5.5 0 0 0 .608.637l6.108-1.598A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.8 9.8 0 0 1-5.032-1.387l-.36-.214-3.733.977.998-3.647-.235-.376A9.818 9.818 0 1 1 12 21.818z"/>
                    </svg>
                    <input type="text" name="whatsapp_vendedor" class="fc" value="<?= htmlspecialchars($whatsapp_vendedor) ?>" placeholder="5544999999999" maxlength="20" inputmode="numeric">
                </div>
                <span class="hint">Código do país + DDD + número, somente dígitos. Ex: <strong>5544999999999</strong>. Deixe vazio para não exibir o botão.</span>
            </div>
            <div class="fg full">
                <label>Imagem Principal</label>
                <div class="upzone">
                    <input type="file" accept="image/*" onchange="uploadImg(event)">
                    <div style="font-size:28px;margin-bottom:6px">🖼️</div>
                    <div style="font-size:13px;color:#6b7280">Clique ou arraste para enviar</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px">PNG, JPG, WEBP — máx. 5MB</div>
                </div>
                <input type="hidden" name="imagem_principal" id="img-val" value="<?= htmlspecialchars($imagem_principal) ?>">
                <img id="img-prev" class="img-prev" style="<?= $imagem_principal ? '' : 'display:none' ?>" src="<?= htmlspecialchars($imagem_principal) ?>">
            </div>
        </div>
    </div>

    <!-- CATEGORIAS -->
    <div class="card">
        <p class="section-title">Categorias de Produto</p>
        <p style="font-size:13px;color:#6b7280;margin-bottom:12px">Selecione uma ou mais categorias em que este produto deve aparecer:</p>
        <?php if (empty($categorias)): ?>
            <div style="background:#fffbeb;padding:12px 16px;border-radius:8px;border-left:4px solid #f59e0b;font-size:13px;color:#92400e">
                ⚠️ Nenhuma categoria de produto ativa. <a href="<?= BASE_URL ?>/admin/categorias_produtos/criar.php" style="color:#92400e;font-weight:700">Criar categoria →</a>
            </div>
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

    <!-- SEO & OPÇÕES -->
    <div class="card">
        <p class="section-title">SEO & Opções</p>
        <div class="form-grid">
            <div class="fg full">
                <label>Meta Description</label>
                <input type="text" name="meta_description" class="fc" value="<?= htmlspecialchars($meta_description) ?>" maxlength="160" placeholder="Para o Google (máx. 160 chars)">
            </div>
            <div class="fg full">
                <label>Meta Keywords</label>
                <input type="text" name="meta_keywords" class="fc" value="<?= htmlspecialchars($meta_keywords) ?>" placeholder="palavra1, palavra2">
            </div>
            <div class="fg full" style="display:flex;gap:24px;flex-wrap:wrap">
                <div class="tog-wrap">
                    <label class="tog"><input type="checkbox" name="destaque" <?= $destaque ? 'checked' : '' ?>><span class="tog-s"></span></label>
                    <span class="tog-label">⭐ Produto em destaque</span>
                </div>
                <div class="tog-wrap">
                    <label class="tog"><input type="checkbox" name="ativo" <?= $ativo ? 'checked' : '' ?>><span class="tog-s"></span></label>
                    <span class="tog-label">Produto ativo (visível no site)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-dark">✅ Criar Produto</button>
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

function gerarSku(){
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let r = 'PRD-';
    for(let i=0;i<6;i++) r += chars[Math.floor(Math.random()*chars.length)];
    document.getElementById('inp-sku').value = r;
    document.getElementById('sku-url-prev').textContent = r;
}

function uploadImg(e){
    const fd = new FormData(); fd.append('imagem', e.target.files[0]);
    fetch(UPLOAD_URL, {method:'POST', body:fd}).then(r=>r.json()).then(d=>{
        if(d.success){
            document.getElementById('img-val').value = d.path;
            const p = document.getElementById('img-prev'); p.src = d.path; p.style.display = 'block';
        } else alert('Erro no upload');
    });
}

// Formata campo WhatsApp: aceita só dígitos
document.querySelector('[name=whatsapp_vendedor]').addEventListener('input', function(){
    this.value = this.value.replace(/\D/g,'');
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>