<?php
/**
 * Layout: Galeria de Imagens
 * Grid de imagens responsivo
 */

// Decodifica os dados JSON
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

// Valores padrão
$titulo = $dados['titulo'] ?? '';
$imagens = $dados['imagens'] ?? [];
$colunas = $dados['colunas'] ?? '3'; // 2, 3 ou 4 colunas
?>

<section class="galeria">
    <div class="galeria-container">
        <?php if (!empty($titulo)): ?>
            <h2 class="galeria-titulo"><?= htmlspecialchars($titulo) ?></h2>
        <?php endif; ?>
        
        <?php if (!empty($imagens) && is_array($imagens)): ?>
            <div class="galeria-grid colunas-<?= htmlspecialchars($colunas) ?>">
                <?php foreach ($imagens as $imagem): ?>
                    <?php if (is_array($imagem)): ?>
                        <div class="galeria-item">
                            <img src="<?= htmlspecialchars($imagem['url'] ?? '') ?>" 
                                 alt="<?= htmlspecialchars($imagem['alt'] ?? '') ?>"
                                 loading="lazy">
                            <?php if (!empty($imagem['legenda'])): ?>
                                <p class="galeria-legenda"><?= htmlspecialchars($imagem['legenda']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="galeria-item">
                            <img src="<?= htmlspecialchars($imagem) ?>" alt="" loading="lazy">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.galeria {
    padding: 80px 20px;
    background: #fff;
}

.galeria-container {
    max-width: 1200px;
    margin: 0 auto;
}

.galeria-titulo {
    font-size: 2.5rem;
    color: #00071c;
    text-align: center;
    margin-bottom: 50px;
    font-weight: 700;
}

.galeria-grid {
    display: grid;
    gap: 20px;
}

.galeria-grid.colunas-2 {
    grid-template-columns: repeat(2, 1fr);
}

.galeria-grid.colunas-3 {
    grid-template-columns: repeat(3, 1fr);
}

.galeria-grid.colunas-4 {
    grid-template-columns: repeat(4, 1fr);
}

.galeria-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.galeria-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.galeria-item img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    display: block;
}

.galeria-legenda {
    padding: 15px;
    background: #f8f9fa;
    margin: 0;
    font-size: 0.9rem;
    color: #555;
}

@media (max-width: 768px) {
    .galeria-grid.colunas-3,
    .galeria-grid.colunas-4 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .galeria {
        padding: 50px 20px;
    }
    
    .galeria-titulo {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .galeria-grid {
        grid-template-columns: 1fr;
    }
}
</style>
