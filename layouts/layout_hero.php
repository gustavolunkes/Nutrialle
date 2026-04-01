<?php
/**
 * Layout: Hero Banner
 * Slideshow de múltiplas imagens + cor do botão customizável
 *
 * campos_json:
 *   badge_texto     — texto do badge superior (opcional)
 *   titulo          — título principal
 *   subtitulo       — parágrafo abaixo do título (opcional)
 *   imagens         — array de URLs ou URLs separadas por quebra de linha
 *   imagem_fundo    — URL única (legado — usado se "imagens" estiver vazio)
 *   botao_texto     — label do botão
 *   botao_link      — href do botão
 *   botao_cor       — cor de fundo do botão  (hex/rgb, padrão #3a8c3f)
 *   botao_cor_text  — cor do texto do botão  (hex/rgb, padrão #ffffff)
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$titulo         = $dados['titulo']          ?? 'Desenvolvemos pessoas, produtos e negócios';
$subtitulo      = $dados['subtitulo']       ?? '';
$badge_texto    = $dados['badge_texto']     ?? '';
$botao_texto    = $dados['botao_texto']     ?? 'Saiba Mais';
$botao_link     = $dados['botao_link']      ?? '#';
$botao_cor      = $dados['botao_cor']       ?? '#3a8c3f';
$botao_cor_text = $dados['botao_cor_text']  ?? '#ffffff';

// ---- Normaliza imagens → array de URLs ----
$imagens = $dados['imagens'] ?? [];

if (empty($imagens) && !empty($dados['imagem_fundo'])) {
    $imagens = [$dados['imagem_fundo']];
}

if (is_string($imagens)) {
    $dec = json_decode($imagens, true);
    if (is_array($dec)) {
        $imagens = $dec;
    } else {
        // Separa por vírgula ou quebra de linha
        $imagens = array_map('trim', preg_split('/[\n,]+/', $imagens));
    }
}

$imagens = array_values(array_filter((array) $imagens));

// ID único por instância (permite múltiplos heroes na mesma página)
$uid = 'hero' . substr(md5(uniqid()), 0, 6);

$btn_bg   = htmlspecialchars($botao_cor,      ENT_QUOTES);
$btn_text = htmlspecialchars($botao_cor_text, ENT_QUOTES);

$total = count($imagens);
?>

<section class="lyt-hero" id="<?= $uid ?>">

    <!-- ---- Slides de fundo ---- -->
    <div class="lyt-hero__slides" aria-hidden="true">
        <?php if (!empty($imagens)): ?>
            <?php foreach ($imagens as $i => $img): ?>
                <div class="lyt-hero__slide <?= $i === 0 ? 'active' : '' ?>"
                     style="background-image:url('<?= htmlspecialchars($img) ?>')"></div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="lyt-hero__slide active"></div><!-- fallback CSS gradient -->
        <?php endif; ?>
    </div>

    <!-- ---- Overlay de escurecimento ---- -->
    <div class="lyt-hero__overlay" aria-hidden="true"></div>

    <!-- ---- Setas de navegação (apenas com 2+ imagens) ---- -->
    <?php if ($total > 1): ?>
        <button class="lyt-hero__arrow lyt-hero__arrow--prev" aria-label="Slide anterior">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <button class="lyt-hero__arrow lyt-hero__arrow--next" aria-label="Próximo slide">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    <?php endif; ?>

    <!-- ---- Conteúdo ---- -->
    <div class="lyt-hero__container">
        <?php if (!empty($badge_texto)): ?>
            <span class="lyt-hero__badge"><?= htmlspecialchars($badge_texto) ?></span>
        <?php endif; ?>

        <h1 class="lyt-hero__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h1>

        <?php if (!empty($subtitulo)): ?>
            <p class="lyt-hero__subtitulo"><?= htmlspecialchars($subtitulo) ?></p>
        <?php endif; ?>

        <?php if (!empty($botao_texto)): ?>
            <a href="<?= htmlspecialchars($botao_link) ?>"
               class="lyt-hero__btn"
               style="--btn-bg:<?= $btn_bg ?>;--btn-text:<?= $btn_text ?>">
                <?= htmlspecialchars($botao_texto) ?>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>

    <!-- ---- Bolinhas de navegação (apenas com 2+ imagens) ---- -->
    <?php if ($total > 1): ?>
        <div class="lyt-hero__dots" role="tablist" aria-label="Navegação do slideshow">
            <?php foreach ($imagens as $i => $_): ?>
                <button class="lyt-hero__dot <?= $i === 0 ? 'active' : '' ?>"
                        role="tab"
                        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $i + 1 ?>"
                        data-idx="<?= $i ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<style>
/* ------------------------------------------------------------------ */
/* Estrutura                                                            */
/* ------------------------------------------------------------------ */
.lyt-hero {
    position: relative;
    min-height: 90vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: #00071c; /* fallback enquanto carrega */
}

