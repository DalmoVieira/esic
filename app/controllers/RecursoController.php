<?php

require_once 'BaseController.php';

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
            // Email para o solicitante
            $this->emailService->enviarConfirmacaoRecurso($recurso, $pedido);
            
            // Email para administradores
            $this->emailService->notificarNovoRecurso($recurso, $pedido);
            
        } catch (Exception $e) {
            error_log("Erro ao enviar notificações de recurso: " . $e->getMessage());
            // Não quebra o fluxo, apenas registra o erro
        }
    }
}

/**
 * Extensão do EmailService para recursos
 */
class EmailService {
    
    private $config;
    
    public function __construct() {
        $this->config = $this->getEmailConfig();
    }
    
    /**
     * Enviar confirmação de pedido
     */
    public function enviarConfirmacaoPedido($pedido) {
        $assunto = "Confirmação de Pedido - Protocolo {$pedido['protocolo']}";
        
        $corpo = "
        <h2>Pedido Registrado com Sucesso</h2>
        
        <p>Prezado(a) <strong>{$pedido['nome_solicitante']}</strong>,</p>
        
        <p>Seu pedido foi registrado com sucesso em nosso sistema.</p>
        
        <h3>Dados do Pedido:</h3>
        <ul>
            <li><strong>Protocolo:</strong> {$pedido['protocolo']}</li>
            <li><strong>Assunto:</strong> {$pedido['assunto']}</li>
            <li><strong>Data do Pedido:</strong> " . date('d/m/Y H:i', strtotime($pedido['created_at'])) . "</li>
            <li><strong>Prazo de Resposta:</strong> " . date('d/m/Y', strtotime($pedido['prazo_resposta'])) . "</li>
        </ul>
        
        <p>Você pode acompanhar o andamento do seu pedido através do nosso site, informando o protocolo e seu email.</p>
        
        <p>Em caso de dúvidas, entre em contato conosco.</p>
        
        <p>Atenciosamente,<br>
        Equipe E-SIC</p>
        ";
        
        return $this->enviarEmail($pedido['email_solicitante'], $assunto, $corpo);
    }
    
    /**
     * Enviar confirmação de recurso
     */
    public function enviarConfirmacaoRecurso($recurso, $pedido) {
        $assunto = "Confirmação de Recurso - Protocolo {$recurso['protocolo_recurso']}";
        
        $tipoTexto = [
            'primeira_instancia' => 'Primeira Instância',
            'segunda_instancia' => 'Segunda Instância',
            'cgu' => 'CGU'
        ];
        
        $corpo = "
        <h2>Recurso Registrado com Sucesso</h2>
        
        <p>Prezado(a) <strong>{$pedido['nome_solicitante']}</strong>,</p>
        
        <p>Seu recurso foi registrado com sucesso em nosso sistema.</p>
        
        <h3>Dados do Recurso:</h3>
        <ul>
            <li><strong>Protocolo do Recurso:</strong> {$recurso['protocolo_recurso']}</li>
            <li><strong>Protocolo do Pedido Original:</strong> {$pedido['protocolo']}</li>
            <li><strong>Tipo:</strong> {$tipoTexto[$recurso['tipo']]}</li>
            <li><strong>Data do Recurso:</strong> " . date('d/m/Y H:i', strtotime($recurso['created_at'])) . "</li>
            <li><strong>Prazo de Resposta:</strong> " . date('d/m/Y', strtotime($recurso['prazo_resposta'])) . "</li>
        </ul>
        
        <p>Você pode acompanhar o andamento do seu recurso através do nosso site, informando o protocolo original e seu email.</p>
        
        <p>Em caso de dúvidas, entre em contato conosco.</p>
        
        <p>Atenciosamente,<br>
        Equipe E-SIC</p>
        ";
        
        return $this->enviarEmail($pedido['email_solicitante'], $assunto, $corpo);
    }
    
