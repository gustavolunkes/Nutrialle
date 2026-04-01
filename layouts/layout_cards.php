<?php
/**
 * Layout: Cards Informativos (3 cards fixos)
 * Três cards com título, descrição e ícone por upload de imagem
 *
 * campos_json:
 *   titulo          — título da seção (opcional)
 *   subtitulo       — subtítulo da seção (opcional)
 *   fundo           — 'claro' | 'escuro' (padrão: claro)
 *   card1_titulo    — título do card 1
 *   card1_descricao — descrição do card 1
 *   card1_icone     — URL do ícone/imagem do card 1 (upload)
 *   card2_titulo    — título do card 2
 *   card2_descricao — descrição do card 2
 *   card2_icone     — URL do ícone/imagem do card 2 (upload)
 *   card3_titulo    — título do card 3
 *   card3_descricao — descrição do card 3
 *   card3_icone     — URL do ícone/imagem do card 3 (upload)
 *   cor_destaque    — cor em hexadecimal, ex: #3a8c3f (padrão: #3a8c3f)
 */

$dados = is_string($conteudo['dados_json'])
    ? json_decode($conteudo['dados_json'], true)
    : $conteudo['dados_json'];

$titulo    = $dados['titulo']    ?? '';
$subtitulo = $dados['subtitulo'] ?? '';
$fundo     = $dados['fundo']     ?? 'claro';
$cor       = $dados['cor_destaque'] ?? '#3a8c3f';

$isDark = ($fundo === 'escuro');

// Monta os 3 cards
$cards = [
    [
        'titulo'    => $dados['card1_titulo']    ?? 'Título do Card 1',
        'descricao' => $dados['card1_descricao'] ?? 'Adicione aqui a descrição do primeiro card.',
        'icone'     => $dados['card1_icone']     ?? '',
    ],
    [
        'titulo'    => $dados['card2_titulo']    ?? 'Título do Card 2',
        'descricao' => $dados['card2_descricao'] ?? 'Adicione aqui a descrição do segundo card.',
        'icone'     => $dados['card2_icone']     ?? '',
    ],
    [
        'titulo'    => $dados['card3_titulo']    ?? 'Título do Card 3',
        'descricao' => $dados['card3_descricao'] ?? 'Adicione aqui a descrição do terceiro card.',
        'icone'     => $dados['card3_icone']     ?? '',
    ],
];

$uid     = 'lytcards3_' . substr(md5(uniqid()), 0, 6);
$cor_esc = htmlspecialchars($cor, ENT_QUOTES);
?>

<style>
    #<?= $uid ?> { --section-accent: <?= $cor_esc ?>; }
    #<?= $uid ?> .lytc3__card { --card-accent: <?= $cor_esc ?>; cursor: pointer; }
</style>

<section class="lytc3 lytc3--<?= $isDark ? 'dark' : 'light' ?>" id="<?= $uid ?>">

    <div class="lytc3__bg-deco" aria-hidden="true"></div>

    <div class="lytc3__container">

        <?php if (!empty($titulo) || !empty($subtitulo)): ?>
            <header class="lytc3__header">
                <?php if (!empty($titulo)): ?>
                    <h2 class="lytc3__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
                <?php endif; ?>
                <?php if (!empty($subtitulo)): ?>
                    <p class="lytc3__subtitulo"><?= htmlspecialchars($subtitulo) ?></p>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <div class="lytc3__grid">
            <?php foreach ($cards as $card): ?>
                <article class="lytc3__card">

                    <?php if (!empty($card['icone'])): ?>
                        <div class="lytc3__icone-wrap">
                            <img
                                src="<?= htmlspecialchars($card['icone']) ?>"
                                alt="Ícone <?= htmlspecialchars($card['titulo']) ?>"
                                class="lytc3__icone-img"
                                loading="lazy"
                            >
                        </div>
                    <?php endif; ?>

                    <h3 class="lytc3__card-titulo"><?= htmlspecialchars($card['titulo']) ?></h3>
                    <p class="lytc3__card-desc"><?= nl2br(htmlspecialchars($card['descricao'])) ?></p>

                    <div class="lytc3__card-line" aria-hidden="true"></div>

                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<style>
