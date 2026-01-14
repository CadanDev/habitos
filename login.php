<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Hábitos</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">🎯 Sistema de Hábitos</h1>
            
            <form id="loginForm">
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
                        placeholder="••••••••"
                        minlength="6"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Entrar
                </button>
            </form>
            
            <div class="auth-link">
                Não tem uma conta? <a href="registro.php">Criar conta</a>
            </div>
        </div>
    </div>
    
    <script>
        // Configuração da API baseada no ambiente
        const API_BASE_URL = window.location.origin + '/api';
    </script>
    <script src="assets/js/app.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            
            await auth.login(email, senha);
        });
    </script>
</body>
</html>
