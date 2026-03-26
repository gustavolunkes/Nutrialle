<?php
/**
 * Helper para renderizar layouts de conteúdo
 */

/**
 * Renderiza um bloco de conteúdo baseado no layout
 * 
 * @param array $conteudo Dados do conteúdo (incluindo layout_id e dados_json)
 * @param PDO $db Conexão com o banco de dados
 * @return void
 */
function render_layout($conteudo, $db) {
    // Busca informações do layout
    $stmt = $db->prepare("SELECT * FROM layouts WHERE id = ? AND ativo = 1");
    $stmt->execute([$conteudo['layout_id']]);
    $layout = $stmt->fetch();
    
    if (!$layout) {
        echo "<!-- Layout não encontrado ou inativo -->";
        return;
    }
    
    // Caminho do arquivo do layout
    $layout_file = __DIR__ . '/../layouts/' . $layout['arquivo'];
    
    if (!file_exists($layout_file)) {
        echo "<!-- Arquivo de layout não encontrado: {$layout['arquivo']} -->";
        return;
    }
    
    // Inclui o arquivo do layout
    // A variável $conteudo estará disponível dentro do layout
    include $layout_file;
}

/**
 * Busca e renderiza todos os conteúdos de uma página
 * 
 * @param int $pagina_id ID da página
 * @param PDO $db Conexão com o banco de dados
 * @return void
 */
function render_page_contents($pagina_id, $db) {
    // Busca todos os conteúdos ativos da página, ordenados
    $stmt = $db->prepare("
        SELECT * FROM conteudos 
        WHERE pagina_id = ? AND ativo = 1 
        ORDER BY ordem ASC
    ");
    $stmt->execute([$pagina_id]);
    $conteudos = $stmt->fetchAll();
    
    if (empty($conteudos)) {
        echo '<div style="padding: 100px 20px; text-align: center; color: #999;">';
        echo '<p>Esta página ainda não possui conteúdo.</p>';
        echo '</div>';
        return;
    }
    
    // Renderiza cada conteúdo
    foreach ($conteudos as $conteudo) {
        render_layout($conteudo, $db);
    }
}


/**
 * Busca e renderiza todos os blocos da página inicial (home_conteudos)
 *
 * @param PDO $db Conexão com o banco de dados
 * @return void
 */
function render_home_contents($db) {
    $stmt = $db->query("
        SELECT * FROM home_conteudos
        WHERE ativo = 1
        ORDER BY ordem ASC
    ");
    $conteudos = $stmt->fetchAll();

    if (empty($conteudos)) {
        echo '<div style="padding: 100px 20px; text-align: center; color: #999;">';
        echo '<p>A página inicial ainda não possui conteúdo.</p>';
        echo '</div>';
        return;
    }

    foreach ($conteudos as $conteudo) {
        echo '<div id="' . (int)$conteudo['ordem'] . '">';
        render_layout($conteudo, $db);
        echo '</div>';
    }
}