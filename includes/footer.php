<?php
// includes/footer.php — Rodapé do site público
// Requer que $db já esteja disponível na página que incluiu este arquivo

$_rf_config  = $db->query("SELECT * FROM rodape_config LIMIT 1")->fetch();
$_rf_tema    = $db->query("SELECT * FROM rodape_cores WHERE ativo = 1 LIMIT 1")->fetch();
$_rf_colunas = $db->query("SELECT * FROM rodape_colunas WHERE ativo = 1 ORDER BY ordem ASC")->fetchAll();
$_rf_redes   = $db->query("SELECT * FROM rodape_redes_sociais WHERE ativo = 1 ORDER BY ordem ASC")->fetchAll();

// Cores com fallback
$_rf_bg    = $_rf_tema['cor_fundo']        ?? '#0a0f1e';
$_rf_txt   = $_rf_tema['cor_texto']        ?? '#ffffff';
$_rf_soft  = $_rf_tema['cor_texto_suave']  ?? '#8892a4';
$_rf_link  = $_rf_tema['cor_link']         ?? '#ffffff';
$_rf_hover = $_rf_tema['cor_link_hover']   ?? '#e91e8c';
$_rf_div   = $_rf_tema['cor_divisor']      ?? '#e91e8c';
$_rf_linha = $_rf_tema['cor_linha']        ?? '#1e2a3a';

// SVGs das redes sociais
$_rf_icones = [
    'facebook'  => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
    'instagram' => '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
    'x'         => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622 5.911-5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    'linkedin'  => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>',
    'youtube'   => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon fill="#fff" points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>',
    'tiktok'    => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.76a4.85 4.85 0 0 1-1.01-.07z"/></svg>',
    'whatsapp'  => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.532 5.862L.054 23.486a.5.5 0 0 0 .609.628l5.805-1.523A11.946 11.946 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.882a9.876 9.876 0 0 1-5.034-1.378l-.36-.214-3.733.979.997-3.648-.235-.374A9.847 9.847 0 0 1 2.118 12C2.118 6.533 6.533 2.118 12 2.118S21.882 6.533 21.882 12 17.467 21.882 12 21.882z"/></svg>',
    'telegram'  => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.26 13.447l-2.95-.924c-.64-.203-.658-.64.136-.954l11.57-4.461c.537-.194 1.006.131.878.113z"/></svg>',
    'github'    => '<svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0 1 12 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>',
];
?>

<style>
.site-footer {
    background: <?= $_rf_bg ?>;
    color: <?= $_rf_txt ?>;
    padding: 60px 0 0;
    font-family: inherit;
}
.site-footer a {
    color: <?= $_rf_link ?>;
    text-decoration: none;
}
.site-footer a:hover {
    color: <?= $_rf_hover ?>;
}
.footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    gap: 48px;
    flex-wrap: wrap;
    padding-bottom: 48px;
}
.footer-brand {
    flex: 1.5;
    min-width: 220px;
}
.footer-brand-name {
    font-size: 20px;
    font-weight: 800;
    color: <?= $_rf_txt ?>;
    margin-bottom: 10px;
    display: block;
}
.footer-brand-name img {
    max-height: 36px;
    display: block;
}
.footer-divisor {
    width: 36px;
    height: 3px;
    background: <?= $_rf_div ?>;
    border-radius: 2px;
    margin-bottom: 14px;
}
.footer-desc {
    font-size: 14px;
    color: <?= $_rf_soft ?>;
    line-height: 1.7;
    max-width: 280px;
}
.footer-col {
    flex: 1;
    min-width: 150px;
}
.footer-col-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: <?= $_rf_txt ?>;
    border-bottom: 2px solid <?= $_rf_div ?>;
    padding-bottom: 10px;
    margin-bottom: 16px;
}
.footer-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-col ul li {
    margin-bottom: 10px;
}
.footer-col ul li a {
    font-size: 14px;
    color: <?= $_rf_soft ?>;
}
.footer-col ul li a:hover {
    color: <?= $_rf_hover ?>;
}
.footer-redes {
    flex: 1;
    min-width: 150px;
}
.footer-redes-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: <?= $_rf_txt ?>;
    border-bottom: 2px solid <?= $_rf_div ?>;
    padding-bottom: 10px;
    margin-bottom: 16px;
}
.footer-redes-icons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.footer-rede-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: <?= $_rf_txt ?>;
    transition: background .2s, color .2s, transform .2s;
    border: 1px solid rgba(255,255,255,0.08);
}
.footer-rede-btn:hover {
    background: <?= $_rf_div ?>;
    color: #fff !important;
    transform: translateY(-2px);
}
.footer-bottom {
    border-top: 1px solid <?= $_rf_linha ?>;
    padding: 18px 24px;
    text-align: center;
    font-size: 13px;
    color: <?= $_rf_soft ?>;
    max-width: 100%;
}
@media (max-width: 768px) {
    .footer-inner { gap: 32px; }
    .footer-brand { flex: 100%; }
}
</style>

