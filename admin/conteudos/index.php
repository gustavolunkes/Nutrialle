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

// Pega o ID da página
$pagina_id = $_GET['pagina_id'] ?? 0;

// Busca a página
$stmt = $db->prepare("SELECT * FROM paginas WHERE id = ?");
$stmt->execute([$pagina_id]);
$pagina = $stmt->fetch();

if (!$pagina) {
    $_SESSION['error'] = 'Página não encontrada';
    header('Location: ' . BASE_URL . '/admin/paginas/index.php');
    exit;
}

// Busca os conteúdos da página
$stmt = $db->prepare("
    SELECT c.*, l.nome as layout_nome, l.descricao as layout_descricao
    FROM conteudos c
    INNER JOIN layouts l ON c.layout_id = l.id
    WHERE c.pagina_id = ?
    ORDER BY c.ordem ASC
");
$stmt->execute([$pagina_id]);
$conteudos = $stmt->fetchAll();

// Configurações da página
$page_title = 'Gerenciar Conteúdos';
$current_module = 'paginas';
$current_page = 'conteudos';

// Mensagens
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Inclui o header e sidebar
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<style>
    .page-header {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .page-header h3 {
        margin: 0 0 10px 0;
        color: #00071c;
    }
    
    .page-info {
        color: #666;
        font-size: 14px;
    }
    
    .content-list {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .content-item {
        padding: 20px 30px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: background 0.3s;
    }
    
    .content-item:hover {
        background: #f8f9fa;
    }
    
    .content-item:last-child {
        border-bottom: none;
    }
    
    .drag-handle {
        cursor: move;
        color: #999;
        font-size: 20px;
    }
    
    .content-info {
        flex: 1;
    }
    
    .content-title {
        font-weight: 600;
        color: #00071c;
        margin-bottom: 5px;
    }
    
    .content-meta {
        font-size: 13px;
        color: #666;
    }
    
    .content-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: #00071c;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 7, 28, 0.3);
    }
    
    .btn-edit {
        background: #3498db;
        color: white;
    }
    
    .btn-edit:hover {
        background: #2980b9;
    }
    
    .btn-delete {
        background: #e74c3c;
        color: white;
    }
    
    .btn-delete:hover {
        background: #c0392b;
    }
    
    .btn-secondary {
        background: #95a5a6;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #7f8c8d;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    
    .empty-state p {
        font-size: 16px;
        margin-bottom: 20px;
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
    
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .badge-active {
        background: #d4edda;
        color: #155724;
    }
    
    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<!-- CONTENT -->
<main class="content">
    <div class="page-header">
        <h3>📄 Gerenciar Conteúdos - <?= htmlspecialchars($pagina['titulo']) ?></h3>
        <div class="page-info">
            <strong>Slug:</strong> <?= htmlspecialchars($pagina['slug']) ?> | 
            <strong>Status:</strong> <?= $pagina['ativo'] ? '✅ Ativa' : '❌ Inativa' ?>
        </div>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div style="margin-bottom: 20px; display: flex; gap: 10px;">
        <a href="<?= BASE_URL ?>/admin/conteudos/criar.php?pagina_id=<?= $pagina_id ?>" class="btn btn-primary">
            ➕ Adicionar Bloco de Conteúdo
        </a>
        <a href="<?= BASE_URL ?>/admin/paginas/index.php" class="btn btn-secondary">
            ← Voltar para Páginas
        </a>
        <a href="<?= BASE_URL ?>/pagina.php?slug=<?= htmlspecialchars($pagina['slug']) ?>" class="btn btn-secondary" target="_blank">
            👁️ Visualizar Página
        </a>
    </div>
    
    <div class="content-list">
        <?php if (empty($conteudos)): ?>
            <div class="empty-state">
                <p>📭 Nenhum conteúdo adicionado ainda</p>
                <a href="<?= BASE_URL ?>/admin/conteudos/criar.php?pagina_id=<?= $pagina_id ?>" class="btn btn-primary">
                    Adicionar Primeiro Bloco
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($conteudos as $conteudo): ?>
                <?php
                $dados = json_decode($conteudo['dados_json'], true);
                $preview_titulo = '';
                
                // Tenta extrair um título dos dados JSON
                if (isset($dados['titulo'])) {
                    $preview_titulo = $dados['titulo'];
                } elseif (isset($dados['texto'])) {
                    $preview_titulo = mb_substr($dados['texto'], 0, 50) . '...';
                }
                ?>
                <div class="content-item">
                    <div class="drag-handle">⋮⋮</div>
                    
                    <div class="content-info">
                        <div class="content-title">
                            <?= htmlspecialchars($conteudo['layout_nome']) ?>
                            <?php if (!empty($preview_titulo)): ?>
                                - <?= htmlspecialchars($preview_titulo) ?>
                            <?php endif; ?>
                        </div>
                        <div class="content-meta">
                            Ordem: <?= $conteudo['ordem'] ?> | 
                            <?= $conteudo['ativo'] ? '<span class="badge badge-active">Ativo</span>' : '<span class="badge badge-inactive">Inativo</span>' ?>
                        </div>
                    </div>
                    
                    <div class="content-actions">
                        <a href="<?= BASE_URL ?>/admin/conteudos/editar.php?id=<?= $conteudo['id'] ?>" class="btn btn-edit">
                            ✏️ Editar
                        </a>
                        <a href="<?= BASE_URL ?>/admin/conteudos/deletar.php?id=<?= $conteudo['id'] ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Tem certeza que deseja deletar este conteúdo?')">
                            🗑️ Deletar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
