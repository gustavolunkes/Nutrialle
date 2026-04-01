<?php
$dados = is_string($conteudo['dados_json'])
    ? json_decode($conteudo['dados_json'], true)
    : $conteudo['dados_json'];
$dados = $dados ?: [];

$mapa_raw    = $dados['mapa_embed_url'] ?? '';
$mapa_altura = intval($dados['mapa_altura'] ?? 450);

// Aceita tanto <iframe> completo quanto só a URL
if (str_contains($mapa_raw, '<iframe')) {
    preg_match('/src=["\']([^"\']+)["\']/', $mapa_raw, $match);
    $mapa_embed_url = $match[1] ?? '';
} else {
    $mapa_embed_url = $mapa_raw;
}

$mapa_embed_url = htmlspecialchars($mapa_embed_url, ENT_QUOTES, 'UTF-8');
?>

<?php if ($mapa_embed_url): ?>
<div class="mapa-section">
    <div class="mapa-wrap" style="--mapa-h:<?= $mapa_altura ?>px">
        <iframe
            src="<?= $mapa_embed_url ?>"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Mapa de localização"
        ></iframe>
    </div>
</div>
<?php endif; ?>

<style>
.mapa-section {
    display: flex;
    justify-content: center;
    padding: 60px 20px;
}

.mapa-wrap {
    width: min(90%, 1150px);
    height: var(--mapa-h, 450px);
    overflow: hidden;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    transition: box-shadow 0.3s ease;
}

.mapa-wrap:hover {
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
}

.mapa-wrap iframe {
    width: 100%;
    height: 100%;
    border: 0;
    display: block;
}

@media (max-width: 600px) {
    .mapa-section {
        padding: 40px 16px;
    }
    .mapa-wrap {
        width: 100%;
        height: 300px;
        border-radius: 12px;
    }
}
</style>