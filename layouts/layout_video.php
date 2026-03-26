<?php
/**
 * Layout: Vídeo
 * Player de vídeo responsivo (YouTube, Vimeo ou arquivo)
 */

// Decodifica os dados JSON
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

// Valores padrão
$titulo = $dados['titulo'] ?? '';
$url_video = $dados['url_video'] ?? '';
$descricao = $dados['descricao'] ?? '';

// Detecta o tipo de vídeo
$tipo_video = 'arquivo';
$video_embed = '';

if (strpos($url_video, 'youtube.com') !== false || strpos($url_video, 'youtu.be') !== false) {
    $tipo_video = 'youtube';
    
    // Extrai o ID do vídeo do YouTube
    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url_video, $matches);
    $video_id = $matches[1] ?? '';
    
    if ($video_id) {
        $video_embed = "https://www.youtube.com/embed/$video_id";
    }
} elseif (strpos($url_video, 'vimeo.com') !== false) {
    $tipo_video = 'vimeo';
    
    // Extrai o ID do vídeo do Vimeo
    preg_match('/vimeo\.com\/([0-9]+)/', $url_video, $matches);
    $video_id = $matches[1] ?? '';
    
    if ($video_id) {
        $video_embed = "https://player.vimeo.com/video/$video_id";
    }
}
?>

<section class="video-section">
    <div class="video-container">
        <?php if (!empty($titulo)): ?>
            <h2 class="video-titulo"><?= htmlspecialchars($titulo) ?></h2>
        <?php endif; ?>
        
        <div class="video-wrapper">
            <?php if ($tipo_video === 'youtube' || $tipo_video === 'vimeo'): ?>
                <iframe 
                    src="<?= htmlspecialchars($video_embed) ?>" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            <?php else: ?>
                <video controls>
                    <source src="<?= htmlspecialchars($url_video) ?>" type="video/mp4">
                    Seu navegador não suporta a reprodução de vídeos.
                </video>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($descricao)): ?>
            <div class="video-descricao">
                <?= nl2br(htmlspecialchars($descricao)) ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.video-section {
    padding: 80px 20px;
    background: #fff;
}

.video-container {
    max-width: 900px;
    margin: 0 auto;
}

.video-titulo {
    font-size: 2.5rem;
    color: #00071c;
    text-align: center;
    margin-bottom: 40px;
    font-weight: 700;
}

.video-wrapper {
    position: relative;
    padding-bottom: 56.25%; /* Proporção 16:9 */
    height: 0;
    overflow: hidden;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.video-wrapper iframe,
.video-wrapper video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 12px;
}

.video-descricao {
    margin-top: 30px;
    font-size: 1.1rem;
    line-height: 1.8;
    color: #555;
    text-align: center;
}

@media (max-width: 768px) {
    .video-section {
        padding: 50px 20px;
    }
    
    .video-titulo {
        font-size: 2rem;
    }
    
    .video-descricao {
        font-size: 1rem;
    }
}
</style>