<footer class="site-footer">
    <div class="footer-inner">

        <!-- MARCA / DESCRIÇÃO -->
        <div class="footer-brand">
            <?php if (!empty($_rf_config['logo_url'])): ?>
                <a href="<?= BASE_URL ?>/" class="footer-brand-name">
                    <img src="<?= htmlspecialchars($_rf_config['logo_url']) ?>" alt="<?= htmlspecialchars($_rf_config['nome_empresa'] ?? SITE_NAME) ?>">
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/" class="footer-brand-name">
                    <?= htmlspecialchars($_rf_config['nome_empresa'] ?? SITE_NAME) ?>
                </a>
            <?php endif; ?>
            <div class="footer-divisor"></div>
            <?php if (!empty($_rf_config['descricao'])): ?>
                <p class="footer-desc"><?= htmlspecialchars($_rf_config['descricao']) ?></p>
            <?php endif; ?>
        </div>

        <!-- COLUNAS DE LINKS -->
        <?php foreach ($_rf_colunas as $col):
            $col_links = $db->prepare("SELECT * FROM rodape_links WHERE coluna_id = ? AND ativo = 1 ORDER BY ordem ASC");
            $col_links->execute([$col['id']]);
            $col_links = $col_links->fetchAll();
            if (empty($col_links)) continue;
        ?>
        <div class="footer-col">
            <div class="footer-col-title"><?= htmlspecialchars($col['titulo']) ?></div>
            <ul>
                <?php foreach ($col_links as $link): ?>
                    <li>
                        <a href="<?= htmlspecialchars($link['url']) ?>"
                           <?= !empty($link['target']) && $link['target'] !== '_self' ? 'target="'.htmlspecialchars($link['target']).'" rel="noopener"' : '' ?>>
                            <?= htmlspecialchars($link['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>

        <!-- REDES SOCIAIS -->
        <?php if (!empty($_rf_redes)): ?>
        <div class="footer-redes">
            <div class="footer-redes-title">Redes Sociais</div>
            <div class="footer-redes-icons">
                <?php foreach ($_rf_redes as $rede): ?>
                    <a href="<?= htmlspecialchars($rede['url']) ?>"
                       class="footer-rede-btn"
                       target="_blank" rel="noopener"
                       title="<?= ucfirst($rede['rede']) ?>">
                        <?= $_rf_icones[$rede['rede']] ?? htmlspecialchars(strtoupper(substr($rede['rede'], 0, 2))) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- BARRA DE COPYRIGHT -->
    <div class="footer-bottom">
        <?= htmlspecialchars($_rf_config['copyright_texto'] ?? '© ' . date('Y') . ' ' . SITE_NAME . '. Todos os direitos reservados.') ?>
    </div>

    <!-- BARRA WIRESTACK -->
    <div class="footer-dev">
        Desenvolvido por: <a href="https://wirestack.com.br/" target="_blank" rel="noopener">WireStack</a>
    </div>
</footer>

<style>
.footer-dev {
    background: rgba(0, 0, 0, 0.25);
    text-align: center;
    padding: 10px 24px;
    font-size: 12px;
    color: <?= $_rf_soft ?>;
}
.footer-dev a {
    color: <?= $_rf_div ?>;
    text-decoration: none;
    font-weight: 600;
    transition: opacity .2s;
}
.footer-dev a:hover {
    opacity: 0.8;
}
</style>