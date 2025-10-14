<?php

namespace App\Models;

class Pedido extends BaseModel
{
    protected $table = 'pedidos';
    
    protected $fillable = [
        'protocolo',
        'nome_solicitante',
        'email_solicitante',
        'cpf_solicitante',
        'telefone_solicitante',
        'endereco_solicitante',
        'categoria_id',
        'orgao_id',
        'descricao_pedido',
        'justificativa',
        'forma_recebimento',
        'endereco_postal',
        'tipo_pessoa',
        'anexos',
        'ip_solicitante',
        'user_agent',
        'status',
        'prazo_atendimento',
        'data_resposta',
        'resposta',
        'anexos_resposta',
        'usuario_responsavel_id',
        'observacoes_internas',
        'prioridade'
    ];
    
    /**
     * Get pedido by protocolo and CPF
     */
    public function getByProtocolo($protocolo, $cpf = null)
    {
        $sql = "SELECT p.*, c.nome as categoria_nome, o.nome as orgao_nome,
                       u.nome as usuario_responsavel_nome
                FROM {$this->table} p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN orgaos o ON p.orgao_id = o.id
                LEFT JOIN usuarios u ON p.usuario_responsavel_id = u.id
                WHERE p.protocolo = ?";
        
        $params = [$protocolo];
        
        if ($cpf) {
            $sql .= " AND p.cpf_solicitante = ?";
            $params[] = preg_replace('/\D/', '', $cpf);
        }
        
        return $this->db->fetch($sql, $params);
    }
    
    /**
     * Get pedido by ID with relations
     */
    public function getById($id)
    {
        $sql = "SELECT p.*, c.nome as categoria_nome, o.nome as orgao_nome,
                       u.nome as usuario_responsavel_nome
                FROM {$this->table} p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN orgaos o ON p.orgao_id = o.id
                LEFT JOIN usuarios u ON p.usuario_responsavel_id = u.id
                WHERE p.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Get recent pedidos
     */
    public function getRecentes($limit = 10)
    {
        $sql = "SELECT p.*, c.nome as categoria_nome
                FROM {$this->table} p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                ORDER BY p.created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Count pedidos by status
     */
    public function countByStatus($status)
    {
        if (is_array($status)) {
            $placeholders = implode(',', array_fill(0, count($status), '?'));
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status IN ({$placeholders})";
            $result = $this->db->fetch($sql, $status);
        } else {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = ?";
            $result = $this->db->fetch($sql, [$status]);
        }
        
        return (int) $result['total'];
    }
    
    /**
     * Get contador for year (for protocol generation)
     */
    public function getContadorAno($ano)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE YEAR(created_at) = ?";
        $result = $this->db->fetch($sql, [$ano]);
        return (int) $result['total'];
    }
    
    /**
     * Get historico of pedido
     */
    public function getHistorico($pedidoId)
    {
        $sql = "SELECT h.*, u.nome as usuario_nome
                FROM pedido_historico h
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.pedido_id = ?
                ORDER BY h.data_acao DESC";
        
        return $this->db->fetchAll($sql, [$pedidoId]);
    }
    
    /**
     * Add historico entry
     */
    public function addHistorico($pedidoId, $data)
    {
        $data['pedido_id'] = $pedidoId;
        return $this->db->insert('pedido_historico', $data);
    }
    
    /**
     * Get categorias
     */
    public function getCategorias()
    {
        $sql = "SELECT * FROM categorias WHERE ativo = 1 ORDER BY nome";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get orgaos
     */
    public function getOrgaos()
    {
        $sql = "SELECT * FROM orgaos WHERE ativo = 1 ORDER BY nome";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get tempo medio de resposta
     */
    public function getTempoMedioResposta()
    {
        $sql = "SELECT AVG(DATEDIFF(data_resposta, created_at)) as tempo_medio
                FROM {$this->table}
                WHERE status = 'atendido'
                AND data_resposta IS NOT NULL
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
        
        $result = $this->db->fetch($sql);
        return round($result['tempo_medio'] ?? 0, 1);
    }
    
    /**
     * Get pedidos por mes
     */
    public function getPedidosPorMes($meses = 12)
    {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as mes,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'atendido' THEN 1 ELSE 0 END) as atendidos
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY mes DESC";
        
        return $this->db->fetchAll($sql, [$meses]);
    }
    
    /**
     * Get categorias mais solicitadas
     */
    public function getCategoriasMaisSolicitadas($limit = 10)
    {
        $sql = "SELECT c.nome, COUNT(p.id) as total
                FROM {$this->table} p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY c.id, c.nome
                ORDER BY total DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Get tempo medio por categoria
     */
    public function getTempoMedioPorCategoria()
    {
        $sql = "SELECT 
                    c.nome as categoria,
                    AVG(DATEDIFF(p.data_resposta, p.created_at)) as tempo_medio
                FROM {$this->table} p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.status = 'atendido'
                AND p.data_resposta IS NOT NULL
                AND p.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY c.id, c.nome
                ORDER BY tempo_medio ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get taxa de atendimento
     */
    public function getTaxaAtendimento()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'atendido' THEN 1 ELSE 0 END) as atendidos
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
        
        $result = $this->db->fetch($sql);
        
        if ($result['total'] > 0) {
            return round(($result['atendidos'] / $result['total']) * 100, 2);
        }
        
        return 0;
    }
}