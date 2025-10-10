<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Recurso.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/AuthLog.php';

class ApiController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->setHeaders();
    }

    /**
     * Configurar headers para API
     */
    private function setHeaders()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Responder OPTIONS para CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Middleware de autenticação para API
     */
    private function requireApiAuth()
    {
        $headers = getallheaders();
        $token = null;
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
        }
        
        if (!$token) {
            return $this->jsonError('Token de autorização necessário', 401);
        }
        
        $userData = $this->auth->verifyJWT($token);
        if (!$userData) {
            return $this->jsonError('Token inválido', 401);
        }
        
        return true;
    }

    /**
     * GET /api/pedidos - Listar pedidos
     */
    public function pedidos()
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? 20), 100); // Máximo 100 por página
            $filters = $this->getApiFilters();
            
            $pedidoModel = new Pedido();
            $pedidos = $pedidoModel->findAllWithPagination($filters, $page, $limit);
            $total = $pedidoModel->count($filters);
            
            return $this->jsonSuccess([
                'pedidos' => $pedidos,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit),
                    'has_next' => $page < ceil($total / $limit),
                    'has_previous' => $page > 1
                ]
            ]);
        } catch (Exception $e) {
            return $this->jsonError('Erro ao buscar pedidos: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/pedidos/{id} - Obter pedido específico
     */
    public function pedido($id)
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $pedidoModel = new Pedido();
            $pedido = $pedidoModel->findById($id);
            
            if (!$pedido) {
                return $this->jsonError('Pedido não encontrado', 404);
            }
            
            return $this->jsonSuccess(['pedido' => $pedido]);
        } catch (Exception $e) {
            return $this->jsonError('Erro ao buscar pedido: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/pedidos - Criar novo pedido
     */
    public function createPedido()
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $data = $this->getJsonInput();
            
            if (!$data) {
                return $this->jsonError('Dados JSON inválidos', 400);
            }
            
            // Validar dados obrigatórios
            $required = ['nome_solicitante', 'email', 'assunto', 'descricao'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->jsonError("Campo obrigatório: {$field}", 400);
                }
            }
            
            $pedidoModel = new Pedido();
            
            // Preparar dados para criação
            $pedidoData = [
                'protocolo' => $pedidoModel->generateProtocol(),
                'nome_solicitante' => $data['nome_solicitante'],
                'email' => $data['email'],
                'telefone' => $data['telefone'] ?? null,
                'assunto' => $data['assunto'],
                'descricao' => $data['descricao'],
                'status' => 'pendente',
                'prazo_resposta' => $pedidoModel->calculateDeadline(),
                'ip_solicitante' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            $pedidoId = $pedidoModel->create($pedidoData);
            
            if ($pedidoId) {
                $pedido = $pedidoModel->findById($pedidoId);
                
                // Enviar email de confirmação (implementar)
                $this->sendConfirmationEmail($pedido);
                
                return $this->jsonSuccess([
                    'message' => 'Pedido criado com sucesso',
                    'pedido' => $pedido
                ], 201);
            } else {
                return $this->jsonError('Erro ao criar pedido');
            }
        } catch (Exception $e) {
            return $this->jsonError('Erro ao criar pedido: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/pedidos/{id} - Atualizar pedido
     */
    public function updatePedido($id)
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $data = $this->getJsonInput();
            
            if (!$data) {
                return $this->jsonError('Dados JSON inválidos', 400);
            }
            
            $pedidoModel = new Pedido();
            $pedido = $pedidoModel->findById($id);
            
            if (!$pedido) {
                return $this->jsonError('Pedido não encontrado', 404);
            }
            
            // Filtrar apenas campos permitidos para atualização
            $allowedFields = ['status', 'resposta', 'respondido_por'];
            $updateData = [];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }
            
            if (empty($updateData)) {
                return $this->jsonError('Nenhum campo válido para atualização', 400);
            }
            
            // Se está respondendo, adicionar data de resposta
            if (isset($updateData['resposta'])) {
                $updateData['data_resposta'] = date('Y-m-d H:i:s');
            }
            
            $success = $pedidoModel->update($id, $updateData);
            
            if ($success) {
                $pedidoAtualizado = $pedidoModel->findById($id);
                return $this->jsonSuccess([
                    'message' => 'Pedido atualizado com sucesso',
                    'pedido' => $pedidoAtualizado
                ]);
            } else {
                return $this->jsonError('Erro ao atualizar pedido');
            }
        } catch (Exception $e) {
            return $this->jsonError('Erro ao atualizar pedido: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/pedidos/protocolo/{protocolo} - Buscar por protocolo
     */
    public function pedidoByProtocolo($protocolo)
    {
        try {
            $pedidoModel = new Pedido();
            $pedido = $pedidoModel->findByProtocol($protocolo);
            
            if (!$pedido) {
                return $this->jsonError('Pedido não encontrado', 404);
            }
            
            // Para busca pública, retornar apenas dados básicos
            $publicData = [
                'protocolo' => $pedido['protocolo'],
                'assunto' => $pedido['assunto'],
                'status' => $pedido['status'],
                'data_criacao' => $pedido['data_criacao'],
                'prazo_resposta' => $pedido['prazo_resposta'],
                'data_resposta' => $pedido['data_resposta']
            ];
            
            return $this->jsonSuccess(['pedido' => $publicData]);
        } catch (Exception $e) {
            return $this->jsonError('Erro ao buscar pedido: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/recursos - Listar recursos
     */
    public function recursos()
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? 20), 100);
            $filters = $this->getApiFilters();
            
            $recursoModel = new Recurso();
            $recursos = $recursoModel->findAllWithPagination($filters, $page, $limit);
            $total = $recursoModel->count($filters);
            
            return $this->jsonSuccess([
                'recursos' => $recursos,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (Exception $e) {
            return $this->jsonError('Erro ao buscar recursos: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/recursos - Criar novo recurso
     */
    public function createRecurso()
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $data = $this->getJsonInput();
            
            if (!$data) {
                return $this->jsonError('Dados JSON inválidos', 400);
            }
            
            // Validar dados obrigatórios
            $required = ['pedido_id', 'tipo', 'justificativa'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->jsonError("Campo obrigatório: {$field}", 400);
                }
            }
            
            // Verificar se o pedido existe
            $pedidoModel = new Pedido();
            $pedido = $pedidoModel->findById($data['pedido_id']);
            
            if (!$pedido) {
                return $this->jsonError('Pedido não encontrado', 404);
            }
            
            $recursoModel = new Recurso();
            
            // Verificar se pode criar recurso
            if (!$recursoModel->canCreateRecurso($data['pedido_id'], $data['tipo'])) {
                return $this->jsonError('Não é possível criar recurso para este pedido', 400);
            }
            
            $recursoData = [
                'pedido_id' => $data['pedido_id'],
                'tipo' => $data['tipo'],
                'justificativa' => $data['justificativa'],
                'status' => 'em_andamento',
                'prazo_resposta' => $recursoModel->calculateDeadline($data['tipo'])
            ];
            
            $recursoId = $recursoModel->create($recursoData);
            
            if ($recursoId) {
                $recurso = $recursoModel->findById($recursoId);
                return $this->jsonSuccess([
                    'message' => 'Recurso criado com sucesso',
                    'recurso' => $recurso
                ], 201);
            } else {
                return $this->jsonError('Erro ao criar recurso');
            }
        } catch (Exception $e) {
            return $this->jsonError('Erro ao criar recurso: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/stats - Estatísticas do sistema
     */
    public function stats()
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $pedidoModel = new Pedido();
            $recursoModel = new Recurso();
            
            $stats = [
                'pedidos' => [
                    'total' => $pedidoModel->count(),
                    'pendentes' => $pedidoModel->count(['status' => 'pendente']),
                    'em_andamento' => $pedidoModel->count(['status' => 'em_andamento']),
                    'deferidos' => $pedidoModel->count(['status' => 'deferido']),
                    'indeferidos' => $pedidoModel->count(['status' => 'indeferido'])
                ],
                'recursos' => [
                    'total' => $recursoModel->count(),
                    'em_andamento' => $recursoModel->count(['status' => 'em_andamento']),
                    'deferidos' => $recursoModel->count(['status' => 'deferido']),
                    'indeferidos' => $recursoModel->count(['status' => 'indeferido'])
                ],
                'periodo_atual' => [
                    'mes_atual' => $pedidoModel->count([
                        'data_inicio' => date('Y-m-01'),
                        'data_fim' => date('Y-m-t')
                    ]),
                    'ano_atual' => $pedidoModel->count([
                        'data_inicio' => date('Y-01-01'),
                        'data_fim' => date('Y-12-31')
                    ])
                ]
            ];
            
            return $this->jsonSuccess($stats);
        } catch (Exception $e) {
            return $this->jsonError('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/config - Configurações públicas do sistema
     */
    public function config()
    {
        try {
            $config = [
                'sistema' => [
                    'nome' => 'Sistema E-SIC',
                    'versao' => '1.0.0',
                    'orgao' => $_ENV['ORGAO_NOME'] ?? 'Órgão Público',
                    'lei' => 'Lei nº 12.527/2011 (Lei de Acesso à Informação)'
                ],
                'prazos' => [
                    'resposta_pedido' => 20, // dias
                    'recurso_primeira_instancia' => 10,
                    'recurso_segunda_instancia' => 10
                ],
                'limites' => [
                    'upload_max_size' => '10MB',
                    'arquivos_por_pedido' => 5,
                    'tipos_arquivos' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png']
                ],
                'contatos' => [
                    'email' => $_ENV['CONTACT_EMAIL'] ?? 'contato@orgao.gov.br',
                    'telefone' => $_ENV['CONTACT_PHONE'] ?? '',
                    'endereco' => $_ENV['CONTACT_ADDRESS'] ?? ''
                ]
            ];
            
            return $this->jsonSuccess($config);
        } catch (Exception $e) {
            return $this->jsonError('Erro ao obter configurações: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/auth/login - Login via API
     */
    public function login()
    {
        try {
            $data = $this->getJsonInput();
            
            if (!$data || empty($data['email']) || empty($data['password'])) {
                return $this->jsonError('Email e senha são obrigatórios', 400);
            }
            
            $loginResult = $this->auth->login($data['email'], $data['password']);
            
            if ($loginResult['success']) {
                $user = $this->auth->user();
                $token = $this->auth->generateJWT($user);
                
                return $this->jsonSuccess([
                    'message' => 'Login realizado com sucesso',
                    'token' => $token,
                    'user' => $this->auth->getUserPublicData($user),
                    'expires_in' => 3600
                ]);
            } else {
                return $this->jsonError($loginResult['message'], 401);
            }
        } catch (Exception $e) {
            return $this->jsonError('Erro no login: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/auth/refresh - Renovar token
     */
    public function refreshToken()
    {
        if (!$this->requireApiAuth()) return;
        
        try {
            $user = $this->auth->user();
            $newToken = $this->auth->generateJWT($user);
            
            return $this->jsonSuccess([
                'token' => $newToken,
                'expires_in' => 3600
            ]);
        } catch (Exception $e) {
            return $this->jsonError('Erro ao renovar token: ' . $e->getMessage());
        }
    }

    // Métodos auxiliares

    private function getApiFilters()
    {
        $filters = [];
        
        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        
        if (!empty($_GET['data_inicio'])) {
            $filters['data_inicio'] = $_GET['data_inicio'];
        }
        
        if (!empty($_GET['data_fim'])) {
            $filters['data_fim'] = $_GET['data_fim'];
        }
        
        if (!empty($_GET['search'])) {
            $filters['q'] = $_GET['search'];
        }
        
        return $filters;
    }

    private function getJsonInput()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    private function jsonSuccess($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'timestamp' => date('c')
        ]);
        exit;
    }

    private function jsonError($message, $code = 500)
    {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'timestamp' => date('c')
        ]);
        exit;
    }

    private function sendConfirmationEmail($pedido)
    {
        // Implementar envio de email de confirmação
        // Por enquanto, apenas log da ação
        $this->logAction('confirmation_email_sent', [
            'pedido_id' => $pedido['id'],
            'protocolo' => $pedido['protocolo'],
            'email' => $pedido['email']
        ]);
    }
}