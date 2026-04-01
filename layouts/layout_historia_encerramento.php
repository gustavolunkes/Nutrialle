<?php
/**
 * Layout: Pilares & Encerramento — Nossa História
 * Texto de fechamento + grid de pilares institucionais
 *
 * campos_json:
 *   badge            — badge da seção (opcional)
 *   titulo           — título do bloco de encerramento
 *   texto_intro      — parágrafo de encerramento/conclusão
 *   cor_destaque     — cor dos ícones e bordas dos pilares (padrão #ff7200)
 *   fundo            — cor de fundo da seção (padrão #1d2a36)
 *   pilar1_titulo    — título do 1º pilar
 *   pilar1_descricao — descrição do 1º pilar (opcional)
 *   pilar2_titulo / pilar2_descricao
 *   pilar3_titulo / pilar3_descricao
 *   pilar4_titulo / pilar4_descricao
 *   pilar5_titulo / pilar5_descricao
 *   pilar6_titulo / pilar6_descricao
 *   botao_texto      — texto do botão CTA (opcional)
 *   botao_link       — URL completa do botão, obrigatória para exibir (ex: https://...)
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge       = $dados['badge']       ?? '';
$titulo      = $dados['titulo']      ?? '';
$texto_intro = $dados['texto_intro'] ?? '';
$cor         = htmlspecialchars($dados['cor_destaque'] ?? '#ff7200', ENT_QUOTES);
$fundo       = htmlspecialchars($dados['fundo']        ?? '#1d2a36', ENT_QUOTES);
$botao_texto = trim($dados['botao_texto'] ?? '');
$botao_link  = trim($dados['botao_link']  ?? '');

// Só exibe o botão se houver texto E uma URL completa válida (http:// ou https://)
$exibir_botao = !empty($botao_texto)
    && !empty($botao_link)
    && (str_starts_with($botao_link, 'https://') || str_starts_with($botao_link, 'http://'));

// Ícones SVG inline por posição (neutros, sem cor fixa)
$icones_svg = [
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
];

// Monta array de pilares dinamicamente (pilar1..pilar6)
$pilares = [];
for ($i = 1; $i <= 6; $i++) {
    $ptitulo = trim($dados["pilar{$i}_titulo"]    ?? '');
    $pdesc   = trim($dados["pilar{$i}_descricao"] ?? '');
    if (!empty($ptitulo)) {
        $pilares[] = [
            'titulo'    => $ptitulo,
            'descricao' => $pdesc,
            'icone'     => $icones_svg[$i - 1] ?? $icones_svg[0],
        ];
    }
}

$uid = 'enc' . substr(md5(uniqid()), 0, 6);
?>

<section class="lyt-encerra" id="<?= $uid ?>" style="background:<?= $fundo ?>;">
    <div class="lyt-encerra__container">

        <!-- Bloco de texto intro -->
        <?php if (!empty($badge) || !empty($titulo) || !empty($texto_intro)): ?>
            <div class="lyt-encerra__intro">
                <?php if (!empty($badge)): ?>
                    <span class="lyt-encerra__badge"
                          style="color:<?= $cor ?>;background:<?= $cor ?>1a;border-color:<?= $cor ?>66;">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($titulo)): ?>
                    <h2 class="lyt-encerra__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
                <?php endif; ?>

                <?php if (!empty($texto_intro)): ?>
                    <p class="lyt-encerra__texto"><?= nl2br(htmlspecialchars($texto_intro)) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Grid de pilares -->
        <?php if (!empty($pilares)): ?>
            <div class="lyt-encerra__grid lyt-encerra__grid--<?= count($pilares) ?>">
                <?php foreach ($pilares as $pilar): ?>
                    <div class="lyt-encerra__pilar">
                        <div class="lyt-encerra__pilar-icone" style="color:<?= $cor ?>;">
                            <?= $pilar['icone'] ?>
                        </div>
                        <h3 class="lyt-encerra__pilar-titulo"
                            style="border-bottom-color:<?= $cor ?>33;">
                            <?= htmlspecialchars($pilar['titulo']) ?>
                        </h3>
                        <?php if (!empty($pilar['descricao'])): ?>
                            <p class="lyt-encerra__pilar-desc">
                                <?= nl2br(htmlspecialchars($pilar['descricao'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- CTA — só exibe se houver texto E URL completa (http/https) -->
        <?php if ($exibir_botao): ?>
            <div class="lyt-encerra__cta">
                <a href="<?= htmlspecialchars($botao_link) ?>"
                   class="lyt-encerra__botao"
                   style="background:<?= $cor ?>;color:#fff;"
                   target="_blank"
                   rel="noopener noreferrer">
                    <?= htmlspecialchars($botao_texto) ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>

<style>
/* ============================================================
   ENCERRAMENTO & PILARES — NOSSA HISTÓRIA
   ============================================================ */
.lyt-encerra {
    padding: 80px 20px;
    overflow: hidden;
}

.lyt-encerra__container {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 56px;
}

/* --- Intro --- */
.lyt-encerra__intro {
    text-align: center;
    max-width: 760px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;
}

.lyt-encerra__badge {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 100px;
    border: 1px solid;
}

.lyt-encerra__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.3;
    letter-spacing: -0.02em;
    margin: 0;
}

.lyt-encerra__texto {
    font-size: 1rem;
    line-height: 1.85;
    color: rgba(255,255,255,0.72);
    margin: 0;
}

/* --- Grid de pilares --- */
.lyt-encerra__grid {
    display: grid;
    gap: 24px;
}

/* Adapta colunas ao número de pilares */
.lyt-encerra__grid--1 { grid-template-columns: 1fr; max-width: 360px; margin: 0 auto; }
.lyt-encerra__grid--2 { grid-template-columns: repeat(2, 1fr); }
.lyt-encerra__grid--3 { grid-template-columns: repeat(3, 1fr); }
.lyt-encerra__grid--4 { grid-template-columns: repeat(4, 1fr); }
.lyt-encerra__grid--5 { grid-template-columns: repeat(3, 1fr); }
.lyt-encerra__grid--6 { grid-template-columns: repeat(3, 1fr); }

/* Pilar card */
.lyt-encerra__pilar {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: background 0.25s, border-color 0.25s;
}
.lyt-encerra__pilar:hover {
    background: rgba(255,255,255,0.10);
    border-color: rgba(255,255,255,0.2);
}

.lyt-encerra__pilar-icone {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
}
.lyt-encerra__pilar-icone svg {
    width: 100%;
    height: 100%;
}

.lyt-encerra__pilar-titulo {
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.01em;
    margin: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid;
}

.lyt-encerra__pilar-desc {
    font-size: 0.9rem;
    line-height: 1.75;
    color: rgba(255,255,255,0.62);
    margin: 0;
}

/* --- CTA --- */
.lyt-encerra__cta {
    text-align: center;
}

.lyt-encerra__botao {
    display: inline-block;
    padding: 14px 36px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-decoration: none;
    transition: opacity 0.2s, transform 0.2s;
}
.lyt-encerra__botao:hover {
    opacity: 0.88;
    transform: translateY(-2px);
}

/* ============================================================
   MOBILE
   ============================================================ */
@media (max-width: 860px) {
    .lyt-encerra__grid--4,
    .lyt-encerra__grid--5,
    .lyt-encerra__grid--6 { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 600px) {
    .lyt-encerra { padding: 60px 20px; }

    .lyt-encerra__grid--2,
    .lyt-encerra__grid--3,
    .lyt-encerra__grid--4,
    .lyt-encerra__grid--5,
    .lyt-encerra__grid--6 { grid-template-columns: 1fr; }
}
</style>