<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title     = 'Página Inicial — Conteúdos';
$current_module = 'home';
$current_page   = 'home-editar';

$db = getDB();

// Busca layouts disponíveis
$layouts = $db->query("SELECT * FROM layouts WHERE ativo = 1 ORDER BY nome")->fetchAll();

// Busca blocos da home
$stmt = $db->query("
    SELECT hc.*, l.nome as layout_nome, l.campos_json, l.arquivo as layout_arquivo
    FROM home_conteudos hc
    JOIN layouts l ON l.id = hc.layout_id
    ORDER BY hc.ordem ASC
");
$conteudos = $stmt->fetchAll();

$error   = '';
$success = '';

// ─── PROCESSAR POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Adicionar bloco
    if ($action === 'add_conteudo') {
        $layout_id  = (int)($_POST['layout_id'] ?? 0);
        $dados_json = $_POST['dados_json'] ?? '{}';
        $maxOrdem   = $db->query("SELECT COALESCE(MAX(ordem),0)+1 FROM home_conteudos")->fetchColumn();
        $db->prepare("INSERT INTO home_conteudos (layout_id, dados_json, ordem, ativo) VALUES (?,?,?,1)")
           ->execute([$layout_id, $dados_json, $maxOrdem]);
        $success = 'Bloco adicionado com sucesso!';
    }

    // Salvar bloco existente
    if ($action === 'salvar_conteudo') {
        $conteudo_id = (int)($_POST['conteudo_id'] ?? 0);
        $dados_json  = $_POST['dados_json'] ?? '{}';
        $ativo_c     = isset($_POST['ativo_conteudo']) ? 1 : 0;
        $db->prepare("UPDATE home_conteudos SET dados_json=?, ativo=? WHERE id=?")
           ->execute([$dados_json, $ativo_c, $conteudo_id]);
        $success = 'Bloco salvo com sucesso!';
    }

    // Deletar bloco
    if ($action === 'del_conteudo') {
        $conteudo_id = (int)($_POST['conteudo_id'] ?? 0);
        $db->prepare("DELETE FROM home_conteudos WHERE id=?")->execute([$conteudo_id]);
        $success = 'Bloco removido!';
    }

    // Reordenar (AJAX)
    if ($action === 'reordenar') {
        $items = json_decode($_POST['items'] ?? '[]', true);
        foreach ($items as $item) {
            $db->prepare("UPDATE home_conteudos SET ordem=? WHERE id=?")
               ->execute([$item['ordem'], $item['id']]);
        }
        echo json_encode(['ok' => true]);
        exit;
    }

    // Recarrega conteúdos após ação
    $stmt = $db->query("
        SELECT hc.*, l.nome as layout_nome, l.campos_json, l.arquivo as layout_arquivo
        FROM home_conteudos hc
        JOIN layouts l ON l.id = hc.layout_id
        ORDER BY hc.ordem ASC
    ");
    $conteudos = $stmt->fetchAll();
}

// ─── ÍCONES POR LAYOUT ────────────────────────────────────────
$layoutIcons = [
    'Hero Banner'           => '🖼️',
    'Texto + Imagem'        => '📝',
    'Galeria de Imagens'    => '🖼️',
    'Cards Informativos'    => '🃏',
    'Formulário de Contato' => '📬',
    'Vídeo'                 => '▶️',
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<style>
:root{
  --brand:#00071c; --accent:#4f6ef7; --accent-s:rgba(79,110,247,.1);
  --ok:#10b981; --err:#ef4444; --warn:#f59e0b; --purple:#8b5cf6;
  --border:#e5e7eb; --bg:#f4f6fb; --card:#fff;
  --text:#111827; --muted:#6b7280;
  --r:12px; --sh:0 2px 10px rgba(0,0,0,.07);
}
.wrap{padding:28px;max-width:1080px}
.info-strip{display:flex;align-items:center;gap:0;background:var(--brand);border-radius:var(--r);margin-bottom:24px;overflow:hidden}
.is-item{padding:16px 22px;border-right:1px solid rgba(255,255,255,.1)}
.is-item:last-child{border-right:none;margin-left:auto}
.is-label{font-size:10px;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.6px;margin-bottom:3px}
.is-val{font-size:13px;font-weight:700;color:#fff}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
.btn-dark{background:var(--brand);color:#fff}.btn-dark:hover{background:#1a2540;transform:translateY(-1px)}
.btn-accent{background:var(--accent);color:#fff}.btn-accent:hover{opacity:.88;transform:translateY(-1px)}
.btn-ghost{background:#f3f4f6;color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--border)}
.btn-ok{background:var(--ok);color:#fff}.btn-ok:hover{opacity:.88}
.btn-err{background:var(--err);color:#fff}.btn-err:hover{opacity:.88}
.btn-sm{padding:6px 13px;font-size:12px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.alert-ok{background:#ecfdf5;color:#065f46;border-left:4px solid var(--ok)}
.alert-err{background:#fef2f2;color:#991b1b;border-left:4px solid var(--err)}
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);padding:28px}
.card-title{font-size:16px;font-weight:700;color:var(--brand);margin-bottom:22px;padding-bottom:14px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px}
.fg{display:flex;flex-direction:column;gap:6px}
.fc{padding:11px 14px;border:2px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;background:#fafafa;transition:border-color .2s,box-shadow .2s}
.fc:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-s)}
textarea.fc{resize:vertical;min-height:88px}
.hint{font-size:11px;color:var(--muted)}
.tog-wrap{display:flex;align-items:center;gap:10px}
.tog{position:relative;width:44px;height:24px}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;border-radius:24px;background:#d1d5db;cursor:pointer;transition:background .2s}
.tog-s:before{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tog input:checked+.tog-s{background:var(--ok)}
.tog input:checked+.tog-s:before{transform:translateX(20px)}
.c-list{display:flex;flex-direction:column;gap:10px}
.c-card{background:var(--card);border:2px solid var(--border);border-radius:var(--r);overflow:hidden;transition:border-color .2s}
.c-card:hover{border-color:var(--accent)}
.c-card.inactive{opacity:.55}
.c-head{display:flex;align-items:center;gap:10px;padding:13px 18px;background:#fafafa;cursor:pointer;user-select:none}
.drag-h{color:var(--muted);font-size:16px;cursor:grab;padding:2px 4px}
.drag-h:active{cursor:grabbing}
.c-badge{padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;background:var(--accent-s);color:var(--accent)}
.c-title{flex:1;font-size:13px;font-weight:600;color:var(--text)}
.c-body{padding:20px;border-top:1px solid var(--border)}
.c-body.hidden{display:none}
.chevron{color:var(--muted);font-size:12px;transition:transform .2s}
.c-head.open .chevron{transform:rotate(180deg)}
.lp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px}
.lp-opt{border:2px solid var(--border);border-radius:10px;padding:14px 10px;cursor:pointer;text-align:center;transition:all .18s;background:#fafafa}
.lp-opt:hover{border-color:var(--accent);background:var(--accent-s)}
.lp-opt.sel{border-color:var(--accent);background:var(--accent-s)}
.lp-opt input{display:none}
.lp-icon{font-size:24px;margin-bottom:5px}
.lp-name{font-size:12px;font-weight:700;color:var(--text)}
.lp-desc{font-size:11px;color:var(--muted);margin-top:2px}
.dyn-fields{background:var(--bg);border-radius:10px;padding:18px;margin-top:10px}
.dyn-fields .fg{margin-bottom:14px}
.pos-list{display:flex;gap:8px}
.pos-opt{flex:1;border:2px solid var(--border);border-radius:10px;padding:10px 6px;cursor:pointer;text-align:center;transition:all .18s;position:relative}
.pos-opt input{position:absolute;opacity:0;pointer-events:none}
.pos-opt:hover{border-color:var(--accent)}
.pos-opt.sel{border-color:var(--accent);background:var(--accent-s)}
.pos-icon{font-size:22px;margin-bottom:3px}
.pos-label{font-size:12px;font-weight:700;color:var(--text)}
.upzone{border:2px dashed var(--border);border-radius:10px;padding:24px 16px;text-align:center;cursor:pointer;transition:all .2s;background:#fafafa;position:relative}
.upzone:hover,.upzone.drag{border-color:var(--accent);background:var(--accent-s)}
.upzone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.up-icon{font-size:30px;margin-bottom:6px}
.up-txt{font-size:13px;color:var(--muted)}
.up-txt b{color:var(--accent)}
.up-sub{font-size:11px;color:#9ca3af;margin-top:3px}
.up-prog{height:4px;background:var(--border);border-radius:4px;margin-top:8px;overflow:hidden;display:none}
.up-bar{height:100%;background:var(--accent);width:0;transition:width .3s;border-radius:4px}
.img-prev-wrap{margin-top:10px;position:relative;display:inline-block}
.img-prev{max-width:100%;max-height:160px;border-radius:8px;border:2px solid var(--border);object-fit:cover;display:block}
.img-rm{position:absolute;top:-8px;right:-8px;width:22px;height:22px;border-radius:50%;background:var(--err);color:#fff;border:none;cursor:pointer;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,.2)}
.modal-ov{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;pointer-events:none;transition:opacity .22s}
.modal-ov.open{opacity:1;pointer-events:all}
.modal{background:#fff;border-radius:16px;padding:28px;width:100%;max-width:680px;max-height:90vh;overflow-y:auto;transform:translateY(16px);transition:transform .22s;box-shadow:0 12px 40px rgba(0,0,0,.18)}
.modal-ov.open .modal{transform:translateY(0)}
.modal-hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-title{font-size:17px;font-weight:700;color:var(--brand)}
.modal-x{background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted);width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:6px}
.modal-x:hover{background:var(--bg)}
.sortable-ghost{opacity:.35;background:var(--accent-s);border-color:var(--accent)}
.sortable-drag{box-shadow:0 8px 24px rgba(0,0,0,.15)}
.empty-c{text-align:center;padding:50px 20px;border:2px dashed var(--border);border-radius:var(--r);background:var(--bg)}
.empty-c .ei{font-size:48px;margin-bottom:10px}
@media(max-width:700px){
  .lp-grid{grid-template-columns:1fr}
  .pos-list{flex-wrap:wrap}
  .is-item{display:none}
  .is-item:first-child,.is-item:last-child{display:block}
}
</style>

<main class="content">
<div class="wrap">

  <!-- INFO STRIP -->
  <div class="info-strip">
    <div class="is-item">
      <div class="is-label">Seção</div>
      <div class="is-val">🏠 Página Inicial</div>
    </div>
    <div class="is-item">
      <div class="is-label">Total de blocos</div>
      <div class="is-val"><?= count($conteudos) ?></div>
    </div>
    <div class="is-item">
      <div class="is-label">URL</div>
      <div class="is-val">/index.php</div>
    </div>
    <div class="is-item">
      <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-ghost btn-sm">🌐 Ver Home</a>
    </div>
  </div>

  <!-- ALERTS -->
  <?php if ($error): ?>
    <div class="alert alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert alert-ok">✅ <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <!-- CARD BLOCOS -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border)">
      <span style="font-size:16px;font-weight:700;color:var(--brand)">🧩 Blocos de Conteúdo da Home</span>
      <button class="btn btn-accent" onclick="openModal()">➕ Adicionar Bloco</button>
    </div>

    <?php if (empty($conteudos)): ?>
      <div class="empty-c">
        <div class="ei">🏠</div>
        <h3 style="margin-bottom:6px">Nenhum bloco ainda</h3>
        <p style="color:var(--muted);font-size:13px;margin-bottom:16px">Adicione blocos para montar o conteúdo da página inicial</p>
        <button class="btn btn-accent" onclick="openModal()">➕ Adicionar primeiro bloco</button>
      </div>
    <?php else: ?>
      <div class="c-list" id="sortable">
        <?php foreach ($conteudos as $c):
          $dados  = json_decode($c['dados_json'], true) ?? [];
          $campos = json_decode($c['campos_json'], true) ?? [];
          $ptitle = $dados['titulo'] ?? (($dados['texto'] ?? '') ? mb_substr(strip_tags($dados['texto']),0,48).'…' : 'Bloco #'.$c['id']);
        ?>
        <div class="c-card <?= $c['ativo']?'':'inactive' ?>" data-id="<?= $c['id'] ?>">
          <div class="c-head" onclick="toggleBloco(this)">
            <span class="drag-h" title="Arrastar para reordenar" onclick="event.stopPropagation()">⠿</span>
            <span class="c-badge"><?= htmlspecialchars($c['layout_nome']) ?></span>
            <span class="c-title"><?= htmlspecialchars($ptitle) ?></span>
            <?php if (!$c['ativo']): ?>
              <span style="font-size:11px;color:var(--muted);background:#f3f4f6;padding:2px 8px;border-radius:20px">Inativo</span>
            <?php endif; ?>
            <span class="chevron">▼</span>
          </div>

          <div class="c-body hidden">
            <form method="POST" id="form-c-<?= $c['id'] ?>">
              <input type="hidden" name="action" value="salvar_conteudo">
              <input type="hidden" name="conteudo_id" value="<?= $c['id'] ?>">
              <input type="hidden" name="dados_json" class="dj-input">

              <div class="dyn-fields">
                <?php renderCampos($campos, $dados, 'e'.$c['id']); ?>
              </div>

              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap;gap:10px">
                <div class="tog-wrap">
                  <label class="tog">
                    <input type="checkbox" name="ativo_conteudo" <?= $c['ativo']?'checked':'' ?>>
                    <span class="tog-s"></span>
                  </label>
                  <span style="font-size:13px;font-weight:600">Bloco ativo</span>
                </div>
                <div style="display:flex;gap:8px">
                  <button type="submit" class="btn btn-dark btn-sm"
                    onclick="buildDJ(this.form, <?= htmlspecialchars(json_encode($campos)) ?>, 'e<?= $c['id'] ?>')">
                    💾 Salvar bloco
                  </button>
                  <button type="submit" form="form-del-<?= $c['id'] ?>" class="btn btn-err btn-sm"
                    onclick="return confirm('Remover este bloco?')">🗑️ Remover</button>
                </div>
              </div>
            </form>

            <form method="POST" id="form-del-<?= $c['id'] ?>" style="display:none">
              <input type="hidden" name="action" value="del_conteudo">
              <input type="hidden" name="conteudo_id" value="<?= $c['id'] ?>">
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <p style="font-size:12px;color:var(--muted);margin-top:14px;text-align:center">⠿ Arraste os blocos para reordenar</p>
    <?php endif; ?>
  </div>

</div>
</main>

<!-- ── MODAL ADICIONAR BLOCO ── -->
<div class="modal-ov" id="modal">
  <div class="modal">
    <div class="modal-hd">
      <div class="modal-title">➕ Novo Bloco de Conteúdo</div>
      <button class="modal-x" onclick="closeModal()">✕</button>
    </div>

    <p style="font-size:13px;color:var(--muted);margin-bottom:14px">Escolha o tipo de bloco:</p>

    <form method="POST" id="form-add">
      <input type="hidden" name="action" value="add_conteudo">
      <input type="hidden" name="dados_json" id="add-dj" value="{}">

      <div class="lp-grid" id="lp-grid">
        <?php foreach ($layouts as $l):
          $icon = $layoutIcons[$l['nome']] ?? '📄';
        ?>
        <label class="lp-opt" onclick="selectLayout(this)">
          <input type="radio" name="layout_id" value="<?= $l['id'] ?>"
                 data-campos='<?= htmlspecialchars($l['campos_json']) ?>' required>
          <div class="lp-icon"><?= $icon ?></div>
          <div class="lp-name"><?= htmlspecialchars($l['nome']) ?></div>
          <div class="lp-desc"><?= htmlspecialchars(mb_substr($l['descricao'],0,48)) ?></div>
        </label>
        <?php endforeach; ?>
      </div>

      <div id="add-fields-wrap" style="display:none">
        <hr style="margin:16px 0;border:none;border-top:1px solid var(--border)">
        <p style="font-size:13px;font-weight:600;color:var(--brand);margin-bottom:12px">⚙️ Preencha os campos:</p>
        <div class="dyn-fields" id="add-fields"></div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
        <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancelar</button>
        <button type="submit" class="btn btn-accent" onclick="buildAddDJ()">✅ Adicionar</button>
      </div>
    </form>
  </div>
</div>

<?php
function renderCampos(array $campos, array $dados, string $prefix) {
    $positions = [
        'esquerda' => ['⬅️','Esquerda'],
        'direita'  => ['➡️','Direita'],
        'topo'     => ['⬆️','Topo'],
        'fundo'    => ['⬇️','Fundo'],
    ];

    foreach ($campos as $key => $type):
        $val   = $dados[$key] ?? '';
        $label = ucfirst(str_replace('_',' ',$key));
        $fid   = "f_{$prefix}_{$key}";
    ?>
    <div class="fg" style="margin-bottom:14px">
      <label><?= htmlspecialchars($label) ?></label>

      <?php if ($key === 'posicao_imagem'): ?>
        <div class="pos-list">
          <?php foreach ($positions as $pv => [$pi,$pl]): ?>
          <label class="pos-opt <?= $val===$pv?'sel':'' ?>" onclick="selPos(this)">
            <input type="radio" class="pos-r" name="pos_<?= $prefix ?>_<?= $key ?>"
                   value="<?= $pv ?>" data-campo="<?= $key ?>" <?= $val===$pv?'checked':'' ?>>
            <div class="pos-icon"><?= $pi ?></div>
            <div class="pos-label"><?= $pl ?></div>
          </label>
          <?php endforeach; ?>
        </div>

      <?php elseif (in_array($key,['imagem','imagem_fundo'])): ?>
        <div class="upzone" id="zone_<?= $fid ?>"
             ondragover="event.preventDefault();this.classList.add('drag')"
             ondragleave="this.classList.remove('drag')"
             ondrop="dropFile(event,'<?= $fid ?>')">
          <input type="file" accept="image/*" onchange="selFile(event,'<?= $fid ?>')">
          <div class="up-icon">📤</div>
          <div class="up-txt">Arraste ou <b>clique para selecionar</b></div>
          <div class="up-sub">PNG, JPG, WEBP — máx. 5MB</div>
        </div>
        <div class="up-prog" id="prog_<?= $fid ?>"><div class="up-bar" id="bar_<?= $fid ?>"></div></div>
        <div class="img-prev-wrap" id="prev_<?= $fid ?>" style="<?= $val?'':'display:none' ?>">
          <?php if ($val): ?>
            <img src="<?= htmlspecialchars($val) ?>" class="img-prev">
            <button type="button" class="img-rm" onclick="rmImg('<?= $fid ?>')">✕</button>
          <?php endif; ?>
        </div>
        <input type="hidden" id="<?= $fid ?>" data-campo="<?= $key ?>" value="<?= htmlspecialchars($val) ?>">

      <?php elseif ($type === 'textarea'): ?>
        <textarea class="fc" id="<?= $fid ?>" data-campo="<?= $key ?>" rows="4"><?= htmlspecialchars($val) ?></textarea>

      <?php else: ?>
        <input type="text" class="fc" id="<?= $fid ?>" data-campo="<?= $key ?>" value="<?= htmlspecialchars($val) ?>">
      <?php endif; ?>
    </div>
    <?php endforeach;
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
// ─ TOGGLE BLOCO ─
function toggleBloco(head) {
  const body = head.nextElementSibling;
  const isOpen = !body.classList.contains('hidden');
  body.classList.toggle('hidden');
  head.classList.toggle('open', !isOpen);
}

// ─ POSIÇÃO ─
function selPos(el) {
  el.closest('.pos-list').querySelectorAll('.pos-opt').forEach(o => o.classList.remove('sel'));
  el.classList.add('sel');
}

// ─ BUILD DADOS_JSON (editar bloco) ─
function buildDJ(form, campos, prefix) {
  const obj = {};
  Object.keys(campos).forEach(key => {
    if (key === 'posicao_imagem') {
      const r = form.querySelector('.pos-r[data-campo="'+key+'"]:checked');
      obj[key] = r ? r.value : '';
    } else {
      const el = document.getElementById('f_'+prefix+'_'+key);
      obj[key] = el ? el.value : '';
    }
  });
  form.querySelector('.dj-input').value = JSON.stringify(obj);
}

// ─ UPLOAD ─
const UPLOAD_URL = '<?= BASE_URL ?>/admin/home/upload.php';

function selFile(e, fid) { if (e.target.files[0]) doUpload(e.target.files[0], fid); }
function dropFile(e, fid) {
  e.preventDefault();
  document.getElementById('zone_'+fid)?.classList.remove('drag');
  if (e.dataTransfer.files[0]) doUpload(e.dataTransfer.files[0], fid);
}
function doUpload(file, fid) {
  if (file.size > 5*1024*1024) { alert('Arquivo muito grande! Máx 5MB.'); return; }
  const prog = document.getElementById('prog_'+fid);
  const bar  = document.getElementById('bar_'+fid);
  prog.style.display = 'block'; bar.style.width = '0%';
  let pct = 0;
  const iv = setInterval(() => { pct = Math.min(pct+12, 85); bar.style.width = pct+'%'; }, 80);
  const fd = new FormData();
  fd.append('imagem', file);
  fetch(UPLOAD_URL, {method:'POST', body:fd})
    .then(r => r.json())
    .then(data => {
      clearInterval(iv); bar.style.width='100%';
      setTimeout(() => { prog.style.display='none'; bar.style.width='0'; }, 500);
      if (data.success) {
        document.getElementById(fid).value = data.path;
        const wrap = document.getElementById('prev_'+fid);
        wrap.style.display = 'inline-block';
        wrap.innerHTML = `<img src="${data.path}" class="img-prev">
          <button type="button" class="img-rm" onclick="rmImg('${fid}')">✕</button>`;
      } else { alert('Erro: '+(data.error||'Tente novamente')); }
    })
    .catch(() => { clearInterval(iv); alert('Falha no upload.'); });
}
function rmImg(fid) {
  document.getElementById(fid).value = '';
  const w = document.getElementById('prev_'+fid);
  if (w) { w.style.display='none'; w.innerHTML=''; }
}

// ─ MODAL ─
function openModal()  { document.getElementById('modal').classList.add('open'); }
function closeModal() { document.getElementById('modal').classList.remove('open'); }
document.getElementById('modal').addEventListener('click', e => { if(e.target===e.currentTarget) closeModal(); });

function selectLayout(el) {
  document.querySelectorAll('.lp-opt').forEach(o => o.classList.remove('sel'));
  el.classList.add('sel');
  const radio  = el.querySelector('input[type=radio]');
  const campos = JSON.parse(radio.dataset.campos || '{}');
  buildAddFields(campos);
}

function buildAddFields(campos) {
  const wrap = document.getElementById('add-fields-wrap');
  const cont = document.getElementById('add-fields');
  cont.innerHTML = '';
  const positions = {
    esquerda:['⬅️','Esquerda'], direita:['➡️','Direita'],
    topo:['⬆️','Topo'], fundo:['⬇️','Fundo']
  };
  Object.entries(campos).forEach(([key, type]) => {
    const label = key.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
    const fid   = 'fa_'+key;
    let html = `<div class="fg" style="margin-bottom:14px"><label>${label}</label>`;
    if (key === 'posicao_imagem') {
      html += '<div class="pos-list">';
      Object.entries(positions).forEach(([pv,[pi,pl]]) => {
        html += `<label class="pos-opt" onclick="selPos(this)">
          <input type="radio" class="pos-r" name="add_pos_${key}" value="${pv}" data-campo="${key}">
          <div class="pos-icon">${pi}</div><div class="pos-label">${pl}</div>
        </label>`;
      });
      html += '</div>';
    } else if (['imagem','imagem_fundo'].includes(key)) {
      html += `<div class="upzone" id="zone_${fid}"
          ondragover="event.preventDefault();this.classList.add('drag')"
          ondragleave="this.classList.remove('drag')"
          ondrop="dropFile(event,'${fid}')">
        <input type="file" accept="image/*" onchange="selFile(event,'${fid}')">
        <div class="up-icon">📤</div>
        <div class="up-txt">Arraste ou <b>clique para selecionar</b></div>
        <div class="up-sub">PNG, JPG, WEBP — máx. 5MB</div>
      </div>
      <div class="up-prog" id="prog_${fid}"><div class="up-bar" id="bar_${fid}"></div></div>
      <div class="img-prev-wrap" id="prev_${fid}" style="display:none"></div>
      <input type="hidden" id="${fid}" data-campo="${key}" value="">`;
    } else if (type === 'textarea') {
      html += `<textarea class="fc" id="${fid}" data-campo="${key}" rows="4"></textarea>`;
    } else {
      html += `<input type="text" class="fc" id="${fid}" data-campo="${key}">`;
    }
    html += '</div>';
    cont.innerHTML += html;
  });
  wrap.style.display = Object.keys(campos).length ? 'block' : 'none';
}

function buildAddDJ() {
  const obj = {};
  document.querySelectorAll('#add-fields [data-campo]').forEach(el => {
    obj[el.dataset.campo] = el.value;
  });
  document.querySelectorAll('#add-fields .pos-r:checked').forEach(r => {
    obj[r.dataset.campo] = r.value;
  });
  document.getElementById('add-dj').value = JSON.stringify(obj);
}

// ─ DRAG & DROP REORDER ─
const sortEl = document.getElementById('sortable');
if (sortEl) {
  new Sortable(sortEl, {
    handle: '.drag-h',
    animation: 180,
    ghostClass: 'sortable-ghost',
    dragClass: 'sortable-drag',
    onEnd: () => {
      const items = [...sortEl.querySelectorAll('.c-card')].map((el,i) => ({id:el.dataset.id, ordem:i+1}));
      const fd = new FormData();
      fd.append('action','reordenar');
      fd.append('items', JSON.stringify(items));
      fetch('', {method:'POST', body:fd});
    }
  });
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>