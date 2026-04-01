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

// Busca todos os layouts ativos
$stmt = $db->query("SELECT * FROM layouts WHERE ativo = 1 ORDER BY nome ASC");
$layouts = $stmt->fetchAll();

// Configurações da página
$page_title = 'Adicionar Conteúdo';
$current_module = 'paginas';
$current_page = 'conteudos-criar';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $layout_id = $_POST['layout_id'] ?? 0;
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Busca o layout selecionado
    $stmt = $db->prepare("SELECT * FROM layouts WHERE id = ?");
    $stmt->execute([$layout_id]);
    $layout = $stmt->fetch();
    
    if (!$layout) {
        $error = 'Layout não encontrado';
    } else {
        // Decodifica os campos do layout
        $campos = json_decode($layout['campos_json'], true);
        
        // Monta o array de dados
        $dados = [];
        foreach ($campos as $campo => $tipo) {
            $dados[$campo] = $_POST[$campo] ?? '';
        }
        
        // Busca a próxima ordem
        $stmt = $db->prepare("SELECT MAX(ordem) as max_ordem FROM conteudos WHERE pagina_id = ?");
        $stmt->execute([$pagina_id]);
        $result = $stmt->fetch();
        $ordem = ($result['max_ordem'] ?? 0) + 1;
        
        try {
            // Insere o conteúdo
            $stmt = $db->prepare("
                INSERT INTO conteudos (pagina_id, layout_id, dados_json, ordem, ativo) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $pagina_id,
                $layout_id,
                json_encode($dados, JSON_UNESCAPED_UNICODE),
                $ordem,
                $ativo
            ]);
            
            $_SESSION['success'] = 'Conteúdo adicionado com sucesso!';
            header('Location: ' . BASE_URL . '/admin/conteudos/index.php?pagina_id=' . $pagina_id);
            exit;
        } catch (Exception $e) {
            $error = 'Erro ao adicionar conteúdo: ' . $e->getMessage();
        }
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
    
    .layout-selector {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .layout-option {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .layout-option:hover {
        border-color: #00071c;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .layout-option.selected {
        border-color: #00071c;
        background: #f0f0f5;
    }
    
    .layout-option input[type="radio"] {
        display: none;
    }
    
    .layout-name {
        font-weight: 600;
        color: #00071c;
        margin-bottom: 5px;
    }
    
    .layout-desc {
        font-size: 12px;
        color: #666;
    }
    
    .form-fields {
        display: none;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid #e0e0e0;
    }
    
    .form-fields.active {
        display: block;
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
    
    .alert-error {
        background: #fee;
        color: #c33;
        border-left: 4px solid #c33;
    }
</style>

<!-- CONTENT -->
<main class="content">
    <div class="form-container">
        <h3 style="margin-bottom: 10px; color: #00071c;">➕ Adicionar Bloco de Conteúdo</h3>
        <p style="color: #666; margin-bottom: 30px;">Página: <strong><?= htmlspecialchars($pagina['titulo']) ?></strong></p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" id="contentForm">
            <h4 style="margin-bottom: 15px;">1. Escolha o tipo de bloco:</h4>
            
            <div class="layout-selector">
                <?php foreach ($layouts as $layout): ?>
                    <label class="layout-option" data-layout-id="<?= $layout['id'] ?>">
                        <input type="radio" name="layout_id" value="<?= $layout['id'] ?>" required>
                        <div class="layout-name"><?= htmlspecialchars($layout['nome']) ?></div>
                        <div class="layout-desc"><?= htmlspecialchars($layout['descricao']) ?></div>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <?php foreach ($layouts as $layout): ?>
                <?php
                $campos = json_decode($layout['campos_json'], true);
                ?>
                <div class="form-fields" id="fields-<?= $layout['id'] ?>">
                    <h4 style="margin-bottom: 20px;">2. Preencha os dados do bloco:</h4>
                    
                    <?php foreach ($campos as $campo => $tipo): ?>
                        <?php if ($campo === 'imagem_fundo') continue; // Pula imagem_fundo ?>
                        <div class="form-group">
                            <label for="<?= $campo ?>">
                                <?= ucfirst(str_replace('_', ' ', $campo)) ?>
                            </label>
                            
                            <?php if ($tipo === 'textarea'): ?>
                                <textarea id="<?= $campo ?>" name="<?= $campo ?>"></textarea>
                                
                                <?php if ($campo === 'imagens'): ?>
                                    <div style="margin-top: 10px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                        <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                                            💡 Dica: Separe URLs por vírgula ou quebra de linha. Use o upload abaixo para adicionar novas imagens.
                                        </p>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <input type="file" id="image_upload_<?= $campo ?>" accept="image/*" style="flex: 1; cursor: pointer;">
                                            <button type="button" onclick="uploadImage('image_upload_<?= $campo ?>', '<?= $campo ?>')" class="btn-primary" style="padding: 8px 16px; white-space: nowrap;">
                                                📤 Enviar Imagem
                                            </button>
                                        </div>
                                        <div id="upload_status_<?= $campo ?>" style="margin-top: 8px; font-size: 12px; display: none;"></div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <input type="text" id="<?= $campo ?>" name="<?= $campo ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="form-group-checkbox">
                        <input type="checkbox" id="ativo" name="ativo" checked>
                        <label for="ativo">Bloco ativo</label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">💾 Salvar Conteúdo</button>
                        <a href="<?= BASE_URL ?>/admin/conteudos/index.php?pagina_id=<?= $pagina_id ?>" class="btn-secondary">❌ Cancelar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const layoutOptions = document.querySelectorAll('.layout-option');
    const formFields = document.querySelectorAll('.form-fields');
    
    layoutOptions.forEach(option => {
        option.addEventListener('click', function() {
            const layoutId = this.dataset.layoutId;
            const radio = this.querySelector('input[type="radio"]');
            
            // Remove seleção de todos
            layoutOptions.forEach(opt => opt.classList.remove('selected'));
            formFields.forEach(field => field.classList.remove('active'));
            
            // Adiciona seleção ao clicado
            this.classList.add('selected');
            radio.checked = true;
            
            // Mostra os campos correspondentes
            document.getElementById('fields-' + layoutId).classList.add('active');
        });
    });
});

// ─ UPLOAD IMAGENS ─
function uploadImage(inputId, fieldId) {
    const input = document.getElementById(inputId);
    const file = input.files[0];
    
    if (!file) {
        alert('Selecione uma imagem');
        return;
    }
    
    const formData = new FormData();
    formData.append('imagem', file);
    
    const statusDiv = document.getElementById('upload_status_' + fieldId);
    statusDiv.style.display = 'block';
    statusDiv.innerHTML = '⏳ Enviando...';
    statusDiv.style.color = '#666';
    
    fetch('<?= BASE_URL ?>/admin/upload_imagem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const textarea = document.getElementById(fieldId);
            const currentValue = textarea.value.trim();
            
            // Adiciona a URL com vírgula separando
            if (currentValue) {
                textarea.value = currentValue + ', ' + data.path;
            } else {
                textarea.value = data.path;
            }
            
            input.value = '';
            statusDiv.innerHTML = '✅ Imagem adicionada com sucesso!';
            statusDiv.style.color = '#28a745';
            
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 3000);
        } else {
            statusDiv.innerHTML = '❌ Erro: ' + (data.error || 'Desconhecido');
            statusDiv.style.color = '#dc3545';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        statusDiv.innerHTML = '❌ Erro ao enviar: ' + error.message;
        statusDiv.style.color = '#dc3545';
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
