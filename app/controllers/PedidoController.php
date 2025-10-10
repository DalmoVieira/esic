<?php

require_once 'BaseController.php';

/**
 * Sistema E-SIC - Pedido Controller
 * 
 * Gerencia pedidos de acesso à informação (parte pública)
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class PedidoController extends BaseController {
    
    private $pedidoModel;
    private $emailService;
    
    public function __construct() {
        parent::__construct();
        $this->pedidoModel = new Pedido();
        $this->emailService = new EmailService();
    }
    
    /**
     * Exibir formulário de novo pedido
     */
    public function formulario() {
        $data = [
            'title' => 'Novo Pedido de Acesso à Informação',
            'categorias' => $this->getCategorias(),
            'unidades' => $this->getUnidades(),
            'breadcrumbs' => [
                'Home' => url('/'),
                'Novo Pedido' => ''
            ]
        ];
        
        echo $this->renderWithLayout('public/novo-pedido', $data);
    }
    
    /**
     * Criar novo pedido
     */
    public function criar() {
        try {
            if (!$this->isPost()) {
                return $this->redirect('novo-pedido');
            }
            
            // Verificar CSRF
            $this->verifyCsrf();
            
            // Obter dados do formulário
            $dados = $this->getPost();
            
            // Validar dados
            $this->validate($dados, [
                'nome_solicitante' => 'required|min:3|max:100',
                'email_solicitante' => 'required|email',
                'assunto' => 'required|min:10|max:200',
                'descricao' => 'required|min:20'
            ]);
            
            // Verificar limite de pedidos por IP (proteção contra spam)
            $this->verificarLimitePorIP();
            
            // Upload de arquivo anexo (se houver)
            $arquivoAnexo = null;
            if (isset($_FILES['arquivo_anexo']) && $_FILES['arquivo_anexo']['error'] === UPLOAD_ERR_OK) {
                $arquivoAnexo = $this->uploadFile($_FILES['arquivo_anexo']);
            }
            
            if ($arquivoAnexo) {
                $dados['arquivo_anexo'] = $arquivoAnexo;
            }
            
            // Criar pedido
            $pedido = $this->pedidoModel->createPedido($dados);
            
            if (!$pedido) {
                throw new Exception("Erro ao criar pedido");
            }
            
            // Enviar emails de notificação
            $this->enviarNotificacoes($pedido);
            
            // Log da ação
            $this->logAction('pedido_criado', "Novo pedido criado: {$pedido['protocolo']}");
            
            // Redirecionar para confirmação
            return $this->redirect(
                "pedido/{$pedido['protocolo']}", 
                'Pedido criado com sucesso! Protocolo: ' . $pedido['protocolo'],
                'success'
            );
            
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            $data = [
                'title' => 'Novo Pedido de Acesso à Informação',
                'errors' => $errors,
                'old' => $this->getPost(),
                'categorias' => $this->getCategorias(),
                'unidades' => $this->getUnidades(),
                'breadcrumbs' => [
                    'Home' => url('/'),
                    'Novo Pedido' => ''
                ]
            ];
            
            echo $this->renderWithLayout('public/novo-pedido', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('Erro ao criar pedido: ' . $e->getMessage(), 'error');
            return $this->redirect('novo-pedido');
        }
    }
    
    /**
     * Página para acompanhar pedido
     */
    public function acompanhar() {
        $data = [
            'title' => 'Acompanhar Pedido',
            'breadcrumbs' => [
                'Home' => url('/'),
                'Acompanhar Pedido' => ''
            ]
        ];
        
        echo $this->renderWithLayout('public/acompanhar', $data);
    }
    
    /**
     * Buscar pedido para acompanhamento
     */
    public function buscar() {
        try {
            if (!$this->isPost()) {
                return $this->redirect('acompanhar');
            }
            
            // Verificar CSRF
            $this->verifyCsrf();
            
            $protocolo = $this->getPost('protocolo');
            $email = $this->getPost('email_solicitante');
            
            if (empty($protocolo) || empty($email)) {
                throw new Exception("Protocolo e email são obrigatórios");
            }
            
            // Buscar pedido
            $pedido = $this->db->selectOne(
                "SELECT * FROM pedidos WHERE protocolo = ? AND email_solicitante = ?",
                [$protocolo, $email]
            );
            
            if (!$pedido) {
                throw new Exception("Pedido não encontrado ou email incorreto");
            }
            
            // Buscar recursos relacionados
            $recursos = $this->db->select(
                "SELECT * FROM recursos WHERE pedido_id = ? ORDER BY created_at DESC",
                [$pedido['id']]
            );
            
            // Redirecionar para visualização
            return $this->redirect("pedido/{$protocolo}");
            
        } catch (Exception $e) {
            $this->setFlashMessage($e->getMessage(), 'error');
            return $this->redirect('acompanhar');
        }
    }
    
    /**
     * Visualizar pedido específico
     */
    public function visualizar($protocolo) {
        try {
            // Buscar pedido
            $pedido = $this->pedidoModel->findByProtocol($protocolo);
            
            if (!$pedido) {
                $this->setFlashMessage('Pedido não encontrado', 'error');
                return $this->redirect('acompanhar');
            }
            
            // Buscar recursos relacionados
            $recursos = $this->db->select(
                "SELECT * FROM recursos WHERE pedido_id = ? ORDER BY created_at DESC",
                [$pedido['id']]
            );
            
            // Calcular dias restantes/transcorridos
            $prazoInfo = $this->calcularPrazoInfo($pedido);
            
            $data = [
                'title' => "Pedido {$protocolo}",
                'pedido' => $pedido,
                'recursos' => $recursos,
                'prazoInfo' => $prazoInfo,
                'podeRecurso' => $this->podeInterporRecurso($pedido),
                'breadcrumbs' => [
                    'Home' => url('/'),
                    'Acompanhar' => url('/acompanhar'),
                    "Pedido {$protocolo}" => ''
                ]
            ];
            
            echo $this->renderWithLayout('public/pedido-detalhes', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('Erro ao carregar pedido: ' . $e->getMessage(), 'error');
            return $this->redirect('acompanhar');
        }
    }
    
    /**
     * Verificar limite de pedidos por IP
     */
    private function verificarLimitePorIP() {
        $ip = Request::ip();
        
        // Verificar quantos pedidos foram feitos nas últimas 24 horas por este IP
        $pedidosRecentes = $this->db->selectOne(
            "SELECT COUNT(*) as count 
             FROM pedidos 
             WHERE ip_solicitante = ? 
             AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)",
            [$ip]
        )['count'];
        
        // Limite de 5 pedidos por IP por dia
        if ($pedidosRecentes >= 5) {
            throw new Exception("Limite de pedidos por dia excedido. Tente novamente amanhã.");
        }
    }
    
    /**
     * Enviar notificações por email
     */
    private function enviarNotificacoes($pedido) {
        try {
            // Email para o solicitante
            $this->emailService->enviarConfirmacaoPedido($pedido);
            
            // Email para administradores
            $this->emailService->notificarNovoPedido($pedido);
            
        } catch (Exception $e) {
            error_log("Erro ao enviar notificações: " . $e->getMessage());
            // Não quebra o fluxo, apenas registra o erro
        }
    }
    
    /**
     * Calcular informações sobre o prazo
     */
    private function calcularPrazoInfo($pedido) {
        $agora = new DateTime();
        $prazoResposta = new DateTime($pedido['prazo_resposta']);
        
        $info = [
            'prazo_resposta' => $pedido['prazo_resposta'],
            'prazo_formatado' => $prazoResposta->format('d/m/Y'),
            'vencido' => false,
            'dias_restantes' => 0,
            'dias_transcorridos' => 0,
            'percentual_transcorrido' => 0
        ];
        
        if ($pedido['status'] === 'respondido' || $pedido['status'] === 'negado') {
            // Pedido já foi respondido
            if ($pedido['data_resposta']) {
                $dataResposta = new DateTime($pedido['data_resposta']);
                $dataCriacao = new DateTime($pedido['created_at']);
                $info['dias_transcorridos'] = $dataCriacao->diff($dataResposta)->days;
                $info['respondido_em'] = $dataResposta->format('d/m/Y H:i');
            }
        } else {
            // Pedido ainda em andamento
            $dataCriacao = new DateTime($pedido['created_at']);
            $diasTotais = $dataCriacao->diff($prazoResposta)->days;
            
            if ($agora > $prazoResposta) {
                $info['vencido'] = true;
                $info['dias_atraso'] = $agora->diff($prazoResposta)->days;
            } else {
                $info['dias_restantes'] = $agora->diff($prazoResposta)->days;
            }
            
            $diasTranscorridos = $dataCriacao->diff($agora)->days;
            $info['dias_transcorridos'] = $diasTranscorridos;
            $info['percentual_transcorrido'] = $diasTotais > 0 ? min(100, round(($diasTranscorridos / $diasTotais) * 100)) : 0;
        }
        
        return $info;
    }
    
    /**
     * Verificar se pode interpor recurso
     */
    private function podeInterporRecurso($pedido) {
        // Só pode recurso se foi respondido ou negado
        if (!in_array($pedido['status'], ['respondido', 'negado'])) {
            return ['pode' => false, 'motivo' => 'Aguardando resposta do pedido'];
        }
        
        // Verificar se ainda está no prazo (10 dias úteis após resposta)
        if (!$pedido['data_resposta']) {
            return ['pode' => false, 'motivo' => 'Data de resposta não definida'];
        }
        
        $dataResposta = new DateTime($pedido['data_resposta']);
        $prazoRecurso = $this->adicionarDiasUteis($dataResposta, 10);
        $agora = new DateTime();
        
        if ($agora > $prazoRecurso) {
            return ['pode' => false, 'motivo' => 'Prazo para recurso expirado'];
        }
        
        // Verificar quantos recursos já foram interpostos
        $recursos = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM recursos WHERE pedido_id = ?",
            [$pedido['id']]
        )['count'];
        
        if ($recursos >= 2) {
            return ['pode' => false, 'motivo' => 'Limite de recursos atingido'];
        }
        
        return [
            'pode' => true, 
            'prazo_limite' => $prazoRecurso->format('d/m/Y'),
            'dias_restantes' => $agora->diff($prazoRecurso)->days
        ];
    }
    
    /**
     * Adicionar dias úteis a uma data
     */
    private function adicionarDiasUteis(DateTime $data, $diasUteis) {
        $diasAdicionados = 0;
        $novaData = clone $data;
        
        while ($diasAdicionados < $diasUteis) {
            $novaData->add(new DateInterval('P1D'));
            
            // Verificar se não é fim de semana (sábado = 6, domingo = 0)
            if ($novaData->format('w') != 0 && $novaData->format('w') != 6) {
                $diasAdicionados++;
            }
        }
        
        return $novaData;
    }
    
    /**
     * Obter categorias disponíveis
     */
    private function getCategorias() {
        return [
            'Documentos Pessoais' => 'Documentos Pessoais',
            'Informações Administrativas' => 'Informações Administrativas',
            'Contratos e Licitações' => 'Contratos e Licitações',
            'Recursos Humanos' => 'Recursos Humanos',
            'Orçamento e Finanças' => 'Orçamento e Finanças',
            'Obras e Serviços' => 'Obras e Serviços',
            'Meio Ambiente' => 'Meio Ambiente',
            'Saúde' => 'Saúde',
            'Educação' => 'Educação',
            'Segurança' => 'Segurança',
            'Outros' => 'Outros'
        ];
    }
    
    /**
     * Obter unidades disponíveis
     */
    private function getUnidades() {
        return [
            'Gabinete' => 'Gabinete',
            'Secretaria de Administração' => 'Secretaria de Administração',
            'Secretaria de Finanças' => 'Secretaria de Finanças',
            'Secretaria de Obras' => 'Secretaria de Obras',
            'Secretaria de Saúde' => 'Secretaria de Saúde',
            'Secretaria de Educação' => 'Secretaria de Educação',
            'Secretaria de Meio Ambiente' => 'Secretaria de Meio Ambiente',
            'Procuradoria Jurídica' => 'Procuradoria Jurídica',
            'Controladoria' => 'Controladoria',
            'Ouvidoria' => 'Ouvidoria'
        ];
    }
}

/**
 * Serviço de Email (implementação básica)
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