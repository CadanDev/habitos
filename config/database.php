<?php
/**
 * Configuração do Banco de Dados
 * 
 * As credenciais são carregadas do arquivo .env
 */

// Carregar variáveis de ambiente
require_once __DIR__ . '/env.php';
loadEnv();

// Separar host e porta se necessário
$dbHost = env('DB_HOST', 'localhost');
$dbPort = 3306; // porta padrão

if (strpos($dbHost, ':') !== false) {
    list($dbHost, $dbPort) = explode(':', $dbHost, 2);
}

define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort);
define('DB_NAME', env('DB_NAME', 'habitos_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

class Database {
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Construir DSN
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // Tentar conexão com timeout
            $this->conn = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            // Log detalhado do erro
            $errorMsg = "Erro de conexão com o banco de dados: " . $e->getMessage();
            
            // Log em arquivo
            if (function_exists('logger')) {
                logger()->error('Falha ao conectar com banco de dados', [
                    'host' => DB_HOST,
                    'port' => DB_PORT,
                    'database' => DB_NAME,
                    'user' => DB_USER,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);
            }
            
            throw new PDOException($errorMsg);
        }
        
        return $this->conn;
    }
}
?>