/* ------------------------------------------------------------------ */
/* Slides                                                               */
/* ------------------------------------------------------------------ */
.lyt-hero__slides {
    position: absolute;
    inset: 0;
    z-index: 0;
}

.lyt-hero__slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-color: #00071c;
    opacity: 1;
    transform: translateX(100%);
    transition: transform 0.6s ease;
}

.lyt-hero__slide.active {
    transform: translateX(0);
}

.lyt-hero__slide.prev {
    transform: translateX(-100%);
}

/* ------------------------------------------------------------------ */
/* Overlay                                                              */
/* ------------------------------------------------------------------ */
.lyt-hero__overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: linear-gradient(
        105deg,
        rgba(0,7,28,.80) 0%,
        rgba(0,7,28,.50) 55%,
        rgba(0,7,28,.18) 100%
    );
}

/* ------------------------------------------------------------------ */
/* Setas de navegação                                                   */
/* ------------------------------------------------------------------ */
.lyt-hero__arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 4;
    border: none;
    background: transparent;
    color: rgba(255,255,255,.6);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    transition: color .25s;
}

.lyt-hero__arrow:hover {
    color: rgba(255,255,255,1);
}

.lyt-hero__arrow--prev { left: 24px; }
.lyt-hero__arrow--next { right: 24px; }

@media (max-width: 768px) {
    .lyt-hero__arrow--prev { left: 12px; }
    .lyt-hero__arrow--next { right: 12px; }
}

/* ------------------------------------------------------------------ */
/* Conteúdo                                                             */
/* ------------------------------------------------------------------ */
.lyt-hero__container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 120px 40px 100px;
}

.lyt-hero__badge {
    display: inline-block;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 100px;
    margin-bottom: 28px;
    backdrop-filter: blur(6px);
}

.lyt-hero__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.4rem, 5.5vw, 4.2rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.15;
    max-width: 720px;
    margin-bottom: 28px;
    letter-spacing: -.02em;
}

.lyt-hero__subtitulo {
    font-size: clamp(0.85rem, 1.8vw, 1.2rem);
    color: rgba(255,255,255,.78);
    max-width: 540px;
    line-height: 1.7;
    margin-bottom: 44px;
}

/* ------------------------------------------------------------------ */
/* Botão — cor via CSS vars injetadas pelo PHP                          */
/* ------------------------------------------------------------------ */
.lyt-hero__btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--btn-bg, #3a8c3f);
    color: var(--btn-text, #ffffff);
    font-size: .95rem;
    font-weight: 600;
    letter-spacing: .04em;
    padding: 16px 34px;
    border-radius: 4px;
    transition: filter .25s, transform .2s, box-shadow .25s;
    box-shadow: 0 4px 20px rgba(0,0,0,.22);
    text-decoration: none;
}

.lyt-hero__btn:hover {
    filter: brightness(.88);
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(0,0,0,.3);
}

.lyt-hero__btn svg { transition: transform .2s; flex-shrink: 0; }
.lyt-hero__btn:hover svg { transform: translateX(4px); }

