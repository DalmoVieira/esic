<?php

/**
 * Sistema E-SIC - Serviço de Email
 * 
 * Classe responsável pelo envio de emails do sistema
 * Suporte para templates, anexos e configurações SMTP
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class EmailService {
    
    private $config;
    private $templates;
    
    public function __construct() {
        $this->loadConfig();
        $this->loadTemplates();
    }
    
    /**
     * Carrega configurações de email
     */
    private function loadConfig() {
        $this->config = [
            'smtp_host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
            'smtp_port' => $_ENV['MAIL_PORT'] ?? 587,
            'smtp_username' => $_ENV['MAIL_USERNAME'] ?? '',
            'smtp_password' => $_ENV['MAIL_PASSWORD'] ?? '',
            'smtp_encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@esic.gov.br',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Sistema E-SIC'
        ];
    }
    
    /**
     * Carrega templates do banco de dados
     */
    private function loadTemplates() {
        try {
            $db = Database::getInstance();
            $templates = $db->select("SELECT * FROM templates_email WHERE ativo = 1");
            
            $this->templates = [];
            foreach ($templates as $template) {
                $this->templates[$template['nome']] = $template;
            }
        } catch (Exception $e) {
            error_log("EmailService Error: " . $e->getMessage());
            $this->templates = [];
        }
    }
    
    /**
     * Envia email usando template
     */
    public function sendWithTemplate($templateName, $to, $variables = []) {
        if (!isset($this->templates[$templateName])) {
            throw new Exception("Template de email não encontrado: {$templateName}");
        }
        
        $template = $this->templates[$templateName];
        
        // Processar variáveis no assunto e corpo
        $subject = $this->processTemplate($template['assunto'], $variables);
        $body = $this->processTemplate($template['corpo'], $variables);
        
        return $this->send($to, $subject, $body, true);
    }
    
    /**
     * Processa template com variáveis
     */
    private function processTemplate($content, $variables) {
        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        return $content;
    }
    
    /**
     * Envia email diretamente
     */
    public function send($to, $subject, $body, $isHtml = false, $attachments = []) {
        try {
            // Headers básicos
            $headers = [
                'From: ' . $this->config['from_name'] . ' <' . $this->config['from_address'] . '>',
                'Reply-To: ' . $this->config['from_address'],
                'X-Mailer: Sistema E-SIC',
                'MIME-Version: 1.0'
            ];
            
            if ($isHtml) {
                $headers[] = 'Content-Type: text/html; charset=UTF-8';
            } else {
                $headers[] = 'Content-Type: text/plain; charset=UTF-8';
            }
            
            // Se há configuração SMTP, usar PHPMailer ou configurar SMTP
            if ($this->config['smtp_host']) {
                return $this->sendSMTP($to, $subject, $body, $isHtml, $attachments);
            } else {
                // Usar mail() do PHP
                return mail($to, $subject, $body, implode("\r\n", $headers));
            }
            
        } catch (Exception $e) {
            error_log("EmailService Send Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envio via SMTP (implementação básica)
     */
    private function sendSMTP($to, $subject, $body, $isHtml, $attachments) {
        // Implementação básica de SMTP
        // Em produção, considere usar PHPMailer ou biblioteca similar
        
        try {
            $socket = fsockopen($this->config['smtp_host'], $this->config['smtp_port'], $errno, $errstr, 30);
            
            if (!$socket) {
                throw new Exception("Não foi possível conectar ao servidor SMTP: {$errstr}");
            }
            
            // Implementação básica do protocolo SMTP
            // Por simplicidade, vamos usar a função mail() por enquanto
            
            fclose($socket);
            
            // Fallback para mail() do PHP
            $headers = [
                'From: ' . $this->config['from_name'] . ' <' . $this->config['from_address'] . '>',
                'Content-Type: ' . ($isHtml ? 'text/html' : 'text/plain') . '; charset=UTF-8'
            ];
            
            return mail($to, $subject, $body, implode("\r\n", $headers));
            
        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia notificação de novo pedido
     */
    public function sendPedidoConfirmacao($pedido) {
        $variables = [
            'protocolo' => $pedido['protocolo'],
            'nome_solicitante' => $pedido['nome_solicitante'],
            'assunto' => $pedido['assunto'],
            'prazo_resposta' => date('d/m/Y', strtotime($pedido['prazo_resposta']))
        ];
        
        return $this->sendWithTemplate('pedido_confirmacao', $pedido['email_solicitante'], $variables);
    }
    
    /**
     * Envia resposta do pedido
     */
    public function sendPedidoResposta($pedido) {
        $variables = [
            'protocolo' => $pedido['protocolo'],
            'nome_solicitante' => $pedido['nome_solicitante'],
            'resposta' => $pedido['resposta']
        ];
        
        return $this->sendWithTemplate('pedido_resposta', $pedido['email_solicitante'], $variables);
    }
    
    /**
     * Notifica administradores sobre novo pedido
     */
    public function notifyAdminNovoPedido($pedido) {
        try {
            $db = Database::getInstance();
            $admins = $db->select("SELECT email FROM usuarios WHERE nivel_acesso IN ('admin', 'gestor') AND ativo = 1");
            
            $variables = [
                'protocolo' => $pedido['protocolo'],
                'nome_solicitante' => $pedido['nome_solicitante'],
                'email_solicitante' => $pedido['email_solicitante'],
                'assunto' => $pedido['assunto']
            ];
            
            $enviados = 0;
            foreach ($admins as $admin) {
                if ($this->sendWithTemplate('novo_pedido_admin', $admin['email'], $variables)) {
                    $enviados++;
                }
            }
            
            return $enviados;
            
        } catch (Exception $e) {
            error_log("EmailService Notify Admin Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Registra tentativa de envio no banco
     */
    public function logEmailSent($tipo, $destinatario, $assunto, $sucesso, $erro = null) {
        try {
            $db = Database::getInstance();
            $db->insert(
                "INSERT INTO notificacoes (tipo, titulo, mensagem, destinatario_email, enviado, tentativas_envio, data_envio, erro_envio) 
                 VALUES (?, ?, ?, ?, ?, 1, NOW(), ?)",
                [$tipo, $assunto, substr($assunto, 0, 200), $destinatario, $sucesso ? 1 : 0, $erro]
            );
        } catch (Exception $e) {
            error_log("EmailService Log Error: " . $e->getMessage());
        }
    }
    
    /**
     * Verifica se email é válido
     */
    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Envia email de teste
     */
    public function sendTest($to) {
        $subject = 'Teste de Email - Sistema E-SIC';
        $body = '<h2>Email de Teste</h2><p>Este é um email de teste do Sistema E-SIC.</p><p>Data: ' . date('d/m/Y H:i:s') . '</p>';
        
        return $this->send($to, $subject, $body, true);
    }
}