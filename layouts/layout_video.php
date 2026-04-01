<?php
/**
 * Layout: Vídeo
 * Player de vídeo responsivo (YouTube, Vimeo ou arquivo)
 */
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

$titulo      = $dados['titulo']     ?? '';
$url_video   = $dados['url_video']  ?? '';
$descricao   = $dados['descricao']  ?? '';
$thumbnail   = $dados['thumbnail']  ?? '';
$badge       = $dados['badge']      ?? '';

// Detecta tipo de vídeo e gera embed
function lyt_video_embed(string $url): string {
    if (empty($url)) return '';

    // YouTube
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0&modestbranding=1';
    }
    // Vimeo
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1';
    }
    // Arquivo de vídeo direto
    return $url;
}

$embed_url = lyt_video_embed($url_video);
$is_iframe = (strpos($embed_url, 'youtube.com/embed') !== false || strpos($embed_url, 'vimeo.com') !== false);
$uid = 'vid' . substr(md5(uniqid()), 0, 6);
?>

<section class="lyt-video">
    <div class="lyt-video__container">

        <?php if (!empty($badge) || !empty($titulo)): ?>
            <div class="lyt-video__header">
                <?php if (!empty($badge)): ?>
                    <span class="lyt-video__badge"><?= htmlspecialchars($badge) ?></span>
                <?php endif; ?>
                <?php if (!empty($titulo)): ?>
                    <h2 class="lyt-video__titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($url_video)): ?>
            <div class="lyt-video__player-wrap" id="<?= $uid ?>">
                <?php if (!empty($thumbnail)): ?>
                    <!-- Thumb com botão play — carrega embed ao clicar (performance) -->
                    <div class="lyt-video__thumb" data-embed="<?= htmlspecialchars($embed_url) ?>" data-iframe="<?= $is_iframe ? '1' : '0' ?>">
                        <img src="<?= htmlspecialchars($thumbnail) ?>" alt="<?= htmlspecialchars($titulo) ?>" loading="lazy">
                        <div class="lyt-video__play-btn" aria-label="Reproduzir vídeo">
                            <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="30" cy="30" r="30" fill="rgba(0,0,0,0.6)"/>
                                <path d="M24 20l18 10-18 10V20z" fill="#ffffff"/>
                            </svg>
                        </div>
                    </div>
                <?php elseif ($is_iframe): ?>
                    <iframe src="<?= htmlspecialchars($embed_url) ?>" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen loading="lazy" title="<?= htmlspecialchars($titulo) ?>"></iframe>
                <?php else: ?>
                    <video controls preload="metadata">
                        <source src="<?= htmlspecialchars($url_video) ?>">
                        Seu navegador não suporta o player de vídeo.
                    </video>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($descricao)): ?>
            <p class="lyt-video__descricao"><?= nl2br(htmlspecialchars($descricao)) ?></p>
        <?php endif; ?>

    </div>
</section>

<style>
.lyt-video {
    padding: 90px 20px;
    background: #f7f8fa;
}

.lyt-video__container {
    max-width: 960px;
    margin: 0 auto;
}

.lyt-video__header {
    text-align: center;
    margin-bottom: 44px;
}

.lyt-video__badge {
    display: inline-block;
    background: #f3eaea;
    color: #ff7200;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 100px;
    margin-bottom: 16px;
}

.lyt-video__titulo {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(1.6rem, 2.8vw, 2.4rem);
    font-weight: 700;
    color: #00071c;
    line-height: 1.3;
    letter-spacing: -0.02em;
}

/* Player */
.lyt-video__player-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 16/9;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    background: #000;
}

.lyt-video__player-wrap iframe,
.lyt-video__player-wrap video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: none;
}

/* Thumbnail com play */
.lyt-video__thumb {
    position: absolute;
    inset: 0;
    cursor: pointer;
}

.lyt-video__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}

.lyt-video__thumb:hover img {
    transform: scale(1.03);
}

.lyt-video__play-btn {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s;
}

.lyt-video__play-btn svg {
    width: 80px;
    height: 80px;
    filter: drop-shadow(0 4px 16px rgba(0,0,0,0.4));
    transition: transform 0.2s;
}

.lyt-video__thumb:hover .lyt-video__play-btn svg {
    transform: scale(1.1);
}

/* Descrição */
.lyt-video__descricao {
    margin-top: 32px;
    font-size: 1rem;
    color: #555;
    line-height: 1.8;
    text-align: center;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .lyt-video { padding: 60px 16px; }
    .lyt-video__play-btn svg { width: 60px; height: 60px; }
}
</style>

<script>
(function() {
    document.querySelectorAll('.lyt-video__thumb').forEach(function(thumb) {
        thumb.addEventListener('click', function() {
            const embedUrl = thumb.dataset.embed;
            const isIframe = thumb.dataset.iframe === '1';
            const wrap = thumb.parentElement;

            if (isIframe) {
                const iframe = document.createElement('iframe');
                iframe.src = embedUrl;
                iframe.frameBorder = '0';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                iframe.allowFullscreen = true;
                wrap.innerHTML = '';
                wrap.appendChild(iframe);
            } else {
                const video = document.createElement('video');
                video.src = embedUrl;
                video.controls = true;
                video.autoplay = true;
                wrap.innerHTML = '';
                wrap.appendChild(video);
            }
        });
    });
})();
</script>
