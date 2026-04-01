<?php
/**
 * Layout: Hero — Nossa História
 * Cabeçalho de seção com badge, título, subtítulo e imagem de fundo opcional
 *
 * campos_json:
 *   badge         — texto do badge (ex: "Nossa História")
 *   titulo        — título principal da seção
 *   subtitulo     — parágrafo de abertura
 *   imagem_fundo  — URL da imagem de fundo (opcional, usa gradiente se vazio)
 *   cor_overlay   — cor do overlay sobre a imagem (hex, padrão #00071c)
 *   opacidade     — opacidade do overlay 0-1 (padrão 0.65)
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge        = $dados['badge']        ?? 'Nossa História';
$titulo       = $dados['titulo']       ?? 'Uma história construída com propósito';
$subtitulo    = $dados['subtitulo']    ?? '';
$imagem_fundo = $dados['imagem_fundo'] ?? '';
$cor_overlay  = $dados['cor_overlay']  ?? '#00071c';
$opacidade    = $dados['opacidade']    ?? '0.65';

$uid = 'hh' . substr(md5(uniqid()), 0, 6);

$bg_style = !empty($imagem_fundo)
    ? "background-image:url('" . htmlspecialchars($imagem_fundo) . "');"
    : '';
?>

<section class="lyt-hist-hero" id="<?= $uid ?>"
         <?= !empty($bg_style) ? "style=\"{$bg_style}\"" : '' ?>>

    <?php if (!empty($imagem_fundo)): ?>
        <div class="lyt-hist-hero__overlay"
             style="background:<?= htmlspecialchars($cor_overlay) ?>;opacity:<?= htmlspecialchars($opacidade) ?>;"></div>
    <?php endif; ?>

    <div class="lyt-hist-hero__container">
        <?php if (!empty($badge)): ?>
            <span class="lyt-hist-hero__badge"><?= htmlspecialchars($badge) ?></span>
        <?php endif; ?>

        <?php if (!empty($titulo)): ?>
            <h2 class="lyt-hist-hero__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
        <?php endif; ?>

        <?php if (!empty($subtitulo)): ?>
            <p class="lyt-hist-hero__subtitulo"><?= nl2br(htmlspecialchars($subtitulo)) ?></p>
        <?php endif; ?>

        <div class="lyt-hist-hero__linha" aria-hidden="true"></div>
    </div>
</section>

<style>
/* ============================================================
   HERO — NOSSA HISTÓRIA
   ============================================================ */
.lyt-hist-hero {
    position: relative;
    padding: 100px 20px 80px;
    background: linear-gradient(135deg, #1d2a36 0%, #1d2a36 100%);
    background-size: cover;
    background-position: center;
    text-align: center;
    overflow: hidden;
}

.lyt-hist-hero__overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
}

.lyt-hist-hero__container {
    position: relative;
    z-index: 2;
    max-width: 780px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.lyt-hist-hero__badge {
    display: inline-block;
    background: rgba(255, 114, 0, 0.15);
    color: #ff7200;
    border: 1px solid rgba(255, 114, 0, 0.4);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    padding: 7px 18px;
    border-radius: 100px;
}

.lyt-hist-hero__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.25;
    letter-spacing: -0.02em;
    margin: 0;
}

.lyt-hist-hero__subtitulo {
    font-size: clamp(1rem, 1.6vw, 1.15rem);
    line-height: 1.8;
    color: rgba(255,255,255,0.78);
    max-width: 640px;
    margin: 0;
}

.lyt-hist-hero__linha {
    width: 60px;
    height: 3px;
    background: #ff7200;
    border-radius: 2px;
    margin-top: 8px;
}

/* ============================================================
   MOBILE
   ============================================================ */
@media (max-width: 600px) {
    .lyt-hist-hero {
        padding: 72px 24px 60px;
    }
}
</style>
