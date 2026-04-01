<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

$page_title       = 'Página não encontrada';
$meta_description = 'A página que você está procurando não foi encontrada.';
$meta_keywords    = '';

http_response_code(404);

include __DIR__ . '/includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap');

/* ── Reset & vars ── */
.nf-wrap *,
.nf-wrap *::before,
.nf-wrap *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.nf-wrap {
    --ink:    #0d0d0d;
    --ink-2:  #555;
    --ink-3:  #999;
    --line:   #e8e8e8;
    --bg:     #f8f7f4;
    --white:  #ffffff;
    --accent: #c0392b;

    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg);
    font-family: 'DM Sans', sans-serif;
    padding: 80px 24px;
    overflow: hidden;
    position: relative;
}

/* ── Background grid ── */
.nf-wrap::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(var(--line) 1px, transparent 1px),
        linear-gradient(90deg, var(--line) 1px, transparent 1px);
    background-size: 48px 48px;
    opacity: 0.55;
    pointer-events: none;
}

/* ── Inner container ── */
.nf-inner {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 80px;
    max-width: 900px;
    width: 100%;
    animation: nf-rise 0.7s cubic-bezier(.22,.68,0,1.2) both;
}

@keyframes nf-rise {
    from { opacity: 0; transform: translateY(32px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Left: big 404 ── */
.nf-display {
    position: relative;
    line-height: 1;
    user-select: none;
}

.nf-num {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(120px, 18vw, 200px);
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -6px;
    line-height: 1;
    display: block;
    position: relative;
    z-index: 2;
}

/* Red underline accent */
.nf-num::after {
    content: '';
    display: block;
    height: 4px;
    width: 56px;
    background: var(--accent);
    border-radius: 2px;
    margin-top: 16px;
    animation: nf-bar 0.8s cubic-bezier(.22,.68,0,1.2) 0.3s both;
    transform-origin: left;
}

@keyframes nf-bar {
    from { transform: scaleX(0); opacity: 0; }
    to   { transform: scaleX(1); opacity: 1; }
}

/* Decorative hollow circle */
.nf-circle {
    position: absolute;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    border: 1px solid var(--ink);
    opacity: 0.06;
    top: -40px;
    left: -60px;
    z-index: 1;
    animation: nf-spin 24s linear infinite;
}

@keyframes nf-spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

/* ── Right: content ── */
.nf-content {
    animation: nf-rise 0.7s cubic-bezier(.22,.68,0,1.2) 0.12s both;
}

.nf-eyebrow {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 20px;
    display: block;
}

.nf-heading {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(24px, 3.5vw, 36px);
    font-weight: 700;
    color: var(--ink);
    line-height: 1.25;
    margin-bottom: 16px;
}

.nf-body {
    font-size: 15px;
    font-weight: 300;
    color: var(--ink-2);
    line-height: 1.75;
    margin-bottom: 36px;
}

/* ── Actions ── */
.nf-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.nf-btn {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-decoration: none;
    border-radius: 4px;
    padding: 12px 22px;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
    cursor: pointer;
    border: none;
}

.nf-btn:hover {
    transform: translateY(-2px);
}

.nf-btn-primary {
    background: var(--ink);
    color: var(--white);
    box-shadow: 0 2px 0 rgba(0,0,0,0.35);
}

.nf-btn-primary:hover {
    background: #222;
    box-shadow: 0 6px 18px rgba(0,0,0,0.18);
}

.nf-btn-ghost {
    background: transparent;
    color: var(--ink-2);
    border: 1px solid var(--line);
}

.nf-btn-ghost:hover {
    border-color: #bbb;
    color: var(--ink);
    background: var(--white);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.nf-btn svg {
    flex-shrink: 0;
    width: 14px;
    height: 14px;
}

/* ── URL note ── */
.nf-url-note {
    margin-top: 28px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 12px;
    color: var(--ink-3);
    line-height: 1.5;
}

.nf-url-note::before {
    content: '';
    display: inline-block;
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: var(--ink-3);
    flex-shrink: 0;
    margin-top: 6px;
}

.nf-url-note code {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    background: rgba(0,0,0,0.05);
    padding: 1px 5px;
    border-radius: 3px;
    word-break: break-all;
}

/* ── Divider line ── */
.nf-divider {
    width: 1px;
    height: 200px;
    background: linear-gradient(to bottom, transparent, var(--line) 30%, var(--line) 70%, transparent);
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    display: none;
}

/* ── Responsive ── */
@media (max-width: 700px) {
    .nf-inner {
        grid-template-columns: 1fr;
        gap: 32px;
        text-align: center;
    }

    .nf-display {
        text-align: center;
    }

    .nf-num::after {
        margin: 16px auto 0;
    }

    .nf-circle {
        left: 50%;
        transform: translateX(-50%);
    }

    .nf-actions {
        justify-content: center;
    }

    .nf-url-note {
        justify-content: center;
    }
}
</style>

<main class="main-content">
    <section class="nf-wrap">

        <div class="nf-inner">

            <!-- Left: 404 display -->
            <div class="nf-display">
                <div class="nf-circle"></div>
                <span class="nf-num">404</span>
            </div>

            <!-- Right: message + actions -->
            <div class="nf-content">

                <span class="nf-eyebrow">Erro &mdash; página não encontrada</span>

                <h1 class="nf-heading">
                    Esse endereço<br>não existe mais.
                </h1>

                <p class="nf-body">
                    A página que você tentou acessar foi removida,
                    renomeada ou simplesmente nunca existiu por aqui.
                </p>

                <div class="nf-actions">
                    <a href="<?= BASE_URL ?>/" class="nf-btn nf-btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10"/>
                        </svg>
                        Ir para a home
                    </a>
                    <a href="javascript:history.back()" class="nf-btn nf-btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 5l-7 7 7 7"/>
                        </svg>
                        Voltar
                    </a>
                </div>

                <?php
                $uri = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                if (!empty($uri) && $uri !== '/'):
                ?>
                <div class="nf-url-note">
                    A URL <code><?= $uri ?></code> não foi encontrada no sistema.
                </div>
                <?php endif; ?>

            </div>

        </div>

    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
</body>
</html>