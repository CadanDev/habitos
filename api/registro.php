<?php
require_once '../config/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Método não permitido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$nome = sanitizeInput($data['nome'] ?? '');
$email = sanitizeInput($data['email'] ?? '');
$senha = $data['senha'] ?? '';

// Validações
if (empty($nome) || empty($email) || empty($senha)) {
    jsonResponse(['error' => 'Todos os campos são obrigatórios'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['error' => 'Email inválido'], 400);
}

// Trava: apenas permitir registro do email específico
$emailPermitido = 'cadan@cadan.com';
if (strtolower($email) !== strtolower($emailPermitido)) {
    jsonResponse(['error' => 'Registro não permitido para este email'], 403);
}

if (strlen($senha) < 6) {
    jsonResponse(['error' => 'A senha deve ter no mínimo 6 caracteres'], 400);
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        logger()->error('Falha ao conectar com o banco de dados');
        jsonResponse(['error' => 'Erro ao conectar com o banco de dados'], 500);
    }
    
    // Verificar se email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        logger()->warning('Tentativa de registro com email já existente', ['email' => $email]);
        jsonResponse(['error' => 'Este email já está cadastrado'], 409);
    }
    
    // Criar novo usuário
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $senhaHash]);
    
    $userId = $conn->lastInsertId();
    
    // Criar sessão automática
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $nome;
    $_SESSION['user_email'] = $email;
    $_SESSION['login_time'] = time();
    
    logger()->info('Novo usuário registrado', ['email' => $email, 'user_id' => $userId, 'nome' => $nome]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Cadastro realizado com sucesso',
        'user' => [
            'id' => $userId,
            'nome' => $nome,
            'email' => $email
        ]
    ], 201);
    
} catch (PDOException $e) {
    logger()->exception($e);
    // Em desenvolvimento, mostrar detalhes do erro
    $isDev = (env('APP_ENV', 'production') === 'development');
    $errorMsg = $isDev ? $e->getMessage() : 'Erro ao realizar cadastro';
    
    jsonResponse(['error' => $errorMsg], 500);
} catch (Exception $e) {
    logger()->exception($e);
    // Capturar outros erros
    $isDev = (env('APP_ENV', 'production') === 'development');
    $errorMsg = $isDev ? $e->getMessage() : 'Erro ao realizar cadastro';
    
    jsonResponse(['error' => $errorMsg], 500);
}
?>
