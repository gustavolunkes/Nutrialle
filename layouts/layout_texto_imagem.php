<?php
/**
 * Layout: Texto + Imagem
 * Posições: 'esquerda' | 'direita' | 'topo' | 'baixo'
 */
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge          = $dados['badge']          ?? 'Sobre nós';
$titulo         = $dados['titulo']         ?? '';
$texto          = $dados['texto']          ?? '';
$imagem         = $dados['imagem']         ?? '';
$imagem2        = $dados['imagem2']        ?? '';
$posicao_imagem = $dados['posicao_imagem'] ?? 'direita';
$tem_imagem2    = !empty($imagem2);

$pos_class = match($posicao_imagem) {
    'esquerda' => 'left',
    'topo'     => 'top',
    'baixo'    => 'bottom',
    default    => 'right',
};

// topo e baixo: inline style no container para nunca ser sobrescrito por CSS de outra instância
$container_attr = ($posicao_imagem === 'topo' || $posicao_imagem === 'baixo')
    ? ' style="display:flex;flex-direction:column;gap:48px;max-width:900px;margin:0 auto;"'
    : '';

// Blocos reutilizáveis
$bloco_texto = implode('', [
    !empty($badge)  ? '<span class="lyt-txtimg__badge">'  . htmlspecialchars($badge)  . '</span>' : '',
    !empty($titulo) ? '<h2 class="lyt-txtimg__titulo">'   . nl2br(htmlspecialchars($titulo)) . '</h2>' : '',
    !empty($texto)  ? '<div class="lyt-txtimg__texto">'   . nl2br(htmlspecialchars($texto))  . '</div>' : '',
]);

$bloco_media = implode('', [
    !empty($imagem)  ? '<div class="lyt-txtimg__img-principal"><img src="' . htmlspecialchars($imagem) . '" alt="' . htmlspecialchars($titulo) . '" loading="lazy"></div>' : '',
    $tem_imagem2     ? '<div class="lyt-txtimg__img-secundaria"><img src="' . htmlspecialchars($imagem2) . '" alt="" loading="lazy"></div>' : '',
]);
?>

<section class="lyt-txtimg lyt-txtimg--img-<?= $pos_class ?><?= $tem_imagem2 ? ' lyt-txtimg--has-img2' : '' ?>">
    <div class="lyt-txtimg__container"<?= $container_attr ?>>

        <?php if ($posicao_imagem === 'topo'): ?>

            <div class="lyt-txtimg__media"><?= $bloco_media ?></div>
            <div class="lyt-txtimg__content"><?= $bloco_texto ?></div>

        <?php elseif ($posicao_imagem === 'baixo'): ?>

            <div class="lyt-txtimg__content"><?= $bloco_texto ?></div>
            <div class="lyt-txtimg__media"><?= $bloco_media ?></div>

        <?php elseif ($posicao_imagem === 'esquerda'): ?>

            <div class="lyt-txtimg__media"><?= $bloco_media ?></div>
            <div class="lyt-txtimg__content"><?= $bloco_texto ?></div>

        <?php else: /* direita */ ?>

            <div class="lyt-txtimg__content"><?= $bloco_texto ?></div>
            <div class="lyt-txtimg__media"><?= $bloco_media ?></div>

        <?php endif; ?>

    </div>
</section>

<style>
/* ============================================================
   BASE
   ============================================================ */
.lyt-txtimg {
    padding: 80px 20px;
    background: #f3f4f6;
    overflow: hidden;
}
.lyt-txtimg--has-img2 {
    padding-bottom: 110px;
}

/* ============================================================
   GRID — esquerda / direita (duas colunas)
   ============================================================ */
.lyt-txtimg--img-left .lyt-txtimg__container,
.lyt-txtimg--img-right .lyt-txtimg__container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 72px;
    align-items: center;
}

/* ============================================================
   MÍDIA
   ============================================================ */
.lyt-txtimg__media {
    position: relative;
}
.lyt-txtimg__img-principal {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 16px 48px rgba(0,0,0,0.12);
}
.lyt-txtimg__img-principal img {
    width: 100%;
    height: auto;
    display: block;
}
.lyt-txtimg__img-secundaria {
    position: absolute;
    bottom: -40px;
    right: -28px;
    width: 46%;
    aspect-ratio: 4/3;
    border-radius: 8px;
    overflow: hidden;
    border: 5px solid #ffffff;
    box-shadow: 0 8px 28px rgba(0,0,0,0.16);
    z-index: 2;
}
.lyt-txtimg--img-left .lyt-txtimg__img-secundaria {
    right: auto;
    left: -28px;
}
.lyt-txtimg__img-secundaria img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* ============================================================
   TEXTO
   ============================================================ */
.lyt-txtimg__badge {
    display: inline-block;
    background: #f3eaea;
    color: #ff7200;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 100px;
    margin-bottom: 22px;
}
.lyt-txtimg__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.6rem, 2.8vw, 2.4rem);
    font-weight: 700;
    color: #00071c;
    line-height: 1.3;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
}
.lyt-txtimg__texto {
    font-size: 1rem;
    line-height: 1.85;
    color: #444;
}
.lyt-txtimg__texto p + p { margin-top: 14px; }

/* ============================================================
   MOBILE ≤ 900px
   ============================================================ */
@media (max-width: 900px) {
    .lyt-txtimg { padding: 60px 20px; }
    .lyt-txtimg--has-img2 { padding-bottom: 80px; }

    /* Duas colunas viram uma */
    .lyt-txtimg--img-left .lyt-txtimg__container,
    .lyt-txtimg--img-right .lyt-txtimg__container {
        grid-template-columns: 1fr;
        gap: 36px;
    }

    /*
     * esquerda/direita no mobile: HTML já tem texto antes de mídia
     * em ambos os casos (esquerda tem mídia|texto no HTML mas aqui
     * resetamos para fluxo natural = texto em cima, mídia embaixo)
     */
    .lyt-txtimg--img-left .lyt-txtimg__media,
    .lyt-txtimg--img-right .lyt-txtimg__media  { order: 2; }
    .lyt-txtimg--img-left .lyt-txtimg__content,
    .lyt-txtimg--img-right .lyt-txtimg__content { order: 1; }

    /* topo/baixo: HTML já define a ordem correta, sem override */

    .lyt-txtimg__img-secundaria {
        right: 10px !important;
        left: auto !important;
        bottom: -32px;
    }
}

@media (max-width: 520px) {
    .lyt-txtimg__img-secundaria { display: none; }
    .lyt-txtimg--has-img2 { padding-bottom: 60px; }
}
</style>