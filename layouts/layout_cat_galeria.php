<?php
/**
 * Layout: Galeria em Grid — Categoria de Produto
 * Grid de imagens clicáveis com lightbox fullscreen + navegação por teclado
 *
 * campos_json:
 *   badge           — badge da seção (opcional)
 *   titulo          — título da galeria (opcional)
 *   subtitulo       — descrição (opcional)
 *   imagens         — URLs separadas por quebra de linha ou vírgula (textarea)
 *   colunas         — '2' | '3' | '4' (padrão: 3)
 *   fundo           — 'claro' | 'escuro' (padrão: claro)
 *   cor_destaque    — cor dos elementos de destaque (padrão #3a8c3f)
 *   legenda1..12    — legenda de cada imagem (opcional)
 *   botao_texto     — texto do botão CTA abaixo da galeria (opcional)
 *   botao_link      — link do botão CTA (opcional)
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge        = trim($dados['badge']        ?? '');
$titulo       = trim($dados['titulo']       ?? '');
$subtitulo    = trim($dados['subtitulo']    ?? '');
$fundo        = $dados['fundo']             ?? 'claro';
$cor          = htmlspecialchars($dados['cor_destaque'] ?? '#3a8c3f', ENT_QUOTES);
$colunas      = in_array((string)($dados['colunas'] ?? ''), ['2','3','4']) ? (int)$dados['colunas'] : 3;
$botao_texto  = trim($dados['botao_texto']  ?? '');
$botao_link   = trim($dados['botao_link']   ?? '');

// ---- Normaliza imagens ----
$imagens_raw = $dados['imagens'] ?? [];
if (is_string($imagens_raw)) {
    $dec = json_decode($imagens_raw, true);
    $imagens_raw = is_array($dec) ? $dec : array_map('trim', preg_split('/[\n,]+/', $imagens_raw));
}
$imagens = array_values(array_filter((array) $imagens_raw));

// Legendas
$legendas = [];
for ($i = 1; $i <= 12; $i++) {
    $legendas[] = trim($dados["legenda{$i}"] ?? '');
}

$isDark  = ($fundo === 'escuro');
$uid     = 'catgal' . substr(md5(uniqid()), 0, 6);
$total   = count($imagens);
?>

<style>#<?= $uid ?> { --gal-accent: <?= $cor ?>; }</style>

<section class="lyt-catgal lyt-catgal--<?= $isDark ? 'dark' : 'light' ?>" id="<?= $uid ?>">
    <div class="lyt-catgal__container">

        <!-- Cabeçalho -->
        <?php if (!empty($badge) || !empty($titulo) || !empty($subtitulo)): ?>
            <div class="lyt-catgal__header">
                <?php if (!empty($badge)): ?>
                    <span class="lyt-catgal__badge"><?= htmlspecialchars($badge) ?></span>
                <?php endif; ?>
                <?php if (!empty($titulo)): ?>
                    <h2 class="lyt-catgal__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
                <?php endif; ?>
                <?php if (!empty($subtitulo)): ?>
                    <p class="lyt-catgal__subtitulo"><?= htmlspecialchars($subtitulo) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Grid -->
        <?php if (!empty($imagens)): ?>
            <div class="lyt-catgal__grid lyt-catgal__grid--<?= $colunas ?>">
                <?php foreach ($imagens as $i => $img): ?>
                    <button class="lyt-catgal__item"
                            data-idx="<?= $i ?>"
                            aria-label="Ampliar imagem <?= $i + 1 ?>">
                        <img src="<?= htmlspecialchars($img) ?>"
                             alt="<?= !empty($legendas[$i]) ? htmlspecialchars($legendas[$i]) : 'Imagem ' . ($i + 1) ?>"
                             class="lyt-catgal__thumb-img"
                             loading="lazy">
                        <div class="lyt-catgal__item-overlay" aria-hidden="true">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                                <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <?php if (!empty($legendas[$i])): ?>
                            <div class="lyt-catgal__item-legenda"><?= htmlspecialchars($legendas[$i]) ?></div>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Lightbox (oculto por padrão) -->
            <div class="lyt-catgal__lightbox" id="<?= $uid ?>_lb" role="dialog" aria-modal="true" aria-label="Visualizador de imagem" hidden>
                <div class="lyt-catgal__lb-backdrop"></div>

                <button class="lyt-catgal__lb-close" aria-label="Fechar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </button>

                <button class="lyt-catgal__lb-nav lyt-catgal__lb-nav--prev" aria-label="Imagem anterior">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div class="lyt-catgal__lb-content">
                    <img src="" alt="" class="lyt-catgal__lb-img">
                    <div class="lyt-catgal__lb-legenda"></div>
                    <div class="lyt-catgal__lb-counter">
                        <span class="lyt-catgal__lb-cur">1</span>/<span class="lyt-catgal__lb-tot"><?= $total ?></span>
                    </div>
                </div>

                <button class="lyt-catgal__lb-nav lyt-catgal__lb-nav--next" aria-label="Próxima imagem">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- CTA -->
        <?php if (!empty($botao_texto) && !empty($botao_link)): ?>
            <div class="lyt-catgal__cta">
                <a href="<?= htmlspecialchars($botao_link) ?>" class="lyt-catgal__btn">
                    <?= htmlspecialchars($botao_texto) ?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>

<style>
/* ================================================================
   GALERIA EM GRID — CATEGORIA DE PRODUTO
   ================================================================ */
