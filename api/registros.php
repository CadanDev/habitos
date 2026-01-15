<?php
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, DELETE');

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
            // Buscar registros de um hábito
            $habitoId = intval($_GET['habito_id'] ?? 0);
            $dataInicio = $_GET['data_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
            $dataFim = $_GET['data_fim'] ?? date('Y-m-d');
            
            if (!$habitoId) {
                // Retornar todos os registros do usuário
                $stmt = $conn->prepare("
                    SELECT r.* 
                    FROM registros r
                    INNER JOIN habitos h ON r.habito_id = h.id
                    WHERE h.usuario_id = ? 
                    AND r.data BETWEEN ? AND ?
                    ORDER BY r.data DESC
                ");
                $stmt->execute([$userId, $dataInicio, $dataFim]);
            } else {
                // Verificar se o hábito pertence ao usuário
                $stmt = $conn->prepare("SELECT id FROM habitos WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$habitoId, $userId]);
                
                if (!$stmt->fetch()) {
                    jsonResponse(['error' => 'Hábito não encontrado'], 404);
                }
                
                $stmt = $conn->prepare("
                    SELECT * FROM registros 
                    WHERE habito_id = ? 
                    AND data BETWEEN ? AND ?
                    ORDER BY data DESC
                ");
                $stmt->execute([$habitoId, $dataInicio, $dataFim]);
            }
            
            $registros = $stmt->fetchAll();
            
            jsonResponse([
                'success' => true,
                'registros' => $registros
            ]);
            break;
            
        case 'POST':
            // Marcar/desmarcar hábito para um dia
            $data = json_decode(file_get_contents('php://input'), true);
            
            $habitoId = intval($data['habito_id'] ?? 0);
            $dataRegistro = $data['data'] ?? date('Y-m-d');
            $concluido = boolval($data['concluido'] ?? true);
            $notas = sanitizeInput($data['notas'] ?? '');
            
            if (!$habitoId) {
                jsonResponse(['error' => 'ID do hábito é obrigatório'], 400);
            }
            
            // Verificar se o hábito pertence ao usuário
            $stmt = $conn->prepare("SELECT id FROM habitos WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$habitoId, $userId]);
            
            if (!$stmt->fetch()) {
                jsonResponse(['error' => 'Hábito não encontrado'], 404);
            }
            
            // Inserir ou atualizar registro
            $stmt = $conn->prepare("
                INSERT INTO registros (habito_id, data, concluido, notas) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE concluido = ?, notas = ?
            ");
            $stmt->execute([$habitoId, $dataRegistro, $concluido, $notas, $concluido, $notas]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Registro atualizado com sucesso',
                'id' => $conn->lastInsertId() ?: null
            ]);
            break;
            
        case 'DELETE':
            // Remover registro
            $registroId = intval($_GET['id'] ?? 0);
            
            if (!$registroId) {
                jsonResponse(['error' => 'ID do registro é obrigatório'], 400);
            }
            
            // Verificar se o registro pertence ao usuário
            $stmt = $conn->prepare("
                SELECT r.id FROM registros r
                INNER JOIN habitos h ON r.habito_id = h.id
                WHERE r.id = ? AND h.usuario_id = ?
            ");
            $stmt->execute([$registroId, $userId]);
            
            if (!$stmt->fetch()) {
                jsonResponse(['error' => 'Registro não encontrado'], 404);
            }
            
            $stmt = $conn->prepare("DELETE FROM registros WHERE id = ?");
            $stmt->execute([$registroId]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Registro removido com sucesso'
            ]);
            break;
            
        default:
            jsonResponse(['error' => 'Método não suportado'], 405);
    }
    
} catch (PDOException $e) {
    jsonResponse(['error' => 'Erro ao processar solicitação: ' . $e->getMessage()], 500);
}
?>
