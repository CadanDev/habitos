<?php

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $logger = new Logger();

    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                listarAbastecimentos($pdo, $usuario_id, $logger);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        case 'POST':
            if ($action === 'abastecer') {
                abastecer($pdo, $usuario_id, $logger);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        case 'DELETE':
            if ($action === 'deletar') {
                deletarAbastecimento($pdo, $usuario_id, $logger);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido']);
    }
} catch (Exception $e) {
    $logger->error('Erro em abastecimentos: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno do servidor']);
}

function listarAbastecimentos($pdo, $usuario_id, $logger)
{
    try {
        $veiculo_id = $_GET['veiculo_id'] ?? null;
        
        if (!$veiculo_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do veículo não fornecido']);
            return;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("
            SELECT id FROM veiculos WHERE id = :veiculo_id AND usuario_id = :usuario_id
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id, ':usuario_id' => $usuario_id]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Acesso negado']);
            return;
        }

        $stmt = $pdo->prepare("
            SELECT id, valor_pago, valor_litro, litros, combustivel, quilometragem, data_abastecimento
            FROM abastecimentos
            WHERE veiculo_id = :veiculo_id
            ORDER BY data_abastecimento DESC
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id]);
        $abastecimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'sucesso' => true,
            'dados' => $abastecimentos,
            'total' => count($abastecimentos)
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao listar abastecimentos: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao listar abastecimentos']);
    }
}

function abastecer($pdo, $usuario_id, $logger)
{
    try {
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $veiculo_id = $dados['veiculo_id'] ?? null;
        
        if (!$veiculo_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do veículo não fornecido']);
            return;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("
            SELECT id FROM veiculos WHERE id = :veiculo_id AND usuario_id = :usuario_id
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id, ':usuario_id' => $usuario_id]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Acesso negado']);
            return;
        }

        // Validar campos obrigatórios
        $validacoes = [
            'valor_pago' => $dados['valor_pago'] ?? null,
            'valor_litro' => $dados['valor_litro'] ?? null,
            'litros' => $dados['litros'] ?? null,
            'data_abastecimento' => $dados['data_abastecimento'] ?? null,
        ];

        foreach ($validacoes as $campo => $valor) {
            if ($valor === null || $valor === '') {
                http_response_code(400);
                echo json_encode(['erro' => "Campo '{$campo}' é obrigatório"]);
                return;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO abastecimentos 
            (veiculo_id, valor_pago, valor_litro, litros, combustivel, quilometragem, data_abastecimento)
            VALUES 
            (:veiculo_id, :valor_pago, :valor_litro, :litros, :combustivel, :quilometragem, :data_abastecimento)
        ");

        $stmt->execute([
            ':veiculo_id' => $veiculo_id,
            ':valor_pago' => (float)$validacoes['valor_pago'],
            ':valor_litro' => (float)$validacoes['valor_litro'],
            ':litros' => (float)$validacoes['litros'],
            ':combustivel' => $dados['combustivel'] ?? 'gasolina',
            ':quilometragem' => $dados['quilometragem'] ?? null,
            ':data_abastecimento' => $validacoes['data_abastecimento'],
        ]);

        $abastecimento_id = $pdo->lastInsertId();
        
        // Atualizar quilometragem do veículo se fornecida
        if (!empty($dados['quilometragem'])) {
            $stmt = $pdo->prepare("UPDATE veiculos SET quilometragem = :quilometragem WHERE id = :id");
            $stmt->execute([':quilometragem' => (int)$dados['quilometragem'], ':id' => $veiculo_id]);
        }

        $logger->info("Abastecimento registrado: ID $abastecimento_id para veículo $veiculo_id");

        http_response_code(201);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Abastecimento registrado com sucesso',
            'abastecimento_id' => $abastecimento_id
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao registrar abastecimento: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao registrar abastecimento']);
    }
}

function deletarAbastecimento($pdo, $usuario_id, $logger)
{
    try {
        $abastecimento_id = $_GET['id'] ?? null;
        
        if (!$abastecimento_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do abastecimento não fornecido']);
            return;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("
            SELECT a.id FROM abastecimentos a
            JOIN veiculos v ON a.veiculo_id = v.id
            WHERE a.id = :id AND v.usuario_id = :usuario_id
        ");
        $stmt->execute([':id' => $abastecimento_id, ':usuario_id' => $usuario_id]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Acesso negado']);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM abastecimentos WHERE id = :id");
        $stmt->execute([':id' => $abastecimento_id]);

        $logger->info("Abastecimento deletado: ID $abastecimento_id para usuário $usuario_id");

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Abastecimento deletado com sucesso'
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao deletar abastecimento: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao deletar abastecimento']);
    }
}
