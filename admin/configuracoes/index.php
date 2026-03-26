<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Configurações do Site';
$current_module = 'configuracoes';
$current_page   = 'configuracoes';

$db      = getDB();
$error   = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Garante que sempre exista exatamente 1 registro
$cfg = $db->query("SELECT * FROM configuracoes_site WHERE id = 1 LIMIT 1")->fetch();
if (!$cfg) {
    $db->exec("INSERT INTO configuracoes_site (id) VALUES (1)");
    $cfg = $db->query("SELECT * FROM configuracoes_site WHERE id = 1 LIMIT 1")->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza entradas
    $meta_descricao     = trim(strip_tags($_POST['meta_descricao']     ?? ''));
    $meta_keywords      = trim(strip_tags($_POST['meta_keywords']      ?? ''));
    $meta_autor         = trim(strip_tags($_POST['meta_autor']         ?? ''));
    $google_analytics   = trim(strip_tags($_POST['google_analytics']   ?? ''));
    $favicon_url        = trim(strip_tags($_POST['favicon_url']        ?? ''));
    $og_titulo          = trim(strip_tags($_POST['og_titulo']          ?? ''));
    $og_descricao       = trim(strip_tags($_POST['og_descricao']       ?? ''));
    $og_imagem          = trim(strip_tags($_POST['og_imagem']          ?? ''));
    $robots_index       = isset($_POST['robots_index'])  ? 1 : 0;
    $robots_follow      = isset($_POST['robots_follow']) ? 1 : 0;

    // Sanitiza Google Analytics ID (apenas letras, números, hífens)
    $google_analytics = preg_replace('/[^A-Za-z0-9\-]/', '', $google_analytics);

    try {
        $stmt = $db->prepare("
            UPDATE configuracoes_site SET
                meta_descricao   = :meta_descricao,
                meta_keywords    = :meta_keywords,
                meta_autor       = :meta_autor,
                google_analytics = :google_analytics,
                favicon_url      = :favicon_url,
                og_titulo        = :og_titulo,
                og_descricao     = :og_descricao,
                og_imagem        = :og_imagem,
                robots_index     = :robots_index,
                robots_follow    = :robots_follow
            WHERE id = 1
        ");
        $stmt->execute(compact(
            'meta_descricao','meta_keywords','meta_autor','google_analytics',
            'favicon_url','og_titulo','og_descricao','og_imagem',
            'robots_index','robots_follow'
        ));
        $_SESSION['success'] = 'Configurações salvas com sucesso!';
        header('Location: ' . BASE_URL . '/admin/configuracoes/index.php');
        exit;
    } catch (Exception $e) {
        $error = 'Erro ao salvar: ' . $e->getMessage();
    }
}

// Valores para exibição no form (prioriza POST para manter preenchimento em caso de erro)
$v = [];
$campos = ['meta_descricao','meta_keywords','meta_autor',
           'google_analytics','favicon_url','og_titulo','og_descricao',
           'og_imagem','robots_index','robots_follow'];
foreach ($campos as $c) {
    $v[$c] = $_POST[$c] ?? $cfg[$c] ?? '';
}

function hce($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{--brand:#00071c;--accent:#4f6ef7;--accent-s:rgba(79,110,247,.1);--ok:#10b981;--err:#ef4444;--warn:#f59e0b;--border:#e5e7eb;--card:#fff;--text:#111827;--muted:#6b7280;--r:12px;--sh:0 2px 10px rgba(0,0,0,.07)}
.wrap{padding:28px;max-width:860px}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-header h2{font-size:20px;font-weight:700;color:var(--brand)}
.tabs{display:flex;gap:4px;border-bottom:2px solid var(--border);margin-bottom:24px;overflow-x:auto;padding-bottom:0}
.tab{padding:10px 18px;font-size:13px;font-weight:600;color:var(--muted);background:none;border:none;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;white-space:nowrap;transition:all .15s}
.tab.active{color:var(--accent);border-bottom-color:var(--accent)}
.tab:hover:not(.active){color:var(--text)}
.tab-panel{display:none}.tab-panel.active{display:block}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:20px}
.card-hd{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);background:#fafafa}
.card-hd h3{font-size:14px;font-weight:700;color:var(--brand);margin:0}
.card-hd p{font-size:12px;color:var(--muted);margin:2px 0 0}
.card-body{padding:24px;display:flex;flex-direction:column;gap:18px}
.btn{display:inline-flex;align-items:center;gap:5px;padding:9px 18px;border:none;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.alert-ok{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.alert-info{padding:12px 16px;border-radius:8px;font-size:12px;margin-bottom:0;background:#eff6ff;color:#1e40af;border-left:4px solid var(--accent)}
.fg{display:flex;flex-direction:column;gap:6px}
.fg label{font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px}
.fg .hint{font-size:11px;color:var(--muted);margin-top:-2px}
.fg input[type=text],.fg textarea{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:#fff;box-sizing:border-box;transition:border .15s;font-family:inherit}
.fg input[type=text]:focus,.fg textarea:focus{border-color:var(--accent);outline:none;box-shadow:0 0 0 3px var(--accent-s)}
.fg textarea{min-height:90px;resize:vertical;line-height:1.5}
.char-bar{display:flex;justify-content:flex-end;font-size:11px;color:var(--muted)}
.char-bar span.over{color:var(--err);font-weight:700}
.toggle-wrap{display:flex;align-items:center;gap:10px}
.toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.slider{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:.3s}
.slider::before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.toggle input:checked + .slider{background:var(--ok)}
.toggle input:checked + .slider::before{transform:translateX(20px)}
.toggle-label{font-size:13px;color:var(--text)}
.form-actions{display:flex;gap:10px;margin-top:8px;align-items:center;padding-top:16px;border-top:1px solid var(--border)}
.preview-box{background:#f9fafb;border:1px solid var(--border);border-radius:8px;padding:14px 16px;font-size:12px;color:var(--muted);font-family:'Courier New',monospace;line-height:1.8;white-space:pre-wrap;word-break:break-all}
.preview-box .pk{color:#4f6ef7}.preview-box .pv{color:#059669}.preview-box .pc{color:#9ca3af}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:640px){.grid-2{grid-template-columns:1fr}}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--muted);margin:0}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600}
.badge-ok{background:#d1fae5;color:#065f46}
.badge-muted{background:#f3f4f6;color:var(--muted)}
</style>

<main class="content">
<div class="wrap">

    <div class="pg-header">
        <h2>⚙️ Configurações do Site</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert-ok">✅ <?= hce($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?= hce($error) ?></div>
    <?php endif; ?>

    <!-- Tabs de navegação -->
    <div class="tabs">
        <button class="tab active" onclick="switchTab('seo', this)">🔍 SEO &amp; Meta</button>
        <button class="tab" onclick="switchTab('identidade', this)">🏷️ Identidade</button>
        <button class="tab" onclick="switchTab('social', this)">📣 Open Graph</button>
        <button class="tab" onclick="switchTab('rastreamento', this)">📊 Rastreamento</button>
        <button class="tab" onclick="switchTab('robots', this)">🤖 Robôs</button>
    </div>

    <form method="POST" id="mainForm">

    <!-- ─── TAB: SEO & META ─────────────────────────────────── -->
    <div class="tab-panel active" id="tab-seo">
        <div class="card">
            <div class="card-hd">
                <div>
                    <h3>🔍 Meta Descrição Global</h3>
                    <p>Aparece nos resultados do Google quando a página não tem uma descrição própria</p>
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label for="meta_descricao">Meta Descrição</label>
                    <span class="hint">Ideal: entre 120 e 160 caracteres. Resumo claro e atrativo do site.</span>
                    <textarea id="meta_descricao" name="meta_descricao"
                              placeholder="Ex: Somos especialistas em soluções digitais para empresas. Conheça nossos serviços e entre em contato."
                              oninput="countChars('meta_descricao','cnt_desc',160)"><?= hce($v['meta_descricao']) ?></textarea>
                    <div class="char-bar"><span id="cnt_desc"><?= strlen($v['meta_descricao'] ?? '') ?>/160</span></div>
                </div>

                <div class="fg">
                    <label for="meta_keywords">Meta Keywords</label>
                    <span class="hint">Palavras-chave separadas por vírgula. Pouco usado pelo Google, mas útil para outros mecanismos.</span>
                    <input type="text" id="meta_keywords" name="meta_keywords"
                           placeholder="Ex: agência digital, sites profissionais, marketing, São Paulo"
                           value="<?= hce($v['meta_keywords']) ?>">
                </div>

                <div class="fg">
                    <label for="meta_autor">Meta Autor</label>
                    <span class="hint">Nome do autor ou empresa responsável pelo site.</span>
                    <input type="text" id="meta_autor" name="meta_autor"
                           placeholder="Ex: WireStack Digital"
                           value="<?= hce($v['meta_autor']) ?>">
                </div>

                <!-- Preview SERP -->
                <div class="fg">
                    <label>Pré-visualização no Google (estimativa)</label>
                    <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:16px 18px">
                        <div style="font-size:12px;color:#202124;font-family:arial,sans-serif">
                            <div style="font-size:18px;color:#1a0dab;font-weight:400;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" id="serp_title"><?= hce(SITE_NAME) ?></div>
                            <div style="font-size:13px;color:#006621;margin:2px 0"><?= hce(BASE_URL) ?></div>
                            <div style="font-size:14px;color:#4d5156;line-height:1.5;margin-top:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden" id="serp_desc"><?= hce($v['meta_descricao'] ?: 'Nenhuma meta descrição definida ainda.') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── TAB: IDENTIDADE ────────────────────────────────── -->
    <div class="tab-panel" id="tab-identidade">
        <div class="card">
            <div class="card-hd">
                <div>
                    <h3>🏷️ Favicon do Site</h3>
                    <p>Ícone exibido na aba do navegador e em favoritos</p>
                </div>
            </div>
            <div class="card-body">

                <!-- Preview atual -->
                <div class="fg">
                    <label>Favicon atual</label>
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:#f9fafb;border:1px solid var(--border);border-radius:10px">
                        <div id="favicon-preview-wrap" style="width:48px;height:48px;background:#fff;border:1px solid var(--border);border-radius:6px;padding:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <?php if (!empty($v['favicon_url'])): ?>
                                <img id="favicon-preview-img" src="<?= hce($v['favicon_url']) ?>" alt="Favicon"
                                     style="max-width:100%;max-height:100%;object-fit:contain"
                                     onerror="this.style.display='none';document.getElementById('favicon-placeholder').style.display='block'">
                                <span id="favicon-placeholder" style="display:none;font-size:22px">🖼️</span>
                            <?php else: ?>
                                <img id="favicon-preview-img" src="" alt="" style="display:none;max-width:100%;max-height:100%;object-fit:contain">
                                <span id="favicon-placeholder" style="font-size:22px">🖼️</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--text)" id="favicon-filename">
                                <?= !empty($v['favicon_url']) ? hce(basename($v['favicon_url'])) : 'Nenhum favicon definido' ?>
                            </div>
                            <div style="font-size:11px;color:var(--muted);margin-top:3px">Apenas favicon.ico — salvo na raiz do projeto</div>
                        </div>
                    </div>
                </div>

                <!-- Drop zone de upload -->
                <div class="fg">
                    <label>Enviar novo favicon</label>
                    <div id="drop-zone"
                         onclick="document.getElementById('favicon-file-input').click()"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         ondrop="handleDrop(event)"
                         style="border:2px dashed var(--border);border-radius:10px;padding:30px 20px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;background:#fafafa">
                        <div style="font-size:36px;margin-bottom:8px">📁</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text)">Clique para selecionar ou arraste aqui</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:4px">Apenas .ico — máx. 1MB</div>
                    </div>
                    <input type="file" id="favicon-file-input"
                           accept=".ico,image/x-icon,image/vnd.microsoft.icon"
                           style="display:none"
                           onchange="uploadFavicon(this.files[0])">

                    <!-- Progresso -->
                    <div id="upload-progress" style="display:none;margin-top:10px">
                        <div style="height:6px;background:#e5e7eb;border-radius:10px;overflow:hidden">
                            <div id="progress-bar" style="height:100%;background:var(--accent);width:0%;transition:width .3s;border-radius:10px"></div>
                        </div>
                        <div style="font-size:11px;color:var(--muted);margin-top:4px" id="upload-status">Enviando...</div>
                    </div>
                    <div id="upload-feedback" style="display:none;margin-top:8px"></div>
                </div>

                <!-- Campo hidden usado pelo form principal -->
                <input type="hidden" name="favicon_url" id="favicon_url_input" value="<?= hce($v['favicon_url']) ?>">

            </div>
        </div>
    </div>

    <!-- ─── TAB: OPEN GRAPH ───────────────────────────────── -->
    <div class="tab-panel" id="tab-social">
        <div class="card">
            <div class="card-hd">
                <div>
                    <h3>📣 Open Graph (Compartilhamento Social)</h3>
                    <p>Controla como o site aparece ao ser compartilhado no WhatsApp, Facebook, LinkedIn etc.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="alert-info">
                    💡 Estas tags definem o <strong>título</strong>, <strong>descrição</strong> e <strong>imagem</strong> que aparecem quando alguém compartilha o link do seu site em redes sociais.
                </div>
                <div class="fg">
                    <label for="og_titulo">Título OG</label>
                    <span class="hint">Título exibido no card de compartilhamento. Se vazio, usa o nome do site.</span>
                    <input type="text" id="og_titulo" name="og_titulo"
                           placeholder="Ex: Minha Empresa — Soluções Digitais"
                           value="<?= hce($v['og_titulo']) ?>"
                           oninput="countChars('og_titulo','cnt_og_titulo',70)">
                    <div class="char-bar"><span id="cnt_og_titulo"><?= strlen($v['og_titulo'] ?? '') ?>/70</span></div>
                </div>
                <div class="fg">
                    <label for="og_descricao">Descrição OG</label>
                    <span class="hint">Texto exibido abaixo do título no card. Ideal: até 200 caracteres.</span>
                    <textarea id="og_descricao" name="og_descricao"
                              placeholder="Ex: Criamos sites e sistemas que transformam negócios. Conheça nossas soluções."
                              oninput="countChars('og_descricao','cnt_og_desc',200)"><?= hce($v['og_descricao']) ?></textarea>
                    <div class="char-bar"><span id="cnt_og_desc"><?= strlen($v['og_descricao'] ?? '') ?>/200</span></div>
                </div>
                <div class="fg">
                    <label for="og_imagem">URL da Imagem OG</label>
                    <span class="hint">Imagem exibida no card de compartilhamento. Tamanho ideal: 1200×630px.</span>
                    <input type="text" id="og_imagem" name="og_imagem"
                           placeholder="Ex: <?= hce(BASE_URL) ?>/assets/img/og-share.jpg"
                           value="<?= hce($v['og_imagem']) ?>">
                </div>
                <?php if (!empty($v['og_imagem'])): ?>
                <div class="fg">
                    <label>Pré-visualização da Imagem</label>
                    <img src="<?= hce($v['og_imagem']) ?>" alt="OG Image"
                         style="max-width:320px;border-radius:8px;border:1px solid var(--border);"
                         onerror="this.style.display='none'">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ─── TAB: RASTREAMENTO ─────────────────────────────── -->
    <div class="tab-panel" id="tab-rastreamento">
        <div class="card">
            <div class="card-hd">
                <div>
                    <h3>📊 Rastreamento &amp; Analytics</h3>
                    <p>Integre ferramentas de análise de tráfego</p>
                </div>
            </div>
            <div class="card-body">
                <div class="alert-info">
                    💡 O ID do Google Analytics é inserido automaticamente no <code>&lt;head&gt;</code> de todas as páginas quando preenchido.
                </div>
                <div class="fg">
                    <label for="google_analytics">Google Analytics ID (GA4)</label>
                    <span class="hint">Formato: G-XXXXXXXXXX — encontre no painel do Google Analytics.</span>
                    <input type="text" id="google_analytics" name="google_analytics"
                           placeholder="Ex: G-XXXXXXXXXX"
                           value="<?= hce($v['google_analytics']) ?>"
                           style="max-width:280px;font-family:monospace">
                </div>
                <?php if (!empty($v['google_analytics'])): ?>
                <div class="fg">
                    <label>Código que será inserido</label>
                    <div class="preview-box">&lt;!-- Google Analytics --&gt;
&lt;script async src=&quot;https://www.googletagmanager.com/gtag/js?id=<?= hce($v['google_analytics']) ?>&quot;&gt;&lt;/script&gt;
&lt;script&gt;
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= hce($v['google_analytics']) ?>');
&lt;/script&gt;</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ─── TAB: ROBÔS ────────────────────────────────────── -->
    <div class="tab-panel" id="tab-robots">
        <div class="card">
            <div class="card-hd">
                <div>
                    <h3>🤖 Diretivas de Robôs (SEO)</h3>
                    <p>Controla como os mecanismos de busca indexam e rastreiam o site</p>
                </div>
            </div>
            <div class="card-body">
                <div class="alert-info">
                    💡 A tag <code>robots</code> é inserida no <code>&lt;head&gt;</code> de todas as páginas. Desativar o índex impede que o Google exiba o site nos resultados de busca.
                </div>
                <div class="fg">
                    <label>Indexação</label>
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="checkbox" name="robots_index" value="1" <?= $v['robots_index'] ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                        <span class="toggle-label">Permitir que mecanismos de busca <strong>indexem</strong> o site</span>
                    </div>
                </div>
                <div class="fg">
                    <label>Rastreamento de Links</label>
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="checkbox" name="robots_follow" value="1" <?= $v['robots_follow'] ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                        <span class="toggle-label">Permitir que robôs <strong>sigam os links</strong> internos do site</span>
                    </div>
                </div>
                <div class="fg">
                    <label>Meta tag resultante</label>
                    <div class="preview-box" id="prev_robots"><?php
                        $ri = $v['robots_index']  ? 'index'   : 'noindex';
                        $rf = $v['robots_follow'] ? 'follow'  : 'nofollow';
                        echo "&lt;meta name=\"robots\" content=\"{$ri}, {$rf}\"&gt;";
                    ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Botão de salvar (sempre visível) ─────────────── -->
    <div class="form-actions">
        <button type="submit" class="btn btn-dark">💾 Salvar Configurações</button>
        <span style="font-size:12px;color:var(--muted);margin-left:auto">As alterações são aplicadas imediatamente em todas as páginas do site.</span>
    </div>

    </form>
</div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
// ─── Tabs ─────────────────────────────────────────────────
function switchTab(id, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    btn.classList.add('active');
}

// ─── Contador de caracteres ───────────────────────────────
function countChars(fieldId, counterId, limit) {
    var el  = document.getElementById(fieldId);
    var cnt = document.getElementById(counterId);
    var len = el.value.length;
    cnt.textContent = len + '/' + limit;
    cnt.className = len > limit ? 'over' : '';
    if (fieldId === 'meta_descricao') {
        var d = document.getElementById('serp_desc');
        if (d) d.textContent = el.value || 'Nenhuma meta descrição definida ainda.';
    }
}

// ─── Preview robots ───────────────────────────────────────
document.querySelectorAll('input[name="robots_index"], input[name="robots_follow"]').forEach(function(el){
    el.addEventListener('change', function(){
        var idx = document.querySelector('input[name="robots_index"]').checked  ? 'index'   : 'noindex';
        var flw = document.querySelector('input[name="robots_follow"]').checked ? 'follow'  : 'nofollow';
        var prev = document.getElementById('prev_robots');
        if (prev) prev.textContent = '<meta name="robots" content="' + idx + ', ' + flw + '">';
    });
});

// ─── Upload de Favicon ────────────────────────────────────
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('drop-zone').style.borderColor = 'var(--accent)';
    document.getElementById('drop-zone').style.background  = 'var(--accent-s)';
}
function handleDragLeave(e) {
    document.getElementById('drop-zone').style.borderColor = 'var(--border)';
    document.getElementById('drop-zone').style.background  = '#fafafa';
}
function handleDrop(e) {
    e.preventDefault();
    handleDragLeave(e);
    var file = e.dataTransfer.files[0];
    if (file) uploadFavicon(file);
}

function uploadFavicon(file) {
    if (!file) return;

    var allowed = ['image/x-icon','image/vnd.microsoft.icon','application/octet-stream'];
    if (!file.name.match(/\.ico$/i)) {
        showFeedback('error', '⚠️ Apenas arquivos .ico são aceitos.');
        return;
    }
    if (file.size > 1 * 1024 * 1024) {
        showFeedback('error', '⚠️ Arquivo muito grande. Máximo: 1MB.');
        return;
    }

    var formData = new FormData();
    formData.append('favicon', file);

    // Mostra progresso
    var prog = document.getElementById('upload-progress');
    var bar  = document.getElementById('progress-bar');
    var stat = document.getElementById('upload-status');
    prog.style.display = 'block';
    bar.style.width    = '30%';
    stat.textContent   = 'Enviando...';
    document.getElementById('upload-feedback').style.display = 'none';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= hce(BASE_URL) ?>/admin/configuracoes/upload_favicon.php');

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            bar.style.width = Math.round((e.loaded / e.total) * 85) + '%';
        }
    };

    xhr.onload = function() {
        bar.style.width = '100%';
        try {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                stat.textContent = 'Concluído!';
                // Atualiza preview
                var img  = document.getElementById('favicon-preview-img');
                var ph   = document.getElementById('favicon-placeholder');
                var name = document.getElementById('favicon-filename');
                img.src           = res.path;
                img.style.display = 'block';
                ph.style.display  = 'none';
                name.textContent  = res.filename;
                // Atualiza campo hidden para o form salvar
                document.getElementById('favicon_url_input').value = res.path;
                showFeedback('ok', '✅ Favicon enviado! Clique em <strong>Salvar Configurações</strong> para confirmar.');
            } else {
                stat.textContent = 'Erro.';
                showFeedback('error', '⚠️ ' + (res.error || 'Erro desconhecido'));
            }
        } catch(e) {
            stat.textContent = 'Erro.';
            showFeedback('error', '⚠️ Resposta inválida do servidor.');
        }
        setTimeout(function(){ prog.style.display = 'none'; bar.style.width = '0%'; }, 1500);
    };

    xhr.onerror = function() {
        prog.style.display = 'none';
        showFeedback('error', '⚠️ Falha na conexão com o servidor.');
    };

    xhr.send(formData);
}

function showFeedback(type, msg) {
    var el = document.getElementById('upload-feedback');
    el.style.display = 'block';
    el.className     = type === 'ok' ? 'alert-ok' : 'alert-err';
    el.innerHTML     = msg;
}
</script>