/* ------------------------------------------------------------------ */
/* Bolinhas de navegação                                                */
/* ------------------------------------------------------------------ */
.lyt-hero__dots {
    position: absolute;
    bottom: 44px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
    display: flex;
    gap: 8px;
    align-items: center;
}

.lyt-hero__dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,.38);
    cursor: pointer;
    padding: 0;
    transition: background .3s, transform .3s, width .3s;
}

.lyt-hero__dot.active {
    background: #fff;
    width: 24px;
    border-radius: 4px;
}

/* ------------------------------------------------------------------ */
/* Responsivo                                                           */
/* ------------------------------------------------------------------ */
@media (max-width: 768px) {
    .lyt-hero            { min-height: 75vh; }
    .lyt-hero__container { padding: 100px 24px 80px; }
    .lyt-hero__titulo    { max-width: 100%; }
    .lyt-hero__arrow     { display: none; }
}
</style>

<script>
(function () {
    const section = document.getElementById('<?= $uid ?>');
    if (!section) return;

    const slides  = section.querySelectorAll('.lyt-hero__slide');
    const dots    = section.querySelectorAll('.lyt-hero__dot');
    const btnPrev = section.querySelector('.lyt-hero__arrow--prev');
    const btnNext = section.querySelector('.lyt-hero__arrow--next');

    if (slides.length <= 1) return;

    const DELAY = 5000; // 5 segundos
    let current = 0;
    let timer   = null;
    let isDragging = false;
    let dragStart = 0;

    /* ---- Atualiza slides com efeito de slide ---- */
    function updateSlides() {
        slides.forEach((slide, i) => {
            slide.classList.remove('active', 'prev');
            if (i === current) {
                slide.classList.add('active');
            } else if (i < current) {
                slide.classList.add('prev');
            }
        });

        dots.forEach((dot, i) => {
            if (i === current) {
                dot.classList.add('active');
                dot.setAttribute('aria-selected', 'true');
            } else {
                dot.classList.remove('active');
                dot.setAttribute('aria-selected', 'false');
            }
        });
    }

    /* ---- Vai para o slide de índice idx ---- */
    function goTo(idx) {
        current = ((idx % slides.length) + slides.length) % slides.length;
        updateSlides();
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function startAuto() {
        clearInterval(timer);
        timer = setInterval(next, DELAY);
    }

    /* ---- Cliques nas setas ---- */
    btnPrev?.addEventListener('click', () => { prev(); startAuto(); });
    btnNext?.addEventListener('click', () => { next(); startAuto(); });

    /* ---- Cliques nas bolinhas ---- */
    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => { goTo(i); startAuto(); });
    });

    /* ---- Drag/Swipe em mobile e desktop ---- */
    section.addEventListener('mousedown', (e) => {
        isDragging = true;
        dragStart = e.clientX;
    }, { passive: true });

    section.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        // Poderia adicionar visual feedback aqui se desejar
    }, { passive: true });

    section.addEventListener('mouseup', (e) => {
        if (!isDragging) return;
        isDragging = false;
        const diff = dragStart - e.clientX;
        if (Math.abs(diff) > 50) {
            goTo(current + (diff > 0 ? 1 : -1));
            startAuto();
        }
    }, { passive: true });

    section.addEventListener('mouseleave', () => {
        isDragging = false;
    });

    /* ---- Swipe em mobile (touch) ---- */
    let touchStart = 0;
    section.addEventListener('touchstart', (e) => {
        touchStart = e.touches[0].clientX;
    }, { passive: true });

    section.addEventListener('touchend', (e) => {
        const diff = touchStart - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) {
            goTo(current + (diff > 0 ? 1 : -1));
            startAuto();
        }
    }, { passive: true });

    /* ---- Teclado (acessibilidade) ---- */
    section.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft')  { prev(); startAuto(); }
        if (e.key === 'ArrowRight') { next(); startAuto(); }
    });

    /* ---- Inicia ---- */
    updateSlides();
    startAuto();
})();
</script>