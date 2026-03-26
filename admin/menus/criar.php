<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Configurações da página
$page_title = 'Novo Menu';
$current_module = 'menus';
$current_page = 'menus-criar';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $target = $_POST['target'] ?? '_self';
    $order_position = intval($_POST['order_position'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validações
    if (empty($name)) {
        $error = 'O nome do menu é obrigatório';
    } elseif (empty($slug)) {
        $error = 'O slug é obrigatório';
    } else {
        try {
            $db = getDB();
            
            // Verifica se o slug já existe
            $stmt = $db->prepare("SELECT id FROM menus WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $error = 'Este slug já está em uso';
            } else {
                // Cria o menu
                $stmt = $db->prepare("INSERT INTO menus (name, slug, url, target, order_position, active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $url, $target, $order_position, $active]);
                
                $_SESSION['success'] = 'Menu criado com sucesso!';
                header('Location: ' . BASE_URL . '/admin/menus/index.php');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Erro ao criar menu: ' . $e->getMessage();
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
        max-width: 700px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
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
    
    .form-group label .required {
        color: #e74c3c;
    }
    
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-group input:focus,
    .form-group select:focus {
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
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .form-hint {
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 5px;
    }
</style>

<!-- CONTENT -->
<main class="content">
    <div class="form-container">
        <h3 style="margin-bottom: 20px; color: #00071c;">📋 Adicionar Novo Menu</h3>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nome do Menu <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required placeholder="Ex: Home" value="<?= htmlspecialchars($name ?? '') ?>">
                    <div class="form-hint">Nome que aparecerá no menu</div>
                </div>
                
                <div class="form-group">
                    <label for="slug">Slug <span class="required">*</span></label>
                    <input type="text" id="slug" name="slug" required placeholder="Ex: home" value="<?= htmlspecialchars($slug ?? '') ?>">
                    <div class="form-hint">URL amigável (apenas letras, números e hífen)</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="url">URL Customizada (link para outro site)</label>
                <input type="text" id="url" name="url" placeholder="Ex: https://exemplo.com" value="<?= htmlspecialchars($url ?? '') ?>">
                <div class="form-hint">Deixe vazio para vincular uma página ao menu</div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="target">Abrir Link</label>
                    <select id="target" name="target">
                        <option value="_self" <?= (isset($target) && $target == '_self') ? 'selected' : '' ?>>Na mesma aba</option>
                        <option value="_blank" <?= (isset($target) && $target == '_blank') ? 'selected' : '' ?>>Em nova aba</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="order_position">Ordem de Exibição</label>
                    <input type="number" id="order_position" name="order_position" min="0" value="<?= htmlspecialchars($order_position ?? 0) ?>">
                    <div class="form-hint">Ordem de exibição no menu (0 = primeiro)</div>
                </div>
            </div>
            
            <div class="form-group-checkbox">
                <input type="checkbox" id="active" name="active" <?= !isset($active) || $active ? 'checked' : '' ?>>
                <label for="active">Menu ativo</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">✅ Criar Menu</button>
                <a href="<?= BASE_URL ?>/admin/menus/index.php" class="btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</main>

<script>
// Auto-gera o slug baseado no nome
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Remove acentos
        .replace(/[^a-z0-9]+/g, '-') // Substitui caracteres especiais por hífen
        .replace(/^-+|-+$/g, ''); // Remove hífens do início e fim
    
    document.getElementById('slug').value = slug;
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>