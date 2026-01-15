<?php
require_once 'config/config.php';

$contatoEmail = env('CONTACT_EMAIL', 'contato@exemplo.com');
$slaTexto = 'Respondemos em até 2 dias úteis.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Sistema de Hábitos</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='90'>✓</text></svg>">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="dashboard.php" class="logo">🎯 Meus Hábitos</a>
            <div class="user-info">
                <a class="btn btn-outline" href="login.php">Login</a>
                <a class="btn btn-primary" href="registro.php">Criar conta</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Contato</h1>
                <span style="color: var(--gray-500); font-size: 14px;">Estamos aqui para ajudar</span>
            </div>
            <div class="grid grid-3" style="margin-bottom: 20px;">
                <div class="card" style="margin-bottom: 0;">
                    <h3 class="card-title" style="font-size: 18px;">Suporte</h3>
                    <p style="color: var(--gray-700);">Dúvidas, bugs ou sugestões.</p>
                    <a class="btn btn-primary" href="mailto:<?php echo htmlspecialchars($contatoEmail); ?>?subject=Suporte%20-%20Sistema%20de%20Habitos">Enviar email</a>
                    <p style="margin-top: 10px; color: var(--gray-600); font-size: 14px;"><?php echo htmlspecialchars($slaTexto); ?></p>
                </div>
                <div class="card" style="margin-bottom: 0;">
                    <h3 class="card-title" style="font-size: 18px;">Privacidade</h3>
                    <p style="color: var(--gray-700);">Solicitar cópia ou exclusão de dados.</p>
                    <a class="btn btn-outline" href="politica-privacidade.php">Ver Política de Privacidade</a>
                </div>
                <div class="card" style="margin-bottom: 0;">
                    <h3 class="card-title" style="font-size: 18px;">Termos</h3>
                    <p style="color: var(--gray-700);">Entenda como o serviço funciona.</p>
                    <a class="btn btn-outline" href="termos-uso.php">Ver Termos de Uso</a>
                </div>
            </div>

            <div class="card" style="margin-bottom: 0;">
                <h3 class="card-title" style="font-size: 18px;">Checklist antes de enviar</h3>
                <ul style="margin-left: 18px; list-style: disc; color: var(--gray-700);">
                    <li>Informe seu email de cadastro.</li>
                    <li>Descreva o problema, passos para reproduzir e prints se possível.</li>
                    <li>Inclua o navegador/sistema e horário aproximado do erro.</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
