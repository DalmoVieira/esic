<?php
/**
 * E-SIC - Sistema de Notificações por Email
 * Gerencia envio de emails para mudanças de status, prazos e respostas
 */

class EmailNotificacao {
    private $smtp_host;
    private $smtp_port;
    private $smtp_user;
    private $smtp_pass;
    private $from_email;
    private $from_name;
    private $base_url;
    
    public function __construct() {
        // Carregar configurações do banco
        $this->carregarConfiguracoes();
    }
    
    /**
     * Carregar configurações de email do banco de dados
     */
    private function carregarConfiguracoes() {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->query("SELECT chave, valor FROM configuracoes WHERE categoria = 'email'");
            $configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            $this->smtp_host = $configs['smtp_host'] ?? 'localhost';
            $this->smtp_port = $configs['smtp_port'] ?? 587;
            $this->smtp_user = $configs['smtp_user'] ?? '';
            $this->smtp_pass = $configs['smtp_pass'] ?? '';
            $this->from_email = $configs['from_email'] ?? 'noreply@rioclaro.sp.gov.br';
            $this->from_name = $configs['from_name'] ?? 'E-SIC Rio Claro';
            $this->base_url = $configs['base_url'] ?? 'http://localhost/esic';
            
        } catch (Exception $e) {
            error_log("Erro ao carregar configurações de email: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar email usando PHPMailer ou mail() nativo
     */
    private function enviarEmail($to, $subject, $body, $isHtml = true) {
        // Verificar se PHPMailer está disponível
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return $this->enviarComPHPMailer($to, $subject, $body, $isHtml);
        } else {
            return $this->enviarComMailNativo($to, $subject, $body, $isHtml);
        }
    }
    
    /**
     * Enviar email com PHPMailer
     */
    private function enviarComPHPMailer($to, $subject, $body, $isHtml) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Configurações SMTP
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = !empty($this->smtp_user);
            $mail->Username = $this->smtp_user;
            $mail->Password = $this->smtp_pass;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->smtp_port;
            $mail->CharSet = 'UTF-8';
            
            // Remetente
            $mail->setFrom($this->from_email, $this->from_name);
            
            // Destinatário
            $mail->addAddress($to);
            
            // Conteúdo
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            if ($isHtml) {
                $mail->AltBody = strip_tags($body);
            }
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erro ao enviar email: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Enviar email com função mail() nativa do PHP
     */
    private function enviarComMailNativo($to, $subject, $body, $isHtml) {
        $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($isHtml) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        return mail($to, $subject, $body, $headers);
    }
    
    /**
     * Notificar novo pedido criado
     */
    public function notificarNovoPedido($pedido, $requerente) {
        $subject = "E-SIC - Pedido Registrado - Protocolo {$pedido['protocolo']}";
        
        $body = $this->getTemplate([
            'titulo' => 'Pedido Registrado com Sucesso',
            'conteudo' => "
                <p>Prezado(a) <strong>{$requerente['nome']}</strong>,</p>
                
                <p>Seu pedido de acesso à informação foi registrado com sucesso no E-SIC da Prefeitura de Rio Claro.</p>
                
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='margin-top: 0;'>Dados do Pedido</h3>
                    <p><strong>Protocolo:</strong> {$pedido['protocolo']}</p>
                    <p><strong>Assunto:</strong> {$pedido['assunto']}</p>
                    <p><strong>Data de Abertura:</strong> " . date('d/m/Y', strtotime($pedido['data_cadastro'])) . "</p>
                    <p><strong>Prazo de Resposta:</strong> " . date('d/m/Y', strtotime($pedido['data_limite'])) . "</p>
                    <p><strong>Status:</strong> Aguardando Análise</p>
                </div>
                
                <p><strong>IMPORTANTE:</strong> Guarde o número do protocolo para acompanhar seu pedido.</p>
                
                <p>Você pode acompanhar o andamento do seu pedido a qualquer momento através do link:</p>
                
                <p style='text-align: center;'>
                    <a href='{$this->base_url}/acompanhar-v2.php?protocolo={$pedido['protocolo']}' 
                       style='background: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; 
                              border-radius: 5px; display: inline-block; margin: 20px 0;'>
                        Acompanhar Pedido
                    </a>
                </p>
                
                <p>Conforme a Lei de Acesso à Informação (Lei 12.527/2011), o prazo para resposta é de até 20 dias, 
                prorrogáveis por mais 10 dias mediante justificativa.</p>
            "
        ]);
        
        return $this->enviarEmail($requerente['email'], $subject, $body);
    }
    
    /**
     * Notificar mudança de status
     */
    public function notificarMudancaStatus($pedido, $requerente, $novoStatus) {
        $statusTexto = $this->getStatusTexto($novoStatus);
        $subject = "E-SIC - Atualização de Status - Protocolo {$pedido['protocolo']}";
        
        $body = $this->getTemplate([
            'titulo' => 'Atualização de Status do Pedido',
            'conteudo' => "
                <p>Prezado(a) <strong>{$requerente['nome']}</strong>,</p>
                
                <p>O status do seu pedido foi atualizado:</p>
                
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Protocolo:</strong> {$pedido['protocolo']}</p>
                    <p><strong>Novo Status:</strong> <span style='color: #0d6efd;'>{$statusTexto}</span></p>
                    <p><strong>Data da Atualização:</strong> " . date('d/m/Y H:i') . "</p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='{$this->base_url}/acompanhar-v2.php?protocolo={$pedido['protocolo']}' 
                       style='background: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; 
                              border-radius: 5px; display: inline-block; margin: 20px 0;'>
                        Ver Detalhes
                    </a>
                </p>
            "
        ]);
        
        return $this->enviarEmail($requerente['email'], $subject, $body);
    }
    
    /**
     * Notificar resposta ao pedido
     */
    public function notificarResposta($pedido, $requerente) {
        $subject = "E-SIC - Pedido Respondido - Protocolo {$pedido['protocolo']}";
        
        $body = $this->getTemplate([
            'titulo' => 'Seu Pedido Foi Respondido',
            'conteudo' => "
                <p>Prezado(a) <strong>{$requerente['nome']}</strong>,</p>
                
                <p>Seu pedido de acesso à informação foi respondido.</p>
                
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Protocolo:</strong> {$pedido['protocolo']}</p>
                    <p><strong>Assunto:</strong> {$pedido['assunto']}</p>
                    <p><strong>Data da Resposta:</strong> " . date('d/m/Y H:i', strtotime($pedido['data_resposta'])) . "</p>
                </div>
                
                <div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #0dcaf0;'>
                    <h4 style='margin-top: 0; color: #055160;'>Resposta:</h4>
                    <p style='white-space: pre-line;'>{$pedido['resposta']}</p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='{$this->base_url}/acompanhar-v2.php?protocolo={$pedido['protocolo']}' 
                       style='background: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; 
                              border-radius: 5px; display: inline-block; margin: 20px 0;'>
                        Ver Resposta Completa
                    </a>
                </p>
                
                <p><small>Caso não esteja satisfeito com a resposta, você pode interpor recurso em até 10 dias.</small></p>
            "
        ]);
        
        return $this->enviarEmail($requerente['email'], $subject, $body);
    }
    
    /**
     * Notificar prazo próximo do vencimento
     */
    public function notificarPrazoProximo($pedido, $requerente, $diasRestantes) {
        $subject = "E-SIC - Prazo Próximo do Vencimento - Protocolo {$pedido['protocolo']}";
        
        $body = $this->getTemplate([
            'titulo' => 'Atenção: Prazo Próximo do Vencimento',
            'conteudo' => "
                <p>Prezado(a) <strong>{$requerente['nome']}</strong>,</p>
                
                <p>Informamos que o prazo para resposta do seu pedido está próximo do vencimento.</p>
                
                <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    <p><strong>Protocolo:</strong> {$pedido['protocolo']}</p>
                    <p><strong>Dias Restantes:</strong> <span style='color: #ff6b6b; font-size: 1.2em;'>{$diasRestantes}</span></p>
                    <p><strong>Data Limite:</strong> " . date('d/m/Y', strtotime($pedido['data_limite'])) . "</p>
                    <p><strong>Status Atual:</strong> {$this->getStatusTexto($pedido['status'])}</p>
                </div>
                
                <p>Estamos trabalhando para responder seu pedido o mais breve possível.</p>
                
                <p style='text-align: center;'>
                    <a href='{$this->base_url}/acompanhar-v2.php?protocolo={$pedido['protocolo']}' 
                       style='background: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; 
                              border-radius: 5px; display: inline-block; margin: 20px 0;'>
                        Acompanhar Pedido
                    </a>
                </p>
            "
        ]);
        
        return $this->enviarEmail($requerente['email'], $subject, $body);
    }
    
    /**
     * Notificar prazo vencido
     */
    public function notificarPrazoVencido($pedido, $requerente) {
        $subject = "E-SIC - Prazo Vencido - Protocolo {$pedido['protocolo']}";
        
        $body = $this->getTemplate([
            'titulo' => 'Prazo de Resposta Vencido',
            'conteudo' => "
                <p>Prezado(a) <strong>{$requerente['nome']}</strong>,</p>
                
                <p>Informamos que o prazo legal para resposta do seu pedido foi ultrapassado.</p>
                
                <div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #dc3545;'>
                    <p><strong>Protocolo:</strong> {$pedido['protocolo']}</p>
                    <p><strong>Data Limite:</strong> " . date('d/m/Y', strtotime($pedido['data_limite'])) . "</p>
                    <p><strong>Status Atual:</strong> {$this->getStatusTexto($pedido['status'])}</p>
                </div>
                
                <p><strong>Você tem direito de:</strong></p>
                <ul>
                    <li>Interpor recurso à autoridade hierarquicamente superior</li>
                    <li>Solicitar informações sobre o andamento do pedido</li>
                    <li>Apresentar reclamação ao órgão de controle</li>
                </ul>
                
                <p style='text-align: center;'>
                    <a href='{$this->base_url}/recurso.php?pedido_id={$pedido['id']}' 
                       style='background: #dc3545; color: white; padding: 12px 30px; text-decoration: none; 
                              border-radius: 5px; display: inline-block; margin: 20px 0;'>
                        Interpor Recurso
                    </a>
                </p>
            "
        ]);
        
        return $this->enviarEmail($requerente['email'], $subject, $body);
    }
    
    /**
     * Notificar novo recurso
     */
    public function notificarNovoRecurso($recurso, $pedido, $requerente) {
        $subject = "E-SIC - Recurso Registrado - Protocolo {$recurso['protocolo']}";
        
        $body = $this->getTemplate([
            'titulo' => 'Recurso Registrado com Sucesso',
            'conteudo' => "
                <p>Prezado(a) <strong>{$requerente['nome']}</strong>,</p>
                
                <p>Seu recurso foi registrado com sucesso no E-SIC.</p>
                
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='margin-top: 0;'>Dados do Recurso</h3>
                    <p><strong>Protocolo do Recurso:</strong> {$recurso['protocolo']}</p>
                    <p><strong>Protocolo do Pedido:</strong> {$pedido['protocolo']}</p>
                    <p><strong>Instância:</strong> {$this->getInstanciaTexto($recurso['instancia'])}</p>
                    <p><strong>Data de Registro:</strong> " . date('d/m/Y H:i') . "</p>
                    <p><strong>Prazo de Resposta:</strong> " . date('d/m/Y', strtotime($recurso['data_limite'])) . "</p>
                </div>
                
                <p>Seu recurso será analisado conforme a Lei de Acesso à Informação.</p>
                
                <p style='text-align: center;'>
                    <a href='{$this->base_url}/acompanhar-v2.php?protocolo={$pedido['protocolo']}' 
                       style='background: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; 
                              border-radius: 5px; display: inline-block; margin: 20px 0;'>
                        Acompanhar Recurso
                    </a>
                </p>
            "
        ]);
        
        return $this->enviarEmail($requerente['email'], $subject, $body);
    }
    
    /**
     * Template HTML base para emails
     */
    private function getTemplate($data) {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$data['titulo']}</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>E-SIC Rio Claro</h1>
                <p style='color: rgba(255,255,255,0.9); margin: 5px 0 0 0;'>Sistema Eletrônico de Informação ao Cidadão</p>
            </div>
            
            <div style='background: white; padding: 30px; border: 1px solid #ddd; border-top: none;'>
                <h2 style='color: #667eea; margin-top: 0;'>{$data['titulo']}</h2>
                {$data['conteudo']}
            </div>
            
            <div style='background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 12px; color: #666;'>
                <p style='margin: 0;'>
                    <strong>Prefeitura Municipal de Rio Claro</strong><br>
                    Rua 1, 1600 - Centro - Rio Claro/SP - CEP 13500-100<br>
                    Tel: (19) 3522-7600
                </p>
                <p style='margin: 10px 0 0 0;'>
                    Esta é uma mensagem automática. Por favor, não responda este email.<br>
                    Para dúvidas, utilize o canal de atendimento do E-SIC.
                </p>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Obter texto do status
     */
    private function getStatusTexto($status) {
        $textos = [
            'aguardando' => 'Aguardando Análise',
            'em_analise' => 'Em Análise',
            'respondido' => 'Respondido',
            'negado' => 'Negado',
            'parcialmente_atendido' => 'Parcialmente Atendido',
            'cancelado' => 'Cancelado'
        ];
        
        return $textos[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
    
    /**
     * Obter texto da instância do recurso
     */
    private function getInstanciaTexto($instancia) {
        $textos = [
            'primeira' => 'Primeira Instância',
            'segunda' => 'Segunda Instância',
            'terceira' => 'Terceira Instância (CGU)'
        ];
        
        return $textos[$instancia] ?? ucfirst($instancia);
    }
}
?>