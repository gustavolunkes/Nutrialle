<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$db = getDB();

// Pega o ID do conteúdo
$conteudo_id = $_GET['id'] ?? 0;

// Busca o conteúdo
$stmt = $db->prepare("
    SELECT c.*, l.nome as layout_nome, l.campos_json, p.titulo as pagina_titulo
    FROM conteudos c
    INNER JOIN layouts l ON c.layout_id = l.id
    INNER JOIN paginas p ON c.pagina_id = p.id
    WHERE c.id = ?
");
$stmt->execute([$conteudo_id]);
$conteudo = $stmt->fetch();

if (!$conteudo) {
    $_SESSION['error'] = 'Conteúdo não encontrado';
    header('Location: ' . BASE_URL . '/admin/paginas/index.php');
    exit;
}

// Decodifica os dados JSON
$dados_salvos = json_decode($conteudo['dados_json'], true);
$campos = json_decode($conteudo['campos_json'], true);

// Configurações da página
$page_title = 'Editar Conteúdo';
$current_module = 'paginas';
$current_page = 'conteudos-editar';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Monta o array de dados
    $dados = [];
    foreach ($campos as $campo => $tipo) {
        $dados[$campo] = $_POST[$campo] ?? '';
    }
    
    try {
        // Atualiza o conteúdo
        $stmt = $db->prepare("
            UPDATE conteudos 
            SET dados_json = ?, ativo = ?
            WHERE id = ?
        ");
        $stmt->execute([
            json_encode($dados, JSON_UNESCAPED_UNICODE),
            $ativo,
            $conteudo_id
        ]);
        
        $success = 'Conteúdo atualizado com sucesso!';
        
        // Atualiza os dados salvos para exibição
        $dados_salvos = $dados;
        $conteudo['ativo'] = $ativo;
    } catch (Exception $e) {
        $error = 'Erro ao atualizar conteúdo: ' . $e->getMessage();
    }
}

// Inclui o header e sidebar
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<style>
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 900px;
    }
    
    .info-box {
        background: #e3f2fd;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #2196f3;
    }
    
    .info-box strong {
        color: #1976d2;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        font-family: inherit;
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #00071c;
        box-shadow: 0 0 0 3px rgba(0, 7, 28, 0.1);
    }
    
    .form-group-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .form-group-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
    
    .btn-primary {
        padding: 12px 24px;
        background: #00071c;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 7, 28, 0.3);
    }
    
    .btn-secondary {
        padding: 12px 24px;
        background: #95a5a6;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background: #7f8c8d;
    }
    
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .alert-error {
        background: #fee;
        color: #c33;
        border-left: 4px solid #c33;
    }
</style>

<!-- CONTENT -->
<main class="content">
    <div class="form-container">
        <h3 style="margin-bottom: 20px; color: #00071c;">✏️ Editar Conteúdo</h3>
        
        <div class="info-box">
            <strong>📄 Página:</strong> <?= htmlspecialchars($conteudo['pagina_titulo']) ?><br>
            <strong>🎨 Layout:</strong> <?= htmlspecialchars($conteudo['layout_nome']) ?><br>
            <strong>📅 Criado em:</strong> <?= date('d/m/Y H:i', strtotime($conteudo['created_at'])) ?><br>
            <strong>🔄 Última atualização:</strong> <?= date('d/m/Y H:i', strtotime($conteudo['updated_at'])) ?>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <?php foreach ($campos as $campo => $tipo): ?>
                <div class="form-group">
                    <label for="<?= $campo ?>">
                        <?= ucfirst(str_replace('_', ' ', $campo)) ?>
                    </label>
                    
                    <?php if ($tipo === 'textarea'): ?>
                        <textarea id="<?= $campo ?>" name="<?= $campo ?>"><?= htmlspecialchars($dados_salvos[$campo] ?? '') ?></textarea>
                    <?php else: ?>
                        <input type="text" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= htmlspecialchars($dados_salvos[$campo] ?? '') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <div class="form-group-checkbox">
                <input type="checkbox" id="ativo" name="ativo" <?= $conteudo['ativo'] ? 'checked' : '' ?>>
                <label for="ativo">Bloco ativo</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">💾 Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/admin/conteudos/index.php?pagina_id=<?= $conteudo['pagina_id'] ?>" class="btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
