<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$page_title     = 'Novo Post do Blog';
$current_module = 'blog';
$current_page   = 'blog-posts';

$error = '';
$db    = getDB();

$categorias = $db->query("SELECT id, nome FROM blog_categorias WHERE ativo = 1 ORDER BY nome ASC")->fetchAll();

$titulo = $slug = $resumo = $conteudo = $imagem_destaque = $alt_imagem = '';
$meta_description = $meta_keywords = $autor = 'Equipe Nutrialle';
$categoria_id = 0;
$tempo_leitura = 3;
$ativo = $destaque = 0;
$publicado_em = date('Y-m-d\TH:i');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria_id     = intval($_POST['categoria_id'] ?? 0);
    $titulo           = trim($_POST['titulo']           ?? '');
    $slug             = trim($_POST['slug']             ?? '');
    $resumo           = trim($_POST['resumo']           ?? '');
    $conteudo         = trim($_POST['conteudo']         ?? '');
    $imagem_destaque  = trim($_POST['imagem_destaque']  ?? '');
    $alt_imagem       = trim($_POST['alt_imagem']       ?? '');
    $tempo_leitura    = intval($_POST['tempo_leitura']  ?? 3);
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords    = trim($_POST['meta_keywords']    ?? '');
    $autor            = trim($_POST['autor']            ?? 'Equipe Nutrialle');
    $publicado_em     = trim($_POST['publicado_em']     ?? date('Y-m-d\TH:i'));
    $ativo            = isset($_POST['ativo'])     ? 1 : 0;
    $destaque         = isset($_POST['destaque'])  ? 1 : 0;

    if (!$titulo)   { $error = 'O título é obrigatório.'; }
    elseif (!$slug) { $error = 'O slug é obrigatório.'; }
    elseif (!$conteudo) { $error = 'O conteúdo é obrigatório.'; }
    else {
        try {
            $chk = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
            $chk->execute([$slug]);
            if ($chk->fetch()) {
                $error = 'Este slug já está em uso.';
            } else {
                $pub = str_replace('T', ' ', $publicado_em);
                $db->prepare("INSERT INTO blog_posts
                    (categoria_id, titulo, slug, resumo, conteudo, imagem_destaque, alt_imagem,
                     tempo_leitura, meta_description, meta_keywords, autor, publicado_em, ativo, destaque)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
                   ->execute([
                        $categoria_id ?: null, $titulo, $slug, $resumo, $conteudo,
                        $imagem_destaque ?: null, $alt_imagem ?: null,
                        $tempo_leitura, $meta_description, $meta_keywords,
                        $autor, $pub, $ativo, $destaque
                   ]);
                $_SESSION['success'] = "Post <strong>$titulo</strong> criado com sucesso!";
                header('Location: ' . BASE_URL . '/admin/blog/posts/index.php');
                exit;
            }
        } catch (Exception $e) { $error = 'Erro: ' . $e->getMessage(); }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
.pg-wrap{padding:28px;max-width:960px}
.pg-header{display:flex;align-items:center;gap:14px;margin-bottom:24px}
.pg-header h2{font-size:20px;font-weight:700;color:#00071c}
.layout-grid{display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start}
.card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:24px;margin-bottom:20px}
.section-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin:0 0 16px;padding-bottom:8px;border-bottom:1px solid #f3f4f6}
.fg{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
.fg label{font-size:13px;font-weight:600;color:#111827}
.req{color:#ef4444}
.fc{padding:11px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s,box-shadow .2s;width:100%;box-sizing:border-box}
.fc:focus{outline:none;border-color:#4f6ef7;background:#fff;box-shadow:0 0 0 3px rgba(79,110,247,.1)}
select.fc{cursor:pointer}
textarea.fc{resize:vertical}
.hint{font-size:11px;color:#9ca3af}
.slug-preview{display:inline-flex;align-items:center;gap:4px;margin-top:4px;font-size:12px;color:#6b7280;background:#f3f4f6;border-radius:6px;padding:3px 9px;border:1px solid #e5e7eb;font-family:monospace}
.slug-preview b{color:#4f6ef7}
.tog-wrap{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.tog{position:relative;width:44px;height:24px;flex-shrink:0}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:#10b981}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.tog-label{font-size:13px;font-weight:600;color:#111827}
.form-actions{display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:#00071c;color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}.btn-ghost:hover{background:#e5e7eb}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid #ef4444;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px}
.char-count{font-size:11px;text-align:right;color:#9ca3af;margin-top:2px}
.char-count.warn{color:#f59e0b}
.char-count.over{color:#ef4444}
/* editor simples */
#inp-conteudo{min-height:300px;font-family:inherit}
</style>

<main class="content">
<div class="pg-wrap">
    <div class="pg-header">
        <a href="<?= BASE_URL ?>/admin/blog/posts/index.php" class="btn btn-ghost">← Voltar</a>
        <h2>📝 Novo Post do Blog</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="form-post">
    <div class="layout-grid">

        <!-- COLUNA PRINCIPAL -->
        <div>
            <div class="card">
                <p class="section-title">Conteúdo do Post</p>

                <div class="fg">
                    <label>Título <span class="req">*</span></label>
                    <input type="text" name="titulo" id="inp-titulo" class="fc" value="<?= htmlspecialchars($titulo) ?>" required placeholder="Ex: Como melhorar a silagem em 5 passos">
                </div>

                <div class="fg">
                    <label>Slug (URL) <span class="req">*</span></label>
                    <input type="text" name="slug" id="inp-slug" class="fc" value="<?= htmlspecialchars($slug) ?>" required placeholder="como-melhorar-silagem-5-passos">
                    <div class="slug-preview">🔗 blog/<b id="slug-val"><?= $slug ?: '...' ?></b></div>
                </div>

                <div class="fg">
                    <label>Resumo / Descrição curta</label>
                    <textarea name="resumo" class="fc" rows="3" id="inp-resumo" placeholder="Breve descrição exibida na listagem do blog..."><?= htmlspecialchars($resumo) ?></textarea>
                    <div class="char-count" id="cc-resumo">0 / 300</div>
                </div>

                <div class="fg">
                    <label>Conteúdo <span class="req">*</span></label>
                    <textarea name="conteudo" class="fc" id="inp-conteudo" required placeholder="<p>Escreva o conteúdo completo do post em HTML...</p>"><?= htmlspecialchars($conteudo) ?></textarea>
                    <span class="hint">Suporta HTML. Use &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;strong&gt;, &lt;em&gt;, etc.</span>
                </div>
            </div>

            <!-- SEO -->
            <div class="card">
                <p class="section-title">SEO</p>
                <div class="fg">
                    <label>Meta Description</label>
                    <textarea name="meta_description" class="fc" rows="2" id="inp-metadesc" placeholder="Descrição para mecanismos de busca (até 160 caracteres)"><?= htmlspecialchars($meta_description) ?></textarea>
                    <div class="char-count" id="cc-metadesc">0 / 160</div>
                </div>
                <div class="fg">
                    <label>Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="fc" value="<?= htmlspecialchars($meta_keywords) ?>" placeholder="silagem, probióticos, bovinos">
                    <span class="hint">Separe com vírgulas</span>
                </div>
            </div>
        </div>

        <!-- COLUNA LATERAL -->
        <div>
            <div class="card">
                <p class="section-title">Publicação</p>

                <div class="tog-wrap">
                    <label class="tog"><input type="checkbox" name="ativo" <?= $ativo ? 'checked' : '' ?>><span class="tog-s"></span></label>
                    <span class="tog-label">Publicado</span>
                </div>
                <div class="tog-wrap">
                    <label class="tog"><input type="checkbox" name="destaque" <?= $destaque ? 'checked' : '' ?>><span class="tog-s"></span></label>
                    <span class="tog-label">⭐ Destaque</span>
                </div>

                <div class="fg">
                    <label>Data de publicação</label>
                    <input type="datetime-local" name="publicado_em" class="fc" value="<?= htmlspecialchars($publicado_em) ?>">
                </div>

                <div class="fg">
                    <label>Autor</label>
                    <input type="text" name="autor" class="fc" value="<?= htmlspecialchars($autor) ?>">
                </div>

                <div class="fg">
                    <label>Tempo de leitura (min)</label>
                    <input type="number" name="tempo_leitura" class="fc" min="1" max="60" value="<?= $tempo_leitura ?>">
                </div>
            </div>

            <div class="card">
                <p class="section-title">Categoria</p>
                <div class="fg">
                    <select name="categoria_id" class="fc">
                        <option value="">— Sem categoria —</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $categoria_id == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card">
                <p class="section-title">Imagem de Destaque</p>
                <div class="fg">
                    <label>URL da Imagem</label>
                    <input type="text" name="imagem_destaque" class="fc" value="<?= htmlspecialchars($imagem_destaque) ?>" placeholder="/uploads/blog/imagem.jpg">
                </div>
                <div class="fg">
                    <label>Texto alternativo (alt)</label>
                    <input type="text" name="alt_imagem" class="fc" value="<?= htmlspecialchars($alt_imagem) ?>" placeholder="Descrição da imagem para acessibilidade">
                </div>
            </div>

            <div class="form-actions" style="flex-direction:column">
                <button type="submit" class="btn btn-dark" style="justify-content:center">✅ Publicar Post</button>
                <a href="<?= BASE_URL ?>/admin/blog/posts/index.php" class="btn btn-ghost" style="justify-content:center">Cancelar</a>
            </div>
        </div>

    </div>
    </form>
</div>
</main>

<script>
const inpTitulo  = document.getElementById('inp-titulo');
const inpSlug    = document.getElementById('inp-slug');
const slugVal    = document.getElementById('slug-val');
const inpResumo  = document.getElementById('inp-resumo');
const ccResumo   = document.getElementById('cc-resumo');
const inpMeta    = document.getElementById('inp-metadesc');
const ccMeta     = document.getElementById('cc-metadesc');

function toSlug(s) {
    return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
}

function updateCharCount(el, counter, max) {
    const len = el.value.length;
    counter.textContent = len + ' / ' + max;
    counter.className = 'char-count' + (len > max ? ' over' : len > max * 0.85 ? ' warn' : '');
}

inpTitulo.addEventListener('input', () => {
    const s = toSlug(inpTitulo.value);
    inpSlug.value = s;
    slugVal.textContent = s || '...';
});
inpSlug.addEventListener('input', () => { slugVal.textContent = inpSlug.value || '...'; });
inpResumo.addEventListener('input', () => updateCharCount(inpResumo, ccResumo, 300));
inpMeta.addEventListener('input', () => updateCharCount(inpMeta, ccMeta, 160));
updateCharCount(inpResumo, ccResumo, 300);
updateCharCount(inpMeta, ccMeta, 160);
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
