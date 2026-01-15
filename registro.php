<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Hábitos</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='90'>✓</text></svg>">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">🎯 Criar Conta</h1>
            
            <form id="registroForm">
                <div class="form-group">
                    <label class="form-label" for="nome">Nome</label>
                    <input 
                        type="text" 
                        id="nome" 
                        class="form-input" 
                        required 
                        placeholder="Seu nome completo"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        class="form-input" 
                        required 
                        placeholder="seu@email.com"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="senha">Senha</label>
                    <input 
                        type="password" 
                        id="senha" 
                        class="form-input" 
                        required 
                        placeholder="Mínimo 6 caracteres"
                        minlength="6"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirmarSenha">Confirmar Senha</label>
                    <input 
                        type="password" 
                        id="confirmarSenha" 
                        class="form-input" 
                        required 
                        placeholder="Digite a senha novamente"
                        minlength="6"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Criar Conta
                </button>
            </form>
            
            <div class="auth-link">
                Já tem uma conta? <a href="login.php">Fazer login</a>
            </div>

            <div class="auth-link" style="font-size: 13px; color: var(--gray-500);">
                <a href="politica-privacidade.php">Política de Privacidade</a> ·
                <a href="termos-uso.php">Termos de Uso</a> ·
                <a href="contato.php">Contato</a>
            </div>
        </div>
    </div>
    
    <script>
        // Configuração da API baseada no ambiente
        const API_BASE_URL = '<?php echo env('BASE_URL', 'http://localhost'); ?>/api';
    </script>
    <script src="assets/js/app.js"></script>
    <script>
        document.getElementById('registroForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmarSenha').value;
            
            if (senha !== confirmarSenha) {
                utils.showAlert('As senhas não coincidem', 'error');
                return;
            }
            
            await auth.register(nome, email, senha);
        });
    </script>
</body>
</html>
