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

if (strlen($senha) < 6) {
    jsonResponse(['error' => 'A senha deve ter no mínimo 6 caracteres'], 400);
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        jsonResponse(['error' => 'Erro ao conectar com o banco de dados'], 500);
    }
    
    // Verificar se email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
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
    jsonResponse(['error' => 'Erro ao realizar cadastro'], 500);
}
?>
