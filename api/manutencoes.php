<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Enums/TipoManutencao.php';

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
                listarManutencoes($pdo, $usuario_id, $logger);
            } elseif ($action === 'tipos') {
                listarTipos($pdo, $usuario_id, $logger);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        case 'POST':
            if ($action === 'criar') {
                criarManutencao($pdo, $usuario_id, $logger);
            } elseif ($action === 'lavada_externa') {
                criarManutencaoEspecifica($pdo, $usuario_id, $logger, TipoManutencao::LAVADA_EXTERNA);
            } elseif ($action === 'lavada_interna') {
                criarManutencaoEspecifica($pdo, $usuario_id, $logger, TipoManutencao::LAVADA_INTERNA);
            } elseif ($action === 'troca_oleo_simples') {
                criarManutencaoEspecifica($pdo, $usuario_id, $logger, TipoManutencao::TROCA_OLEO_SIMPLES);
            } elseif ($action === 'troca_oleo_avancada') {
                criarManutencaoEspecifica($pdo, $usuario_id, $logger, TipoManutencao::TROCA_OLEO_AVANCADA);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'Ação não especificada']);
            }
            break;

        case 'DELETE':
            if ($action === 'deletar') {
                deletarManutencao($pdo, $usuario_id, $logger);
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
    $logger->error('Erro em manutenções: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno do servidor']);
}

function listarManutencoes($pdo, $usuario_id, $logger)
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
            SELECT id, tipo, valor_pago, descricao, gravidade, eu_que_fiz, quilometragem, data_manutencao
            FROM manutencoes
            WHERE veiculo_id = :veiculo_id
            ORDER BY data_manutencao DESC
        ");
        $stmt->execute([':veiculo_id' => $veiculo_id]);
        $manutencoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'sucesso' => true,
            'dados' => $manutencoes,
            'total' => count($manutencoes)
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao listar manutenções: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao listar manutenções']);
    }
}

