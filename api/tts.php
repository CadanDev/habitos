<?php
/**
 * API Proxy para OpenAI Text-to-Speech
 * Evita expor a API key no frontend
 */

require_once '../config/config.php';

// Verificar se usuário está logado
requireLogin();

// Obter dados da requisição
$data = json_decode(file_get_contents('php://input'), true);
$text = $data['text'] ?? '';
$voice = $data['voice'] ?? 'nova';
$speed = floatval($data['speed'] ?? 1.0);

// Validar dados
if (empty($text)) {
    http_response_code(400);
    echo json_encode(['error' => 'Texto não fornecido']);
    exit;
}

// Validar voz
$validVoices = ['alloy', 'echo', 'fable', 'onyx', 'nova', 'shimmer'];
if (!in_array($voice, $validVoices)) {
    $voice = 'nova';
}

// Validar velocidade
if ($speed < 0.25 || $speed > 4.0) {
    $speed = 1.0;
}

// Obter API key do ambiente
$apiKey = getenv('OPENAI_API_KEY');

// Se não houver API key configurada, retornar erro
if (empty($apiKey)) {
    http_response_code(503);
    echo json_encode([
        'error' => 'OpenAI API não configurada. Configure a variável OPENAI_API_KEY no arquivo .env'
    ]);
    exit;
}

// Fazer requisição para OpenAI
$ch = curl_init('https://api.openai.com/v1/audio/speech');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => 'tts-1', // ou 'tts-1-hd' para melhor qualidade
        'input' => $text,
        'voice' => $voice,
        'speed' => $speed
    ]),
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Verificar se houve erro
if ($httpCode !== 200) {
    http_response_code($httpCode);
    
    // Tentar decodificar erro da OpenAI
    $errorData = json_decode($response, true);
    $errorMessage = $errorData['error']['message'] ?? 'Erro ao gerar áudio';
    
    echo json_encode(['error' => $errorMessage]);
    exit;
}

// Retornar áudio
header('Content-Type: audio/mpeg');
header('Content-Length: ' . strlen($response));
echo $response;
