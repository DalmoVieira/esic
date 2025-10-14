<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../libraries/EmailService.php';

/**
 * Sistema E-SIC - Recurso Controller
 * 
 * Gerencia recursos administrativos (parte pública)
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class RecursoController extends BaseController {
    
    private $pedidoModel;
    private $recursoModel;
    private $emailService;
    
    public function __construct() {
        parent::__construct();
        $this->pedidoModel = new Pedido();
        $this->recursoModel = new Recurso();
        $this->emailService = new EmailService();
    }
    
    /**
     * Exibir formulário de recurso
     */
    public function formulario($protocolo) {
        try {
            // Buscar pedido original
            $pedido = $this->pedidoModel->findByProtocol($protocolo);
            
            if (!$pedido) {
                $this->setFlashMessage('Pedido não encontrado', 'error');
                return $this->redirect('acompanhar');
            }
            
            // Verificar se pode interpor recurso
            $podeRecurso = $this->recursoModel->podeInterporRecurso($pedido['id']);
            
            if (!$podeRecurso['pode']) {
                $this->setFlashMessage($podeRecurso['motivo'], 'error');
                return $this->redirect("pedido/{$protocolo}");
            }
            
            // Buscar recursos já interpostos
            $recursosExistentes = $this->recursoModel->getRecursosPedido($pedido['id']);
            
            $data = [
                'title' => "Recurso - Pedido {$protocolo}",
                'pedido' => $pedido,
                'recursos_existentes' => $recursosExistentes,
                'prazo_info' => $podeRecurso,
                'breadcrumbs' => [
                    'Home' => url('/'),
                    'Acompanhar' => url('/acompanhar'),
                    "Pedido {$protocolo}" => url("/pedido/{$protocolo}"),
                    'Recurso' => ''
                ]
            ];
            
            echo $this->renderWithLayout('public/recurso-formulario', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('Erro: ' . $e->getMessage(), 'error');
            return $this->redirect('acompanhar');
        }
    }
    
    /**
     * Criar novo recurso
     */
    public function criar($protocolo) {
        try {
            if (!$this->isPost()) {
                return $this->redirect("recurso/{$protocolo}");
            }
            
            // Verificar CSRF
            $this->verifyCsrf();
            
            // Buscar pedido original
            $pedido = $this->pedidoModel->findByProtocol($protocolo);
            
            if (!$pedido) {
                throw new Exception("Pedido não encontrado");
            }
            
            // Verificar se ainda pode interpor recurso
            $podeRecurso = $this->recursoModel->podeInterporRecurso($pedido['id']);
            
            if (!$podeRecurso['pode']) {
                throw new Exception($podeRecurso['motivo']);
            }
            
            // Obter dados do formulário
            $dados = $this->getPost();
            
            // Validar dados
            $this->validate($dados, [
                'justificativa' => 'required|min:50',
                'email_confirmacao' => 'required|email'
            ]);
            
            // Verificar se o email confere com o do pedido original
            if ($dados['email_confirmacao'] !== $pedido['email_solicitante']) {
                throw new Exception("Email de confirmação não confere com o email do pedido original");
            }
            
            // Verificar limite de recursos por IP
            $this->verificarLimiteRecursosPorIP();
            
            // Upload de arquivo anexo (se houver)
            $arquivoAnexo = null;
            if (isset($_FILES['arquivo_anexo']) && $_FILES['arquivo_anexo']['error'] === UPLOAD_ERR_OK) {
                $arquivoAnexo = $this->uploadFile($_FILES['arquivo_anexo']);
            }
            
            // Determinar tipo de recurso
            $recursosExistentes = count($this->recursoModel->getRecursosPedido($pedido['id']));
            $tipoRecurso = $recursosExistentes === 0 ? 'primeira_instancia' : 'segunda_instancia';
            
            // Preparar dados do recurso
            $dadosRecurso = [
                'pedido_id' => $pedido['id'],
                'tipo' => $tipoRecurso,
                'justificativa' => $dados['justificativa'],
                'arquivo_anexo' => $arquivoAnexo
            ];
            
            // Criar recurso
            $recurso = $this->recursoModel->createRecurso($dadosRecurso);
            
            if (!$recurso) {
                throw new Exception("Erro ao criar recurso");
            }
            
            // Enviar notificações
            $this->enviarNotificacoesRecurso($recurso, $pedido);
            
            // Log da ação
            $this->logAction('recurso_criado', "Novo recurso criado: {$recurso['protocolo_recurso']} para pedido {$protocolo}");
            
            // Redirecionar com sucesso
            return $this->redirect(
                "pedido/{$protocolo}",
                "Recurso interposto com sucesso! Protocolo: {$recurso['protocolo_recurso']}",
                'success'
            );
            
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            $data = [
                'title' => "Recurso - Pedido {$protocolo}",
                'pedido' => $pedido ?? null,
                'errors' => $errors,
                'old' => $this->getPost(),
                'recursos_existentes' => isset($pedido) ? $this->recursoModel->getRecursosPedido($pedido['id']) : [],
                'breadcrumbs' => [
                    'Home' => url('/'),
                    'Acompanhar' => url('/acompanhar'),
                    "Pedido {$protocolo}" => url("/pedido/{$protocolo}"),
                    'Recurso' => ''
                ]
            ];
            
            echo $this->renderWithLayout('public/recurso-formulario', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('Erro ao criar recurso: ' . $e->getMessage(), 'error');
            return $this->redirect("pedido/{$protocolo}");
        }
    }
    
    /**
     * Verificar limite de recursos por IP
     */
    private function verificarLimiteRecursosPorIP() {
        $ip = Request::ip();
        
        // Verificar quantos recursos foram feitos nas últimas 24 horas por este IP
        $recursosRecentes = $this->db->selectOne(
            "SELECT COUNT(*) as count 
             FROM recursos r
             INNER JOIN pedidos p ON r.pedido_id = p.id
             WHERE p.ip_solicitante = ? 
             AND r.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)",
            [$ip]
        )['count'];
        
        // Limite de 3 recursos por IP por dia
        if ($recursosRecentes >= 3) {
            throw new Exception("Limite de recursos por dia excedido. Tente novamente amanhã.");
        }
    }
    
    /**
     * Enviar notificações sobre o recurso
     */
    private function enviarNotificacoesRecurso($recurso, $pedido) {
        try {
            // Email para o solicitante (usar template genérico ou criar específico)
            $variables = [
                'protocolo' => $recurso['protocolo_recurso'],
                'nome_solicitante' => $pedido['nome_solicitante'],
                'assunto' => 'Recurso registrado'
            ];
            $this->emailService->sendWithTemplate('pedido_confirmacao', $pedido['email_solicitante'], $variables);
            
            // Notificar administradores (implementar se necessário)
            // $this->emailService->notifyAdminNovoPedido($pedido);
            
        } catch (Exception $e) {
            error_log("Erro ao enviar notificações de recurso: " . $e->getMessage());
            // Não quebra o fluxo, apenas registra o erro
        }
    }
}