    /**
     * Notificar administradores sobre novo pedido
     */
    public function notificarNovoPedido($pedido) {
        // Buscar emails de administradores
        $db = Database::getInstance();
        $admins = $db->select(
            "SELECT email FROM usuarios WHERE nivel_acesso IN ('admin', 'gestor') AND ativo = 1"
        );
        
        $assunto = "Novo Pedido E-SIC - Protocolo {$pedido['protocolo']}";
        
        $corpo = "
        <h2>Novo Pedido Cadastrado</h2>
        
        <p>Um novo pedido foi cadastrado no sistema E-SIC:</p>
        
        <h3>Dados do Pedido:</h3>
        <ul>
            <li><strong>Protocolo:</strong> {$pedido['protocolo']}</li>
            <li><strong>Solicitante:</strong> {$pedido['nome_solicitante']}</li>
            <li><strong>Email:</strong> {$pedido['email_solicitante']}</li>
            <li><strong>Assunto:</strong> {$pedido['assunto']}</li>
            <li><strong>Unidade:</strong> {$pedido['unidade_responsavel']}</li>
            <li><strong>Data:</strong> " . date('d/m/Y H:i', strtotime($pedido['created_at'])) . "</li>
        </ul>
        
        <p>Acesse o sistema administrativo para gerenciar este pedido.</p>
        ";
        
        foreach ($admins as $admin) {
            $this->enviarEmail($admin['email'], $assunto, $corpo);
        }
    }
    
    /**
     * Notificar administradores sobre novo recurso
     */
    public function notificarNovoRecurso($recurso, $pedido) {
        // Buscar emails de administradores
        $db = Database::getInstance();
        $admins = $db->select(
            "SELECT email FROM usuarios WHERE nivel_acesso IN ('admin', 'gestor') AND ativo = 1"
        );
        
        $tipoTexto = [
            'primeira_instancia' => 'Primeira Instância',
            'segunda_instancia' => 'Segunda Instância',
            'cgu' => 'CGU'
        ];
        
        $assunto = "Novo Recurso E-SIC - Protocolo {$recurso['protocolo_recurso']}";
        
        $corpo = "
        <h2>Novo Recurso Cadastrado</h2>
        
        <p>Um novo recurso foi cadastrado no sistema E-SIC:</p>
        
        <h3>Dados do Recurso:</h3>
        <ul>
            <li><strong>Protocolo do Recurso:</strong> {$recurso['protocolo_recurso']}</li>
            <li><strong>Protocolo do Pedido:</strong> {$pedido['protocolo']}</li>
            <li><strong>Tipo:</strong> {$tipoTexto[$recurso['tipo']]}</li>
            <li><strong>Solicitante:</strong> {$pedido['nome_solicitante']}</li>
            <li><strong>Email:</strong> {$pedido['email_solicitante']}</li>
            <li><strong>Data:</strong> " . date('d/m/Y H:i', strtotime($recurso['created_at'])) . "</li>
            <li><strong>Prazo:</strong> " . date('d/m/Y', strtotime($recurso['prazo_resposta'])) . "</li>
        </ul>
        
        <h3>Justificativa:</h3>
        <p>" . nl2br(htmlspecialchars($recurso['justificativa'])) . "</p>
        
        <p>Acesse o sistema administrativo para gerenciar este recurso.</p>
        ";
        
        foreach ($admins as $admin) {
            $this->enviarEmail($admin['email'], $assunto, $corpo);
        }
    }
    
    /**
     * Método básico para envio de email
     */
    private function enviarEmail($destinatario, $assunto, $corpo) {
        // Implementação básica com headers
        // Em produção, use PHPMailer ou similar
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$this->config['from_name']} <{$this->config['from_email']}>",
            "Reply-To: {$this->config['from_email']}",
            'X-Mailer: PHP/' . phpversion()
        ];
        
        $success = mail($destinatario, $assunto, $corpo, implode("\r\n", $headers));
        
        if (!$success) {
            error_log("Falha ao enviar email para: {$destinatario}");
        }
        
        return $success;
    }
    
    /**
     * Obter configurações de email
     */
    private function getEmailConfig() {
        return [
            'from_email' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@esic.gov.br',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Sistema E-SIC'
        ];
    }
}