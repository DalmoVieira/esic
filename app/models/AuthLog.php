<?php

require_once 'Model.php';

/**
 * Sistema E-SIC - Model AuthLog
 * 
 * Gerencia logs de autenticação e segurança do sistema
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class AuthLog extends Model {
    
    protected $table = 'auth_logs';
    protected $fillable = [
        'usuario_id', 'email', 'tipo_evento', 'ip_address', 
        'user_agent', 'sucesso', 'detalhes'
    ];
    
    /**
     * Registrar evento de autenticação
     */
    public function registrarEvento($usuarioId, $email, $tipoEvento, $sucesso = true, $detalhes = null) {
        return $this->create([
            'usuario_id' => $usuarioId,
            'email' => $email,
            'tipo_evento' => $tipoEvento,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'sucesso' => $sucesso ? 1 : 0,
            'detalhes' => $detalhes
        ]);
    }
    
    /**
     * Registrar login bem-sucedido
     */
    public function registrarLogin($usuarioId, $email) {
        return $this->registrarEvento($usuarioId, $email, 'login', true);
    }
    
    /**
     * Registrar logout
     */
    public function registrarLogout($usuarioId, $email) {
        return $this->registrarEvento($usuarioId, $email, 'logout', true);
    }
    
    /**
     * Registrar tentativa de login falhada
     */
    public function registrarTentativaFalha($usuarioId, $email, $motivo = null) {
        return $this->registrarEvento($usuarioId, $email, 'tentativa_falha', false, $motivo);
    }
    
    /**
     * Registrar reset de senha
     */
    public function registrarResetSenha($usuarioId, $email) {
        return $this->registrarEvento($usuarioId, $email, 'reset_senha', true);
    }
    
    /**
     * Registrar bloqueio de usuário
     */
    public function registrarBloqueio($usuarioId, $email, $motivo = null) {
        return $this->registrarEvento($usuarioId, $email, 'bloqueio', false, $motivo);
    }
    
    /**
     * Registrar desbloqueio de usuário
     */
    public function registrarDesbloqueio($usuarioId, $email) {
        return $this->registrarEvento($usuarioId, $email, 'desbloqueio', true);
    }
    
    /**
     * Obter logs de um usuário específico
     */
    public function getLogsByUser($usuarioId, $limit = 50) {
        return $this->db->select(
            "SELECT * FROM auth_logs WHERE usuario_id = ? ORDER BY created_at DESC LIMIT ?",
            [$usuarioId, $limit]
        );
    }
    
    /**
     * Obter logs por email
     */
    public function getLogsByEmail($email, $limit = 50) {
        return $this->db->select(
            "SELECT * FROM auth_logs WHERE email = ? ORDER BY created_at DESC LIMIT ?",
            [$email, $limit]
        );
    }
    
    /**
     * Obter tentativas de login falhadas por IP
     */
    public function getTentativasFalhasPorIP($ip, $periodo = 24) {
        return $this->db->select(
            "SELECT * FROM auth_logs 
             WHERE ip_address = ? 
             AND tipo_evento = 'tentativa_falha' 
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR) 
             ORDER BY created_at DESC",
            [$ip, $periodo]
        );
    }
    
    /**
     * Contar tentativas de login falhadas por IP em período
     */
    public function contarTentativasFalhasPorIP($ip, $periodo = 1) {
        return $this->db->selectOne(
            "SELECT COUNT(*) as count FROM auth_logs 
             WHERE ip_address = ? 
             AND tipo_evento = 'tentativa_falha' 
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)",
            [$ip, $periodo]
        )['count'];
    }
    
    /**
     * Verificar se IP está sendo usado para ataques de força bruta
     */
    public function verificarForcaBruta($ip, $tentativasMax = 10, $periodo = 1) {
        $tentativas = $this->contarTentativasFalhasPorIP($ip, $periodo);
        return $tentativas >= $tentativasMax;
    }
    
    /**
     * Obter estatísticas de autenticação
     */
    public function getEstatisticas($periodo = 'hoje') {
        $whereClause = $this->buildPeriodWhereClause($periodo);
        $params = [];
        
        $stats = [];
        
        // Total de eventos
        $stats['total_eventos'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM auth_logs WHERE {$whereClause}",
            $params
        )['count'];
        
        // Logins bem-sucedidos
        $stats['logins_sucesso'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM auth_logs 
             WHERE tipo_evento = 'login' AND sucesso = 1 AND {$whereClause}",
            $params
        )['count'];
        
        // Tentativas falhadas
        $stats['tentativas_falhas'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM auth_logs 
             WHERE tipo_evento = 'tentativa_falha' AND {$whereClause}",
            $params
        )['count'];
        
        // Usuários únicos que fizeram login
        $stats['usuarios_unicos'] = $this->db->selectOne(
            "SELECT COUNT(DISTINCT usuario_id) as count FROM auth_logs 
             WHERE tipo_evento = 'login' AND sucesso = 1 AND {$whereClause}",
            $params
        )['count'];
        
        // IPs únicos
        $stats['ips_unicos'] = $this->db->selectOne(
            "SELECT COUNT(DISTINCT ip_address) as count FROM auth_logs WHERE {$whereClause}",
            $params
        )['count'];
        
        // Por tipo de evento
        $stats['por_tipo'] = $this->db->select(
            "SELECT tipo_evento, COUNT(*) as total FROM auth_logs 
             WHERE {$whereClause} GROUP BY tipo_evento ORDER BY total DESC",
            $params
        );
        
        // Top IPs com mais tentativas
        $stats['top_ips'] = $this->db->select(
            "SELECT ip_address, COUNT(*) as total FROM auth_logs 
             WHERE {$whereClause} GROUP BY ip_address ORDER BY total DESC LIMIT 10",
            $params
        );
        
        return $stats;
    }
    
    /**
     * Obter logs com paginação e filtros
     */
    public function getLogs($page = 1, $perPage = 50, $filters = []) {
        $where = "1=1";
        $params = [];
        
        // Filtro por usuário
        if (!empty($filters['usuario_id'])) {
            $where .= " AND usuario_id = ?";
            $params[] = $filters['usuario_id'];
        }
        
        // Filtro por email
        if (!empty($filters['email'])) {
            $where .= " AND email LIKE ?";
            $params[] = '%' . $filters['email'] . '%';
        }
        
        // Filtro por tipo de evento
        if (!empty($filters['tipo_evento'])) {
            $where .= " AND tipo_evento = ?";
            $params[] = $filters['tipo_evento'];
        }
        
        // Filtro por sucesso
        if (isset($filters['sucesso'])) {
            $where .= " AND sucesso = ?";
            $params[] = $filters['sucesso'] ? 1 : 0;
        }
        
        // Filtro por IP
        if (!empty($filters['ip_address'])) {
            $where .= " AND ip_address = ?";
            $params[] = $filters['ip_address'];
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
        
        $where .= " ORDER BY created_at DESC";
        
        return $this->paginate($page, $perPage, $where, $params);
    }
    
    /**
     * Obter atividade recente de um usuário
     */
    public function getAtividadeRecente($usuarioId, $limit = 10) {
        return $this->db->select(
            "SELECT tipo_evento, ip_address, created_at, sucesso, detalhes
             FROM auth_logs 
             WHERE usuario_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?",
            [$usuarioId, $limit]
        );
    }
    
    /**
     * Detectar atividades suspeitas
     */
    public function detectarAtividadeSuspeita() {
        $suspeitas = [];
        
        // Múltiplos IPs para mesmo usuário em pouco tempo
        $multiplosIps = $this->db->select(
            "SELECT usuario_id, email, COUNT(DISTINCT ip_address) as ips_count 
             FROM auth_logs 
             WHERE tipo_evento = 'login' 
             AND sucesso = 1 
             AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) 
             GROUP BY usuario_id, email 
             HAVING ips_count > 3"
        );
        
        foreach ($multiplosIps as $item) {
            $suspeitas[] = [
                'tipo' => 'multiplos_ips',
                'descricao' => "Usuário {$item['email']} logou de {$item['ips_count']} IPs diferentes na última hora",
                'dados' => $item
            ];
        }
        
        // IPs com muitas tentativas falhadas
        $ipsSuspeitos = $this->db->select(
            "SELECT ip_address, COUNT(*) as tentativas 
             FROM auth_logs 
             WHERE tipo_evento = 'tentativa_falha' 
             AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) 
             GROUP BY ip_address 
             HAVING tentativas >= 10 
             ORDER BY tentativas DESC"
        );
        
        foreach ($ipsSuspeitos as $item) {
            $suspeitas[] = [
                'tipo' => 'forca_bruta',
                'descricao' => "IP {$item['ip_address']} teve {$item['tentativas']} tentativas falhadas na última hora",
                'dados' => $item
            ];
        }
        
        // Logins fora do horário comercial
        $foraHorario = $this->db->select(
            "SELECT usuario_id, email, ip_address, created_at 
             FROM auth_logs 
             WHERE tipo_evento = 'login' 
             AND sucesso = 1 
             AND (HOUR(created_at) < 6 OR HOUR(created_at) > 22) 
             AND DATE(created_at) = CURRENT_DATE"
        );
        
        foreach ($foraHorario as $item) {
            $suspeitas[] = [
                'tipo' => 'horario_incomum',
                'descricao' => "Login do usuário {$item['email']} fora do horário comercial",
                'dados' => $item
            ];
        }
        
        return $suspeitas;
    }
    
    /**
     * Limpar logs antigos
     */
    public function limparLogsAntigos($diasRetencao = 365) {
        $deleted = $this->db->execute(
            "DELETE FROM auth_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$diasRetencao]
        );
        
        return $deleted;
    }
    
    /**
     * Gerar relatório de segurança
     */
    public function gerarRelatorioSeguranca($dataInicio, $dataFim) {
        $relatorio = [];
        
        // Período do relatório
        $relatorio['periodo'] = [
            'inicio' => $dataInicio,
            'fim' => $dataFim
        ];
        
        // Estatísticas gerais
        $relatorio['estatisticas'] = $this->db->select(
            "SELECT 
                COUNT(*) as total_eventos,
                COUNT(CASE WHEN tipo_evento = 'login' AND sucesso = 1 THEN 1 END) as logins_sucesso,
                COUNT(CASE WHEN tipo_evento = 'tentativa_falha' THEN 1 END) as tentativas_falhas,
                COUNT(CASE WHEN tipo_evento = 'bloqueio' THEN 1 END) as bloqueios,
                COUNT(DISTINCT usuario_id) as usuarios_unicos,
                COUNT(DISTINCT ip_address) as ips_unicos
             FROM auth_logs 
             WHERE DATE(created_at) BETWEEN ? AND ?",
            [$dataInicio, $dataFim]
        );
        
        // Eventos por dia
        $relatorio['eventos_por_dia'] = $this->db->select(
            "SELECT 
                DATE(created_at) as data,
                COUNT(*) as total_eventos,
                COUNT(CASE WHEN tipo_evento = 'login' AND sucesso = 1 THEN 1 END) as logins,
                COUNT(CASE WHEN tipo_evento = 'tentativa_falha' THEN 1 END) as falhas
             FROM auth_logs 
             WHERE DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE(created_at)
             ORDER BY data",
            [$dataInicio, $dataFim]
        );
        
        // Top usuários mais ativos
        $relatorio['usuarios_mais_ativos'] = $this->db->select(
            "SELECT 
                u.nome,
                al.email,
                COUNT(*) as total_eventos
             FROM auth_logs al
             LEFT JOIN usuarios u ON al.usuario_id = u.id
             WHERE DATE(al.created_at) BETWEEN ? AND ?
             GROUP BY al.usuario_id, al.email, u.nome
             ORDER BY total_eventos DESC
             LIMIT 10",
            [$dataInicio, $dataFim]
        );
        
        // IPs mais suspeitos
        $relatorio['ips_suspeitos'] = $this->db->select(
            "SELECT 
                ip_address,
                COUNT(*) as total_eventos,
                COUNT(CASE WHEN tipo_evento = 'tentativa_falha' THEN 1 END) as falhas,
                COUNT(CASE WHEN tipo_evento = 'login' AND sucesso = 1 THEN 1 END) as sucessos
             FROM auth_logs 
             WHERE DATE(created_at) BETWEEN ? AND ?
             GROUP BY ip_address
             HAVING falhas > sucessos AND falhas >= 5
             ORDER BY falhas DESC",
            [$dataInicio, $dataFim]
        );
        
        return $relatorio;
    }
    
    /**
     * Construir cláusula WHERE para período
     */
    private function buildPeriodWhereClause($periodo) {
        switch ($periodo) {
            case 'hoje':
                return "DATE(created_at) = CURRENT_DATE";
            case 'ontem':
                return "DATE(created_at) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)";
            case 'semana':
                return "created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            case 'mes':
                return "created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            case 'ano':
                return "created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            default:
                return "1=1"; // Todos os registros
        }
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

    /**
     * Verificar rate limiting
     */
    public function checkRateLimit($email, $maxAttempts = 5, $minutes = 15) {
        $attempts = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM auth_logs 
             WHERE email = ? 
             AND tipo_evento = 'tentativa_falha' 
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$email, $minutes]
        )['count'];
        
        return $attempts < $maxAttempts;
    }

    /**
     * Registrar tentativa de login
     */
    public function logAttempt($email, $event, $userId = null, $details = null) {
        $success = in_array($event, ['login_success', 'logout']);
        
        return $this->create([
            'usuario_id' => $userId,
            'email' => $email,
            'tipo_evento' => $event,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'sucesso' => $success ? 1 : 0,
            'detalhes' => is_array($details) ? json_encode($details) : $details
        ]);
    }
}