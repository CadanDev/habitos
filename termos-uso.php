<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso - Sistema de Hábitos</title>
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
                <h1 class="card-title">Termos de Uso</h1>
                <span style="color: var(--gray-500); font-size: 14px;">Última atualização: 15/01/2026</span>
            </div>
            <div class="col" style="gap: 20px; display: flex; flex-direction: column;">
                <p>Ao usar o Sistema de Hábitos você concorda com estes termos. Leia-os com atenção.</p>

                <div>
                    <h3 style="margin-bottom: 8px;">1. Conta e acesso</h3>
                    <ul style="margin-left: 18px; list-style: disc; color: var(--gray-700);">
                        <li>Você é responsável por manter suas credenciais seguras.</li>
                        <li>Use um email válido e mantenha-o atualizado.</li>
                        <li>Podemos encerrar ou suspender contas em caso de abuso ou violação destes termos.</li>
                    </ul>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">2. Uso aceitável</h3>
                    <ul style="margin-left: 18px; list-style: disc; color: var(--gray-700);">
                        <li>Não use o serviço para fins ilegais ou para armazenar conteúdo ilícito.</li>
                        <li>Não tente burlar autenticação ou explorar vulnerabilidades.</li>
                        <li>Respeite limites razoáveis de uso para manter a estabilidade do sistema.</li>
                    </ul>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">3. Conteúdo e dados</h3>
                    <p>Os dados que você registra permanecem de sua titularidade. Você nos autoriza a processá-los para prestar o serviço. Consulte a <a href="politica-privacidade.php">Política de Privacidade</a> para detalhes.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">4. Disponibilidade e alterações</h3>
                    <p>Buscamos manter o serviço disponível, mas interrupções podem ocorrer. Podemos atualizar funcionalidades e esta página periodicamente.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">5. Limitação de responsabilidade</h3>
                    <p>O serviço é fornecido "no estado em que se encontra". Na extensão permitida pela lei, não garantimos disponibilidade contínua nem nos responsabilizamos por perdas decorrentes de uso ou indisponibilidade.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">6. Suporte</h3>
                    <p>Canais de contato estão em <a href="contato.php">Contato</a>. Faremos o possível para responder em tempo hábil.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">7. Lei aplicável</h3>
                    <p>Estes termos são regidos pelas leis brasileiras. Em caso de conflito, o foro escolhido é o da comarca de São Paulo/SP, salvo disposição legal diversa.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
