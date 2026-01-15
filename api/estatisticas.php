<?php
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    jsonResponse(['error' => 'Não autenticado'], 401);
}

$userId = getUserId();

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Total de hábitos ativos
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM habitos WHERE usuario_id = ? AND ativo = 1");
    $stmt->execute([$userId]);
    $totalHabitos = $stmt->fetch()['total'];
    
    // Registros de hoje
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT r.habito_id) as concluidos
        FROM registros r
        INNER JOIN habitos h ON r.habito_id = h.id
        WHERE h.usuario_id = ? AND r.data = CURDATE() AND r.concluido = 1
    ");
    $stmt->execute([$userId]);
    $habitosHoje = $stmt->fetch()['concluidos'];
    
    // Sequência (streak) atual
    $stmt = $conn->prepare("
        SELECT h.id, h.nome,
               (SELECT COUNT(DISTINCT r2.data)
                FROM registros r2
                WHERE r2.habito_id = h.id 
                AND r2.concluido = 1
                AND r2.data <= CURDATE()
                AND NOT EXISTS (
                    SELECT 1 FROM registros r3
                    WHERE r3.habito_id = h.id
                    AND r3.data > r2.data
                    AND r3.data <= CURDATE()
                    AND r3.concluido = 0
                )
               ) as sequencia
        FROM habitos h
        WHERE h.usuario_id = ? AND h.ativo = 1
        ORDER BY sequencia DESC
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $melhorSequencia = $stmt->fetch();
    
    // Taxa de conclusão semanal
    $stmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT CASE WHEN r.concluido = 1 THEN r.id END) as concluidos,
            (SELECT COUNT(*) * 7 FROM habitos WHERE usuario_id = ? AND ativo = 1) as total_possivel
        FROM registros r
        INNER JOIN habitos h ON r.habito_id = h.id
        WHERE h.usuario_id = ? 
        AND r.data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ");
    $stmt->execute([$userId, $userId]);
    $semana = $stmt->fetch();
    $taxaSemanal = $semana['total_possivel'] > 0 
        ? round(($semana['concluidos'] / $semana['total_possivel']) * 100) 
        : 0;
    
    // Histórico dos últimos 30 dias
    $stmt = $conn->prepare("
        SELECT 
            r.data,
            COUNT(DISTINCT CASE WHEN r.concluido = 1 THEN r.habito_id END) as concluidos,
            (SELECT COUNT(*) FROM habitos WHERE usuario_id = ? AND ativo = 1) as total
        FROM registros r
        INNER JOIN habitos h ON r.habito_id = h.id
        WHERE h.usuario_id = ?
        AND r.data >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY r.data
        ORDER BY r.data ASC
    ");
    $stmt->execute([$userId, $userId]);
    $historico = $stmt->fetchAll();
    
    jsonResponse([
        'success' => true,
        'estatisticas' => [
            'total_habitos' => intval($totalHabitos),
            'habitos_hoje' => intval($habitosHoje),
            'melhor_sequencia' => [
                'nome' => $melhorSequencia['nome'] ?? null,
                'dias' => intval($melhorSequencia['sequencia'] ?? 0)
            ],
            'taxa_semanal' => intval($taxaSemanal),
            'historico_30_dias' => $historico
        ]
    ]);
    
} catch (PDOException $e) {
    jsonResponse(['error' => 'Erro ao buscar estatísticas'], 500);
}
?>
