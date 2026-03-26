<?php
/**
 * Layout: Hero Banner
 * Seção principal com título, subtítulo, imagem de fundo e botão
 */

// Decodifica os dados JSON
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

// Valores padrão
$titulo = $dados['titulo'] ?? '';
$subtitulo = $dados['subtitulo'] ?? '';
$imagem_fundo = $dados['imagem_fundo'] ?? '';
$botao_texto = $dados['botao_texto'] ?? '';
$botao_link = $dados['botao_link'] ?? '#';
?>

<section class="hero-banner" style="background-image: linear-gradient(rgba(0, 7, 28, 0.6), rgba(0, 7, 28, 0.6)), url('<?= htmlspecialchars($imagem_fundo) ?>');">
    <div class="hero-content">
        <?php if (!empty($titulo)): ?>
            <h1 class="hero-title"><?= htmlspecialchars($titulo) ?></h1>
        <?php endif; ?>
        
        <?php if (!empty($subtitulo)): ?>
            <p class="hero-subtitle"><?= htmlspecialchars($subtitulo) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($botao_texto)): ?>
            <a href="<?= htmlspecialchars($botao_link) ?>" class="hero-button">
                <?= htmlspecialchars($botao_texto) ?>
            </a>
        <?php endif; ?>
    </div>
</section>

<style>
.hero-banner {
    min-height: 500px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 60px 20px;
}

.hero-content {
    max-width: 800px;
    color: white;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.5rem;
    margin-bottom: 30px;
    opacity: 0.95;
}

.hero-button {
    display: inline-block;
    padding: 15px 40px;
    background: #fff;
    color: #00071c;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s;
}

.hero-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .hero-banner {
        min-height: 400px;
    }
}
</style>
