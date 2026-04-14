<?php
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Security.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $ip       = $_SERVER['REMOTE_ADDR'];

    if (empty($email) || empty($password)) {
        $error = 'Preencha todos os campos';
    } else {

        // Checa rate limit ANTES de qualquer consulta ao banco
        if (!Security::checkLoginAttempts($email, $ip)) {
            $error = 'Muitas tentativas de login. Tente novamente em 10 minutos.';
        } else {

            try {
                $db = getDB();
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND active = 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Login bem-sucedido
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['user_name']  = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role']  = $user['role'];

                    // Regenera ID da sessão por segurança
                    session_regenerate_id(true);

                    // Registra a sessão no banco
                    $session_id = session_id();
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];

                    $stmt = $db->prepare("INSERT INTO sessions (id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$session_id, $user['id'], $ip, $user_agent]);

                    header('Location: dashboard.php');
                    exit;

                } else {
                    $error = 'Email ou senha incorretos';

                    // Registra tentativa de login falha via Security
                    Security::logLoginAttempt($email, $ip);
                }

            } catch (Exception $e) {
                $error = 'Erro ao fazer login. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wire Stack</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Wire Stack</h1>
            <p>Painel Administrativo</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       placeholder="seu@email.com"
                       value="<?= htmlspecialchars($email ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div class="footer">
            <p>&copy; <?= date('Y') ?> - Wire Stack</p>
        </div>
    </div>
</body>
</html>