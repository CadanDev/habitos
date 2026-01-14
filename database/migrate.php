<?php
/**
 * Sistema de Migrações de Banco de Dados
 * 
 * Executa migrações SQL pendentes
 * Uso: php database/migrate.php
 */

require_once __DIR__ . '/../config/database.php';

class MigrationRunner {
    private $conn;
    private $migrationsPath;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->migrationsPath = __DIR__ . '/migrations';
        $this->ensureMigrationsTable();
    }
    
    /**
     * Criar tabela de controle de migrações se não existir
     */
    private function ensureMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $this->conn->exec($sql);
        echo "✓ Tabela de migrações verificada\n";
    }
    
    /**
     * Obter migrações já executadas
     */
    private function getExecutedMigrations() {
        $stmt = $this->conn->query("SELECT migration FROM migrations ORDER BY migration");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Obter arquivos de migração disponíveis
     */
    private function getMigrationFiles() {
        if (!is_dir($this->migrationsPath)) {
            echo "✗ Pasta de migrações não encontrada!\n";
            return [];
        }
        
        $files = glob($this->migrationsPath . '/*.sql');
        sort($files);
        
        return array_map(function($file) {
            return basename($file);
        }, $files);
    }
    
    /**
     * Executar uma migração
     */
    private function executeMigration($filename) {
        $filepath = $this->migrationsPath . '/' . $filename;
        
        if (!file_exists($filepath)) {
            echo "✗ Arquivo não encontrado: $filename\n";
            return false;
        }
        
        $sql = file_get_contents($filepath);
        
        if (empty(trim($sql))) {
            echo "⚠ Arquivo vazio: $filename\n";
            return false;
        }
        
        try {
            // Executar SQL (sem transação pois pode conter múltiplos statements)
            $this->conn->exec($sql);
            
            // Registrar migração
            $stmt = $this->conn->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$filename]);
            
            echo "✓ Executado: $filename\n";
            return true;
            
        } catch (PDOException $e) {
            echo "✗ Erro em $filename: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Executar todas as migrações pendentes
     */
    public function run() {
        echo "\n=== Sistema de Migrações ===\n\n";
        
        $executed = $this->getExecutedMigrations();
        $available = $this->getMigrationFiles();
        
        $pending = array_diff($available, $executed);
        
        if (empty($pending)) {
            echo "✓ Nenhuma migração pendente\n";
            echo "\nMigrações executadas: " . count($executed) . "\n";
            return;
        }
        
        echo "Migrações pendentes: " . count($pending) . "\n\n";
        
        $success = 0;
        $failed = 0;
        
        foreach ($pending as $migration) {
            if ($this->executeMigration($migration)) {
                $success++;
            } else {
                $failed++;
                break; // Parar se uma migração falhar
            }
        }
        
        echo "\n=== Resultado ===\n";
        echo "✓ Sucesso: $success\n";
        if ($failed > 0) {
            echo "✗ Falhas: $failed\n";
        }
        echo "\nTotal executadas: " . ($success + count($executed)) . "\n";
    }
    
    /**
     * Listar status de todas as migrações
     */
    public function status() {
        echo "\n=== Status das Migrações ===\n\n";
        
        $executed = $this->getExecutedMigrations();
        $available = $this->getMigrationFiles();
        
        if (empty($available)) {
            echo "Nenhuma migração encontrada.\n";
            return;
        }
        
        foreach ($available as $migration) {
            $status = in_array($migration, $executed) ? '✓' : '○';
            $label = in_array($migration, $executed) ? 'Executada' : 'Pendente';
            echo "$status $migration [$label]\n";
        }
        
        echo "\nTotal: " . count($available) . " | Executadas: " . count($executed) . " | Pendentes: " . (count($available) - count($executed)) . "\n";
    }
}

// Executar
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        die("✗ Erro ao conectar com o banco de dados\n");
    }
    
    $runner = new MigrationRunner($conn);
    
    // Verificar argumento
    $command = $argv[1] ?? 'run';
    
    switch ($command) {
        case 'status':
            $runner->status();
            break;
        case 'run':
        default:
            $runner->run();
            break;
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
?>
