<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
require_once '../config/config.php';

if (!isLoggedIn()) {
    jsonResponse(['error' => 'N�o autenticado'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$userId = getUserId();

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    switch ($method) {
        case 'GET':
            // Listar h�bitos do usu�rio com configura��o de alertas
            $stmt = $conn->prepare("
                  SELECT h.*, 
                       COUNT(DISTINCT r.id) as total_registros,
                       COUNT(DISTINCT CASE WHEN r.data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN r.id END) as registros_semana,
                       a.id AS alerta_id,
                       a.ativo AS alerta_ativo,
                       a.tipo AS alerta_tipo,
                       a.hora AS alerta_hora,
                       a.dias AS alerta_dias,
                       a.intervalo_minutos AS alerta_intervalo_minutos,
                       a.descanso_segundos AS alerta_descanso_segundos,
                       a.mensagem_alerta AS alerta_mensagem,
                       a.mensagem_descanso AS alerta_mensagem_descanso,
                       a.mensagem_fim_descanso AS alerta_mensagem_fim_descanso,
                       a.descanso_requer_trigger AS alerta_descanso_requer_trigger
                FROM habitos h
                LEFT JOIN registros r ON h.id = r.habito_id AND r.concluido = 1
                LEFT JOIN alertas a ON a.habito_id = h.id
                WHERE h.usuario_id = ? AND h.ativo = 1
                GROUP BY h.id
                ORDER BY h.data_criacao DESC
            ");
            $stmt->execute([$userId]);
            $habitos = $stmt->fetchAll();
            
            jsonResponse([
                'success' => true,
                'habitos' => $habitos
            ]);
            break;
            
        case 'POST':
            // Criar novo h�bito (com configura��o opcional de alerta)
            $data = json_decode(file_get_contents('php://input'), true);
            
            $nome = sanitizeInput($data['nome'] ?? '');
            $descricao = sanitizeInput($data['descricao'] ?? '');
            $cor = sanitizeInput($data['cor'] ?? '#3b82f6');
            $icone = sanitizeInput($data['icone'] ?? '');
            $meta_semanal = intval($data['meta_semanal'] ?? 7);
            
            if (empty($nome)) {
                jsonResponse(['error' => 'Nome do h�bito � obrigat�rio'], 400);
            }
            
            $stmt = $conn->prepare("
                INSERT INTO habitos (usuario_id, nome, descricao, cor, icone, meta_semanal) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $nome, $descricao, $cor, $icone, $meta_semanal]);
            $novoHabitoId = $conn->lastInsertId();

            // Configura��o de alerta (opcional)
            $alertaAtivo = isset($data['alerta_ativo']) ? (bool)$data['alerta_ativo'] : null;
            $alertaTipo = sanitizeInput($data['alerta_tipo'] ?? '');
            $alertaHora = sanitizeInput($data['alerta_hora'] ?? null);
            $alertaDias = sanitizeInput($data['alerta_dias'] ?? null);
            $alertaIntervalo = isset($data['alerta_intervalo_minutos']) ? intval($data['alerta_intervalo_minutos']) : null;
            $alertaDescanso = isset($data['alerta_descanso_segundos']) ? intval($data['alerta_descanso_segundos']) : null;
            $alertaMensagem = sanitizeInput($data['alerta_mensagem'] ?? null);
            $alertaMensagemDescanso = sanitizeInput($data['alerta_mensagem_descanso'] ?? null);
            $alertaMensagemFimDescanso = sanitizeInput($data['alerta_mensagem_fim_descanso'] ?? null);

            if ($alertaAtivo !== null && !empty($alertaTipo)) {
                $stmtAlerta = $conn->prepare("
                    INSERT INTO alertas (habito_id, ativo, tipo, hora, dias, intervalo_minutos, descanso_segundos, mensagem_alerta, mensagem_descanso, mensagem_fim_descanso)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtAlerta->execute([$novoHabitoId, $alertaAtivo ? 1 : 0, $alertaTipo, $alertaHora, $alertaDias, $alertaIntervalo, $alertaDescanso, $alertaMensagem, $alertaMensagemDescanso, $alertaMensagemFimDescanso]);
            }
            
            jsonResponse([
                'success' => true,
                'message' => 'H�bito criado com sucesso',
                'id' => $novoHabitoId
            ], 201);
            break;
            
        case 'PUT':
            // Atualizar h�bito e configura��o de alerta
            $data = json_decode(file_get_contents('php://input'), true);
            $habitoId = intval($data['id'] ?? 0);
            
            if (!$habitoId) {
                jsonResponse(['error' => 'ID do h�bito � obrigat�rio'], 400);
            }
            
            // Verificar se o h�bito pertence ao usu�rio
            $stmt = $conn->prepare("SELECT id FROM habitos WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$habitoId, $userId]);
            
            if (!$stmt->fetch()) {
                jsonResponse(['error' => 'H�bito n�o encontrado'], 404);
            }
            
            $nome = sanitizeInput($data['nome'] ?? '');
            $descricao = sanitizeInput($data['descricao'] ?? '');
            $cor = sanitizeInput($data['cor'] ?? '#3b82f6');
            $icone = sanitizeInput($data['icone'] ?? '');
            $meta_semanal = intval($data['meta_semanal'] ?? 7);
            
            $stmt = $conn->prepare("
                UPDATE habitos 
                SET nome = ?, descricao = ?, cor = ?, icone = ?, meta_semanal = ?
                WHERE id = ? AND usuario_id = ?
            ");
            $stmt->execute([$nome, $descricao, $cor, $icone, $meta_semanal, $habitoId, $userId]);

            // Atualizar/inserir configura��o de alerta se enviada
            $alertaAtivo = isset($data['alerta_ativo']) ? (bool)$data['alerta_ativo'] : null;
            $alertaTipo = sanitizeInput($data['alerta_tipo'] ?? '');
            $alertaHora = sanitizeInput($data['alerta_hora'] ?? null);
            $alertaDias = sanitizeInput($data['alerta_dias'] ?? null);
            $alertaIntervalo = isset($data['alerta_intervalo_minutos']) ? intval($data['alerta_intervalo_minutos']) : null;
            $alertaDescanso = isset($data['alerta_descanso_segundos']) ? intval($data['alerta_descanso_segundos']) : null;
            $alertaMensagem = sanitizeInput($data['alerta_mensagem'] ?? null);
            $alertaMensagemDescanso = sanitizeInput($data['alerta_mensagem_descanso'] ?? null);
            $alertaMensagemFimDescanso = sanitizeInput($data['alerta_mensagem_fim_descanso'] ?? null);

            if ($alertaAtivo !== null && !empty($alertaTipo)) {
                // Verifica se j� existe registro de alerta para o h�bito
                $stmtCheck = $conn->prepare("SELECT id FROM alertas WHERE habito_id = ?");
                $stmtCheck->execute([$habitoId]);
                $existe = $stmtCheck->fetch();

                if ($existe) {
                    $stmtAlerta = $conn->prepare("
                        UPDATE alertas
                        SET ativo = ?, tipo = ?, hora = ?, dias = ?, intervalo_minutos = ?, descanso_segundos = ?, mensagem_alerta = ?, mensagem_descanso = ?, mensagem_fim_descanso = ?
                        WHERE habito_id = ?
                    ");
                    $stmtAlerta->execute([$alertaAtivo ? 1 : 0, $alertaTipo, $alertaHora, $alertaDias, $alertaIntervalo, $alertaDescanso, $alertaMensagem, $alertaMensagemDescanso, $alertaMensagemFimDescanso, $habitoId]);
                } else {
                    $stmtAlerta = $conn->prepare("
                        INSERT INTO alertas (habito_id, ativo, tipo, hora, dias, intervalo_minutos, descanso_segundos, mensagem_alerta, mensagem_descanso, mensagem_fim_descanso)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmtAlerta->execute([$habitoId, $alertaAtivo ? 1 : 0, $alertaTipo, $alertaHora, $alertaDias, $alertaIntervalo, $alertaDescanso, $alertaMensagem, $alertaMensagemDescanso, $alertaMensagemFimDescanso]);
                }
            }
            
            jsonResponse([
                'success' => true,
                'message' => 'H�bito atualizado com sucesso'
            ]);
            break;
            
        case 'DELETE':
            // Deletar h�bito (soft delete)
            $habitoId = intval($_GET['id'] ?? 0);
            
            if (!$habitoId) {
                jsonResponse(['error' => 'ID do h�bito � obrigat�rio'], 400);
            }
            
            $stmt = $conn->prepare("UPDATE habitos SET ativo = 0 WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$habitoId, $userId]);
            
            if ($stmt->rowCount() > 0) {
                jsonResponse([
                    'success' => true,
                    'message' => 'H�bito removido com sucesso'
                ]);
            } else {
                jsonResponse(['error' => 'H�bito n�o encontrado'], 404);
            }
            break;
            
        default:
            jsonResponse(['error' => 'M�todo n�o suportado'], 405);
    }
    
} catch (Exception $e) {
    logger('ERROR', 'API Hábitos - ' . $e->getMessage());
    jsonResponse(['error' => 'Erro ao processar solicitação: ' . ($e->getMessage())], 500);
}
?>
