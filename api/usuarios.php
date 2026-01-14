<?php
require_once '../config/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, PUT');

if (!isLoggedIn()) {
    jsonResponse(['error' => 'Não autenticado'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$userId = getUserId();

try {
    $db = new Database();
    $conn = $db->getConnection();

    switch ($method) {
        case 'GET':
            $stmt = $conn->prepare("SELECT id, nome, email, avatar, tema, tts_voice, tts_volume, tts_rate, tts_pitch FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if (!$user) {
                jsonResponse(['error' => 'Usuário não encontrado'], 404);
            }
            jsonResponse(['success' => true, 'user' => $user]);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $ttsVoice = sanitizeInput($data['tts_voice'] ?? null);
            $ttsVolume = isset($data['tts_volume']) ? floatval($data['tts_volume']) : null;
            $ttsRate = isset($data['tts_rate']) ? floatval($data['tts_rate']) : null;
            $ttsPitch = isset($data['tts_pitch']) ? floatval($data['tts_pitch']) : null;

            $stmt = $conn->prepare("UPDATE usuarios SET tts_voice = ?, tts_volume = ?, tts_rate = ?, tts_pitch = ? WHERE id = ?");
            $stmt->execute([$ttsVoice, $ttsVolume, $ttsRate, $ttsPitch, $userId]);

            jsonResponse(['success' => true, 'message' => 'Preferências atualizadas']);
            break;
        default:
            jsonResponse(['error' => 'Método não suportado'], 405);
    }
} catch (PDOException $e) {
    jsonResponse(['error' => 'Erro ao processar solicitação'], 500);
}
?>