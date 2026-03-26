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
$page_title = 'Editar Menu';
$current_module = 'menus';
$current_page = 'menus-editar';

$error = '';
$success = '';
$db = getDB();

// Verifica se o ID foi passado
$menu_id = $_GET['id'] ?? 0;

// Busca o menu
$stmt = $db->prepare("SELECT * FROM menus WHERE id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch();

if (!$menu) {
    $_SESSION['error'] = 'Menu não encontrado';
    header('Location: ' . BASE_URL . '/admin/menus/index.php');
    exit;
}

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
            // Verifica se o slug já existe (exceto o próprio menu)
            $stmt = $db->prepare("SELECT id FROM menus WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $menu_id]);
            if ($stmt->fetch()) {
                $error = 'Este slug já está em uso';
            } else {
                // Atualiza o menu
                $stmt = $db->prepare("UPDATE menus SET name = ?, slug = ?, url = ?, target = ?, order_position = ?, active = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $url, $target, $order_position, $active, $menu_id]);
                
                $success = 'Menu atualizado com sucesso!';
                
                // Atualiza os dados do menu
                $menu['name'] = $name;
                $menu['slug'] = $slug;
                $menu['url'] = $url;
                $menu['target'] = $target;
                $menu['order_position'] = $order_position;
                $menu['active'] = $active;
            }
        } catch (Exception $e) {
            $error = 'Erro ao atualizar menu: ' . $e->getMessage();
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
</style>

<!-- CONTENT -->
<main class="content">
    <div class="form-container">
        <h3 style="margin-bottom: 20px; color: #00071c;">✏️ Editar Menu</h3>
        
        <div class="info-box">
            <strong>📋 ID do Menu:</strong> #<?= $menu['id'] ?><br>
            <strong>📅 Criado em:</strong> <?= date('d/m/Y H:i', strtotime($menu['created_at'])) ?>
        </div>
        
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
                    <input type="text" id="name" name="name" required placeholder="Ex: Home" value="<?= htmlspecialchars($menu['name']) ?>">
                    <div class="form-hint">Nome que aparecerá no menu</div>
                </div>
                
                <div class="form-group">
                    <label for="slug">Slug <span class="required">*</span></label>
                    <input type="text" id="slug" name="slug" required placeholder="Ex: home" value="<?= htmlspecialchars($menu['slug']) ?>">
                    <div class="form-hint">URL amigável (apenas letras, números e hífen)</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="url">URL Customizada (link para outro site)</label>
                <input type="text" id="url" name="url" placeholder="Ex: https://exemplo.com" value="<?= htmlspecialchars($menu['url']) ?>">
                <div class="form-hint">Deixe vazio para vincular uma página ao menu</div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="target">Abrir Link</label>
                    <select id="target" name="target">
                        <option value="_self" <?= $menu['target'] == '_self' ? 'selected' : '' ?>>Na mesma aba</option>
                        <option value="_blank" <?= $menu['target'] == '_blank' ? 'selected' : '' ?>>Em nova aba</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="order_position">Ordem de Exibição</label>
                    <input type="number" id="order_position" name="order_position" min="0" value="<?= htmlspecialchars($menu['order_position']) ?>">
                    <div class="form-hint">Ordem de exibição no menu (0 = primeiro)</div>
                </div>
            </div>
            
            <div class="form-group-checkbox">
                <input type="checkbox" id="active" name="active" <?= $menu['active'] ? 'checked' : '' ?>>
                <label for="active">Menu ativo</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">💾 Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/admin/menus/index.php" class="btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</main>

<script>
// Auto-gera o slug baseado no nome (apenas quando o campo slug estiver vazio)
document.getElementById('name').addEventListener('input', function() {
    const slugField = document.getElementById('slug');
    
    // Só atualiza o slug se ele estiver vazio
    if (slugField.value === '') {
        const name = this.value;
        const slug = name
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remove acentos
            .replace(/[^a-z0-9]+/g, '-') // Substitui caracteres especiais por hífen
            .replace(/^-+|-+$/g, ''); // Remove hífens do início e fim
        
        slugField.value = slug;
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>