<?php
/**
 * Sistema E-SIC - API de Recursos
 * 
 * Gerencia operações de recursos contra decisões
 * 
 * @author Sistema E-SIC Rio Claro
 * @version 2.0
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tratar requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir dependências
require_once '../app/config/Database.php';

session_start();

// Função para calcular data limite de recurso
function calcularDataLimiteRecurso($tipo = 'primeira_instancia') {
    $prazos = [
        'primeira_instancia' => 5,  // 5 dias úteis
        'segunda_instancia' => 10,  // 10 dias úteis
        'terceira_instancia' => 15  // 15 dias úteis
    ];
    
    $prazo = $prazos[$tipo] ?? 5;
    
    $dataLimite = new DateTime();
    $diasAdicionados = 0;
    
    while ($diasAdicionados < $prazo) {
        $dataLimite->add(new DateInterval('P1D'));
        if ($dataLimite->format('N') < 6) {
            $diasAdicionados++;
        }
    }
    
    return $dataLimite->format('Y-m-d');
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        
        case 'criar':
            // Validar dados obrigatórios
            if (empty($_POST['pedido_id']) || empty($_POST['motivo']) || empty($_POST['justificativa'])) {
                throw new Exception("Campos obrigatórios não preenchidos");
            }
            
            $pedidoId = intval($_POST['pedido_id']);
            $tipo = $_POST['tipo'] ?? 'primeira_instancia';
            $motivo = $_POST['motivo'];
            $justificativa = $_POST['justificativa'];
            
            // Validar se pedido existe e está em situação que permite recurso
            $stmt = $pdo->prepare("
                SELECT p.*, u.id as requerente_id 
                FROM pedidos p
                JOIN usuarios u ON p.requerente_id = u.id
                WHERE p.id = ?
            ");
            $stmt->execute([$pedidoId]);
            $pedido = $stmt->fetch();
            
            if (!$pedido) {
                throw new Exception("Pedido não encontrado");
            }
            
            if (!in_array($pedido['status'], ['negado', 'parcialmente_atendido'])) {
                throw new Exception("Recurso só pode ser interposto contra pedidos negados ou parcialmente atendidos");
            }
            
            // Verificar se já existe recurso para este pedido
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recursos WHERE pedido_id = ? AND tipo = ?");
            $stmt->execute([$pedidoId, $tipo]);
            $recursoExistente = $stmt->fetch();
            
            if ($recursoExistente['total'] > 0) {
                throw new Exception("Já existe um recurso desta instância para este pedido");
            }
            
            $pdo->beginTransaction();
            
            try {
                // Gerar protocolo do recurso
                $ano = date('Y');
                $stmt = $pdo->prepare("SELECT COUNT(*) + 1 as proximo FROM recursos WHERE YEAR(data_cadastro) = ?");
                $stmt->execute([$ano]);
                $resultado = $stmt->fetch();
                $protocoloRecurso = sprintf('R%d%06d', $ano, $resultado['proximo']);
                
                // Calcular data limite
                $dataLimite = calcularDataLimiteRecurso($tipo);
                
                // Criar recurso
                $stmt = $pdo->prepare("
                    INSERT INTO recursos (protocolo, pedido_id, requerente_id, tipo, motivo, justificativa, 
                                         status, data_limite)
                    VALUES (?, ?, ?, ?, ?, ?, 'aguardando', ?)
                ");
                $stmt->execute([
                    $protocoloRecurso,
                    $pedidoId,
                    $pedido['requerente_id'],
                    $tipo,
                    $motivo,
                    $justificativa,
                    $dataLimite
                ]);
                
                $recursoId = $pdo->lastInsertId();
                
                // Atualizar status do pedido para indicar que há recurso
                $stmt = $pdo->prepare("UPDATE pedidos SET status = 'em_analise' WHERE id = ?");
                $stmt->execute([$pedidoId]);
                
                // Criar tramitação do recurso
                $stmt = $pdo->prepare("
                    INSERT INTO tramitacoes (recurso_id, status_novo, observacoes, usuario_id)
                    VALUES (?, 'aguardando', 'Recurso interposto', ?)
                ");
                $stmt->execute([$recursoId, $pedido['requerente_id']]);
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Recurso enviado com sucesso!',
                    'data' => [
                        'protocolo_recurso' => $protocoloRecurso,
                        'protocolo_pedido' => $pedido['protocolo'],
                        'recurso_id' => $recursoId,
                        'data_limite' => date('d/m/Y', strtotime($dataLimite))
                    ]
                ]);
                
            } catch (Exception $e) {
                $pdo->rollback();
                throw $e;
            }
            break;
            
        case 'listar':
            // Listar recursos
            $limit = min(intval($_GET['limit'] ?? 50), 100);
            $offset = max(intval($_GET['offset'] ?? 0), 0);
            
            $where = [];
            $params = [];
            
            if (!empty($_GET['pedido_id'])) {
                $where[] = 'r.pedido_id = ?';
                $params[] = $_GET['pedido_id'];
            }
            
            if (!empty($_GET['status'])) {
                $where[] = 'r.status = ?';
                $params[] = $_GET['status'];
            }
            
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $sql = "
                SELECT 
                    r.*,
                    p.protocolo as pedido_protocolo,
                    p.assunto as pedido_assunto,
                    u.nome as requerente_nome,
                    u.email as requerente_email
                FROM recursos r
                JOIN pedidos p ON r.pedido_id = p.id
                JOIN usuarios u ON r.requerente_id = u.id
                {$whereClause}
                ORDER BY r.data_cadastro DESC
                LIMIT {$limit} OFFSET {$offset}
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $recursos = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $recursos
            ]);
            break;
            
        case 'buscar':
            // Buscar recurso por protocolo
            $protocolo = $_GET['protocolo'] ?? '';
            if (empty($protocolo)) {
                throw new Exception("Protocolo é obrigatório");
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    r.*,
                    p.protocolo as pedido_protocolo,
                    p.assunto as pedido_assunto,
                    p.descricao as pedido_descricao,
                    p.resposta as pedido_resposta,
                    u.nome as requerente_nome,
                    u.email as requerente_email
                FROM recursos r
                JOIN pedidos p ON r.pedido_id = p.id
                JOIN usuarios u ON r.requerente_id = u.id
                WHERE r.protocolo = ?
            ");
            $stmt->execute([$protocolo]);
            $recurso = $stmt->fetch();
            
            if (!$recurso) {
                throw new Exception("Recurso não encontrado");
            }
            
            // Buscar tramitações do recurso
            $stmt = $pdo->prepare("
                SELECT 
                    t.*,
                    u.nome as usuario_nome
                FROM tramitacoes t
                LEFT JOIN usuarios u ON t.usuario_id = u.id
                WHERE t.recurso_id = ?
                ORDER BY t.data_tramitacao ASC
            ");
            $stmt->execute([$recurso['id']]);
            $tramitacoes = $stmt->fetchAll();
            
            $recurso['tramitacoes'] = $tramitacoes;
            $recurso['data_cadastro_formatada'] = date('d/m/Y H:i', strtotime($recurso['data_cadastro']));
            $recurso['data_limite_formatada'] = date('d/m/Y', strtotime($recurso['data_limite']));
            
            echo json_encode([
                'success' => true,
                'data' => $recurso
            ]);
            break;
            
        case 'responder':
            // Responder recurso (somente admin)
            if (empty($_POST['recurso_id']) || empty($_POST['decisao'])) {
                throw new Exception("Dados incompletos");
            }
            
            $recursoId = intval($_POST['recurso_id']);
            $decisao = $_POST['decisao'];
            $statusDecisao = $_POST['status_decisao']; // deferido ou indeferido
            $responsavelId = $_POST['responsavel_id'] ?? 1; // TODO: usar ID do usuário logado
            
            $pdo->beginTransaction();
            
            try {
                // Atualizar recurso
                $stmt = $pdo->prepare("
                    UPDATE recursos 
                    SET decisao = ?, status = ?, data_decisao = NOW(), responsavel_decisao_id = ?
                    WHERE id = ?
                ");
                $stmt->execute([$decisao, $statusDecisao, $responsavelId, $recursoId]);
                
                // Buscar dados do recurso para atualizar pedido
                $stmt = $pdo->prepare("SELECT pedido_id FROM recursos WHERE id = ?");
                $stmt->execute([$recursoId]);
                $recurso = $stmt->fetch();
                
                // Atualizar status do pedido baseado na decisão do recurso
                if ($statusDecisao === 'deferido') {
                    $stmt = $pdo->prepare("UPDATE pedidos SET status = 'em_analise' WHERE id = ?");
                } else {
                    $stmt = $pdo->prepare("UPDATE pedidos SET status = 'negado' WHERE id = ?");
                }
                $stmt->execute([$recurso['pedido_id']]);
                
                // Criar tramitação
                $stmt = $pdo->prepare("
                    INSERT INTO tramitacoes (recurso_id, status_anterior, status_novo, observacoes, usuario_id)
                    VALUES (?, 'aguardando', ?, 'Decisão do recurso', ?)
                ");
                $stmt->execute([$recursoId, $statusDecisao, $responsavelId]);
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Recurso respondido com sucesso!'
                ]);
                
            } catch (Exception $e) {
                $pdo->rollback();
                throw $e;
            }
            break;
            
        default:
            throw new Exception("Ação não reconhecida: {$action}");
    }
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>