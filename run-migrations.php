<?php
/**
 * Executor de Migrações via Web
 * Acesse via navegador para rodar as migrations
 * 
 * ⚠️ IMPORTANTE: Remova este arquivo após usar em produção!
 */

require_once __DIR__ . '/config/config.php';

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

class MigrationWebRunner {
    private $conn;
    private $migrationsPath;
    private $output = [];
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->migrationsPath = __DIR__ . '/database/migrations';
        $this->ensureMigrationsTable();
    }
    
    private function log($message, $type = 'info') {
        $this->output[] = ['message' => $message, 'type' => $type];
    }
    
    private function ensureMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $this->conn->exec($sql);
            $this->log('✓ Tabela de migrações verificada', 'success');
        } catch (PDOException $e) {
            $this->log('✗ Erro ao criar tabela de migrações: ' . $e->getMessage(), 'error');
        }
    }
    
    private function getExecutedMigrations() {
        try {
            $stmt = $this->conn->query("SELECT migration FROM migrations ORDER BY migration");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            $this->log('✗ Erro ao obter migrações executadas: ' . $e->getMessage(), 'error');
            return [];
        }
    }
    
    private function getMigrationFiles() {
        if (!is_dir($this->migrationsPath)) {
            $this->log('✗ Pasta de migrações não encontrada: ' . $this->migrationsPath, 'error');
            return [];
        }
        
        $files = glob($this->migrationsPath . '/*.sql');
        sort($files);
        
        return array_map(function($file) {
            return basename($file);
        }, $files);
    }
    
    private function executeMigration($filename) {
        $filepath = $this->migrationsPath . '/' . $filename;
        
        if (!file_exists($filepath)) {
            $this->log("✗ Arquivo não encontrado: $filename", 'error');
            return false;
        }
        
        $sql = file_get_contents($filepath);
        
        if (empty(trim($sql))) {
            $this->log("⚠ Arquivo vazio: $filename", 'warning');
            return false;
        }
        
        try {
            $this->conn->exec($sql);
            
            $stmt = $this->conn->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$filename]);
            
            $this->log("✓ Executado: $filename", 'success');
            logger()->info('Migration executada com sucesso', ['file' => $filename]);
            return true;
            
        } catch (PDOException $e) {
            $this->log("✗ Erro em $filename: " . $e->getMessage(), 'error');
            logger()->error('Erro ao executar migration', [
                'file' => $filename,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    public function run() {
        $this->log('=== Iniciando Migrações ===', 'info');
        
        $executed = $this->getExecutedMigrations();
        $available = $this->getMigrationFiles();
        
        $pending = array_diff($available, $executed);
        
        if (empty($pending)) {
            $this->log('✓ Nenhuma migração pendente', 'success');
            $this->log("Migrações executadas: " . count($executed), 'info');
            return $this->output;
        }
        
        $this->log("Migrações pendentes: " . count($pending), 'info');
        
        $success = 0;
        $failed = 0;
        
        foreach ($pending as $migration) {
            if ($this->executeMigration($migration)) {
                $success++;
            } else {
                $failed++;
                break;
            }
        }
        
        $this->log('=== Resultado ===', 'info');
        $this->log("✓ Sucesso: $success", 'success');
        if ($failed > 0) {
            $this->log("✗ Falhas: $failed", 'error');
        }
        $this->log("Total executadas: " . ($success + count($executed)), 'info');
        
        return $this->output;
    }
    
    public function status() {
        $this->log('=== Status das Migrações ===', 'info');
        
        $executed = $this->getExecutedMigrations();
        $available = $this->getMigrationFiles();
        
        if (empty($available)) {
            $this->log('Nenhuma migração encontrada.', 'warning');
            return $this->output;
        }
        
        foreach ($available as $migration) {
            $status = in_array($migration, $executed);
            $label = $status ? 'Executada' : 'Pendente';
            $type = $status ? 'success' : 'warning';
            $icon = $status ? '✓' : '○';
            
            $this->log("$icon $migration [$label]", $type);
        }
        
        $this->log("Total: " . count($available) . " | Executadas: " . count($executed) . " | Pendentes: " . (count($available) - count($executed)), 'info');
        
        return $this->output;
    }

    public function fresh() {
        $this->log('=== RESETAR BANCO DE DADOS (FRESH) ===', 'warning');
        $this->log('⚠️ Deletando todas as tabelas...', 'warning');
        
        try {
            // Desabilitar verificação de chaves estrangeiras
            $this->conn->exec("SET FOREIGN_KEY_CHECKS=0");
            
            // Obter todas as tabelas do banco atual
            $stmt = $this->conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Deletar todas as tabelas
            foreach ($tables as $table) {
                $this->conn->exec("DROP TABLE IF EXISTS `$table`");
                $this->log("✓ Deletada tabela: $table", 'warning');
            }
            
            // Reabilitar verificação de chaves estrangeiras
            $this->conn->exec("SET FOREIGN_KEY_CHECKS=1");
            
            $this->log('✓ Banco de dados limpo com sucesso', 'success');
            $this->log('', 'info');
            
            // Recriar tabela de migrações
            $this->log('Recriando tabela de migrações...', 'info');
            $this->ensureMigrationsTable();
            
            $this->log('', 'info');
            $this->log('Agora executando todas as migrações...', 'info');
            $this->log('', 'info');
            
            // Executar todas as migrações
            $available = $this->getMigrationFiles();
            $success = 0;
            $failed = 0;
            
            foreach ($available as $migration) {
                if ($this->executeMigration($migration)) {
                    $success++;
                } else {
                    $failed++;
                }
            }
            
            $this->log('', 'info');
            $this->log('=== RESULTADO FRESH ===', 'info');
            $this->log("✓ Sucesso: $success migrações executadas", 'success');
            if ($failed > 0) {
                $this->log("✗ Falhas: $failed", 'error');
            }
            $this->log('✓ Banco de dados resetado com sucesso do zero!', 'success');
            
            return $this->output;
        } catch (PDOException $e) {
            $this->log('✗ Erro ao resetar banco de dados: ' . $e->getMessage(), 'error');
            logger()->error('Erro ao fazer fresh do banco', ['error' => $e->getMessage()]);
            return $this->output;
        }
    }
}

// Processar requisição
$action = $_GET['action'] ?? 'status';
$output = [];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception('Erro ao conectar com o banco de dados');
    }
    
    $runner = new MigrationWebRunner($conn);
    
    if ($action === 'run') {
        $output = $runner->run();
    } elseif ($action === 'fresh') {
        $output = $runner->fresh();
    } else {
        $output = $runner->status();
    }
    
} catch (Exception $e) {
    $output[] = ['message' => '✗ Erro: ' . $e->getMessage(), 'type' => 'error'];
    logger()->exception($e);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executor de Migrações</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .migration-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .migration-output {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Courier New', monospace;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .log-line {
            padding: 5px 0;
            border-bottom: 1px solid #333;
        }
        
        .log-success {
            color: #4ec9b0;
        }
        
        .log-error {
            color: #f48771;
        }
        
        .log-warning {
            color: #dcdcaa;
        }
        
        .log-info {
            color: #9cdcfe;
        }
        
        .migration-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        
        .btn-migrate {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #0e639c;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1177bb;
        }
        
        .btn-secondary {
            background: #858585;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #6e6e6e;
        }
        
        .alert-warning {
            background: #5a5a00;
            color: #ffff99;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="migration-container">
        <h1>🔧 Executor de Migrações</h1>
        
        <div class="alert-warning">
            ⚠️ <strong>Importante:</strong> Este arquivo deve ser removido após uso em produção por questões de segurança!
        </div>
        
        <div class="migration-buttons">
            <?php 
            $keyParam = '';
            if (env('APP_ENV') !== 'development') {
                $currentKey = $_GET['key'] ?? $_POST['key'] ?? '';
                if (!empty($currentKey)) {
                    $keyParam = '&key=' . urlencode($currentKey);
                }
            }
            ?>
            <a href="?action=status<?php echo $keyParam; ?>" class="btn-migrate btn-secondary">
                📋 Ver Status
            </a>
            <a href="?action=run<?php echo $keyParam; ?>" class="btn-migrate btn-primary">
                ▶️ Executar Migrações
            </a>
            <button onclick="if(confirm('⚠️ ATENÇÃO CRÍTICA!\n\nIsto vai:\n• Deletar TODAS as tabelas\n• Limpar TODOS os dados\n• Executar as migrações do zero\n\nVocê tem certeza?')) { if(confirm('🔥 CONFIRMAÇÃO FINAL\n\nEsta ação é irreversível!\n\nTem certeza absoluta?')) { window.location.href='?action=fresh<?php echo $keyParam; ?>'; } }" class="btn-migrate btn-secondary" style="background: #cc3300; border: 1px solid #994400; color: white; font-weight: bold;">
                🔥 Fresh (Resetar Tudo)
            </button>
        </div>
        
        <div class="migration-output">
            <?php foreach ($output as $log): ?>
                <div class="log-line log-<?php echo htmlspecialchars($log['type']); ?>">
                    <?php echo htmlspecialchars($log['message']); ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($output)): ?>
                <div class="log-line log-info">
                    Clique em "Ver Status" ou "Executar Migrações" para começar.
                </div>
            <?php endif; ?>
        </div>
        
        <p style="margin-top: 20px; color: #858585; font-size: 12px;">
            💡 Dica: Use "Ver Status" para ver quais migrations estão pendentes antes de executar.
        </p>
    </div>
</body>
</html>
