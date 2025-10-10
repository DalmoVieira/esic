<?php

require_once 'Model.php';

/**
 * Sistema E-SIC - Model Recurso
 * 
 * Gerencia recursos administrativos contra respostas de pedidos
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class Recurso extends Model {
    
    protected $table = 'recursos';
    protected $fillable = [
        'pedido_id', 'protocolo_recurso', 'tipo', 'justificativa',
        'status', 'prazo_resposta', 'resposta', 'resposta_usuario_id',
        'observacoes', 'arquivo_anexo', 'arquivo_resposta'
    ];
    
    /**
     * Criar novo recurso
     */
    public function createRecurso($data) {
        // Validar dados obrigatórios
        $this->validate($data, [
            'pedido_id' => 'required',
            'justificativa' => 'required|min:50'
        ]);
        
        // Gerar protocolo do recurso
        $data['protocolo_recurso'] = $this->generateProtocol($data['pedido_id']);
        
        // Definir valores padrão
        $data['tipo'] = $data['tipo'] ?? 'primeira_instancia';
        $data['status'] = 'pendente';
        $data['prazo_resposta'] = $this->calculateResponseDeadline($data['tipo']);
        
        return $this->create($data);
    }
    
    /**
     * Gerar protocolo do recurso
     */
    private function generateProtocol($pedidoId) {
        // Buscar protocolo do pedido original
        $pedido = $this->db->selectOne("SELECT protocolo FROM pedidos WHERE id = ?", [$pedidoId]);
        
        if (!$pedido) {
            throw new Exception("Pedido não encontrado");
        }
        
        // Contar recursos existentes para este pedido
        $count = $this->count('pedido_id', $pedidoId) + 1;
        
        return $pedido['protocolo'] . '-REC-' . str_pad($count, 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Calcular prazo de resposta do recurso
     */
    private function calculateResponseDeadline($tipo) {
        $days = 10; // Primeira instância: 10 dias
        
        if ($tipo === 'segunda_instancia') {
            $days = 15; // Segunda instância: 15 dias
        } elseif ($tipo === 'cgu') {
            $days = 20; // CGU: 20 dias
        }
        
        return $this->addBusinessDays(new DateTime(), $days)->format('Y-m-d');
    }
    
    /**
     * Adicionar dias úteis à data
     */
    private function addBusinessDays(DateTime $date, $businessDays) {
        $addedDays = 0;
        
        while ($addedDays < $businessDays) {
            $date->add(new DateInterval('P1D'));
            
            // Pular finais de semana (sábado = 6, domingo = 7)
            if ($date->format('N') < 6) {
                $addedDays++;
            }
        }
        
        return $date;
    }
    
    /**
     * Buscar recurso por protocolo
     */
    public function findByProtocol($protocolo) {
        return $this->first('protocolo_recurso', $protocolo);
    }
    
    /**
     * Buscar recursos de um pedido
     */
    public function getRecursosPedido($pedidoId) {
        return $this->where('pedido_id', $pedidoId);
    }
    
    /**
     * Responder recurso
     */
    public function responderRecurso($id, $resposta, $usuarioId, $arquivoResposta = null) {
        $data = [
            'resposta' => $resposta,
            'resposta_usuario_id' => $usuarioId,
            'data_resposta' => date('Y-m-d H:i:s'),
            'status' => 'deferido' // Por padrão, será deferido. Pode ser alterado depois
        ];
        
        if ($arquivoResposta) {
            $data['arquivo_resposta'] = $arquivoResposta;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Alterar status do recurso
     */
    public function alterarStatus($id, $novoStatus, $observacoes = null) {
        $data = ['status' => $novoStatus];
        
        if ($observacoes) {
            $data['observacoes'] = $observacoes;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Listar recursos com paginação e filtros
     */
    public function listarRecursos($page = 1, $perPage = 20, $filters = []) {
        $where = "1=1";
        $params = [];
        
        // Filtro por status
        if (!empty($filters['status'])) {
            $where .= " AND r.status = ?";
            $params[] = $filters['status'];
        }
        
        // Filtro por tipo
        if (!empty($filters['tipo'])) {
            $where .= " AND r.tipo = ?";
            $params[] = $filters['tipo'];
        }
        
        // Filtro por período
        if (!empty($filters['data_inicio'])) {
            $where .= " AND DATE(r.created_at) >= ?";
            $params[] = $filters['data_inicio'];
        }
        
        if (!empty($filters['data_fim'])) {
            $where .= " AND DATE(r.created_at) <= ?";
            $params[] = $filters['data_fim'];
        }
        
        // Filtro por protocolo
        if (!empty($filters['busca'])) {
            $where .= " AND (r.protocolo_recurso LIKE ? OR p.protocolo LIKE ?)";
            $searchTerm = '%' . $filters['busca'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Query com JOIN para pegar dados do pedido
        $dataQuery = "
            SELECT 
                r.*,
                p.protocolo as pedido_protocolo,
                p.nome_solicitante,
                p.assunto as pedido_assunto,
                u.nome as resposta_usuario_nome
            FROM recursos r
            INNER JOIN pedidos p ON r.pedido_id = p.id
            LEFT JOIN usuarios u ON r.resposta_usuario_id = u.id
            WHERE {$where}
            ORDER BY r.created_at DESC
        ";
        
        // Para paginação, precisamos contar também
        $countQuery = "
            SELECT COUNT(*) as count 
            FROM recursos r
            INNER JOIN pedidos p ON r.pedido_id = p.id
            WHERE {$where}
        ";
        
        $offset = ($page - 1) * $perPage;
        $total = $this->db->selectOne($countQuery, $params)['count'];
        
        $dataQuery .= " LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->select($dataQuery, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    /**
     * Obter recursos pendentes
     */
    public function getRecursosPendentes() {
        return $this->db->select("
            SELECT 
                r.*,
                p.protocolo as pedido_protocolo,
                p.nome_solicitante,
                p.assunto as pedido_assunto
            FROM recursos r
            INNER JOIN pedidos p ON r.pedido_id = p.id
            WHERE r.status = 'pendente'
            ORDER BY r.created_at ASC
        ");
    }
    
    /**
     * Obter recursos com prazo vencido
     */
    public function getRecursosAtrasados() {
        return $this->db->select("
            SELECT 
                r.*,
                p.protocolo as pedido_protocolo,
                p.nome_solicitante,
                p.assunto as pedido_assunto,
                DATEDIFF(CURRENT_DATE, r.prazo_resposta) as dias_atraso
            FROM recursos r
            INNER JOIN pedidos p ON r.pedido_id = p.id
            WHERE r.status IN ('pendente', 'em_andamento') 
            AND r.prazo_resposta < CURRENT_DATE 
            ORDER BY dias_atraso DESC
        ");
    }
    
    /**
     * Obter recursos próximos ao vencimento
     */
    public function getRecursosProximosVencimento($dias = 3) {
        return $this->db->select("
            SELECT 
                r.*,
                p.protocolo as pedido_protocolo,
                p.nome_solicitante,
                p.assunto as pedido_assunto,
                DATEDIFF(r.prazo_resposta, CURRENT_DATE) as dias_restantes
            FROM recursos r
            INNER JOIN pedidos p ON r.pedido_id = p.id
            WHERE r.status IN ('pendente', 'em_andamento') 
            AND r.prazo_resposta BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL ? DAY) 
            ORDER BY dias_restantes ASC
        ", [$dias]);
    }
    
    /**
     * Estatísticas dos recursos
     */
    public function getEstatisticas($periodo = null) {
        $whereClause = "";
        $params = [];
        
        if ($periodo) {
            switch ($periodo) {
                case 'hoje':
                    $whereClause = "WHERE DATE(r.created_at) = CURRENT_DATE";
                    break;
                case 'semana':
                    $whereClause = "WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                    break;
                case 'mes':
                    $whereClause = "WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                    break;
                case 'ano':
                    $whereClause = "WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                    break;
            }
        }
        
        $stats = [];
        
        // Total de recursos
        $stats['total'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM recursos r {$whereClause}",
            $params
        )['count'];
        
        // Por status
        $stats['por_status'] = $this->db->select(
            "SELECT status, COUNT(*) as total FROM recursos r {$whereClause} GROUP BY status",
            $params
        );
        
        // Por tipo
        $stats['por_tipo'] = $this->db->select(
            "SELECT tipo, COUNT(*) as total FROM recursos r {$whereClause} GROUP BY tipo",
            $params
        );
        
        // Tempo médio de resposta
        $tempoQuery = "SELECT AVG(DATEDIFF(data_resposta, r.created_at)) as media FROM recursos r WHERE r.data_resposta IS NOT NULL";
        if ($whereClause) {
            $tempoQuery .= " AND " . str_replace('WHERE ', '', $whereClause);
        }
        
        $stats['tempo_medio_resposta'] = $this->db->selectOne($tempoQuery, $params)['media'];
        
        // Taxa de deferimento
        $totalQuery = "SELECT COUNT(*) as count FROM recursos r WHERE r.status IN ('deferido', 'indeferido')";
        if ($whereClause) {
            $totalQuery .= " AND " . str_replace('WHERE ', '', $whereClause);
        }
        $totalComResposta = $this->db->selectOne($totalQuery, $params)['count'];
        
        $deferidosQuery = "SELECT COUNT(*) as count FROM recursos r WHERE r.status = 'deferido'";
        if ($whereClause) {
            $deferidosQuery .= " AND " . str_replace('WHERE ', '', $whereClause);
        }
        $deferidos = $this->db->selectOne($deferidosQuery, $params)['count'];
        
        $stats['taxa_deferimento'] = $totalComResposta > 0 ? round(($deferidos / $totalComResposta) * 100, 2) : 0;
        
        return $stats;
    }
    
    /**
     * Obter dados para dashboard de recursos
     */
    public function getDashboardData() {
        $data = [];
        
        // Totais gerais
        $data['totais'] = [
            'total' => $this->count(),
            'pendentes' => $this->count('status', 'pendente'),
            'em_andamento' => $this->count('status', 'em_andamento'),
            'deferidos' => $this->count('status', 'deferido'),
            'indeferidos' => $this->count('status', 'indeferido')
        ];
        
        // Por tipo
        $data['por_tipo'] = $this->db->select(
            "SELECT tipo, COUNT(*) as total FROM recursos GROUP BY tipo"
        );
        
        // Recursos hoje
        $data['hoje'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM recursos WHERE DATE(created_at) = CURRENT_DATE"
        )['count'];
        
        // Recursos esta semana
        $data['semana'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM recursos WHERE YEARWEEK(created_at) = YEARWEEK(NOW())"
        )['count'];
        
        // Recursos atrasados
        $data['atrasados'] = count($this->getRecursosAtrasados());
        
        // Próximos ao vencimento
        $data['proximos_vencimento'] = count($this->getRecursosProximosVencimento());
        
        // Últimos recursos
        $data['ultimos_recursos'] = $this->db->select("
            SELECT 
                r.protocolo_recurso,
                r.tipo,
                r.status,
                r.created_at,
                p.protocolo as pedido_protocolo,
                p.nome_solicitante
            FROM recursos r
            INNER JOIN pedidos p ON r.pedido_id = p.id
            ORDER BY r.created_at DESC 
            LIMIT 10
        ");
        
        return $data;
    }
    
    /**
     * Verificar se pedido pode ter recurso
     */
    public function podeInterporRecurso($pedidoId) {
        // Buscar pedido
        $pedido = $this->db->selectOne("SELECT * FROM pedidos WHERE id = ?", [$pedidoId]);
        
        if (!$pedido) {
            return ['pode' => false, 'motivo' => 'Pedido não encontrado'];
        }
        
        // Verificar se pedido foi respondido ou negado
        if (!in_array($pedido['status'], ['respondido', 'negado'])) {
            return ['pode' => false, 'motivo' => 'Pedido ainda não foi respondido'];
        }
        
        // Verificar prazo para recurso (10 dias após resposta)
        if ($pedido['data_resposta']) {
            $dataResposta = new DateTime($pedido['data_resposta']);
            $prazoLimite = $this->addBusinessDays($dataResposta, 10);
            
            if (new DateTime() > $prazoLimite) {
                return ['pode' => false, 'motivo' => 'Prazo para recurso expirado'];
            }
        }
        
        // Verificar quantos recursos já foram interpostos
        $recursosCount = $this->count('pedido_id', $pedidoId);
        
        if ($recursosCount >= 2) {
            return ['pode' => false, 'motivo' => 'Limite de recursos atingido'];
        }
        
        return ['pode' => true, 'motivo' => ''];
    }
    
    /**
     * Buscar recursos para relatório
     */
    public function getRelatorio($filtros = []) {
        $query = "
            SELECT 
                r.*,
                p.protocolo as pedido_protocolo,
                p.nome_solicitante,
                p.email_solicitante,
                p.assunto as pedido_assunto,
                u.nome as resposta_usuario_nome
            FROM recursos r
            INNER JOIN pedidos p ON r.pedido_id = p.id
            LEFT JOIN usuarios u ON r.resposta_usuario_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Filtros
        if (!empty($filtros['data_inicio'])) {
            $query .= " AND DATE(r.created_at) >= ?";
            $params[] = $filtros['data_inicio'];
        }
        
        if (!empty($filtros['data_fim'])) {
            $query .= " AND DATE(r.created_at) <= ?";
            $params[] = $filtros['data_fim'];
        }
        
        if (!empty($filtros['status'])) {
            if (is_array($filtros['status'])) {
                $placeholders = str_repeat('?,', count($filtros['status']) - 1) . '?';
                $query .= " AND r.status IN ({$placeholders})";
                $params = array_merge($params, $filtros['status']);
            } else {
                $query .= " AND r.status = ?";
                $params[] = $filtros['status'];
            }
        }
        
        if (!empty($filtros['tipo'])) {
            $query .= " AND r.tipo = ?";
            $params[] = $filtros['tipo'];
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        return $this->db->select($query, $params);
    }

    /**
     * Verificar se pode criar recurso para um pedido
     */
    public function canCreateRecurso($pedidoId, $tipo) {
        // Verificar se o pedido existe e foi respondido
        $pedido = $this->db->selectOne(
            "SELECT * FROM pedidos WHERE id = ?",
            [$pedidoId]
        );
        
        if (!$pedido) {
            return false;
        }
        
        // Só pode criar recurso se o pedido foi indeferido ou parcialmente deferido
        if (!in_array($pedido['status'], ['indeferido', 'parcialmente_deferido'])) {
            return false;
        }
        
        // Verificar se ainda está no prazo
        if ($pedido['data_resposta']) {
            $dataResposta = new DateTime($pedido['data_resposta']);
            $prazoLimite = clone $dataResposta;
            $prazoLimite->add(new DateInterval('P10D')); // 10 dias para recurso
            
            if (new DateTime() > $prazoLimite) {
                return false;
            }
        }
        
        // Verificar se já não existe recurso do mesmo tipo
        $existingRecurso = $this->db->selectOne(
            "SELECT id FROM recursos WHERE pedido_id = ? AND tipo = ?",
            [$pedidoId, $tipo]
        );
        
        return !$existingRecurso;
    }

    /**
     * Calcular prazo de resposta para recurso
     */
    public function calculateDeadline($tipo) {
        $days = 10; // Prazo padrão de 10 dias úteis para recursos
        
        switch ($tipo) {
            case 'primeira_instancia':
                $days = 10;
                break;
            case 'segunda_instancia':
                $days = 10;
                break;
            case 'terceira_instancia':
                $days = 15;
                break;
        }
        
        $date = new DateTime();
        $addedDays = 0;
        
        while ($addedDays < $days) {
            $date->add(new DateInterval('P1D'));
            
            // Pular fins de semana (sábado = 6, domingo = 0)
            if ($date->format('w') != 0 && $date->format('w') != 6) {
                $addedDays++;
            }
        }
        
        return $date->format('Y-m-d H:i:s');
    }
}