<?php
/**
 * Layout: Benefícios — Categoria de Produto
 * Grid de benefícios/diferenciais da categoria com ícone, título e texto
 * Até 6 benefícios configuráveis + botão CTA centralizado
 *
 * campos_json:
 *   badge            — badge da seção (opcional)
 *   titulo           — título da seção
 *   subtitulo        — subtítulo/intro da seção (opcional)
 *   imagem_fundo     — URL de imagem de fundo sutil (opcional)
 *   fundo            — 'claro' | 'escuro' (padrão: claro)
 *   cor_destaque     — cor dos ícones e elementos de destaque (padrão #ff7200)
 *   benef1_titulo    — título do benefício 1
 *   benef1_texto     — descrição do benefício 1
 *   benef2_titulo / benef2_texto
 *   benef3_titulo / benef3_texto
 *   benef4_titulo / benef4_texto
 *   benef5_titulo / benef5_texto
 *   benef6_titulo / benef6_texto
 *   botao_texto      — texto do botão CTA
 *   botao_link       — URL completa da página de produtos
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge        = trim($dados['badge']        ?? '');
$titulo       = trim($dados['titulo']       ?? '');
$subtitulo    = trim($dados['subtitulo']    ?? '');
$imagem_fundo = trim($dados['imagem_fundo'] ?? '');
$fundo        = $dados['fundo']             ?? 'claro';
$cor          = htmlspecialchars($dados['cor_destaque'] ?? '#ff7200', ENT_QUOTES);
$botao_texto  = trim($dados['botao_texto']  ?? '');
$botao_link   = trim($dados['botao_link']   ?? '');

$isDark       = ($fundo === 'escuro');
$exibir_botao = !empty($botao_texto) && !empty($botao_link);

// Ícones SVG por posição
$icones = [
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
];

// Monta benefícios (ignora os vazios)
$beneficios = [];
for ($i = 1; $i <= 6; $i++) {
    $bt = trim($dados["benef{$i}_titulo"] ?? '');
    $bx = trim($dados["benef{$i}_texto"]  ?? '');
    if (!empty($bt)) {
        $beneficios[] = [
            'titulo' => $bt,
            'texto'  => $bx,
            'icone'  => $icones[$i - 1] ?? $icones[0],
        ];
    }
}

$total = count($beneficios);
$uid   = 'catbenef' . substr(md5(uniqid()), 0, 6);
?>

<style>
    #<?= $uid ?> { --benef-accent: <?= $cor ?>; }
</style>

<section class="lyt-catbenef lyt-catbenef--<?= $isDark ? 'dark' : 'light' ?>" id="<?= $uid ?>"
    <?= !empty($imagem_fundo) ? "style=\"background-image:url('" . htmlspecialchars($imagem_fundo) . "')\"" : '' ?>>

    <?php if (!empty($imagem_fundo)): ?>
        <div class="lyt-catbenef__bg-overlay" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="lyt-catbenef__container">

        <!-- Cabeçalho -->
        <?php if (!empty($badge) || !empty($titulo) || !empty($subtitulo)): ?>
            <div class="lyt-catbenef__header">
                <?php if (!empty($badge)): ?>
                    <span class="lyt-catbenef__badge"><?= htmlspecialchars($badge) ?></span>
                <?php endif; ?>
                <?php if (!empty($titulo)): ?>
                    <h2 class="lyt-catbenef__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
                <?php endif; ?>
                <?php if (!empty($subtitulo)): ?>
                    <p class="lyt-catbenef__subtitulo"><?= htmlspecialchars($subtitulo) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Grid de benefícios -->
        <?php if (!empty($beneficios)): ?>
            <div class="lyt-catbenef__grid lyt-catbenef__grid--<?= $total ?>">
                <?php foreach ($beneficios as $benef): ?>
                    <div class="lyt-catbenef__item">
                        <div class="lyt-catbenef__icone-wrap">
                            <div class="lyt-catbenef__icone">
                                <?= $benef['icone'] ?>
                            </div>
                        </div>
                        <div class="lyt-catbenef__item-content">
                            <h3 class="lyt-catbenef__item-titulo"><?= htmlspecialchars($benef['titulo']) ?></h3>
                            <?php if (!empty($benef['texto'])): ?>
                                <p class="lyt-catbenef__item-texto"><?= nl2br(htmlspecialchars($benef['texto'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- CTA -->
        <?php if ($exibir_botao): ?>
            <div class="lyt-catbenef__cta">
                <a href="<?= htmlspecialchars($botao_link) ?>"
                   class="lyt-catbenef__btn">
                    <?= htmlspecialchars($botao_texto) ?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>

<style>
/* ============================================================
   BENEFÍCIOS — CATEGORIA DE PRODUTO
   ============================================================ */
.lyt-catbenef {
    position: relative;
    padding: 90px 24px;
    background-size: cover;
    background-position: center;
    overflow: hidden;
}

