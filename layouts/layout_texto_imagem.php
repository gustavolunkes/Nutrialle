<?php
/**
 * Layout: Texto + Imagem
 * Seção com texto descritivo ao lado de uma imagem
 */

// Decodifica os dados JSON
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

// Valores padrão
$titulo = $dados['titulo'] ?? '';
$texto = $dados['texto'] ?? '';
$imagem = $dados['imagem'] ?? '';
$posicao_imagem = $dados['posicao_imagem'] ?? 'direita'; // 'esquerda' ou 'direita'
?>

<section class="texto-imagem <?= $posicao_imagem === 'esquerda' ? 'imagem-esquerda' : 'imagem-direita' ?>">
    <div class="texto-imagem-container">
        <div class="texto-imagem-content">
            <?php if (!empty($titulo)): ?>
                <h2 class="texto-imagem-titulo"><?= htmlspecialchars($titulo) ?></h2>
            <?php endif; ?>
            
            <?php if (!empty($texto)): ?>
                <div class="texto-imagem-texto">
                    <?= nl2br(htmlspecialchars($texto)) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($imagem)): ?>
            <div class="texto-imagem-imagem">
                <img src="<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($titulo) ?>">
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.texto-imagem {
    padding: 80px 20px;
    background: #f8f9fa;
}

.texto-imagem-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.imagem-esquerda .texto-imagem-container {
    grid-template-columns: 1fr 1fr;
}

.imagem-esquerda .texto-imagem-imagem {
    order: -1;
}

.texto-imagem-titulo {
    font-size: 2.5rem;
    color: #00071c;
    margin-bottom: 25px;
    font-weight: 700;
}

.texto-imagem-texto {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #555;
}

.texto-imagem-imagem img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .texto-imagem-container {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .imagem-esquerda .texto-imagem-imagem {
        order: 0;
    }
    
    .texto-imagem-titulo {
        font-size: 2rem;
    }
    
    .texto-imagem {
        padding: 50px 20px;
    }
}
</style>
