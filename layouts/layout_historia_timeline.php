<?php
/**
 * Layout: Linha do Tempo — Nossa História
 * Exibe até 6 marcos cronológicos em formato timeline vertical
 *
 * campos_json:
 *   badge              — badge da seção (opcional)
 *   titulo_secao       — título acima da timeline (opcional)
 *   cor_destaque       — cor dos marcadores e bordas (hex, padrão #ff7200)
 *   marco1_rotulo      — label do marco (ex: "A Conversa", "A Ideia")
 *   marco1_texto       — texto descritivo do marco
 *   marco2_rotulo / marco2_texto
 *   marco3_rotulo / marco3_texto
 *   marco4_rotulo / marco4_texto
 *   marco5_rotulo / marco5_texto
 *   marco6_rotulo / marco6_texto
 */

$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$badge        = $dados['badge']        ?? '';
$titulo_secao = $dados['titulo_secao'] ?? '';
$cor_destaque = $dados['cor_destaque'] ?? '#ff7200';

// Monta array de marcos dinamicamente (marco1..marco6)
$marcos = [];
for ($i = 1; $i <= 6; $i++) {
    $rotulo = trim($dados["marco{$i}_rotulo"] ?? '');
    $texto  = trim($dados["marco{$i}_texto"]  ?? '');
    if (!empty($rotulo) || !empty($texto)) {
        $marcos[] = ['rotulo' => $rotulo, 'texto' => $texto];
    }
}

$uid = 'tl' . substr(md5(uniqid()), 0, 6);
$cor = htmlspecialchars($cor_destaque, ENT_QUOTES);
?>

<section class="lyt-timeline" id="<?= $uid ?>">
    <div class="lyt-timeline__container">

        <?php if (!empty($badge) || !empty($titulo_secao)): ?>
            <div class="lyt-timeline__header">
                <?php if (!empty($badge)): ?>
                    <span class="lyt-timeline__badge"
                          style="color:<?= $cor ?>;background:<?= $cor ?>1a;border-color:<?= $cor ?>66;">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($titulo_secao)): ?>
                    <h2 class="lyt-timeline__titulo"><?= nl2br(htmlspecialchars($titulo_secao)) ?></h2>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($marcos)): ?>
            <div class="lyt-timeline__trilha">
                <!-- Linha vertical central -->
                <div class="lyt-timeline__linha" style="background:<?= $cor ?>33;" aria-hidden="true"></div>

                <?php foreach ($marcos as $idx => $marco): ?>
                    <?php $lado = ($idx % 2 === 0) ? 'esquerdo' : 'direito'; ?>
                    <div class="lyt-timeline__item lyt-timeline__item--<?= $lado ?>">

                        <!-- Marcador central -->
                        <div class="lyt-timeline__marcador" aria-hidden="true"
                             style="border-color:<?= $cor ?>;background:#fff;">
                            <div class="lyt-timeline__marcador-inner"
                                 style="background:<?= $cor ?>;"></div>
                        </div>

                        <!-- Card de conteúdo -->
                        <div class="lyt-timeline__card">
                            <?php if (!empty($marco['rotulo'])): ?>
                                <span class="lyt-timeline__rotulo"
                                      style="color:<?= $cor ?>;">
                                    <?= htmlspecialchars($marco['rotulo']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($marco['texto'])): ?>
                                <p class="lyt-timeline__texto">
                                    <?= nl2br(htmlspecialchars($marco['texto'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<style>
/* ============================================================
   TIMELINE — NOSSA HISTÓRIA
   ============================================================ */
.lyt-timeline {
    padding: 80px 20px;
    background: #f8f9fa;
    overflow: hidden;
}

.lyt-timeline__container {
    max-width: 1000px;
    margin: 0 auto;
}

/* Header */
.lyt-timeline__header {
    text-align: center;
    margin-bottom: 64px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.lyt-timeline__badge {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 100px;
    border: 1px solid;
}

.lyt-timeline__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 700;
    color: #00071c;
    line-height: 1.3;
    letter-spacing: -0.02em;
    margin: 0;
}

/* Trilha */
.lyt-timeline__trilha {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0;
}

.lyt-timeline__linha {
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 2px;
    transform: translateX(-50%);
    border-radius: 1px;
    z-index: 0;
}

/* Item */
.lyt-timeline__item {
    position: relative;
    display: grid;
    grid-template-columns: 1fr 40px 1fr;
    align-items: start;
    margin-bottom: 48px;
    z-index: 1;
}
.lyt-timeline__item:last-child { margin-bottom: 0; }

/* Marcador central (coluna do meio) */
.lyt-timeline__marcador {
    grid-column: 2;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid;
    display: flex;
    align-items: center;
    justify-content: center;
    justify-self: center;
    margin-top: 4px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    flex-shrink: 0;
}

.lyt-timeline__marcador-inner {
    width: 14px;
    height: 14px;
    border-radius: 50%;
}

/* Card */
.lyt-timeline__card {
    background: #ffffff;
    border-radius: 10px;
    padding: 24px 28px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    border: 1px solid #eaeaea;
}

/* Lado esquerdo: card na col 1, marcador na col 2, col 3 vazia */
.lyt-timeline__item--esquerdo .lyt-timeline__card {
    grid-column: 1;
    grid-row: 1;
    margin-right: 28px;
    text-align: right;
}
.lyt-timeline__item--esquerdo .lyt-timeline__marcador {
    grid-column: 2;
    grid-row: 1;
}

/* Lado direito: col 1 vazia, marcador na col 2, card na col 3 */
.lyt-timeline__item--direito .lyt-timeline__marcador {
    grid-column: 2;
    grid-row: 1;
}
.lyt-timeline__item--direito .lyt-timeline__card {
    grid-column: 3;
    grid-row: 1;
    margin-left: 28px;
}

/* Texto do card */
.lyt-timeline__rotulo {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.lyt-timeline__texto {
    font-size: 0.97rem;
    line-height: 1.8;
    color: #444;
    margin: 0;
}

/* ============================================================
   MOBILE ≤ 700px — tudo empilhado, linha à esquerda
   ============================================================ */
@media (max-width: 700px) {
    .lyt-timeline { padding: 60px 20px; }

    .lyt-timeline__trilha {
        padding-left: 52px;
    }

    .lyt-timeline__linha {
        left: 19px;
        transform: none;
    }

    .lyt-timeline__item {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 36px;
    }

    .lyt-timeline__marcador {
        position: absolute;
        left: -52px;
        top: 4px;
        width: 36px;
        height: 36px;
        margin: 0;
    }

    .lyt-timeline__marcador-inner {
        width: 12px;
        height: 12px;
    }

    /* Todos os cards ocupam largura total */
    .lyt-timeline__item--esquerdo .lyt-timeline__card,
    .lyt-timeline__item--direito .lyt-timeline__card {
        grid-column: unset;
        grid-row: unset;
        margin: 0;
        text-align: left;
        width: 100%;
    }
}
</style>
