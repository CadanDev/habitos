<?php
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Método não permitido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$email = sanitizeInput($data['email'] ?? '');
$senha = $data['senha'] ?? '';

if (empty($email) || empty($senha)) {
    jsonResponse(['error' => 'Email e senha são obrigatórios'], 400);
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Atualizar último acesso
        $updateStmt = $conn->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?");
        $updateStmt->execute([$usuario['id']]);
        
        // Criar sessão
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_name'] = $usuario['nome'];
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['login_time'] = time();
        
        logger()->info('Login bem-sucedido', ['email' => $email, 'user_id' => $usuario['id']]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email']
            ]
        ]);
    } else {
        logger()->warning('Tentativa de login falhou', ['email' => $email]);
        jsonResponse(['error' => 'Email ou senha inválidos'], 401);
    }
} catch (PDOException $e) {
    logger()->exception($e);
    jsonResponse(['error' => 'Erro ao realizar login'], 500);
}
?>
