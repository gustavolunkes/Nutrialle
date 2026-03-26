<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();

// Define variáveis esperadas pelo header
$page_title       = 'Página não encontrada';
$meta_description = 'A página que você está procurando não foi encontrada.';
$meta_keywords    = '';

// Envia o status HTTP correto
http_response_code(404);

include __DIR__ . '/includes/header.php';
?>

<style>
/* ── 404 Page ── */
.not-found {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 24px;
    background: #f5f7fa;
    position: relative;
    overflow: hidden;
}

/* Círculos decorativos de fundo */
.not-found::before,
.not-found::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}
.not-found::before {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(var(--nf-accent-rgb, 30, 42, 58), 0.07) 0%, transparent 70%);
    top: -150px;
    right: -100px;
}
.not-found::after {
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(var(--nf-accent-rgb, 30, 42, 58), 0.05) 0%, transparent 70%);
    bottom: -100px;
    left: -80px;
}

.not-found-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.10), 0 4px 16px rgba(0,0,0,0.06);
    padding: 64px 56px;
    max-width: 560px;
    width: 100%;
    text-align: center;
    position: relative;
    z-index: 1;
    animation: nf-fade-up 0.5s ease both;
}

@keyframes nf-fade-up {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Número 404 */
.nf-code {
    font-size: clamp(96px, 20vw, 140px);
    font-weight: 900;
    line-height: 1;
    letter-spacing: -4px;
    background: linear-gradient(135deg, var(--hdr-bg, #00071c) 0%, var(--hdr-border, #1e2a3a) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
    position: relative;
    display: inline-block;
}

/* Bolinha animada no "0" do meio */
.nf-code::after {
    content: '';
    position: absolute;
    width: 12px;
    height: 12px;
    background: var(--hdr-nav-link, #e91e8c);
    border-radius: 50%;
    bottom: 18px;
    left: 50%;
    transform: translateX(-50%);
    animation: nf-pulse 1.8s ease-in-out infinite;
}

@keyframes nf-pulse {
    0%, 100% { transform: translateX(-50%) scale(1);   opacity: 1; }
    50%       { transform: translateX(-50%) scale(1.6); opacity: 0.5; }
}

.nf-divider {
    width: 48px;
    height: 3px;
    border-radius: 2px;
    background: var(--hdr-bg, #00071c);
    margin: 20px auto 24px;
    opacity: 0.15;
}

.nf-title {
    font-size: 22px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 12px;
}

.nf-desc {
    font-size: 15px;
    color: #6b7280;
    line-height: 1.7;
    margin-bottom: 36px;
}

/* Botões */
.nf-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.nf-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 26px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
    cursor: pointer;
}

.nf-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.nf-btn-primary {
    background: var(--hdr-bg, #00071c);
    color: var(--hdr-text, #ffffff);
}

.nf-btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.nf-btn-secondary:hover {
    background: #e5e7eb;
}

/* Ícone de seta (SVG inline) */
.nf-btn svg {
    flex-shrink: 0;
}

/* Mensagem do slug que tentou acessar */
.nf-slug-info {
    margin-top: 32px;
    padding: 12px 16px;
    background: #fef9ec;
    border: 1px solid #fde68a;
    border-radius: 8px;
    font-size: 13px;
    color: #92400e;
    word-break: break-all;
}

.nf-slug-info code {
    font-family: monospace;
    font-size: 12px;
    background: rgba(0,0,0,0.06);
    padding: 2px 6px;
    border-radius: 4px;
}

@media (max-width: 600px) {
    .not-found-card {
        padding: 44px 28px;
    }
    .nf-code {
        font-size: 96px;
    }
}
</style>

<main class="main-content">
    <section class="not-found">
        <div class="not-found-card">

            <div class="nf-code">404</div>

            <div class="nf-divider"></div>

            <h1 class="nf-title">Página não encontrada</h1>
            <p class="nf-desc">
                Ops! A página que você está procurando não existe,<br>
                foi removida ou o endereço está incorreto.
            </p>

            <div class="nf-actions">
                <a href="<?= BASE_URL ?>/" class="nf-btn nf-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/>
                    </svg>
                    Ir para a Home
                </a>
                <a href="javascript:history.back()" class="nf-btn nf-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 12H5M12 5l-7 7 7 7"/>
                    </svg>
                    Voltar
                </a>
            </div>

            <?php
            // Mostra a URL que tentou ser acessada
            $uri = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            if (!empty($uri) && $uri !== '/'):
            ?>
            <div class="nf-slug-info">
                A URL <code><?= $uri ?></code> não foi encontrada no sistema.
            </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/menu.js"></script>
</body>
</html>