.lyt-catgal { padding: 80px 24px; }
.lyt-catgal--light { background: #fff; }
.lyt-catgal--dark  { background: #1a2332; }

.lyt-catgal__container {
    max-width: 1160px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 48px;
}

/* Cabeçalho */
.lyt-catgal__header {
    text-align: center;
    display: flex; flex-direction: column; align-items: center; gap: 14px;
}
.lyt-catgal__badge {
    display: inline-block;
    background: var(--gal-accent);
    color: #fff;
    font-size: .7rem; font-weight: 700;
    letter-spacing: .18em; text-transform: uppercase;
    padding: 5px 14px; border-radius: 100px;
}
.lyt-catgal__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.5rem, 3vw, 2.4rem);
    font-weight: 700; line-height: 1.25; letter-spacing: -.02em; margin: 0;
}
.lyt-catgal--light .lyt-catgal__titulo { color: #0d1b2a; }
.lyt-catgal--dark  .lyt-catgal__titulo { color: #fff; }
.lyt-catgal__subtitulo { font-size: 1rem; line-height: 1.75; max-width: 580px; margin: 0; }
.lyt-catgal--light .lyt-catgal__subtitulo { color: #6b7280; }
.lyt-catgal--dark  .lyt-catgal__subtitulo { color: rgba(255,255,255,.65); }

/* Grid */
.lyt-catgal__grid { display: grid; gap: 12px; }
.lyt-catgal__grid--2 { grid-template-columns: repeat(2, 1fr); }
.lyt-catgal__grid--3 { grid-template-columns: repeat(3, 1fr); }
.lyt-catgal__grid--4 { grid-template-columns: repeat(4, 1fr); }

/* Itens */
.lyt-catgal__item {
    position: relative;
    aspect-ratio: 4/3;
    border: none; padding: 0; cursor: pointer;
    border-radius: 10px; overflow: hidden;
    background: #eee;
    transition: transform .3s, box-shadow .3s;
}
.lyt-catgal__item:hover { transform: scale(1.02); box-shadow: 0 12px 40px rgba(0,0,0,.2); z-index: 1; }
.lyt-catgal__thumb-img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
    transition: transform .4s;
}
.lyt-catgal__item:hover .lyt-catgal__thumb-img { transform: scale(1.06); }

/* Overlay hover */
.lyt-catgal__item-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.0);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    transition: background .3s, opacity .3s;
    opacity: 0;
}
.lyt-catgal__item:hover .lyt-catgal__item-overlay { background: rgba(0,0,0,.42); opacity: 1; }

/* Legenda no hover */
.lyt-catgal__item-legenda {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 10px 14px;
    background: linear-gradient(transparent, rgba(0,0,0,.7));
    color: rgba(255,255,255,.9); font-size: .8rem; line-height: 1.4;
    opacity: 0; transition: opacity .3s;
}
.lyt-catgal__item:hover .lyt-catgal__item-legenda { opacity: 1; }

/* ---- LIGHTBOX ---- */
.lyt-catgal__lightbox {
    position: fixed; inset: 0; z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.lyt-catgal__lightbox[hidden] { display: none; }

.lyt-catgal__lb-backdrop {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.92);
    backdrop-filter: blur(8px);
}

.lyt-catgal__lb-content {
    position: relative; z-index: 2;
    display: flex; flex-direction: column; align-items: center;
    max-width: 90vw; max-height: 90vh;
    gap: 14px;
}
.lyt-catgal__lb-img {
    max-width: 100%; max-height: 78vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 24px 80px rgba(0,0,0,.5);
    transition: opacity .25s;
}
.lyt-catgal__lb-legenda {
    color: rgba(255,255,255,.7);
    font-size: .88rem; text-align: center;
    min-height: 1.2em;
}
.lyt-catgal__lb-counter {
    color: rgba(255,255,255,.45);
    font-size: .78rem; font-weight: 600;
    letter-spacing: .08em;
}

