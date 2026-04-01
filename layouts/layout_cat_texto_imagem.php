<?php
/**
 * Layout: Texto + Imagem — Categoria de Produto
 * Bloco institucional com texto descritivo, imagem lateral e botão CTA
 *
 * campos_json:
 *   badge           — badge da seção (ex: "Bovinos de Leite")
 *   titulo          — título do bloco
 *   texto           — texto descritivo da categoria (suporta quebras de linha)
 *   imagem          — URL da imagem (upload)
 *   posicao_imagem  — 'direita' | 'esquerda' (padrão: direita)
 *   fundo           — 'claro' | 'escuro' (padrão: claro)
 *   cor_destaque    — cor do badge e botão (padrão #ff7200)
 *   botao_texto     — texto do botão CTA
 *   botao_link      — URL completa da página de produtos
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge          = trim($dados['badge']          ?? '');
$titulo         = trim($dados['titulo']         ?? '');
$texto          = trim($dados['texto']          ?? '');
$imagem         = trim($dados['imagem']         ?? '');
$posicao        = $dados['posicao_imagem']      ?? 'direita';
$fundo          = $dados['fundo']               ?? 'claro';
$cor            = htmlspecialchars($dados['cor_destaque'] ?? '#ff7200', ENT_QUOTES);
$botao_texto    = trim($dados['botao_texto']    ?? '');
$botao_link     = trim($dados['botao_link']     ?? '');

$isDark       = ($fundo === 'escuro');
$img_esquerda = ($posicao === 'esquerda');
$exibir_botao = !empty($botao_texto) && !empty($botao_link);

$uid = 'cattxtimg' . substr(md5(uniqid()), 0, 6);
?>

<style>
    #<?= $uid ?> { --cat-accent: <?= $cor ?>; }
</style>

<section class="lyt-cattxt lyt-cattxt--<?= $isDark ? 'dark' : 'light' ?>" id="<?= $uid ?>">
    <div class="lyt-cattxt__container lyt-cattxt__container--<?= $img_esquerda ? 'left' : 'right' ?>">

        <!-- Bloco de texto -->
        <div class="lyt-cattxt__content">

            <?php if (!empty($badge)): ?>
                <span class="lyt-cattxt__badge"><?= htmlspecialchars($badge) ?></span>
            <?php endif; ?>

            <?php if (!empty($titulo)): ?>
                <h2 class="lyt-cattxt__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
            <?php endif; ?>

            <div class="lyt-cattxt__divisor" aria-hidden="true"></div>

            <?php if (!empty($texto)): ?>
                <div class="lyt-cattxt__texto"><?= nl2br(htmlspecialchars($texto)) ?></div>
            <?php endif; ?>

            <?php if ($exibir_botao): ?>
                <a href="<?= htmlspecialchars($botao_link) ?>"
                   class="lyt-cattxt__btn">
                    <?= htmlspecialchars($botao_texto) ?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>

        </div>

        <!-- Imagem -->
        <?php if (!empty($imagem)): ?>
            <div class="lyt-cattxt__media">
                <div class="lyt-cattxt__img-wrap">
                    <img src="<?= htmlspecialchars($imagem) ?>"
                         alt="<?= htmlspecialchars($titulo) ?>"
                         class="lyt-cattxt__img"
                         loading="lazy">
                </div>
                <div class="lyt-cattxt__img-deco" aria-hidden="true"></div>
            </div>
        <?php endif; ?>

    </div>
</section>

<style>
/* ============================================================
   TEXTO + IMAGEM — CATEGORIA DE PRODUTO
   ============================================================ */
.lyt-cattxt {
    padding: 90px 24px;
    overflow: hidden;
}

.lyt-cattxt--light { background: #ffffff; }
.lyt-cattxt--dark  { background: #1d2a36; }

/* Grid */
.lyt-cattxt__container {
    max-width: 1160px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 72px;
    align-items: center;
}

/* Inverte a ordem visual quando imagem à esquerda */
.lyt-cattxt__container--left .lyt-cattxt__media  { order: -1; }
.lyt-cattxt__container--left .lyt-cattxt__content { order: 1; }

/* --- Conteúdo --- */
.lyt-cattxt__badge {
    display: inline-block;
    background: var(--cat-accent, #ff7200);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 100px;
    margin-bottom: 20px;
}

.lyt-cattxt__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.6rem, 3vw, 2.6rem);
    font-weight: 700;
    line-height: 1.25;
    letter-spacing: -0.02em;
    margin: 0 0 20px;
}
.lyt-cattxt--light .lyt-cattxt__titulo { color: #00071c; }
.lyt-cattxt--dark  .lyt-cattxt__titulo { color: #ffffff; }

.lyt-cattxt__divisor {
    width: 48px;
    height: 3px;
    background: var(--cat-accent, #ff7200);
    border-radius: 2px;
    margin-bottom: 24px;
}

.lyt-cattxt__texto {
    font-size: 1rem;
    line-height: 1.85;
    margin-bottom: 36px;
}
.lyt-cattxt--light .lyt-cattxt__texto { color: #4b5563; }
.lyt-cattxt--dark  .lyt-cattxt__texto { color: rgba(255,255,255,0.72); }

.lyt-cattxt__btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--cat-accent, #ff7200);
    color: #ffffff;
    font-size: 0.93rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    padding: 14px 30px;
    border-radius: 6px;
    text-decoration: none;
    transition: filter 0.25s, transform 0.2s;
}
.lyt-cattxt__btn:hover {
    filter: brightness(0.88);
    transform: translateY(-2px);
}
.lyt-cattxt__btn svg { transition: transform 0.2s; flex-shrink: 0; }
.lyt-cattxt__btn:hover svg { transform: translateX(4px); }

/* --- Imagem --- */
.lyt-cattxt__media {
    position: relative;
}

.lyt-cattxt__img-wrap {
    position: relative;
    z-index: 2;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.14);
}

.lyt-cattxt__img {
    width: 100%;
    height: 460px;
    object-fit: cover;
    display: block;
}

/* Decoração geométrica atrás da imagem */
.lyt-cattxt__img-deco {
    position: absolute;
    bottom: -20px;
    right: -20px;
    width: 60%;
    height: 60%;
    background: var(--cat-accent, #ff7200);
    opacity: 0.12;
    border-radius: 14px;
    z-index: 1;
}
.lyt-cattxt__container--left .lyt-cattxt__img-deco {
    right: auto;
    left: -20px;
}

/* ============================================================
   MOBILE ≤ 860px
   ============================================================ */
@media (max-width: 860px) {
    .lyt-cattxt { padding: 64px 24px; }

    .lyt-cattxt__container {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    /* Imagem sempre embaixo no mobile */
    .lyt-cattxt__container--left .lyt-cattxt__media,
    .lyt-cattxt__container--right .lyt-cattxt__media { order: 2; }
    .lyt-cattxt__container--left .lyt-cattxt__content,
    .lyt-cattxt__container--right .lyt-cattxt__content { order: 1; }

    .lyt-cattxt__img { height: 300px; }
}
</style>
