<?php
/**
 * Script de Teste de Conexão com Banco de Dados
 * Útil para debug em produção
 */

// Carregar configurações
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

echo "=== TESTE DE CONEXÃO COM BANCO DE DADOS ===\n\n";

// Mostrar valores carregados
echo "Variáveis carregadas:\n";
echo "DB_HOST: " . env('DB_HOST', 'não definido') . "\n";
echo "DB_NAME: " . env('DB_NAME', 'não definido') . "\n";
echo "DB_USER: " . env('DB_USER', 'não definido') . "\n";
echo "DB_PASS: " . (env('DB_PASS', 'não definido') === 'não definido' ? 'não definido' : '***') . "\n";
echo "DB_CHARSET: " . env('DB_CHARSET', 'não definido') . "\n\n";

// Separar host e porta
$dbHost = env('DB_HOST', 'localhost');
$dbPort = 3306;

if (strpos($dbHost, ':') !== false) {
    list($dbHost, $dbPort) = explode(':', $dbHost, 2);
}

echo "Host após parse: {$dbHost}\n";
echo "Port após parse: {$dbPort}\n\n";

// Tentar conexão
echo "Tentando conectar...\n\n";

try {
    $dsn = "mysql:host=" . $dbHost . ";port=" . $dbPort . ";dbname=" . env('DB_NAME') . ";charset=" . env('DB_CHARSET', 'utf8mb4');
    
    echo "DSN: {$dsn}\n\n";
    
    $conn = new PDO(
        $dsn,
        env('DB_USER'),
        env('DB_PASS')
    );
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ CONEXÃO BEM-SUCEDIDA!\n\n";
    
    // Testar uma query simples
    $result = $conn->query("SELECT 1 as test");
    $row = $result->fetch();
    
    if ($row) {
        echo "✅ QUERY DE TESTE BEM-SUCEDIDA!\n";
        echo "Resultado: " . json_encode($row) . "\n";
    }
    
} catch(PDOException $e) {
    echo "❌ ERRO DE CONEXÃO:\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n\n";
    
    // Debug adicional
    echo "Dicas de solução:\n";
    echo "1. Verifique se as credenciais estão corretas\n";
    echo "2. Verifique se o host está acessível (pode ter restrição de IP)\n";
    echo "3. Verifique se a senha tem caracteres especiais não escapados\n";
    echo "4. No Hostinger, verifique se o usuário tem permissão para acessar de fora\n";
}
?>
