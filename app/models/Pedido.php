<?php

require_once 'Model.php';

/**
 * Sistema E-SIC - Model Pedido
 * 
 * Gerencia pedidos de acesso à informação
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class Pedido extends Model {
    
    protected $table = 'pedidos';
    protected $fillable = [
        'protocolo', 'nome_solicitante', 'email_solicitante', 'telefone_solicitante',
        'cpf_solicitante', 'endereco_solicitante', 'assunto', 'descricao',
        'forma_recebimento', 'endereco_resposta', 'categoria', 'subcategoria',
        'unidade_responsavel', 'status', 'prioridade', 'prazo_resposta',
        'resposta', 'resposta_usuario_id', 'observacoes', 'arquivo_anexo',
        'arquivo_resposta', 'ip_solicitante', 'user_agent', 'origem'
    ];
    
    /**
     * Criar novo pedido com protocolo automático
     */
    public function createPedido($data) {
        // Validar dados obrigatórios
        $this->validate($data, [
            'nome_solicitante' => 'required|min:3|max:100',
            'email_solicitante' => 'required|email',
            'assunto' => 'required|min:10|max:200',
            'descricao' => 'required|min:20'
        ]);
        
        // Gerar protocolo automático
        $data['protocolo'] = $this->generateProtocol();
        
        // Definir valores padrão
        $data['status'] = 'pendente';
        $data['prioridade'] = $data['prioridade'] ?? 'normal';
        $data['prazo_resposta'] = $this->calculateResponseDeadline();
        $data['origem'] = $data['origem'] ?? 'site';
        $data['visualizado'] = false;
        
        // Dados da requisição
        $data['ip_solicitante'] = $this->getClientIP();
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        return $this->create($data);
    }
    
    /**
     * Gerar protocolo automático
     */
    public function generateProtocol() {
        $date = date('Ymd');
        
        // Buscar último número sequencial do dia
        $lastNumber = $this->db->selectOne(
            "SELECT MAX(CAST(SUBSTRING(protocolo, -4) AS UNSIGNED)) as last_number 
             FROM pedidos 
             WHERE protocolo LIKE ?",
            ["ESIC-{$date}-%"]
        );
        
        $nextNumber = ($lastNumber['last_number'] ?? 0) + 1;
        
        return sprintf('ESIC-%s-%04d', $date, $nextNumber);
    }
    
    /**
     * Calcular prazo de resposta (20 dias úteis)
     */
    public function calculateResponseDeadline($businessDays = 20) {
        $date = new DateTime();
        $addedDays = 0;
        
        while ($addedDays < $businessDays) {
            $date->add(new DateInterval('P1D'));
            
            // Pular finais de semana
            if ($date->format('N') < 6) {
                $addedDays++;
            }
        }
        
        return $date->format('Y-m-d');
    }

    /**
     * Alias para calculateResponseDeadline
     */
    public function calculateDeadline($businessDays = 20) {
        return $this->calculateResponseDeadline($businessDays);
    }
    
    /**
     * Buscar pedido por protocolo
     */
    public function findByProtocol($protocolo) {
        return $this->first('protocolo', $protocolo);
    }
    
    /**
     * Buscar pedidos por email do solicitante
     */
    public function findByEmail($email) {
        return $this->where('email_solicitante', $email);
    }
    
    /**
     * Responder pedido
     */
    public function responderPedido($id, $resposta, $usuarioId, $arquivoResposta = null) {
        $data = [
            'resposta' => $resposta,
            'resposta_usuario_id' => $usuarioId,
            'data_resposta' => date('Y-m-d H:i:s'),
            'status' => 'respondido'
        ];
        
        if ($arquivoResposta) {
            $data['arquivo_resposta'] = $arquivoResposta;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Alterar status do pedido
     */
    public function alterarStatus($id, $novoStatus, $observacoes = null) {
        $data = ['status' => $novoStatus];
        
        if ($observacoes) {
            $data['observacoes'] = $observacoes;
        }
        
        // Se for negado, definir data de resposta
        if ($novoStatus === 'negado') {
            $data['data_resposta'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Marcar como visualizado
     */
    public function marcarVisualizado($id) {
        return $this->update($id, ['visualizado' => true]);
    }
    
    /**
     * Listar pedidos com paginação e filtros
     */
    public function listarPedidos($page = 1, $perPage = 20, $filters = []) {
        $where = "1=1";
        $params = [];
        
        // Filtro por status
        if (!empty($filters['status'])) {
            $where .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        // Filtro por unidade
        if (!empty($filters['unidade'])) {
            $where .= " AND unidade_responsavel = ?";
            $params[] = $filters['unidade'];
        }
        
        // Filtro por período
        if (!empty($filters['data_inicio'])) {
            $where .= " AND DATE(created_at) >= ?";
            $params[] = $filters['data_inicio'];
        }
        
        if (!empty($filters['data_fim'])) {
            $where .= " AND DATE(created_at) <= ?";
            $params[] = $filters['data_fim'];
        }
        
        // Filtro por protocolo ou assunto
        if (!empty($filters['busca'])) {
            $where .= " AND (protocolo LIKE ? OR assunto LIKE ? OR nome_solicitante LIKE ?)";
            $searchTerm = '%' . $filters['busca'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $where .= " ORDER BY created_at DESC";
        
        return $this->paginate($page, $perPage, $where, $params);
    }
    
    /**
     * Obter pedidos pendentes
     */
    public function getPedidosPendentes() {
        return $this->where('status', 'pendente');
    }
    
    /**
     * Obter pedidos com prazo vencido
     */
    public function getPedidosAtrasados() {
        return $this->db->select(
            "SELECT *, DATEDIFF(CURRENT_DATE, prazo_resposta) as dias_atraso 
             FROM pedidos 
             WHERE status IN ('pendente', 'em_andamento') 
             AND prazo_resposta < CURRENT_DATE 
             ORDER BY dias_atraso DESC"
        );
    }
    
    /**
     * Obter pedidos próximos ao vencimento (3 dias)
     */
    public function getPedidosProximosVencimento($dias = 3) {
        return $this->db->select(
            "SELECT *, DATEDIFF(prazo_resposta, CURRENT_DATE) as dias_restantes 
             FROM pedidos 
             WHERE status IN ('pendente', 'em_andamento') 
             AND prazo_resposta BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL ? DAY) 
             ORDER BY dias_restantes ASC",
            [$dias]
        );
    }

    /**
     * Alias para getPedidosProximosVencimento
     */
    public function getPedidosPrazoVencendo($dias = 3) {
        return $this->getPedidosProximosVencimento($dias);
    }

    /**
     * Obter pedidos recentes
     */
    public function getRecent($limit = 10) {
        return $this->db->select(
            "SELECT * FROM pedidos 
             ORDER BY created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Campos de busca por texto
     */
    protected function getSearchFields() {
        return ['protocolo', 'assunto', 'nome_solicitante', 'email'];
    }
    
    /**
     * Estatísticas dos pedidos
     */
    public function getEstatisticas($periodo = null) {
        $whereClause = "";
        $params = [];
        
        if ($periodo) {
            switch ($periodo) {
                case 'hoje':
                    $whereClause = "WHERE DATE(created_at) = CURRENT_DATE";
                    break;
                case 'semana':
                    $whereClause = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                    break;
                case 'mes':
                    $whereClause = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                    break;
                case 'ano':
                    $whereClause = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                    break;
            }
        }
        
        $stats = [];
        
        // Total de pedidos
        $stats['total'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM pedidos {$whereClause}",
            $params
        )['count'];
        
        // Por status
        $stats['por_status'] = $this->db->select(
            "SELECT status, COUNT(*) as total FROM pedidos {$whereClause} GROUP BY status",
            $params
        );
        
        // Tempo médio de resposta
        $tempoQuery = "SELECT AVG(DATEDIFF(data_resposta, created_at)) as media FROM pedidos WHERE data_resposta IS NOT NULL";
        if ($whereClause) {
            $tempoQuery .= " AND " . str_replace('WHERE ', '', $whereClause);
        }
        
        $stats['tempo_medio_resposta'] = $this->db->selectOne($tempoQuery, $params)['media'];
        
        // Por categoria
        $categoriaQuery = "SELECT categoria, COUNT(*) as total FROM pedidos " . $whereClause . " GROUP BY categoria ORDER BY total DESC LIMIT 10";
        $stats['por_categoria'] = $this->db->select($categoriaQuery, $params);
        
        // Por unidade
        $unidadeQuery = "SELECT unidade_responsavel, COUNT(*) as total FROM pedidos " . $whereClause . " GROUP BY unidade_responsavel ORDER BY total DESC";
        $stats['por_unidade'] = $this->db->select($unidadeQuery, $params);
        
        return $stats;
    }
    
    /**
     * Obter dados para dashboard
     */
    public function getDashboardData() {
        $data = [];
        
        // Totais gerais
        $data['totais'] = [
            'total' => $this->count(),
            'pendentes' => $this->count('status', 'pendente'),
            'em_andamento' => $this->count('status', 'em_andamento'),
            'respondidos' => $this->count('status', 'respondido'),
            'negados' => $this->count('status', 'negado')
        ];
        
        // Pedidos hoje
        $data['hoje'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM pedidos WHERE DATE(created_at) = CURRENT_DATE"
        )['count'];
        
        // Pedidos esta semana
        $data['semana'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM pedidos WHERE YEARWEEK(created_at) = YEARWEEK(NOW())"
        )['count'];
        
        // Pedidos este mês
        $data['mes'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM pedidos WHERE YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())"
        )['count'];
        
        // Pedidos atrasados
        $data['atrasados'] = count($this->getPedidosAtrasados());
        
        // Próximos ao vencimento
        $data['proximos_vencimento'] = count($this->getPedidosProximosVencimento());
        
        // Últimos pedidos
        $data['ultimos_pedidos'] = $this->db->select(
            "SELECT protocolo, nome_solicitante, assunto, status, created_at 
             FROM pedidos 
             ORDER BY created_at DESC 
             LIMIT 10"
        );
        
        return $data;
    }
    
    /**
     * Buscar pedidos para relatório
     */
    public function getRelatorio($filtros = []) {
        $query = "
            SELECT 
                p.*,
                u.nome as resposta_usuario_nome
            FROM pedidos p
            LEFT JOIN usuarios u ON p.resposta_usuario_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Filtros
        if (!empty($filtros['data_inicio'])) {
            $query .= " AND DATE(p.created_at) >= ?";
            $params[] = $filtros['data_inicio'];
        }
        
        if (!empty($filtros['data_fim'])) {
            $query .= " AND DATE(p.created_at) <= ?";
            $params[] = $filtros['data_fim'];
        }
        
        if (!empty($filtros['status'])) {
            if (is_array($filtros['status'])) {
                $placeholders = str_repeat('?,', count($filtros['status']) - 1) . '?';
                $query .= " AND p.status IN ({$placeholders})";
                $params = array_merge($params, $filtros['status']);
            } else {
                $query .= " AND p.status = ?";
                $params[] = $filtros['status'];
            }
        }
        
        if (!empty($filtros['unidade'])) {
            $query .= " AND p.unidade_responsavel = ?";
            $params[] = $filtros['unidade'];
        }
        
        if (!empty($filtros['categoria'])) {
            $query .= " AND p.categoria = ?";
            $params[] = $filtros['categoria'];
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obter todas as categorias utilizadas
     */
    public function getCategorias() {
        $result = $this->db->select(
            "SELECT DISTINCT categoria 
             FROM pedidos 
             WHERE categoria IS NOT NULL AND categoria != '' 
             ORDER BY categoria"
        );
        
        return array_column($result, 'categoria');
    }
    
    /**
     * Obter todas as unidades utilizadas
     */
    public function getUnidades() {
        $result = $this->db->select(
            "SELECT DISTINCT unidade_responsavel 
             FROM pedidos 
             WHERE unidade_responsavel IS NOT NULL AND unidade_responsavel != '' 
             ORDER BY unidade_responsavel"
        );
        
        return array_column($result, 'unidade_responsavel');
    }
    
    /**
     * Obter IP do cliente
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }
        
        return '0.0.0.0';
    }
}