function listarTipos($pdo, $usuario_id, $logger)
{
    try {
        $tipos = [];
        
        foreach (TipoManutencao::cases() as $tipo) {
            $tipos[] = [
                'value' => $tipo->value,
                'label' => $tipo->label(),
                'color' => $tipo->color()
            ];
        }
        
        // Agrupar por categoria
        $grouped = TipoManutencao::grouped();
        $tiposAgrupados = [];
        
        foreach ($grouped as $categoria => $tiposCategoria) {
            $tiposAgrupados[$categoria] = array_map(function($tipo) {
                return [
                    'value' => $tipo->value,
                    'label' => $tipo->label(),
                    'color' => $tipo->color()
                ];
            }, $tiposCategoria);
        }
        
        echo json_encode([
            'sucesso' => true,
            'tipos' => $tipos,
            'agrupados' => $tiposAgrupados
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao listar tipos: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao listar tipos']);
    }
}

function criarManutencao($pdo, $usuario_id, $logger)
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
            'tipo' => $dados['tipo'] ?? null,
            'valor_pago' => $dados['valor_pago'] ?? null,
            'data_manutencao' => $dados['data_manutencao'] ?? null,
        ];

        foreach ($validacoes as $campo => $valor) {
            if ($valor === null || $valor === '') {
                http_response_code(400);
                echo json_encode(['erro' => "Campo '{$campo}' é obrigatório"]);
                return;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO manutencoes 
            (veiculo_id, tipo, valor_pago, descricao, gravidade, eu_que_fiz, quilometragem, data_manutencao)
            VALUES 
            (:veiculo_id, :tipo, :valor_pago, :descricao, :gravidade, :eu_que_fiz, :quilometragem, :data_manutencao)
        ");

        $stmt->execute([
            ':veiculo_id' => $veiculo_id,
            ':tipo' => $validacoes['tipo'],
            ':valor_pago' => (float)$validacoes['valor_pago'],
            ':descricao' => $dados['descricao'] ?? null,
            ':gravidade' => $dados['gravidade'] ?? 'normal',
            ':eu_que_fiz' => $dados['eu_que_fiz'] ? 1 : 0,
            ':quilometragem' => $dados['quilometragem'] ?? null,
            ':data_manutencao' => $validacoes['data_manutencao'],
        ]);

        $manutencao_id = $pdo->lastInsertId();
        
        // Atualizar quilometragem do veículo se fornecida
        if (!empty($dados['quilometragem'])) {
            $stmt = $pdo->prepare("UPDATE veiculos SET quilometragem = :quilometragem WHERE id = :id");
            $stmt->execute([':quilometragem' => (int)$dados['quilometragem'], ':id' => $veiculo_id]);
        }

        $logger->info("Manutenção criada: ID $manutencao_id para veículo $veiculo_id");

        http_response_code(201);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Manutenção registrada com sucesso',
            'manutencao_id' => $manutencao_id
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao criar manutenção: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao criar manutenção']);
    }
}

function criarManutencaoEspecifica($pdo, $usuario_id, $logger, TipoManutencao $tipo)
{
    try {
        $dados = json_decode(file_get_contents('php://input'), true);
        $dados['tipo'] = $tipo->value;
        
        // Repassar para criarManutencao
        $_REQUEST['action'] = 'criar';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Simular a requisição POST
        $GLOBALS['_POST_DATA'] = $dados;
        
        // Chamada direto do handler
        ob_start();
        
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
            'data_manutencao' => $dados['data_manutencao'] ?? null,
        ];

        foreach ($validacoes as $campo => $valor) {
            if ($valor === null || $valor === '') {
                http_response_code(400);
                echo json_encode(['erro' => "Campo '{$campo}' é obrigatório"]);
                return;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO manutencoes 
            (veiculo_id, tipo, valor_pago, descricao, gravidade, eu_que_fiz, quilometragem, data_manutencao)
            VALUES 
            (:veiculo_id, :tipo, :valor_pago, :descricao, :gravidade, :eu_que_fiz, :quilometragem, :data_manutencao)
        ");

        $stmt->execute([
            ':veiculo_id' => $veiculo_id,
            ':tipo' => $tipo->value,
            ':valor_pago' => (float)$validacoes['valor_pago'],
            ':descricao' => $dados['descricao'] ?? null,
            ':gravidade' => $dados['gravidade'] ?? 'normal',
            ':eu_que_fiz' => $dados['eu_que_fiz'] ? 1 : 0,
            ':quilometragem' => $dados['quilometragem'] ?? null,
            ':data_manutencao' => $validacoes['data_manutencao'],
        ]);

        $manutencao_id = $pdo->lastInsertId();
        
        // Atualizar quilometragem do veículo se fornecida
        if (!empty($dados['quilometragem'])) {
            $stmt = $pdo->prepare("UPDATE veiculos SET quilometragem = :quilometragem WHERE id = :id");
            $stmt->execute([':quilometragem' => (int)$dados['quilometragem'], ':id' => $veiculo_id]);
        }

        $logger->info("Manutenção {$tipo->label()} criada: ID $manutencao_id para veículo $veiculo_id");

        http_response_code(201);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Manutenção ' . $tipo->label() . ' registrada com sucesso',
            'manutencao_id' => $manutencao_id,
            'tipo' => $tipo->label()
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao criar manutenção: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao criar manutenção']);
    }
}

function deletarManutencao($pdo, $usuario_id, $logger)
{
    try {
        $manutencao_id = $_GET['id'] ?? null;
        
        if (!$manutencao_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID da manutenção não fornecido']);
            return;
        }

        // Verificar permissão
        $stmt = $pdo->prepare("
            SELECT m.id FROM manutencoes m
            JOIN veiculos v ON m.veiculo_id = v.id
            WHERE m.id = :id AND v.usuario_id = :usuario_id
        ");
        $stmt->execute([':id' => $manutencao_id, ':usuario_id' => $usuario_id]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Acesso negado']);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM manutencoes WHERE id = :id");
        $stmt->execute([':id' => $manutencao_id]);

        $logger->info("Manutenção deletada: ID $manutencao_id para usuário $usuario_id");

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Manutenção deletada com sucesso'
        ]);
    } catch (Exception $e) {
        $logger->error('Erro ao deletar manutenção: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao deletar manutenção']);
    }
}
