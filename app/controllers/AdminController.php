<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Recurso.php';
require_once __DIR__ . '/../models/AuthLog.php';

class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole(['administrador', 'operador']);
    }

    /**
     * Dashboard administrativo
     */
    public function dashboard()
    {
        try {
            $stats = $this->getDashboardStats();
            $recentRequests = $this->getRecentRequests();
            $pendingRequests = $this->getPendingRequests();
            $systemAlerts = $this->getSystemAlerts();
            
            $this->render('admin/dashboard', [
                'title' => 'Dashboard Administrativo',
                'stats' => $stats,
                'recent_requests' => $recentRequests,
                'pending_requests' => $pendingRequests,
                'system_alerts' => $systemAlerts
            ]);
        } catch (Exception $e) {
            $this->error('Erro ao carregar dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Listar todos os pedidos
     */
    public function pedidos()
    {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = 25;
            $filters = $this->getFilters();
            
            $pedidoModel = new Pedido();
            $pedidos = $pedidoModel->findAllWithPagination($filters, $page, $limit);
            $total = $pedidoModel->count($filters);
            
            $this->render('admin/pedidos/list', [
                'title' => 'Gerenciar Pedidos',
                'pedidos' => $pedidos,
                'pagination' => $this->calculatePagination($total, $page, $limit),
                'filters' => $filters
            ]);
        } catch (Exception $e) {
            $this->error('Erro ao listar pedidos: ' . $e->getMessage());
        }
    }

    /**
     * Visualizar pedido específico
     */
    public function viewPedido($id)
    {
        try {
            $pedidoModel = new Pedido();
            $pedido = $pedidoModel->findById($id);
            
            if (!$pedido) {
                $this->error('Pedido não encontrado', 404);
                return;
            }
            
            $recursos = $this->getRecursosByPedido($id);
            $timeline = $this->getPedidoTimeline($id);
            $attachments = $this->getPedidoAttachments($id);
            
            $this->render('admin/pedidos/view', [
                'title' => 'Pedido #' . $pedido['protocolo'],
                'pedido' => $pedido,
                'recursos' => $recursos,
                'timeline' => $timeline,
                'attachments' => $attachments
            ]);
        } catch (Exception $e) {
            $this->error('Erro ao visualizar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Processar pedido (responder/deferir/indeferir)
     */
    public function processPedido($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/pedidos/' . $id);
            return;
        }

        try {
            $data = $this->validatePedidoProcess($_POST);
            
            if (!$data['valid']) {
                $this->error('Dados inválidos: ' . implode(', ', $data['errors']));
                return;
            }
            
            $pedidoModel = new Pedido();
            $pedido = $pedidoModel->findById($id);
            
            if (!$pedido) {
                $this->error('Pedido não encontrado', 404);
                return;
            }
            
            // Processar anexos se houver
            $attachments = [];
            if (!empty($_FILES['anexos']['name'][0])) {
                $attachments = $this->handleFileUploads($_FILES['anexos'], 'responses');
            }
            
            // Atualizar pedido
            $updateData = [
                'status' => $data['data']['status'],
                'resposta' => $data['data']['resposta'],
                'respondido_por' => $this->user['id'],
                'data_resposta' => date('Y-m-d H:i:s'),
                'anexos_resposta' => !empty($attachments) ? json_encode($attachments) : null
            ];
            
            $success = $pedidoModel->update($id, $updateData);
            
            if ($success) {
                // Registrar no log
                $this->logAction('pedido_processed', [
                    'pedido_id' => $id,
                    'protocolo' => $pedido['protocolo'],
                    'status' => $data['data']['status']
                ]);
                
                // Enviar notificação por email
                $this->sendPedidoStatusEmail($pedido, $updateData);
                
                $this->success('Pedido processado com sucesso');
                $this->redirect('/admin/pedidos/' . $id);
            } else {
                $this->error('Erro ao processar pedido');
            }
        } catch (Exception $e) {
            $this->error('Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Gerenciar recursos
     */
    public function recursos()
    {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = 25;
            $filters = $this->getFilters();
            
            $recursoModel = new Recurso();
            $recursos = $recursoModel->findAllWithPagination($filters, $page, $limit);
            $total = $recursoModel->count($filters);
            
            $this->render('admin/recursos/list', [
                'title' => 'Gerenciar Recursos',
                'recursos' => $recursos,
                'pagination' => $this->calculatePagination($total, $page, $limit),
                'filters' => $filters
            ]);
        } catch (Exception $e) {
            $this->error('Erro ao listar recursos: ' . $e->getMessage());
        }
    }

    /**
     * Processar recurso
     */
    public function processRecurso($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/recursos/' . $id);
            return;
        }

        try {
            $data = $this->validateRecursoProcess($_POST);
            
            if (!$data['valid']) {
                $this->error('Dados inválidos: ' . implode(', ', $data['errors']));
                return;
            }
            
            $recursoModel = new Recurso();
            $recurso = $recursoModel->findById($id);
            
            if (!$recurso) {
                $this->error('Recurso não encontrado', 404);
                return;
            }
            
            $updateData = [
                'status' => $data['data']['status'],
                'decisao' => $data['data']['decisao'],
                'decidido_por' => $this->user['id'],
                'data_decisao' => date('Y-m-d H:i:s')
            ];
            
            $success = $recursoModel->update($id, $updateData);
            
            if ($success) {
                $this->logAction('recurso_processed', [
                    'recurso_id' => $id,
                    'status' => $data['data']['status']
                ]);
                
                $this->success('Recurso processado com sucesso');
                $this->redirect('/admin/recursos/' . $id);
            } else {
                $this->error('Erro ao processar recurso');
            }
        } catch (Exception $e) {
            $this->error('Erro ao processar recurso: ' . $e->getMessage());
        }
    }

    /**
     * Gerenciar usuários do sistema
     */
    public function usuarios()
    {
        $this->requireRole(['administrador']);
        
        try {
            $usuarioModel = new Usuario();
            $usuarios = $usuarioModel->findAll(['tipo' => ['administrador', 'operador']]);
            
            $this->render('admin/usuarios/list', [
                'title' => 'Gerenciar Usuários',
                'usuarios' => $usuarios
            ]);
        } catch (Exception $e) {
            $this->error('Erro ao listar usuários: ' . $e->getMessage());
        }
    }

    /**
     * Criar novo usuário
     */
    public function createUser()
    {
        $this->requireRole(['administrador']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = $this->validateUserData($_POST);
                
                if (!$data['valid']) {
                    $this->error('Dados inválidos: ' . implode(', ', $data['errors']));
                    return;
                }
                
                $usuarioModel = new Usuario();
                $userId = $usuarioModel->create($data['data']);
                
                if ($userId) {
                    $this->logAction('user_created', ['user_id' => $userId]);
                    $this->success('Usuário criado com sucesso');
                    $this->redirect('/admin/usuarios');
                } else {
                    $this->error('Erro ao criar usuário');
                }
            } catch (Exception $e) {
                $this->error('Erro ao criar usuário: ' . $e->getMessage());
            }
        } else {
            $this->render('admin/usuarios/create', [
                'title' => 'Novo Usuário'
            ]);
        }
    }

    /**
     * Editar usuário
     */
    public function editUser($id)
    {
        $this->requireRole(['administrador']);
        
        try {
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->findById($id);
            
            if (!$usuario) {
                $this->error('Usuário não encontrado', 404);
                return;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $this->validateUserData($_POST, $id);
                
                if (!$data['valid']) {
                    $this->error('Dados inválidos: ' . implode(', ', $data['errors']));
                    return;
                }
                
                $success = $usuarioModel->update($id, $data['data']);
                
                if ($success) {
                    $this->logAction('user_updated', ['user_id' => $id]);
                    $this->success('Usuário atualizado com sucesso');
                    $this->redirect('/admin/usuarios');
                } else {
                    $this->error('Erro ao atualizar usuário');
                }
            } else {
                $this->render('admin/usuarios/edit', [
                    'title' => 'Editar Usuário',
                    'usuario' => $usuario
                ]);
            }
        } catch (Exception $e) {
            $this->error('Erro ao editar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Relatórios e estatísticas
     */
    public function relatorios()
    {
        try {
            $periodo = $_GET['periodo'] ?? 'mes';
            $dateRange = $this->getDateRange($periodo);
            
            $stats = [
                'pedidos' => $this->getPedidosStats($dateRange),
                'recursos' => $this->getRecursosStats($dateRange),
                'usuarios' => $this->getUsuariosStats($dateRange),
                'performance' => $this->getPerformanceStats($dateRange)
            ];
            
            $this->render('admin/relatorios', [
                'title' => 'Relatórios e Estatísticas',
                'stats' => $stats,
                'periodo' => $periodo,
                'date_range' => $dateRange
            ]);
        } catch (Exception $e) {
            $this->error('Erro ao gerar relatórios: ' . $e->getMessage());
        }
    }

    /**
     * Configurações do sistema
     */
    public function configuracoes()
    {
        $this->requireRole(['administrador']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = $this->validateConfigData($_POST);
                
                if (!$data['valid']) {
                    $this->error('Dados inválidos: ' . implode(', ', $data['errors']));
                    return;
                }
                
                $success = $this->updateConfiguracoes($data['data']);
                
                if ($success) {
                    $this->logAction('config_updated');
                    $this->success('Configurações atualizadas com sucesso');
                }
            } catch (Exception $e) {
                $this->error('Erro ao salvar configurações: ' . $e->getMessage());
            }
        }
        
        $configuracoes = $this->getConfiguracoes();
        
        $this->render('admin/configuracoes', [
            'title' => 'Configurações do Sistema',
            'configuracoes' => $configuracoes
        ]);
    }

    // Métodos auxiliares privados
    private function getDashboardStats()
    {
        $pedidoModel = new Pedido();
        $recursoModel = new Recurso();
        
        return [
            'total_pedidos' => $pedidoModel->count(),
            'pedidos_pendentes' => $pedidoModel->count(['status' => 'em_andamento']),
            'pedidos_hoje' => $pedidoModel->count(['data_criacao' => date('Y-m-d')]),
            'recursos_pendentes' => $recursoModel->count(['status' => 'em_andamento']),
            'prazo_vencendo' => $pedidoModel->getPedidosPrazoVencendo(3)
        ];
    }

    private function getRecentRequests()
    {
        $pedidoModel = new Pedido();
        return $pedidoModel->getRecent(10);
    }

    private function getPendingRequests()
    {
        $pedidoModel = new Pedido();
        return $pedidoModel->findAll(['status' => 'em_andamento'], 'prazo_resposta ASC', 5);
    }

    private function getSystemAlerts()
    {
        $alerts = [];
        
        // Verificar pedidos com prazo vencendo
        $pedidoModel = new Pedido();
        $vencendo = $pedidoModel->getPedidosPrazoVencendo(3);
        
        if (count($vencendo) > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => count($vencendo) . ' pedido(s) com prazo vencendo em 3 dias',
                'link' => '/admin/pedidos?prazo_vencendo=1'
            ];
        }
        
        return $alerts;
    }

    private function validatePedidoProcess($data)
    {
        $errors = [];
        $result = [
            'status' => $data['status'] ?? '',
            'resposta' => $data['resposta'] ?? ''
        ];
        
        if (empty($result['status']) || !in_array($result['status'], ['deferido', 'indeferido'])) {
            $errors[] = 'Status inválido';
        }
        
        if (empty($result['resposta'])) {
            $errors[] = 'Resposta é obrigatória';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $result
        ];
    }

    private function validateRecursoProcess($data)
    {
        $errors = [];
        $result = [
            'status' => $data['status'] ?? '',
            'decisao' => $data['decisao'] ?? ''
        ];
        
        if (empty($result['status']) || !in_array($result['status'], ['deferido', 'indeferido'])) {
            $errors[] = 'Status inválido';
        }
        
        if (empty($result['decisao'])) {
            $errors[] = 'Decisão é obrigatória';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $result
        ];
    }

    private function validateUserData($data, $userId = null)
    {
        $errors = [];
        $result = [
            'nome' => $data['nome'] ?? '',
            'email' => $data['email'] ?? '',
            'tipo' => $data['tipo'] ?? '',
            'ativo' => isset($data['ativo']) ? 1 : 0
        ];
        
        if (empty($result['nome'])) {
            $errors[] = 'Nome é obrigatório';
        }
        
        if (empty($result['email']) || !filter_var($result['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        if (!in_array($result['tipo'], ['administrador', 'operador'])) {
            $errors[] = 'Tipo de usuário inválido';
        }
        
        // Verificar se email já existe (exceto para o próprio usuário na edição)
        $usuarioModel = new Usuario();
        $existing = $usuarioModel->findByEmail($result['email']);
        if ($existing && (!$userId || $existing['id'] != $userId)) {
            $errors[] = 'Email já está em uso';
        }
        
        // Senha apenas para novos usuários
        if (!$userId) {
            if (empty($data['senha'])) {
                $errors[] = 'Senha é obrigatória';
            } else {
                $result['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
            }
        } elseif (!empty($data['senha'])) {
            $result['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $result
        ];
    }

    private function getRecursosByPedido($pedidoId)
    {
        $recursoModel = new Recurso();
        return $recursoModel->findAll(['pedido_id' => $pedidoId]);
    }

    private function getPedidoTimeline($pedidoId)
    {
        try {
            $sql = "SELECT 
                        h.acao,
                        h.observacoes,
                        h.created_at,
                        u.nome as usuario_nome
                    FROM historico_pedidos h
                    LEFT JOIN usuarios u ON h.usuario_id = u.id
                    WHERE h.pedido_id = ?
                    ORDER BY h.created_at DESC";
            
            return $this->db->select($sql, [$pedidoId]);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getPedidoAttachments($pedidoId)
    {
        try {
            $pedidoModel = new Pedido();
            $pedido = $pedidoModel->findById($pedidoId);
            
            $attachments = [];
            
            // Anexos da solicitação
            if (!empty($pedido['anexos'])) {
                $anexosSolicitacao = json_decode($pedido['anexos'], true);
                if (is_array($anexosSolicitacao)) {
                    foreach ($anexosSolicitacao as $anexo) {
                        $attachments[] = array_merge($anexo, ['type' => 'solicitacao']);
                    }
                }
            }
            
            // Anexos da resposta
            if (!empty($pedido['anexos_resposta'])) {
                $anexosResposta = json_decode($pedido['anexos_resposta'], true);
                if (is_array($anexosResposta)) {
                    foreach ($anexosResposta as $anexo) {
                        $attachments[] = array_merge($anexo, ['type' => 'resposta']);
                    }
                }
            }
            
            return $attachments;
        } catch (Exception $e) {
            return [];
        }
    }

    private function sendPedidoStatusEmail($pedido, $updateData)
    {
        try {
            // Implementar envio de email usando PHPMailer ou similar
            // Por enquanto, apenas log da ação
            $this->logAction('email_sent', [
                'pedido_id' => $pedido['id'],
                'protocolo' => $pedido['protocolo'],
                'status' => $updateData['status'],
                'email' => $pedido['email']
            ]);
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $e->getMessage());
        }
    }

    private function getDateRange($periodo)
    {
        $now = new DateTime();
        
        switch ($periodo) {
            case 'hoje':
                return [
                    'inicio' => $now->format('Y-m-d 00:00:00'),
                    'fim' => $now->format('Y-m-d 23:59:59')
                ];
                
            case 'semana':
                $inicio = clone $now;
                $inicio->modify('monday this week');
                return [
                    'inicio' => $inicio->format('Y-m-d 00:00:00'),
                    'fim' => $now->format('Y-m-d 23:59:59')
                ];
                
            case 'mes':
                return [
                    'inicio' => $now->format('Y-m-01 00:00:00'),
                    'fim' => $now->format('Y-m-t 23:59:59')
                ];
                
            case 'ano':
                return [
                    'inicio' => $now->format('Y-01-01 00:00:00'),
                    'fim' => $now->format('Y-12-31 23:59:59')
                ];
                
            default:
                return [
                    'inicio' => $now->format('Y-m-01 00:00:00'),
                    'fim' => $now->format('Y-m-t 23:59:59')
                ];
        }
    }

    private function getPedidosStats($dateRange)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'deferido' THEN 1 ELSE 0 END) as deferidos,
                        SUM(CASE WHEN status = 'indeferido' THEN 1 ELSE 0 END) as indeferidos,
                        SUM(CASE WHEN status = 'em_andamento' THEN 1 ELSE 0 END) as em_andamento,
                        AVG(CASE 
                            WHEN status IN ('deferido', 'indeferido') 
                            THEN DATEDIFF(data_resposta, data_criacao) 
                            ELSE NULL 
                        END) as tempo_medio_resposta
                    FROM pedidos 
                    WHERE data_criacao BETWEEN ? AND ?";
            
            $result = $this->db->select($sql, [$dateRange['inicio'], $dateRange['fim']]);
            return $result[0] ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getRecursosStats($dateRange)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'deferido' THEN 1 ELSE 0 END) as deferidos,
                        SUM(CASE WHEN status = 'indeferido' THEN 1 ELSE 0 END) as indeferidos,
                        SUM(CASE WHEN status = 'em_andamento' THEN 1 ELSE 0 END) as em_andamento
                    FROM recursos 
                    WHERE data_criacao BETWEEN ? AND ?";
            
            $result = $this->db->select($sql, [$dateRange['inicio'], $dateRange['fim']]);
            return $result[0] ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getUsuariosStats($dateRange)
    {
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT p.email) as cidadaos_ativos,
                        COUNT(*) as total_solicitacoes
                    FROM pedidos p 
                    WHERE p.data_criacao BETWEEN ? AND ?";
            
            $result = $this->db->select($sql, [$dateRange['inicio'], $dateRange['fim']]);
            return $result[0] ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getPerformanceStats($dateRange)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_pedidos,
                        SUM(CASE 
                            WHEN status IN ('deferido', 'indeferido') 
                            AND data_resposta <= prazo_resposta 
                            THEN 1 ELSE 0 
                        END) as no_prazo,
                        SUM(CASE 
                            WHEN status IN ('deferido', 'indeferido') 
                            AND data_resposta > prazo_resposta 
                            THEN 1 ELSE 0 
                        END) as atrasados,
                        SUM(CASE 
                            WHEN status = 'em_andamento' 
                            AND NOW() > prazo_resposta 
                            THEN 1 ELSE 0 
                        END) as vencidos
                    FROM pedidos 
                    WHERE data_criacao BETWEEN ? AND ?";
            
            $result = $this->db->select($sql, [$dateRange['inicio'], $dateRange['fim']]);
            $stats = $result[0] ?? [];
            
            // Calcular percentuais
            if ($stats['total_pedidos'] > 0) {
                $stats['percentual_no_prazo'] = round(($stats['no_prazo'] / $stats['total_pedidos']) * 100, 2);
            } else {
                $stats['percentual_no_prazo'] = 0;
            }
            
            return $stats;
        } catch (Exception $e) {
            return [];
        }
    }

    private function getConfiguracoes()
    {
        try {
            $sql = "SELECT chave, valor, descricao, tipo FROM configuracoes ORDER BY chave";
            $configs = $this->db->select($sql);
            
            $result = [];
            foreach ($configs as $config) {
                $result[$config['chave']] = [
                    'valor' => $config['valor'],
                    'descricao' => $config['descricao'],
                    'tipo' => $config['tipo']
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    private function updateConfiguracoes($data)
    {
        try {
            $this->db->beginTransaction();
            
            foreach ($data as $chave => $valor) {
                $sql = "UPDATE configuracoes SET valor = ?, updated_at = NOW() WHERE chave = ?";
                $this->db->execute($sql, [$valor, $chave]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function validateConfigData($data)
    {
        $errors = [];
        $result = [];
        
        // Validar configurações específicas
        $allowedConfigs = [
            'prazo_resposta_dias',
            'prazo_recurso_dias',
            'email_notificacoes',
            'manutencao_ativa',
            'uploads_max_size',
            'site_titulo',
            'site_descricao'
        ];
        
        foreach ($data as $chave => $valor) {
            if (in_array($chave, $allowedConfigs)) {
                $result[$chave] = $valor;
            }
        }
        
        // Validações específicas
        if (isset($result['prazo_resposta_dias'])) {
            $prazo = (int)$result['prazo_resposta_dias'];
            if ($prazo < 1 || $prazo > 90) {
                $errors[] = 'Prazo de resposta deve estar entre 1 e 90 dias';
            }
        }
        
        if (isset($result['uploads_max_size'])) {
            $size = (int)$result['uploads_max_size'];
            if ($size < 1 || $size > 100) {
                $errors[] = 'Tamanho máximo de upload deve estar entre 1 e 100 MB';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $result
        ];
    }
}