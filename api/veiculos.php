<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/logger.php';

header('Content-Type: application/json');
session_start();

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
                listarVeiculos($pdo, $usuario_id, $logger);
            } elseif ($action === 'detalhes') {
                detalhesVeiculo($pdo, $usuario_id, $logger);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        case 'POST':
            if ($action === 'criar') {
                criarVeiculo($pdo, $usuario_id, $logger);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        case 'PUT':
            if ($action === 'atualizar') {
                atualizarVeiculo($pdo, $usuario_id, $logger);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        case 'DELETE':
            if ($action === 'deletar') {
                deletarVeiculo($pdo, $usuario_id, $logger);
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
    $logger->error('Erro em veículos: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno do servidor']);
}

function listarVeiculos($pdo, $usuario_id, $logger)
{
    try {
        $stmt = $pdo->prepare("
            SELECT id, modelo, marca, cor, ano, apelido, quilometragem, data_criacao, data_atualizacao
            FROM veiculos
            WHERE usuario_id = :usuario_id
            ORDER BY data_criacao DESC
        ");
        $stmt->execute([':usuario_id' => $usuario_id]);
        $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'sucesso' => true,
            'dados' => $veiculos,
            'total' => count($veiculos)
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao listar veículos: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao listar veículos']);
    }
}

function detalhesVeiculo($pdo, $usuario_id, $logger)
{
    try {
        $veiculo_id = $_GET['id'] ?? null;
        
        if (!$veiculo_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do veículo não fornecido']);
            return;
        }

        // Buscar veículo
        $stmt = $pdo->prepare("
            SELECT id, modelo, marca, cor, ano, apelido, quilometragem, data_criacao, data_atualizacao
            FROM veiculos
            WHERE id = :id AND usuario_id = :usuario_id
        ");
        $stmt->execute([':id' => $veiculo_id, ':usuario_id' => $usuario_id]);
        $veiculo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$veiculo) {
            http_response_code(404);
            echo json_encode(['erro' => 'Veículo não encontrado']);
            return;
        }

        // Buscar últimos abastecimentos
        $stmt = $pdo->prepare("
            SELECT id, valor_pago, valor_litro, litros, combustivel, quilometragem, data_abastecimento
            FROM abastecimentos
            WHERE veiculo_id = :veiculo_id
            ORDER BY data_abastecimento DESC
            LIMIT 5
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id]);
        $abastecimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Buscar últimas manutenções
        $stmt = $pdo->prepare("
            SELECT id, tipo, valor_pago, descricao, gravidade, eu_que_fiz, quilometragem, data_manutencao
            FROM manutencoes
            WHERE veiculo_id = :veiculo_id
            ORDER BY data_manutencao DESC
            LIMIT 5
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id]);
        $manutencoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular totais
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_abastecimentos,
                SUM(valor_pago) as total_gasto_abastecimentos,
                AVG(valor_litro) as preco_medio_litro
            FROM abastecimentos
            WHERE veiculo_id = :veiculo_id
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id]);
        $stats_abastecimentos = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_manutencoes,
                SUM(valor_pago) as total_gasto_manutencoes
            FROM manutencoes
            WHERE veiculo_id = :veiculo_id
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id]);
        $stats_manutencoes = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'sucesso' => true,
            'veiculo' => $veiculo,
            'abastecimentos' => [
                'registros' => $abastecimentos,
                'stats' => $stats_abastecimentos
            ],
            'manutencoes' => [
                'registros' => $manutencoes,
                'stats' => $stats_manutencoes
            ]
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao buscar detalhes do veículo: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao buscar detalhes do veículo']);
    }
}

function criarVeiculo($pdo, $usuario_id, $logger)
{
    try {
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $validacoes = [
            'modelo' => !empty($dados['modelo']) ? $dados['modelo'] : null,
            'marca' => !empty($dados['marca']) ? $dados['marca'] : null,
            'ano' => !empty($dados['ano']) ? (int)$dados['ano'] : null,
            'cor' => $dados['cor'] ?? null,
            'apelido' => $dados['apelido'] ?? null,
        ];

        foreach (['modelo', 'marca', 'ano'] as $campo) {
            if ($validacoes[$campo] === null) {
                http_response_code(400);
                echo json_encode(['erro' => "Campo '{$campo}' é obrigatório"]);
                return;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO veiculos (usuario_id, modelo, marca, cor, ano, apelido, quilometragem)
            VALUES (:usuario_id, :modelo, :marca, :cor, :ano, :apelido, :quilometragem)
        ");

        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':modelo' => $validacoes['modelo'],
            ':marca' => $validacoes['marca'],
            ':cor' => $validacoes['cor'],
            ':ano' => $validacoes['ano'],
            ':apelido' => $validacoes['apelido'],
            ':quilometragem' => $dados['quilometragem'] ?? 0,
        ]);

        $veiculo_id = $pdo->lastInsertId();
        $logger->info("Veículo criado: ID $veiculo_id para usuário $usuario_id");

        http_response_code(201);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Veículo cadastrado com sucesso',
            'veiculo_id' => $veiculo_id
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao criar veículo: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao criar veículo']);
    }
}

function atualizarVeiculo($pdo, $usuario_id, $logger)
{
    try {
        $veiculo_id = $_GET['id'] ?? null;
        
        if (!$veiculo_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do veículo não fornecido']);
            return;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("SELECT id FROM veiculos WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute([':id' => $veiculo_id, ':usuario_id' => $usuario_id]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Acesso negado']);
            return;
        }

        $dados = json_decode(file_get_contents('php://input'), true);
        
        $atualizacoes = [];
        $params = [':id' => $veiculo_id];
        
        $campos = ['modelo', 'marca', 'cor', 'ano', 'apelido', 'quilometragem'];
        
        foreach ($campos as $campo) {
            if (isset($dados[$campo])) {
                $atualizacoes[] = "$campo = :$campo";
                $params[":$campo"] = $dados[$campo];
            }
        }

        if (empty($atualizacoes)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Nenhum campo para atualizar']);
            return;
        }

        $sql = "UPDATE veiculos SET " . implode(', ', $atualizacoes) . ", data_atualizacao = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $logger->info("Veículo atualizado: ID $veiculo_id para usuário $usuario_id");

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Veículo atualizado com sucesso'
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao atualizar veículo: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar veículo']);
    }
}

function deletarVeiculo($pdo, $usuario_id, $logger)
{
    try {
        $veiculo_id = $_GET['id'] ?? null;
        
        if (!$veiculo_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do veículo não fornecido']);
            return;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("SELECT id FROM veiculos WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute([':id' => $veiculo_id, ':usuario_id' => $usuario_id]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Acesso negado']);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM veiculos WHERE id = :id");
        $stmt->execute([':id' => $veiculo_id]);

        $logger->info("Veículo deletado: ID $veiculo_id para usuário $usuario_id");

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Veículo deletado com sucesso'
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao deletar veículo: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao deletar veículo']);
    }
}
