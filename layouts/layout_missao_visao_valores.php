<?php
/**
 * Layout: Missão, Visão & Valores
 * Três cards institucionais em grid sobre fundo branco
 *
 * campos_json:
 *   badge            — badge da seção (opcional, ex: "Quem Somos")
 *   titulo_secao     — título acima dos cards (opcional)
 *   subtitulo_secao  — parágrafo introdutório (opcional)
 *   cor_destaque     — cor dos ícones, badge e bordas (padrão #ff7200)
 *
 *   card1_titulo     — título do 1º card (ex: "Nossa Missão")
 *   card1_texto      — texto do 1º card
 *   card2_titulo     — título do 2º card (ex: "Nossa Visão")
 *   card2_texto      — texto do 2º card
 *   card3_titulo     — título do 3º card (ex: "Nossos Valores")
 *   card3_texto      — texto do 3º card
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge           = trim($dados['badge']           ?? '');
$titulo_secao    = trim($dados['titulo_secao']    ?? '');
$subtitulo_secao = trim($dados['subtitulo_secao'] ?? '');
$cor             = htmlspecialchars($dados['cor_destaque'] ?? '#ff7200', ENT_QUOTES);

$cards = [
    [
        'titulo' => trim($dados['card1_titulo'] ?? 'Nossa Missão'),
        'texto'  => trim($dados['card1_texto']  ?? ''),
        'icone'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="2" x2="12" y2="5"/><line x1="12" y1="19" x2="12" y2="22"/><line x1="2" y1="12" x2="5" y2="12"/><line x1="19" y1="12" x2="22" y2="12"/></svg>',
    ],
    [
        'titulo' => trim($dados['card2_titulo'] ?? 'Nossa Visão'),
        'texto'  => trim($dados['card2_texto']  ?? ''),
        'icone'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
    ],
    [
        'titulo' => trim($dados['card3_titulo'] ?? 'Nossos Valores'),
        'texto'  => trim($dados['card3_texto']  ?? ''),
        'icone'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
    ],
];

// Remove cards sem texto
$cards = array_filter($cards, fn($c) => !empty($c['texto']));

$uid = 'mvv' . substr(md5(uniqid()), 0, 6);
?>

<section class="lyt-mvv" id="<?= $uid ?>">
    <div class="lyt-mvv__container">

        <!-- Cabeçalho da seção -->
        <?php if (!empty($badge) || !empty($titulo_secao) || !empty($subtitulo_secao)): ?>
            <div class="lyt-mvv__header">
                <?php if (!empty($badge)): ?>
                    <span class="lyt-mvv__badge"
                          style="color:<?= $cor ?>;background:<?= $cor ?>18;border-color:<?= $cor ?>55;">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($titulo_secao)): ?>
                    <h2 class="lyt-mvv__titulo"><?= nl2br(htmlspecialchars($titulo_secao)) ?></h2>
                <?php endif; ?>

                <?php if (!empty($subtitulo_secao)): ?>
                    <p class="lyt-mvv__subtitulo"><?= nl2br(htmlspecialchars($subtitulo_secao)) ?></p>
                <?php endif; ?>

                <div class="lyt-mvv__divisor" style="background:<?= $cor ?>;" aria-hidden="true"></div>
            </div>
        <?php endif; ?>

        <!-- Grid de cards -->
        <?php if (!empty($cards)): ?>
            <div class="lyt-mvv__grid">
                <?php foreach ($cards as $card): ?>
                    <div class="lyt-mvv__card">

                        <div class="lyt-mvv__card-icone-wrap" style="background:<?= $cor ?>18;">
                            <div class="lyt-mvv__card-icone" style="color:<?= $cor ?>;">
                                <?= $card['icone'] ?>
                            </div>
                        </div>

                        <div class="lyt-mvv__card-linha" style="background:<?= $cor ?>;"></div>

                        <h3 class="lyt-mvv__card-titulo"><?= htmlspecialchars($card['titulo']) ?></h3>

                        <p class="lyt-mvv__card-texto"><?= nl2br(htmlspecialchars($card['texto'])) ?></p>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<style>
/* ============================================================
   MISSÃO, VISÃO & VALORES
   ============================================================ */
.lyt-mvv {
    padding: 80px 20px;
    background: #ffffff;
}

.lyt-mvv__container {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 56px;
}

/* --- Cabeçalho --- */
.lyt-mvv__header {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.lyt-mvv__badge {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 100px;
    border: 1px solid;
}

.lyt-mvv__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 700;
    color: #00071c;
    line-height: 1.3;
    letter-spacing: -0.02em;
    margin: 0;
}

.lyt-mvv__subtitulo {
    font-size: 1rem;
    line-height: 1.8;
    color: #6b7280;
    max-width: 620px;
    margin: 0;
}

.lyt-mvv__divisor {
    width: 50px;
    height: 3px;
    border-radius: 2px;
    margin-top: 4px;
}

/* --- Grid --- */
.lyt-mvv__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
}

/* --- Card --- */
.lyt-mvv__card {
    background: #f9fafb;
    cursor: pointer;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 36px 28px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    transition: box-shadow 0.25s, transform 0.25s, border-color 0.25s;
}
.lyt-mvv__card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.09);
    transform: translateY(-4px);
    border-color: #d1d5db;
}

/* Ícone */
.lyt-mvv__card-icone-wrap {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.lyt-mvv__card-icone {
    width: 28px;
    height: 28px;
}
.lyt-mvv__card-icone svg {
    width: 100%;
    height: 100%;
}

/* Linha decorativa */
.lyt-mvv__card-linha {
    width: 36px;
    height: 2px;
    border-radius: 1px;
}

/* Título do card */
.lyt-mvv__card-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: #00071c;
    letter-spacing: -0.01em;
    margin: 0;
}

/* Texto do card */
.lyt-mvv__card-texto {
    font-size: 0.95rem;
    line-height: 1.85;
    color: #4b5563;
    margin: 0;
    flex: 1;
}

/* ============================================================
   TABLET ≤ 860px
   ============================================================ */
@media (max-width: 860px) {
    .lyt-mvv__grid {
        grid-template-columns: 1fr;
        max-width: 520px;
        margin: 0 auto;
    }

    .lyt-mvv__card:hover {
        transform: none;
    }
}

/* ============================================================
   MOBILE ≤ 600px
   ============================================================ */
@media (max-width: 600px) {
    .lyt-mvv { padding: 60px 20px; }
    .lyt-mvv__container { gap: 40px; }
    .lyt-mvv__card { padding: 28px 20px; }
}
</style>
