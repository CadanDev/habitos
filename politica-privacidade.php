<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade - Sistema de Hábitos</title>
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
                <h1 class="card-title">Política de Privacidade</h1>
                <span style="color: var(--gray-500); font-size: 14px;">Última atualização: 15/01/2026</span>
            </div>
            <div class="col" style="gap: 20px; display: flex; flex-direction: column;">
                <p>Esta política explica como tratamos seus dados pessoais ao utilizar o Sistema de Hábitos.</p>

                <div>
                    <h3 style="margin-bottom: 8px;">Dados que coletamos</h3>
                    <ul style="margin-left: 18px; list-style: disc; color: var(--gray-700);">
                        <li>Dados de conta: nome, email e senha (armazenada com hash).</li>
                        <li>Dados de uso: hábitos, registros, veículos, abastecimentos e manutenções que você cria.</li>
                        <li>Dados técnicos: logs de erro e eventos para depuração.</li>
                        <li>Cookies de sessão: usados apenas para manter você autenticado.</li>
                    </ul>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">Como usamos os dados</h3>
                    <ul style="margin-left: 18px; list-style: disc; color: var(--gray-700);">
                        <li>Prestar o serviço principal (gestão de hábitos e veículos).</li>
                        <li>Autenticar e manter sua sessão com cookies HttpOnly.</li>
                        <li>Melhorar estabilidade, segurança e desempenho.</li>
                        <li>Enviar comunicações essenciais sobre a conta, quando necessário.</li>
                    </ul>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">Compartilhamento</h3>
                    <p>Não vendemos seus dados. Compartilhamos apenas quando exigido por lei ou para proteger direitos, segurança e integridade do serviço.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">Cookies e preferências</h3>
                    <p>Usamos um cookie de sessão com flag HttpOnly (e secure em HTTPS) para autenticação. Não utilizamos rastreamento de terceiros.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">Retenção e exclusão</h3>
                    <p>Mantemos seus dados enquanto a conta estiver ativa. Você pode solicitar a exclusão; removeremos ou anonimizaremos dados, salvo obrigações legais.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">Segurança</h3>
                    <p>Empregamos controles de acesso, hashing de senhas e registro de auditoria. Nenhuma transmissão é 100% segura, mas buscamos boas práticas de proteção.</p>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">Seus direitos</h3>
                    <ul style="margin-left: 18px; list-style: disc; color: var(--gray-700);">
                        <li>Acessar, corrigir ou atualizar seus dados.</li>
                        <li>Solicitar exclusão da conta e dos dados associados.</li>
                        <li>Revogar consentimentos não essenciais (quando aplicável).</li>
                    </ul>
                </div>

                <div>
                    <h3 style="margin-bottom: 8px;">Contato</h3>
                    <p>Dúvidas ou solicitações: veja os canais em <a href="contato.php">Contato</a>.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