.lyt-catbenef--light { background-color: #f3f4f6; }
.lyt-catbenef--dark  { background-color: #1d2a36; }

/* Overlay quando há imagem de fundo */
.lyt-catbenef__bg-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 0;
}

.lyt-catbenef__container {
    position: relative;
    z-index: 1;
    max-width: 1160px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 56px;
}

/* --- Cabeçalho --- */
.lyt-catbenef__header {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.lyt-catbenef__badge {
    display: inline-block;
    background: var(--benef-accent);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 100px;
}

.lyt-catbenef__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.02em;
    margin: 0;
}
.lyt-catbenef--light .lyt-catbenef__titulo { color: #00071c; }
.lyt-catbenef--dark  .lyt-catbenef__titulo,
.lyt-catbenef:has(.lyt-catbenef__bg-overlay) .lyt-catbenef__titulo { color: #ffffff; }

.lyt-catbenef__subtitulo {
    font-size: 1rem;
    line-height: 1.8;
    max-width: 600px;
    margin: 0;
}
.lyt-catbenef--light .lyt-catbenef__subtitulo { color: #6b7280; }
.lyt-catbenef--dark  .lyt-catbenef__subtitulo,
.lyt-catbenef:has(.lyt-catbenef__bg-overlay) .lyt-catbenef__subtitulo { color: rgba(255,255,255,0.72); }

/* --- Grid --- */
.lyt-catbenef__grid {
    display: grid;
    gap: 24px;
}

.lyt-catbenef__grid--1 { grid-template-columns: 1fr; max-width: 500px; margin: 0 auto; }
.lyt-catbenef__grid--2 { grid-template-columns: repeat(2, 1fr); }
.lyt-catbenef__grid--3 { grid-template-columns: repeat(3, 1fr); }
.lyt-catbenef__grid--4 { grid-template-columns: repeat(2, 1fr); }
.lyt-catbenef__grid--5,
.lyt-catbenef__grid--6 { grid-template-columns: repeat(3, 1fr); }

/* --- Item --- */
.lyt-catbenef__item {
    display: flex;
    gap: 18px;
    align-items: flex-start;
    padding: 28px 24px;
    border-radius: 12px;
    transition: transform 0.25s, box-shadow 0.25s;
}

.lyt-catbenef--light .lyt-catbenef__item {
    background: #ffffff;
    border: 1px solid #e5e7eb;
}
.lyt-catbenef--dark .lyt-catbenef__item,
.lyt-catbenef:has(.lyt-catbenef__bg-overlay) .lyt-catbenef__item {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
}

@media (hover: hover) {
    .lyt-catbenef__item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(0,0,0,0.10);
    }
}

/* Ícone */
.lyt-catbenef__icone-wrap {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: color-mix(in srgb, var(--benef-accent) 14%, transparent);
    display: flex;
    align-items: center;
    justify-content: center;
}

.lyt-catbenef__icone {
    width: 24px;
    height: 24px;
    color: var(--benef-accent);
}
.lyt-catbenef__icone svg {
    width: 100%;
    height: 100%;
}

/* Conteúdo do item */
.lyt-catbenef__item-titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 8px;
    letter-spacing: -0.01em;
}
.lyt-catbenef--light .lyt-catbenef__item-titulo { color: #00071c; }
.lyt-catbenef--dark  .lyt-catbenef__item-titulo,
.lyt-catbenef:has(.lyt-catbenef__bg-overlay) .lyt-catbenef__item-titulo { color: #ffffff; }

.lyt-catbenef__item-texto {
    font-size: 0.9rem;
    line-height: 1.75;
    margin: 0;
}
.lyt-catbenef--light .lyt-catbenef__item-texto { color: #6b7280; }
.lyt-catbenef--dark  .lyt-catbenef__item-texto,
.lyt-catbenef:has(.lyt-catbenef__bg-overlay) .lyt-catbenef__item-texto { color: rgba(255,255,255,0.65); }

/* --- CTA --- */
.lyt-catbenef__cta {
    text-align: center;
}

.lyt-catbenef__btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--benef-accent);
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    padding: 16px 36px;
    border-radius: 6px;
    text-decoration: none;
    transition: filter 0.25s, transform 0.2s;
}
.lyt-catbenef__btn:hover {
    filter: brightness(0.88);
    transform: translateY(-2px);
}
.lyt-catbenef__btn svg { transition: transform 0.2s; flex-shrink: 0; }
.lyt-catbenef__btn:hover svg { transform: translateX(4px); }

/* ============================================================
   MOBILE
   ============================================================ */
@media (max-width: 860px) {
    .lyt-catbenef__grid--3,
    .lyt-catbenef__grid--5,
    .lyt-catbenef__grid--6 { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 580px) {
    .lyt-catbenef { padding: 64px 16px; }

    .lyt-catbenef__grid--2,
    .lyt-catbenef__grid--3,
    .lyt-catbenef__grid--4,
    .lyt-catbenef__grid--5,
    .lyt-catbenef__grid--6 { grid-template-columns: 1fr; }

    .lyt-catbenef__item { flex-direction: column; gap: 14px; }
}
</style>
