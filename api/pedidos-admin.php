<?php
header('Content-Type: application/json');
require_once '../app/config/Database.php';

// Permitir CORS para desenvolvimento
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $action = $_REQUEST['action'] ?? '';
    
    switch ($action) {
        case 'listar':
            listarPedidos($pdo);
            break;
            
        case 'responder':
            responderPedido($pdo);
            break;
            
        case 'alterar_status':
            alterarStatus($pdo);
            break;
            
        case 'atribuir_orgao':
            atribuirOrgao($pdo);
            break;
            
        case 'prorrogar_prazo':
            prorrogarPrazo($pdo);
            break;
            
        case 'adicionar_tramitacao':
            adicionarTramitacao($pdo);
            break;
            
        default:
            throw new Exception('Ação não especificada ou inválida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Listar pedidos com filtros
 */
function listarPedidos($pdo) {
    $status = $_GET['status'] ?? '';
    $prazo = $_GET['prazo'] ?? '';
    $orgao_id = $_GET['orgao_id'] ?? '';
    $protocolo = $_GET['protocolo'] ?? '';
    $page = (int)($_GET['page'] ?? 1);
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    // Construir query base
    $sql = "
        SELECT 
            p.*,
            u.nome as requerente_nome,
            u.email as requerente_email,
            u.cpf_cnpj as requerente_cpf_cnpj,
            o.nome as orgao_nome,
            o.sigla as orgao_sigla,
            DATEDIFF(p.data_limite, CURDATE()) as dias_restantes,
            (SELECT COUNT(*) FROM recursos WHERE pedido_id = p.id) as total_recursos
        FROM pedidos p
        LEFT JOIN usuarios u ON p.requerente_id = u.id
        LEFT JOIN orgaos_setores o ON p.orgao_id = o.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Aplicar filtros
    if ($status) {
        $sql .= " AND p.status = :status";
        $params[':status'] = $status;
    }
    
    if ($orgao_id) {
        $sql .= " AND p.orgao_id = :orgao_id";
        $params[':orgao_id'] = $orgao_id;
    }
    
    if ($protocolo) {
        $sql .= " AND p.protocolo LIKE :protocolo";
        $params[':protocolo'] = "%{$protocolo}%";
    }
    
    // Filtro de prazo
    if ($prazo === 'vencido') {
        $sql .= " AND DATE(p.data_limite) < CURDATE() AND p.status NOT IN ('respondido', 'negado', 'cancelado')";
    } elseif ($prazo === 'proximo') {
        $sql .= " AND DATEDIFF(p.data_limite, CURDATE()) BETWEEN 0 AND 5 AND p.status NOT IN ('respondido', 'negado', 'cancelado')";
    } elseif ($prazo === 'normal') {
        $sql .= " AND DATEDIFF(p.data_limite, CURDATE()) > 5";
    }
    
    // Ordenação e paginação
    $sql .= " ORDER BY p.data_cadastro DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind dos parâmetros
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $pedidos = $stmt->fetchAll();
    
    // Contar total
    $sqlCount = "SELECT COUNT(*) FROM pedidos p WHERE 1=1";
    if ($status) $sqlCount .= " AND status = :status";
    if ($orgao_id) $sqlCount .= " AND orgao_id = :orgao_id";
    if ($protocolo) $sqlCount .= " AND protocolo LIKE :protocolo";
    
    $stmtCount = $pdo->prepare($sqlCount);
    foreach ($params as $key => $value) {
        if ($key !== ':limit' && $key !== ':offset') {
            $stmtCount->bindValue($key, $value);
        }
    }
    $stmtCount->execute();
    $total = $stmtCount->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'data' => $pedidos,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

/**
 * Responder pedido
 */
function responderPedido($pdo) {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $tipo_resposta = $_POST['tipo_resposta'] ?? null;
    $resposta = $_POST['resposta'] ?? null;
    $motivo_negativa = $_POST['motivo_negativa'] ?? null;
    
    // Validações
    if (!$pedido_id || !$tipo_resposta || !$resposta) {
        throw new Exception('Dados incompletos para resposta');
    }
    
    // Determinar novo status
    $novo_status = match($tipo_resposta) {
        'deferido' => 'respondido',
        'parcial' => 'parcialmente_atendido',
        'indeferido' => 'negado',
        default => throw new Exception('Tipo de resposta inválido')
    };
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    try {
        // Buscar pedido
        $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = :id");
        $stmt->execute([':id' => $pedido_id]);
        $pedido = $stmt->fetch();
        
        if (!$pedido) {
            throw new Exception('Pedido não encontrado');
        }
        
        // Atualizar pedido
        $stmt = $pdo->prepare("
            UPDATE pedidos 
            SET 
                status = :status,
                resposta = :resposta,
                data_resposta = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':status' => $novo_status,
            ':resposta' => $resposta,
            ':id' => $pedido_id
        ]);
        
        // Registrar tramitação
        $descricao_tramitacao = match($tipo_resposta) {
            'deferido' => 'Pedido DEFERIDO - Informação fornecida',
            'parcial' => 'Pedido PARCIALMENTE ATENDIDO',
            'indeferido' => 'Pedido INDEFERIDO - ' . ($motivo_negativa ?: 'Negativa de acesso'),
            default => 'Resposta registrada'
        };
        
        $stmt = $pdo->prepare("
            INSERT INTO tramitacoes (pedido_id, descricao, usuario_id, data_tramitacao)
            VALUES (:pedido_id, :descricao, 1, NOW())
        ");
        
        $stmt->execute([
            ':pedido_id' => $pedido_id,
            ':descricao' => $descricao_tramitacao
        ]);
        
        // Registrar log
        $stmt = $pdo->prepare("
            INSERT INTO logs_sistema (usuario_id, acao, detalhes, data_log)
            VALUES (1, 'responder_pedido', :detalhes, NOW())
        ");
        
        $detalhes = json_encode([
            'pedido_id' => $pedido_id,
            'protocolo' => $pedido['protocolo'],
            'tipo_resposta' => $tipo_resposta,
            'novo_status' => $novo_status
        ]);
        
        $stmt->execute([':detalhes' => $detalhes]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Resposta registrada com sucesso',
            'data' => [
                'pedido_id' => $pedido_id,
                'protocolo' => $pedido['protocolo'],
                'novo_status' => $novo_status
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Alterar status do pedido
 */
function alterarStatus($pdo) {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $observacao = $_POST['observacao'] ?? '';
    
    if (!$pedido_id || !$status) {
        throw new Exception('Dados incompletos');
    }
    
    // Validar status
    $status_validos = ['aguardando', 'em_analise', 'respondido', 'negado', 'parcialmente_atendido', 'cancelado'];
    if (!in_array($status, $status_validos)) {
        throw new Exception('Status inválido');
    }
    
    $pdo->beginTransaction();
    
    try {
        // Buscar pedido
        $stmt = $pdo->prepare("SELECT protocolo FROM pedidos WHERE id = :id");
        $stmt->execute([':id' => $pedido_id]);
        $pedido = $stmt->fetch();
        
        if (!$pedido) {
            throw new Exception('Pedido não encontrado');
        }
        
        // Atualizar status
        $stmt = $pdo->prepare("
            UPDATE pedidos 
            SET status = :status
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':status' => $status,
            ':id' => $pedido_id
        ]);
        
        // Registrar tramitação
        $descricao = "Status alterado para: " . strtoupper(str_replace('_', ' ', $status));
        if ($observacao) {
            $descricao .= " - " . $observacao;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO tramitacoes (pedido_id, descricao, usuario_id, data_tramitacao)
            VALUES (:pedido_id, :descricao, 1, NOW())
        ");
        
        $stmt->execute([
            ':pedido_id' => $pedido_id,
            ':descricao' => $descricao
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Status alterado com sucesso',
            'data' => [
                'pedido_id' => $pedido_id,
                'protocolo' => $pedido['protocolo'],
                'novo_status' => $status
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Atribuir órgão/setor ao pedido
 */
function atribuirOrgao($pdo) {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $orgao_id = $_POST['orgao_id'] ?? null;
    
    if (!$pedido_id || !$orgao_id) {
        throw new Exception('Dados incompletos');
    }
    
    $pdo->beginTransaction();
    
    try {
        // Verificar se órgão existe
        $stmt = $pdo->prepare("SELECT nome FROM orgaos_setores WHERE id = :id");
        $stmt->execute([':id' => $orgao_id]);
        $orgao = $stmt->fetch();
        
        if (!$orgao) {
            throw new Exception('Órgão não encontrado');
        }
        
        // Atualizar pedido
        $stmt = $pdo->prepare("
            UPDATE pedidos 
            SET orgao_id = :orgao_id,
                status = 'em_analise'
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':orgao_id' => $orgao_id,
            ':id' => $pedido_id
        ]);
        
        // Registrar tramitação
        $stmt = $pdo->prepare("
            INSERT INTO tramitacoes (pedido_id, descricao, usuario_id, data_tramitacao)
            VALUES (:pedido_id, :descricao, 1, NOW())
        ");
        
        $stmt->execute([
            ':pedido_id' => $pedido_id,
            ':descricao' => "Pedido atribuído ao órgão: {$orgao['nome']}"
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Órgão atribuído com sucesso'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Prorrogar prazo do pedido
 */
function prorrogarPrazo($pdo) {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $dias = (int)($_POST['dias'] ?? 0);
    $justificativa = $_POST['justificativa'] ?? '';
    
    if (!$pedido_id || $dias <= 0 || !$justificativa) {
        throw new Exception('Dados incompletos para prorrogação');
    }
    
    // Limite de 10 dias de prorrogação conforme LAI
    if ($dias > 10) {
        throw new Exception('Prorrogação não pode exceder 10 dias conforme Lei 12.527/2011');
    }
    
    $pdo->beginTransaction();
    
    try {
        // Buscar pedido
        $stmt = $pdo->prepare("SELECT protocolo, data_limite FROM pedidos WHERE id = :id");
        $stmt->execute([':id' => $pedido_id]);
        $pedido = $stmt->fetch();
        
        if (!$pedido) {
            throw new Exception('Pedido não encontrado');
        }
        
        // Calcular nova data limite
        $data_limite_atual = new DateTime($pedido['data_limite']);
        $data_limite_nova = clone $data_limite_atual;
        $data_limite_nova->modify("+{$dias} days");
        
        // Atualizar pedido
        $stmt = $pdo->prepare("
            UPDATE pedidos 
            SET data_limite = :nova_data
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':nova_data' => $data_limite_nova->format('Y-m-d'),
            ':id' => $pedido_id
        ]);
        
        // Registrar tramitação
        $stmt = $pdo->prepare("
            INSERT INTO tramitacoes (pedido_id, descricao, usuario_id, data_tramitacao)
            VALUES (:pedido_id, :descricao, 1, NOW())
        ");
        
        $descricao = "Prazo prorrogado por {$dias} dias. " .
                     "Data limite anterior: " . $data_limite_atual->format('d/m/Y') . ". " .
                     "Nova data limite: " . $data_limite_nova->format('d/m/Y') . ". " .
                     "Justificativa: {$justificativa}";
        
        $stmt->execute([
            ':pedido_id' => $pedido_id,
            ':descricao' => $descricao
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Prazo prorrogado com sucesso',
            'data' => [
                'protocolo' => $pedido['protocolo'],
                'data_limite_anterior' => $data_limite_atual->format('d/m/Y'),
                'data_limite_nova' => $data_limite_nova->format('d/m/Y'),
                'dias_prorrogados' => $dias
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Adicionar tramitação manual
 */
function adicionarTramitacao($pdo) {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $descricao = $_POST['descricao'] ?? null;
    
    if (!$pedido_id || !$descricao) {
        throw new Exception('Dados incompletos');
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO tramitacoes (pedido_id, descricao, usuario_id, data_tramitacao)
            VALUES (:pedido_id, :descricao, 1, NOW())
        ");
        
        $stmt->execute([
            ':pedido_id' => $pedido_id,
            ':descricao' => $descricao
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Tramitação registrada com sucesso'
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}
?>