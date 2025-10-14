<?php

namespace App\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;
    private $config;
    
    public function __construct($config = [])
    {
        $this->config = array_merge([
            'host' => MAIL_HOST,
            'port' => MAIL_PORT,
            'username' => MAIL_USERNAME,
            'password' => MAIL_PASSWORD,
            'encryption' => MAIL_ENCRYPTION,
            'from_address' => MAIL_FROM_ADDRESS,
            'from_name' => MAIL_FROM_NAME
        ], $config);
        
        $this->initializeMailer();
    }
    
    /**
     * Initialize PHPMailer
     */
    private function initializeMailer()
    {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = $this->config['encryption'];
            $this->mailer->Port = $this->config['port'];
            $this->mailer->CharSet = 'UTF-8';
            
            // Default from
            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
            
        } catch (Exception $e) {
            throw new \Exception('Erro na configuração do email: ' . $e->getMessage());
        }
    }
    
    /**
     * Send email
     */
    public function send($to, $subject, $body, $options = [])
    {
        try {
            // Reset mailer
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->clearCustomHeaders();
            
            // Recipients
            if (is_array($to)) {
                foreach ($to as $address => $name) {
                    if (is_numeric($address)) {
                        $this->mailer->addAddress($name);
                    } else {
                        $this->mailer->addAddress($address, $name);
                    }
                }
            } else {
                $this->mailer->addAddress($to);
            }
            
            // CC and BCC
            if (!empty($options['cc'])) {
                if (is_array($options['cc'])) {
                    foreach ($options['cc'] as $cc) {
                        $this->mailer->addCC($cc);
                    }
                } else {
                    $this->mailer->addCC($options['cc']);
                }
            }
            
            if (!empty($options['bcc'])) {
                if (is_array($options['bcc'])) {
                    foreach ($options['bcc'] as $bcc) {
                        $this->mailer->addBCC($bcc);
                    }
                } else {
                    $this->mailer->addBCC($options['bcc']);
                }
            }
            
            // Reply to
            if (!empty($options['reply_to'])) {
                $this->mailer->addReplyTo($options['reply_to']);
            }
            
            // Attachments
            if (!empty($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    if (is_array($attachment)) {
                        $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
                    } else {
                        $this->mailer->addAttachment($attachment);
                    }
                }
            }
            
            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            // Alternative plain text
            if (!empty($options['alt_body'])) {
                $this->mailer->AltBody = $options['alt_body'];
            } else {
                $this->mailer->AltBody = strip_tags($body);
            }
            
            // Priority
            if (!empty($options['priority'])) {
                $this->mailer->Priority = $options['priority'];
            }
            
            // Send email
            $result = $this->mailer->send();
            
            // Log successful send
            $this->logEmail($to, $subject, 'sent');
            
            return $result;
            
        } catch (Exception $e) {
            // Log failed send
            $this->logEmail($to, $subject, 'failed', $e->getMessage());
            throw new \Exception('Falha no envio do email: ' . $e->getMessage());
        }
    }
    
    /**
     * Send template email
     */
    public function sendTemplate($template, $to, $subject, $variables = [], $options = [])
    {
        $body = $this->renderTemplate($template, $variables);
        return $this->send($to, $subject, $body, $options);
    }
    
    /**
     * Render email template
     */
    public function renderTemplate($template, $variables = [])
    {
        $templatePath = APP_PATH . '/views/emails/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template de email não encontrado: {$template}");
        }
        
        // Extract variables
        extract($variables);
        
        // Start output buffering
        ob_start();
        
        // Include template
        include $templatePath;
        
        // Get content
        $content = ob_get_clean();
        
        return $content;
    }
    
    /**
     * Send bulk emails
     */
    public function sendBulk($emails, $subject, $body, $options = [])
    {
        $results = [];
        $batchSize = $options['batch_size'] ?? 50;
        $delay = $options['delay'] ?? 1; // seconds between batches
        
        $batches = array_chunk($emails, $batchSize);
        
        foreach ($batches as $batch) {
            foreach ($batch as $email) {
                try {
                    $to = is_array($email) ? $email['email'] : $email;
                    $personalizedBody = $body;
                    
                    // Personalize body if variables provided
                    if (is_array($email) && isset($email['variables'])) {
                        $personalizedBody = $this->personalizeCommunication($body, $email['variables']);
                    }
                    
                    $this->send($to, $subject, $personalizedBody, $options);
                    $results[] = ['email' => $to, 'status' => 'sent'];
                    
                } catch (\Exception $e) {
                    $results[] = ['email' => $to, 'status' => 'failed', 'error' => $e->getMessage()];
                }
            }
            
            // Delay between batches to avoid overwhelming server
            if ($delay > 0) {
                sleep($delay);
            }
        }
        
        return $results;
    }
    
    /**
     * Personalize email content with variables
     */
    private function personalizeCommunication($content, $variables)
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }
    
    /**
     * Validate email address
     */
    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Test SMTP connection
     */
    public function testConnection()
    {
        try {
            $this->mailer->smtpConnect();
            $this->mailer->smtpClose();
            return true;
        } catch (Exception $e) {
            throw new \Exception('Falha na conexão SMTP: ' . $e->getMessage());
        }
    }
    
    /**
     * Get mailer info
     */
    public function getMailerInfo()
    {
        return [
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'encryption' => $this->config['encryption'],
            'username' => $this->config['username']
        ];
    }
    
    /**
     * Log email activity
     */
    private function logEmail($to, $subject, $status, $error = null)
    {
        $log = [
            'to' => is_array($to) ? implode(', ', array_keys($to)) : $to,
            'subject' => $subject,
            'status' => $status,
            'error' => $error,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $logFile = LOG_PATH . '/email_' . date('Y-m') . '.log';
        file_put_contents($logFile, json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Create email queue entry
     */
    public function queue($to, $subject, $body, $options = [], $priority = 'normal', $sendAt = null)
    {
        $queueData = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'options' => json_encode($options),
            'priority' => $priority,
            'send_at' => $sendAt ?: date('Y-m-d H:i:s'),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'attempts' => 0
        ];
        
        // Save to database queue table
        $db = \App\Core\Database::getInstance();
        return $db->insert('email_queue', $queueData);
    }
    
    /**
     * Process email queue
     */
    public function processQueue($limit = 100)
    {
        $db = \App\Core\Database::getInstance();
        
        $sql = "SELECT * FROM email_queue 
                WHERE status = 'pending' 
                AND send_at <= NOW() 
                AND attempts < 3 
                ORDER BY priority DESC, created_at ASC 
                LIMIT ?";
        
        $emails = $db->fetchAll($sql, [$limit]);
        $processed = 0;
        
        foreach ($emails as $email) {
            try {
                $options = json_decode($email['options'], true) ?: [];
                
                $this->send($email['to'], $email['subject'], $email['body'], $options);
                
                // Mark as sent
                $db->update('email_queue', [
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s')
                ], 'id = ?', [$email['id']]);
                
                $processed++;
                
            } catch (\Exception $e) {
                // Update attempts and error
                $attempts = $email['attempts'] + 1;
                $status = $attempts >= 3 ? 'failed' : 'pending';
                
                $db->update('email_queue', [
                    'attempts' => $attempts,
                    'status' => $status,
                    'last_error' => $e->getMessage(),
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = ?', [$email['id']]);
            }
        }
        
        return $processed;
    }
    
    /**
     * Clean old queue entries
     */
    public function cleanQueue($olderThanDays = 30)
    {
        $db = \App\Core\Database::getInstance();
        
        $sql = "DELETE FROM email_queue 
                WHERE status IN ('sent', 'failed') 
                AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        return $db->query($sql, [$olderThanDays]);
    }
}