.lyt-catgal__lb-close {
    position: absolute; top: 20px; right: 20px; z-index: 3;
    width: 44px; height: 44px; border-radius: 50%;
    border: none; background: rgba(255,255,255,.1);
    color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.lyt-catgal__lb-close:hover { background: rgba(255,255,255,.25); }

.lyt-catgal__lb-nav {
    position: absolute; top: 50%; transform: translateY(-50%);
    z-index: 3;
    width: 52px; height: 52px; border-radius: 50%;
    border: none; background: rgba(255,255,255,.1);
    color: rgba(255,255,255,.85); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, color .2s;
}
.lyt-catgal__lb-nav:hover { background: var(--gal-accent); color: #fff; }
.lyt-catgal__lb-nav--prev { left: 16px; }
.lyt-catgal__lb-nav--next { right: 16px; }

/* CTA */
.lyt-catgal__cta { text-align: center; }
.lyt-catgal__btn {
    display: inline-flex; align-items: center; gap: 10px;
    background: var(--gal-accent); color: #fff;
    font-size: .95rem; font-weight: 700;
    letter-spacing: .04em; padding: 16px 36px;
    border-radius: 6px; text-decoration: none;
    transition: filter .25s, transform .2s;
}
.lyt-catgal__btn:hover { filter: brightness(.88); transform: translateY(-2px); }
.lyt-catgal__btn svg { transition: transform .2s; }
.lyt-catgal__btn:hover svg { transform: translateX(4px); }

/* Mobile */
@media (max-width: 860px) {
    .lyt-catgal__grid--4 { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 640px) {
    .lyt-catgal { padding: 56px 16px; }
    .lyt-catgal__grid--3,
    .lyt-catgal__grid--4 { grid-template-columns: repeat(2, 1fr); }
    .lyt-catgal__lb-nav { width: 40px; height: 40px; }
    .lyt-catgal__lb-nav--prev { left: 6px; }
    .lyt-catgal__lb-nav--next { right: 6px; }
}
@media (max-width: 400px) {
    .lyt-catgal__grid--2,
    .lyt-catgal__grid--3,
    .lyt-catgal__grid--4 { grid-template-columns: 1fr; }
}
</style>

<script>
(function () {
    const wrap = document.getElementById('<?= $uid ?>');
    if (!wrap) return;

    const IMGS = <?= json_encode($imagens) ?>;
    const LEGS = <?= json_encode($legendas) ?>;
    const lb       = wrap.querySelector('.lyt-catgal__lightbox');
    const lbImg    = wrap.querySelector('.lyt-catgal__lb-img');
    const lbLeg    = wrap.querySelector('.lyt-catgal__lb-legenda');
    const lbCur    = wrap.querySelector('.lyt-catgal__lb-cur');
    const lbClose  = wrap.querySelector('.lyt-catgal__lb-close');
    const lbBack   = wrap.querySelector('.lyt-catgal__lb-backdrop');
    const lbPrev   = wrap.querySelector('.lyt-catgal__lb-nav--prev');
    const lbNext   = wrap.querySelector('.lyt-catgal__lb-nav--next');
    const items    = wrap.querySelectorAll('.lyt-catgal__item');

    if (!lb || IMGS.length === 0) return;

    let cur = 0, touchX = 0;

    function open(idx) {
        cur = ((idx % IMGS.length) + IMGS.length) % IMGS.length;
        lbImg.src = IMGS[cur];
        lbImg.alt = LEGS[cur] || ('Imagem ' + (cur + 1));
        lbLeg.textContent = LEGS[cur] || '';
        lbCur.textContent = cur + 1;
        lb.hidden = false;
        document.body.style.overflow = 'hidden';
        lbClose.focus();
    }

    function close() {
        lb.hidden = true;
        document.body.style.overflow = '';
    }

    items.forEach(btn => btn.addEventListener('click', () => open(+btn.dataset.idx)));
    lbClose.addEventListener('click', close);
    lbBack.addEventListener('click',  close);
    lbPrev.addEventListener('click',  () => open(cur - 1));
    lbNext.addEventListener('click',  () => open(cur + 1));

    // Swipe
    lb.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend',   e => {
        const diff = touchX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) open(cur + (diff > 0 ? 1 : -1));
    }, { passive: true });

    // Teclado
    document.addEventListener('keydown', e => {
        if (lb.hidden) return;
        if (e.key === 'Escape')     close();
        if (e.key === 'ArrowLeft')  open(cur - 1);
        if (e.key === 'ArrowRight') open(cur + 1);
    });
})();
</script>
