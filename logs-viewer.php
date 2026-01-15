<?php
/**
 * Visualizador de Logs
 * Página para debug - deve ser removida em produção
 */

require_once 'config/config.php';

// Função para verificar chave de admin
function verifyAdminKey($providedKey) {
    $adminKey = env('ADMIN_KEY', '');
    
    if (empty($adminKey)) {
        return false;
    }
    
    // Comparação usando hash timing-safe
    return hash_equals(hash('sha256', $adminKey), hash('sha256', $providedKey));
}

// Segurança - requer chave de admin em produção
if (env('APP_ENV', 'production') !== 'development') {
    $providedKey = $_GET['key'] ?? $_POST['key'] ?? '';
    
    if (!verifyAdminKey($providedKey) && !isLoggedIn()) {
        http_response_code(403);
        die('Acesso negado. Forneça a chave de administração via parâmetro ?key=SUA_CHAVE');
    }
}

$logType = $_GET['type'] ?? 'error';
$logDir = __DIR__ . '/logs';

// Validar tipo de log
$validTypes = ['error', 'warning', 'info', 'debug', 'application'];
if (!in_array($logType, $validTypes)) {
    $logType = 'error';
}

// Obter lista de arquivos de log
$logFiles = [];
if (is_dir($logDir)) {
    $files = scandir($logDir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if (strpos($file, $logType) === 0 && strpos($file, '.log') !== false) {
            $logFiles[] = $file;
        }
    }
}

// Ler conteúdo do primeiro arquivo (mais recente)
$logContent = '';
if (!empty($logFiles)) {
    $currentFile = $logFiles[0];
    $filePath = $logDir . '/' . $currentFile;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        // Mostrar últimas linhas primeiro
        $lines = array_reverse(explode("\n", $content));
        $logContent = implode("\n", array_slice($lines, 0, 500));
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizador de Logs</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .logs-container {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Courier New', monospace;
            padding: 20px;
            border-radius: 8px;
            margin: 20px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .log-controls {
            margin: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .log-button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background: #0e639c;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .log-button:hover {
            background: #1177bb;
        }
        
        .log-button.active {
            background: #07925f;
        }
        
        .log-error {
            color: #f48771;
        }
        
        .log-warning {
            color: #dcdcaa;
        }
        
        .log-info {
            color: #4ec9b0;
        }
        
        .log-debug {
            color: #9cdcfe;
        }
        
        .log-header {
            margin: 20px;
        }
        
        .log-file-info {
            color: #858585;
            font-size: 12px;
            margin: 10px 20px;
        }
    </style>
</head>
<body>
    <div class="log-header">
        <h1>📋 Visualizador de Logs</h1>
        <p>Sistema de logs da aplicação - <strong>Apenas para desenvolvimento</strong></p>
    </div>
    
    <div class="log-controls">
        <?php 
        $keyParam = '';
        if (env('APP_ENV') !== 'development') {
            $currentKey = $_GET['key'] ?? $_POST['key'] ?? '';
            if (!empty($currentKey)) {
                $keyParam = '&key=' . urlencode($currentKey);
            }
        }
        ?>
        <a href="?type=error<?php echo $keyParam; ?>" class="log-button <?php echo $logType === 'error' ? 'active' : ''; ?>">🔴 Erros</a>
        <a href="?type=warning<?php echo $keyParam; ?>" class="log-button <?php echo $logType === 'warning' ? 'active' : ''; ?>">🟡 Avisos</a>
        <a href="?type=info<?php echo $keyParam; ?>" class="log-button <?php echo $logType === 'info' ? 'active' : ''; ?>">ℹ️ Informações</a>
        <a href="?type=debug<?php echo $keyParam; ?>" class="log-button <?php echo $logType === 'debug' ? 'active' : ''; ?>">🐛 Debug</a>
        <a href="?type=application<?php echo $keyParam; ?>" class="log-button <?php echo $logType === 'application' ? 'active' : ''; ?>">📄 Geral</a>
    </div>
    
    <?php if (!empty($logFiles)): ?>
        <div class="log-file-info">
            <strong>Arquivo atual:</strong> <?php echo htmlspecialchars($logFiles[0]); ?> 
            (<?php echo count($logFiles); ?> arquivo(s) disponível(eis))
        </div>
    <?php endif; ?>
    
    <div class="logs-container">
        <?php if ($logContent): ?>
            <pre><?php echo htmlspecialchars($logContent); ?></pre>
        <?php else: ?>
            <p>Nenhum log encontrado para o tipo selecionado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
