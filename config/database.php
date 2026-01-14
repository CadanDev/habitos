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
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Lançar exceção ao invés de fazer echo
            throw new PDOException("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
        
        return $this->conn;
    }
}
?>
