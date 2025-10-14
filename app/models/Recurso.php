<?php

namespace App\Models;

class Recurso extends BaseModel
{
    protected $table = 'recursos';
    
    protected $fillable = [
        'pedido_id',
        'protocolo', 
        'tipo_recurso',
        'justificativa',
        'instancia',
        'status',
        'decisao',
        'data_interposicao',
        'prazo_resposta',
        'data_resposta',
        'resposta',
        'usuario_responsavel_id',
        'observacoes_internas',
        'anexos'
    ];
    
    /**
     * Get recursos by pedido ID
     */
    public function getByPedidoId($pedidoId)
    {
        $sql = "SELECT r.*, u.nome as usuario_responsavel_nome
                FROM {$this->table} r
                LEFT JOIN usuarios u ON r.usuario_responsavel_id = u.id
                WHERE r.pedido_id = ?
                ORDER BY r.instancia, r.created_at";
        
        return $this->db->fetchAll($sql, [$pedidoId]);
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
     * Get tipos de recurso
     */
    public function getTipos()
    {
        return [
            'nao_fornecimento' => 'Não fornecimento da informação',
            'negativa_acesso' => 'Negativa de acesso à informação', 
            'informacao_incompleta' => 'Informação fornecida incompleta',
            'cobranca_indevida' => 'Cobrança indevida de taxas',
            'prazo_nao_cumprido' => 'Prazo não cumprido',
            'classificacao_indevida' => 'Classificação indevida da informação'
        ];
    }
}