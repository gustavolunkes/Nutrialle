<?php
/**
 * Layout: Formulário de Contato
 * Formulário com campos de nome, email, telefone e mensagem
 */

// Decodifica os dados JSON
$dados = is_string($conteudo['dados_json']) ? json_decode($conteudo['dados_json'], true) : $conteudo['dados_json'];

// Valores padrão
$titulo = $dados['titulo'] ?? 'Entre em Contato';
$email_destino = $dados['email_destino'] ?? '';
$mensagem_sucesso = $dados['mensagem_sucesso'] ?? 'Mensagem enviada com sucesso!';

// Processamento do formulário
$form_enviado = false;
$form_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_contato'])) {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    
    if (empty($nome) || empty($email) || empty($mensagem)) {
        $form_erro = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_erro = 'Por favor, insira um e-mail válido.';
    } else {
        // Aqui você pode implementar o envio de e-mail
        // Por enquanto, apenas marca como enviado
        $form_enviado = true;
        
        // Exemplo de envio de e-mail (descomente para usar)
        /*
        $assunto = "Contato do site - $nome";
        $corpo = "Nome: $nome\nE-mail: $email\nTelefone: $telefone\n\nMensagem:\n$mensagem";
        $headers = "From: $email\r\nReply-To: $email";
        
        if (mail($email_destino, $assunto, $corpo, $headers)) {
            $form_enviado = true;
        } else {
            $form_erro = 'Erro ao enviar mensagem. Tente novamente.';
        }
        */
    }
}
?>

<section class="contato-section">
    <div class="contato-container">
        <?php if (!empty($titulo)): ?>
            <h2 class="contato-titulo"><?= htmlspecialchars($titulo) ?></h2>
        <?php endif; ?>
        
        <?php if ($form_enviado): ?>
            <div class="contato-sucesso">
                ✅ <?= htmlspecialchars($mensagem_sucesso) ?>
            </div>
        <?php else: ?>
            <?php if (!empty($form_erro)): ?>
                <div class="contato-erro">
                    ❌ <?= htmlspecialchars($form_erro) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="contato-form">
                <input type="hidden" name="form_contato" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome *</label>
                        <input type="text" id="nome" name="nome" required 
                               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" 
                           value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="mensagem">Mensagem *</label>
                    <textarea id="mensagem" name="mensagem" rows="6" required><?= htmlspecialchars($_POST['mensagem'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="contato-submit">Enviar Mensagem</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<style>
.contato-section {
    padding: 80px 20px;
    background: #fff;
}

.contato-container {
    max-width: 700px;
    margin: 0 auto;
}

.contato-titulo {
    font-size: 2.5rem;
    color: #00071c;
    text-align: center;
    margin-bottom: 40px;
    font-weight: 700;
}

.contato-sucesso {
    background: #d4edda;
    color: #155724;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    font-size: 1.1rem;
    border-left: 4px solid #28a745;
}

.contato-erro {
    background: #f8d7da;
    color: #721c24;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
    border-left: 4px solid #dc3545;
}

.contato-form {
    background: #f8f9fa;
    padding: 40px;
    border-radius: 12px;
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
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    font-family: inherit;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #00071c;
}

.contato-submit {
    width: 100%;
    padding: 15px;
    background: #00071c;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.contato-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 7, 28, 0.3);
}

@media (max-width: 768px) {
    .contato-section {
        padding: 50px 20px;
    }
    
    .contato-titulo {
        font-size: 2rem;
    }
    
    .contato-form {
        padding: 30px 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