/* =============================================
   Layout: Cards Informativos 3 colunas
   ============================================= */

.lytc3 {
    position: relative;
    padding: 100px 24px;
    overflow: hidden;
}

/* Temas */
.lytc3--light {
    background: #f3f4f6;
    color: #111827;
}
.lytc3--dark {
    background: #0b1120;
    color: #f1f5f9;
}

/* Decoração de fundo */
.lytc3__bg-deco {
    position: absolute;
    top: -120px;
    right: -120px;
    width: 520px;
    height: 520px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(58,140,63,0.07) 0%, transparent 70%);
    pointer-events: none;
}

/* Wrapper */
.lytc3__container {
    max-width: 1160px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Cabeçalho */
.lytc3__header {
    text-align: center;
    margin-bottom: 64px;
}

.lytc3__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.75rem, 3.2vw, 2.75rem);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.03em;
    margin: 0 0 16px;
}
.lytc3--light .lytc3__titulo { color: #0f172a; }
.lytc3--dark  .lytc3__titulo { color: #f8fafc; }

.lytc3__subtitulo {
    font-size: 1.05rem;
    line-height: 1.75;
    max-width: 560px;
    margin: 0 auto;
}
.lytc3--light .lytc3__subtitulo { color: #64748b; }
.lytc3--dark  .lytc3__subtitulo { color: rgba(248,250,252,0.55); }

/* Grid 3 colunas */
.lytc3__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
}

/* Card */
.lytc3__card {
    position: relative;
    padding: 44px 36px 40px;
    border-radius: 18px;
    overflow: hidden;
    text-align: center;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                box-shadow 0.3s ease;
}

.lytc3--light .lytc3__card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.lytc3--dark .lytc3__card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 2px 12px rgba(0,0,0,0.2);
}

/* Ícone via imagem (upload) */
.lytc3__icone-wrap {
    margin-bottom: 26px;
    display: flex;
    justify-content: center;
}

.lytc3__icone-img {
    width: 64px;
    height: 64px;
    object-fit: contain;
    border-radius: 14px;
    display: block;
}

/* Título do card */
.lytc3__card-titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.2rem;
    font-weight: 700;
    line-height: 1.35;
    margin: 0 0 14px;
    letter-spacing: -0.01em;
}
.lytc3--light .lytc3__card-titulo { color: #0f172a; }
.lytc3--dark  .lytc3__card-titulo { color: #f8fafc; }

/* Descrição do card */
.lytc3__card-desc {
    font-size: 0.95rem;
    line-height: 1.8;
    margin: 0;
}
.lytc3--light .lytc3__card-desc { color: #64748b; }
.lytc3--dark  .lytc3__card-desc { color: rgba(248,250,252,0.55); }

/* Linha decorativa no hover */
.lytc3__card-line {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--card-accent);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.35s ease;
    border-radius: 0 0 18px 18px;
}

/* Hover apenas em dispositivos que suportam (desktop) */
@media (hover: hover) {
    .lytc3__card:hover {
        transform: translateY(-6px);
    }
    .lytc3--light .lytc3__card:hover {
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .lytc3--dark .lytc3__card:hover {
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .lytc3__card:hover .lytc3__card-line {
        transform: scaleX(1);
    }
}

/* Responsivo */
@media (max-width: 900px) {
    .lytc3__grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 580px) {
    .lytc3 { padding: 70px 16px; }
    .lytc3__grid {
        grid-template-columns: 1fr;
    }
    .lytc3__card { padding: 36px 28px 32px; }
    .lytc3__header { margin-bottom: 48px; }
}
</style>