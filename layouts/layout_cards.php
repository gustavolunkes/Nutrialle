<?php
/**
 * Layout: Cards Informativos
 * Cards em grid com ícone, título e descrição
 */

// Decodifica os dados JSON
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

// Valores padrão
$titulo = $dados['titulo'] ?? '';
$cards = $dados['cards'] ?? [];
?>

<section class="cards-section">
    <div class="cards-container">
        <?php if (!empty($titulo)): ?>
            <h2 class="cards-titulo"><?= htmlspecialchars($titulo) ?></h2>
        <?php endif; ?>
        
        <?php if (!empty($cards) && is_array($cards)): ?>
            <div class="cards-grid">
                <?php foreach ($cards as $card): ?>
                    <div class="card-item">
                        <?php if (!empty($card['icone'])): ?>
                            <div class="card-icone">
                                <?= $card['icone'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($card['titulo'])): ?>
                            <h3 class="card-titulo"><?= htmlspecialchars($card['titulo']) ?></h3>
                        <?php endif; ?>
                        
                        <?php if (!empty($card['descricao'])): ?>
                            <p class="card-descricao"><?= htmlspecialchars($card['descricao']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($card['link'])): ?>
                            <a href="<?= htmlspecialchars($card['link']) ?>" class="card-link">
                                Saiba mais →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.cards-section {
    padding: 80px 20px;
    background: #f8f9fa;
}

.cards-container {
    max-width: 1200px;
    margin: 0 auto;
}

.cards-titulo {
    font-size: 2.5rem;
    color: #00071c;
    text-align: center;
    margin-bottom: 50px;
    font-weight: 700;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

.card-item {
    background: white;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    text-align: center;
}

.card-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.card-icone {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #00071c;
}

.card-titulo {
    font-size: 1.5rem;
    color: #00071c;
    margin-bottom: 15px;
    font-weight: 600;
}

.card-descricao {
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
}

.card-link {
    display: inline-block;
    color: #00071c;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.card-link:hover {
    color: #0066cc;
    transform: translateX(5px);
}

@media (max-width: 768px) {
    .cards-section {
        padding: 50px 20px;
    }
    
    .cards-titulo {
        font-size: 2rem;
    }
    
    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>
