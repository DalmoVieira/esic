<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Pedido;

/**
 * Sistema E-SIC - Home Controller
 * 
 * Controla páginas públicas do sistema
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */
class HomeController extends Controller
{
    private $pedidoModel;
    
    private $pedidoModel;
    
    public function __construct() {
        parent::__construct();
        $this->pedidoModel = new Pedido();
    }
    
    /**
     * Página inicial
     */
    public function index() {
        // Obter estatísticas públicas
        $stats = $this->getPublicStats();
        
        // Dados para a view
        $data = [
            'title' => 'Sistema E-SIC - Acesso à Informação',
            'stats' => $stats,
            'orgao' => $this->getOrgaoInfo()
        ];
        
        echo $this->renderWithLayout('public/home', $data);
    }
    
    /**
     * Página sobre a LAI
     */
    public function lai() {
        $data = [
            'title' => 'Lei de Acesso à Informação - LAI',
            'breadcrumbs' => [
                'Home' => url('/'),
                'Lei de Acesso à Informação' => ''
            ]
        ];
        
        echo $this->renderWithLayout('public/lai', $data);
    }
    
    /**
     * Página sobre o sistema
     */
    public function sobre() {
        $data = [
            'title' => 'Sobre o Sistema E-SIC',
            'orgao' => $this->getOrgaoInfo(),
            'breadcrumbs' => [
                'Home' => url('/'),
                'Sobre' => ''
            ]
        ];
        
        echo $this->renderWithLayout('public/sobre', $data);
    }
    
    /**
     * Página de transparência
     */
    public function transparencia() {
        // Obter dados de transparência
        $transparencia = $this->getTransparenciaData();
        
        $data = [
            'title' => 'Transparência',
            'transparencia' => $transparencia,
            'breadcrumbs' => [
                'Home' => url('/'),
                'Transparência' => ''
            ]
        ];
        
        echo $this->renderWithLayout('public/transparencia', $data);
    }
    
    /**
     * Obter estatísticas públicas
     */
    private function getPublicStats() {
        try {
            $stats = [];
            
            // Total de pedidos
            $stats['total_pedidos'] = $this->pedidoModel->count();
            
            // Pedidos respondidos
            $stats['pedidos_respondidos'] = $this->pedidoModel->count('status', 'respondido');
            
            // Taxa de resposta
            $stats['taxa_resposta'] = $stats['total_pedidos'] > 0 
                ? round(($stats['pedidos_respondidos'] / $stats['total_pedidos']) * 100, 1) 
                : 0;
            
            // Tempo médio de resposta
            $tempoMedio = $this->db->selectOne(
                "SELECT AVG(DATEDIFF(data_resposta, created_at)) as media 
                 FROM pedidos 
                 WHERE data_resposta IS NOT NULL"
            );
            
            $stats['tempo_medio_resposta'] = round($tempoMedio['media'] ?? 0, 1);
            
            // Pedidos este mês
            $stats['pedidos_mes'] = $this->db->selectOne(
                "SELECT COUNT(*) as count 
                 FROM pedidos 
                 WHERE YEAR(created_at) = YEAR(CURDATE()) 
                 AND MONTH(created_at) = MONTH(CURDATE())"
            )['count'];
            
            // Pedidos por categoria (top 5)
            $stats['por_categoria'] = $this->db->select(
                "SELECT categoria, COUNT(*) as total 
                 FROM pedidos 
                 WHERE categoria IS NOT NULL 
                 GROUP BY categoria 
                 ORDER BY total DESC 
                 LIMIT 5"
            );
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Erro ao obter estatísticas: " . $e->getMessage());
            
            return [
                'total_pedidos' => 0,
                'pedidos_respondidos' => 0,
                'taxa_resposta' => 0,
                'tempo_medio_resposta' => 0,
                'pedidos_mes' => 0,
                'por_categoria' => []
            ];
        }
    }
    
    /**
     * Obter informações do órgão
     */
    private function getOrgaoInfo() {
        $config = $this->getSystemConfig();
        
        return [
            'nome' => $config['orgao_nome'] ?? 'Órgão Público',
            'endereco' => $config['orgao_endereco'] ?? '',
            'telefone' => $config['orgao_telefone'] ?? '',
            'email' => $config['orgao_email'] ?? '',
            'site' => $config['orgao_site'] ?? '',
            'cidade' => $config['orgao_cidade'] ?? '',
            'uf' => $config['orgao_uf'] ?? '',
            'cep' => $config['orgao_cep'] ?? ''
        ];
    }
    
    /**
     * Obter dados de transparência
     */
    private function getTransparenciaData() {
        try {
            $data = [];
            
            // Estatísticas por mês (últimos 12 meses)
            $data['pedidos_por_mes'] = $this->db->select(
                "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as mes,
                    DATE_FORMAT(created_at, '%m/%Y') as mes_formato,
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'respondido' THEN 1 END) as respondidos,
                    COUNT(CASE WHEN status = 'negado' THEN 1 END) as negados
                 FROM pedidos 
                 WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                 GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                 ORDER BY mes DESC"
            );
            
            // Estatísticas por categoria
            $data['por_categoria'] = $this->db->select(
                "SELECT 
                    COALESCE(categoria, 'Não informado') as categoria,
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'respondido' THEN 1 END) as respondidos,
                    ROUND(AVG(CASE 
                        WHEN data_resposta IS NOT NULL 
                        THEN DATEDIFF(data_resposta, created_at) 
                        ELSE NULL 
                    END), 1) as tempo_medio
                 FROM pedidos 
                 GROUP BY categoria 
                 ORDER BY total DESC"
            );
            
            // Estatísticas por unidade
            $data['por_unidade'] = $this->db->select(
                "SELECT 
                    COALESCE(unidade_responsavel, 'Não informado') as unidade,
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'respondido' THEN 1 END) as respondidos,
                    ROUND(AVG(CASE 
                        WHEN data_resposta IS NOT NULL 
                        THEN DATEDIFF(data_resposta, created_at) 
                        ELSE NULL 
                    END), 1) as tempo_medio
                 FROM pedidos 
                 GROUP BY unidade_responsavel 
                 ORDER BY total DESC"
            );
            
            // Tempo médio de resposta por mês
            $data['tempo_resposta_mes'] = $this->db->select(
                "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as mes,
                    DATE_FORMAT(created_at, '%m/%Y') as mes_formato,
                    ROUND(AVG(DATEDIFF(data_resposta, created_at)), 1) as tempo_medio
                 FROM pedidos 
                 WHERE data_resposta IS NOT NULL
                 AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                 GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                 ORDER BY mes DESC"
            );
            
            // Recursos interpostos
            $data['recursos'] = $this->db->select(
                "SELECT 
                    COUNT(*) as total_recursos,
                    COUNT(CASE WHEN status = 'deferido' THEN 1 END) as deferidos,
                    COUNT(CASE WHEN status = 'indeferido' THEN 1 END) as indeferidos,
                    COUNT(CASE WHEN status = 'pendente' THEN 1 END) as pendentes
                 FROM recursos"
            );
            
            return $data;
            
        } catch (Exception $e) {
            error_log("Erro ao obter dados de transparência: " . $e->getMessage());
            
            return [
                'pedidos_por_mes' => [],
                'por_categoria' => [],
                'por_unidade' => [],
                'tempo_resposta_mes' => [],
                'recursos' => []
            ];
        }
    }
}