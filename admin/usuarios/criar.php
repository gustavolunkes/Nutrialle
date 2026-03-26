<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Configurações da página
$page_title = 'Novo Usuário';
$current_module = 'usuarios';
$current_page = 'usuarios-criar';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'viewer';
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validações
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Preencha todos os campos obrigatórios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido';
    } elseif (strlen($password) < 6) {
        $error = 'A senha deve ter no mínimo 6 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não coincidem';
    } else {
        try {
            $db = getDB();
            
            // Verifica se email já existe
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Este email já está cadastrado';
            } else {
                // Cria o usuário
                $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                
                $stmt = $db->prepare("INSERT INTO users (name, email, password, role, active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $password_hash, $role, $active]);
                
                $success = 'Usuário criado com sucesso!';
                
                // Limpa os campos
                $name = $email = $password = $confirm_password = '';
            }
        } catch (Exception $e) {
            $error = 'Erro ao criar usuário: ' . $e->getMessage();
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
        max-width: 600px;
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
        <h3 style="margin-bottom: 20px; color: #00071c;">Adicionar Novo Usuário</h3>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Nome Completo <span class="required">*</span></label>
                <input type="text" id="name" name="name" required placeholder="João Silva" value="<?= htmlspecialchars($name ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" required placeholder="joao@exemplo.com" value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Senha <span class="required">*</span></label>
                <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                <div class="form-hint">A senha deve ter no mínimo 6 caracteres</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Senha <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Digite a senha novamente">
            </div>
            
            <div class="form-group">
                <label for="role">Tipo de Usuário</label>
                <select id="role" name="role">
                    <option value="viewer" <?= (isset($role) && $role == 'viewer') ? 'selected' : '' ?>>Visualizador</option>
                    <option value="editor" <?= (isset($role) && $role == 'editor') ? 'selected' : '' ?>>Editor</option>
                    <option value="admin" <?= (isset($role) && $role == 'admin') ? 'selected' : '' ?>>Administrador</option>
                </select>
                <div class="form-hint">
                    <strong>Visualizador:</strong> Apenas visualiza<br>
                    <strong>Editor:</strong> Pode editar conteúdo<br>
                    <strong>Administrador:</strong> Acesso total
                </div>
            </div>
            
            <div class="form-group-checkbox">
                <input type="checkbox" id="active" name="active" <?= !isset($active) || $active ? 'checked' : '' ?>>
                <label for="active">Usuário ativo</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">✅ Criar Usuário</button>
                <a href="index.php" class="btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>