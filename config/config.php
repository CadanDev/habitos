<?php
/**
 * Configurações Gerais da Aplicação
 */

// Carregar variáveis de ambiente
require_once __DIR__ . '/env.php';
loadEnv();

// Iniciar sessão com cookie protegido por HttpOnly
if (session_status() === PHP_SESSION_NONE) {
    $cookieParams = session_get_cookie_params();
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => $cookieParams['lifetime'],
        'path' => $cookieParams['path'] ?? '/',
        'domain' => $cookieParams['domain'] ?? '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => $cookieParams['samesite'] ?? 'Lax',
    ]);

    session_start();
}

// Timezone
date_default_timezone_set(env('TIMEZONE', 'America/Sao_Paulo'));

// URL base da aplicação
define('BASE_URL', env('BASE_URL', 'http://localhost/habitos'));

// Configurações de segurança
define('SESSION_TIMEOUT', env('SESSION_TIMEOUT', 3600)); // 1 hora em segundos

// Incluir arquivo de banco de dados
require_once __DIR__ . '/database.php';

// Incluir sistema de log
require_once __DIR__ . '/logger.php';

// Funções auxiliares
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
?>
