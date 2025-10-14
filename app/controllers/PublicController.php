<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Pedido;
use App\Models\Recurso;
use App\Models\Configuracao;
use App\Utils\Validator;
use App\Utils\EmailService;
use App\Utils\FileUpload;

class PublicController extends Controller
{
    private $pedidoModel;
    private $recursoModel;
    private $configModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->pedidoModel = new Pedido();
        $this->recursoModel = new Recurso();
        $this->configModel = new Configuracao();
    }
    
    /**
     * Página inicial do sistema
     */
    public function index()
    {
        $data = [
            'title' => 'Sistema E-SIC - Acesso à Informação',
            'config' => $this->configModel->getConfiguracoes(),
            'estatisticas' => $this->getEstatisticasPublicas(),
            'pedidos_recentes' => $this->pedidoModel->getRecentes(5),
            'informacoes_lai' => $this->getInformacoesLAI()
        ];
        
        $this->view('public/home', $data);
    }
    
    /**
     * Formulário para novo pedido
     */
    public function novoPedido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processarNovoPedido();
        }
        
        $data = [
            'title' => 'Novo Pedido de Informação',
            'config' => $this->configModel->getConfiguracoes(),
            'categorias' => $this->pedidoModel->getCategorias(),
            'orgaos' => $this->pedidoModel->getOrgaos()
        ];
        
        $this->view('public/novo-pedido', $data);
    }
    
    /**
     * Processar envio de novo pedido
     */
    private function processarNovoPedido()
    {
        try {
            // Validar CSRF
            if (!$this->validateCSRF()) {
                throw new \Exception('Token de segurança inválido');
            }
            
            // Validar dados
            $validator = new Validator($_POST);
            $validator->required([
                'nome_solicitante',
                'email_solicitante', 
                'cpf_solicitante',
                'descricao_pedido',
                'forma_recebimento'
            ]);
            
            $validator->email('email_solicitante');
            $validator->cpf('cpf_solicitante');
            $validator->maxLength('descricao_pedido', 2000);
            
            if (!$validator->isValid()) {
                throw new \Exception('Dados inválidos: ' . implode(', ', $validator->getErrors()));
            }
            
            // Processar upload de arquivos se houver
            $anexos = [];
            if (!empty($_FILES['anexos']['name'][0])) {
                $fileUpload = new FileUpload();
                $anexos = $fileUpload->processMultiple($_FILES['anexos'], [
                    'max_size' => 10 * 1024 * 1024, // 10MB
                    'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                    'upload_path' => UPLOAD_PATH . '/pedidos'
                ]);
            }
            
            // Preparar dados do pedido
            $dadosPedido = [
                'protocolo' => $this->gerarProtocolo(),
                'nome_solicitante' => trim($_POST['nome_solicitante']),
                'email_solicitante' => trim($_POST['email_solicitante']),
                'cpf_solicitante' => preg_replace('/\D/', '', $_POST['cpf_solicitante']),
                'telefone_solicitante' => $_POST['telefone_solicitante'] ?? null,
                'endereco_solicitante' => $_POST['endereco_solicitante'] ?? null,
                'categoria_id' => $_POST['categoria_id'] ?? 1,
                'orgao_id' => $_POST['orgao_id'] ?? 1,
                'descricao_pedido' => trim($_POST['descricao_pedido']),
                'justificativa' => $_POST['justificativa'] ?? null,
                'forma_recebimento' => $_POST['forma_recebimento'],
                'endereco_postal' => $_POST['endereco_postal'] ?? null,
                'tipo_pessoa' => $_POST['tipo_pessoa'] ?? 'fisica',
                'anexos' => json_encode($anexos),
                'ip_solicitante' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'status' => 'recebido',
                'data_criacao' => date('Y-m-d H:i:s'),
                'prazo_atendimento' => $this->calcularPrazoAtendimento()
            ];
            
            // Salvar pedido
            $pedidoId = $this->pedidoModel->create($dadosPedido);
            
            if (!$pedidoId) {
                throw new \Exception('Erro ao salvar pedido');
            }
            
            // Enviar email de confirmação
            $this->enviarEmailConfirmacao($dadosPedido);
            
            // Log da atividade
            $this->logPedidoActivity('novo_pedido', $pedidoId, $dadosPedido['protocolo']);
            
            // Redirecionar com sucesso
            $_SESSION['flash_success'] = "Pedido enviado com sucesso! Protocolo: {$dadosPedido['protocolo']}";
            $this->redirect('/pedido/' . $dadosPedido['protocolo']);
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('/novo-pedido');
        }
    }
    
    /**
     * Acompanhar pedidos
     */
    public function acompanhar()
    {
        $data = [
            'title' => 'Acompanhar Pedido',
            'config' => $this->configModel->getConfiguracoes()
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['protocolo'])) {
            $protocolo = trim($_POST['protocolo']);
            $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
            
            $pedido = $this->pedidoModel->getByProtocolo($protocolo, $cpf);
            
            if ($pedido) {
                $data['pedido'] = $pedido;
                $data['historico'] = $this->pedidoModel->getHistorico($pedido['id']);
                $data['recursos'] = $this->recursoModel->getByPedidoId($pedido['id']);
            } else {
                $data['erro'] = 'Pedido não encontrado. Verifique o protocolo e CPF.';
            }
        }
        
        $this->view('public/acompanhar', $data);
    }
    
    /**
     * Detalhes do pedido
     */
    public function pedidoDetalhes($protocolo)
    {
        $cpf = $_GET['cpf'] ?? '';
        $pedido = $this->pedidoModel->getByProtocolo($protocolo, $cpf);
        
        if (!$pedido) {
            $_SESSION['flash_error'] = 'Pedido não encontrado';
            $this->redirect('/acompanhar');
            return;
        }
        
        $data = [
            'title' => "Pedido {$protocolo}",
            'pedido' => $pedido,
            'historico' => $this->pedidoModel->getHistorico($pedido['id']),
            'recursos' => $this->recursoModel->getByPedidoId($pedido['id']),
            'anexos' => json_decode($pedido['anexos'] ?? '[]', true),
            'config' => $this->configModel->getConfiguracoes()
        ];
        
        $this->view('public/pedido-detalhes', $data);
    }
    
    /**
     * Formulário de recurso
     */
    public function recurso($pedidoId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processarRecurso();
        }
        
        $pedido = null;
        if ($pedidoId) {
            $pedido = $this->pedidoModel->getById($pedidoId);
        }
        
        $data = [
            'title' => 'Interpor Recurso',
            'pedido' => $pedido,
            'config' => $this->configModel->getConfiguracoes(),
            'tipos_recurso' => $this->recursoModel->getTipos()
        ];
        
        $this->view('public/recurso-formulario', $data);
    }
    
    /**
     * Processar recurso
     */
    private function processarRecurso()
    {
        try {
            // Validar CSRF
            if (!$this->validateCSRF()) {
                throw new \Exception('Token de segurança inválido');
            }
            
            // Validar dados
            $validator = new Validator($_POST);
            $validator->required([
                'pedido_id',
                'tipo_recurso',
                'justificativa_recurso'
            ]);
            
            if (!$validator->isValid()) {
                throw new \Exception('Dados inválidos');
            }
            
            // Verificar se o pedido existe e permite recurso
            $pedido = $this->pedidoModel->getById($_POST['pedido_id']);
            if (!$pedido || !$this->podeInterporRecurso($pedido)) {
                throw new \Exception('Não é possível interpor recurso para este pedido');
            }
            
            // Preparar dados do recurso
            $dadosRecurso = [
                'pedido_id' => $_POST['pedido_id'],
                'protocolo' => $this->gerarProtocoloRecurso(),
                'tipo_recurso' => $_POST['tipo_recurso'],
                'justificativa' => trim($_POST['justificativa_recurso']),
                'instancia' => $this->getProximaInstancia($pedido['id']),
                'status' => 'aguardando_analise',
                'data_interposicao' => date('Y-m-d H:i:s'),
                'prazo_resposta' => $this->calcularPrazoRecurso(),
                'ip_solicitante' => $_SERVER['REMOTE_ADDR']
            ];
            
            // Salvar recurso
            $recursoId = $this->recursoModel->create($dadosRecurso);
            
            if (!$recursoId) {
                throw new \Exception('Erro ao salvar recurso');
            }
            
            // Atualizar status do pedido
            $this->pedidoModel->update($pedido['id'], [
                'status' => 'em_recurso',
                'data_ultima_atualizacao' => date('Y-m-d H:i:s')
            ]);
            
            // Registrar no histórico
            $this->pedidoModel->addHistorico($pedido['id'], [
                'acao' => 'recurso_interposto',
                'descricao' => "Recurso interposto - {$dadosRecurso['tipo_recurso']}",
                'usuario_id' => null,
                'data_acao' => date('Y-m-d H:i:s')
            ]);
            
            // Enviar email de confirmação
            $this->enviarEmailRecurso($pedido, $dadosRecurso);
            
            // Registrar log
            $this->logPedidoActivity('novo_recurso', $recursoId, $dadosRecurso['protocolo']);
            
            $_SESSION['flash_success'] = "Recurso interposto com sucesso! Protocolo: {$dadosRecurso['protocolo']}";
            $this->redirect('/pedido/' . $pedido['protocolo']);
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('/recurso');
        }
    }
    
    /**
     * Página sobre a LAI
     */
    public function lai()
    {
        $data = [
            'title' => 'Lei de Acesso à Informação',
            'config' => $this->configModel->getConfiguracoes(),
            'estatisticas' => $this->getEstatisticasLAI()
        ];
        
        $this->view('public/lai', $data);
    }
    
    /**
     * Página sobre o órgão
     */
    public function sobre()
    {
        $data = [
            'title' => 'Sobre o Órgão',
            'config' => $this->configModel->getConfiguracoes(),
            'estrutura' => $this->getEstruturaOrganizacional()
        ];
        
        $this->view('public/sobre', $data);
    }
    
    /**
     * Portal da transparência
     */
    public function transparencia()
    {
        $data = [
            'title' => 'Portal da Transparência',
            'config' => $this->configModel->getConfiguracoes(),
            'dados_transparencia' => $this->getDadosTransparencia()
        ];
        
        $this->view('public/transparencia', $data);
    }
    
    /**
     * Download de arquivo público
     */
    public function download($arquivo)
    {
        try {
            $caminho = UPLOAD_PATH . '/public/' . basename($arquivo);
            
            if (!file_exists($caminho)) {
                throw new \Exception('Arquivo não encontrado');
            }
            
            // Verificar se é um arquivo permitido para download público
            $extensao = pathinfo($caminho, PATHINFO_EXTENSION);
            $extensoesPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
            
            if (!in_array(strtolower($extensao), $extensoesPermitidas)) {
                throw new \Exception('Tipo de arquivo não permitido');
            }
            
            // Fazer download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($arquivo) . '"');
            header('Content-Length: ' . filesize($caminho));
            readfile($caminho);
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('/');
        }
    }
    
    /**
     * API para busca de pedidos (AJAX)
     */
    public function buscarPedido()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }
        
        try {
            $protocolo = trim($_POST['protocolo'] ?? '');
            $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
            
            if (empty($protocolo) || empty($cpf)) {
                throw new \Exception('Protocolo e CPF são obrigatórios');
            }
            
            $pedido = $this->pedidoModel->getByProtocolo($protocolo, $cpf);
            
            if (!$pedido) {
                throw new \Exception('Pedido não encontrado');
            }
            
            echo json_encode([
                'success' => true,
                'pedido' => [
                    'protocolo' => $pedido['protocolo'],
                    'status' => $pedido['status'],
                    'data_criacao' => date('d/m/Y', strtotime($pedido['data_criacao'])),
                    'prazo_atendimento' => date('d/m/Y', strtotime($pedido['prazo_atendimento'])),
                    'descricao' => substr($pedido['descricao_pedido'], 0, 100) . '...'
                ]
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    // Métodos auxiliares privados
    
    private function gerarProtocolo()
    {
        $ano = date('Y');
        $contador = $this->pedidoModel->getContadorAno($ano);
        return sprintf('%s%06d', $ano, $contador + 1);
    }
    
    private function gerarProtocoloRecurso()
    {
        $ano = date('Y');
        $contador = $this->recursoModel->getContadorAno($ano);
        return sprintf('R%s%06d', $ano, $contador + 1);
    }
    
    private function calcularPrazoAtendimento()
    {
        $config = $this->configModel->getConfiguracoes();
        $prazo = $config['prazo_resposta'] ?? 20;
        return date('Y-m-d', strtotime("+{$prazo} weekdays"));
    }
    
    private function calcularPrazoRecurso()
    {
        $config = $this->configModel->getConfiguracoes();
        $prazo = $config['prazo_analise_recurso'] ?? 5;
        return date('Y-m-d', strtotime("+{$prazo} weekdays"));
    }
    
    private function podeInterporRecurso($pedido)
    {
        $statusPermitidos = ['negado', 'parcialmente_atendido', 'nao_respondido'];
        return in_array($pedido['status'], $statusPermitidos);
    }
    
    private function getProximaInstancia($pedidoId)
    {
        $recursos = $this->recursoModel->getByPedidoId($pedidoId);
        return count($recursos) + 1;
    }
    
    private function getEstatisticasPublicas()
    {
        return [
            'total_pedidos' => $this->pedidoModel->count(),
            'pedidos_atendidos' => $this->pedidoModel->countByStatus('atendido'),
            'pedidos_em_andamento' => $this->pedidoModel->countByStatus(['recebido', 'em_analise']),
            'tempo_medio_resposta' => $this->pedidoModel->getTempoMedioResposta()
        ];
    }
    
    private function getInformacoesLAI()
    {
        $config = $this->configModel->getConfiguracoes();
        return [
            'prazo_resposta' => $config['prazo_resposta'] ?? 20,
            'prazo_recurso' => $config['prazo_recurso'] ?? 10,
            'texto_informativo' => $config['texto_info_lai'] ?? ''
        ];
    }
    
    private function getEstatisticasLAI()
    {
        return [
            'pedidos_por_mes' => $this->pedidoModel->getPedidosPorMes(),
            'categorias_mais_solicitadas' => $this->pedidoModel->getCategoriasMaisSolicitadas(),
            'tempo_medio_por_categoria' => $this->pedidoModel->getTempoMedioPorCategoria(),
            'taxa_atendimento' => $this->pedidoModel->getTaxaAtendimento()
        ];
    }
    
    private function getEstruturaOrganizacional()
    {
        // Retornar estrutura organizacional do órgão
        return [
            'dirigentes' => [],
            'setores' => [],
            'competencias' => []
        ];
    }
    
    private function getDadosTransparencia()
    {
        // Retornar dados de transparência
        return [
            'receitas' => [],
            'despesas' => [],
            'contratos' => [],
            'servidores' => []
        ];
    }
    
    private function enviarEmailConfirmacao($dadosPedido)
    {
        try {
            $emailService = new EmailService();
            $config = $this->configModel->getConfiguracoes();
            
            $assunto = "E-SIC - Confirmação de Pedido #{$dadosPedido['protocolo']}";
            $mensagem = $this->renderEmailTemplate('confirmacao_pedido', [
                'nome' => $dadosPedido['nome_solicitante'],
                'protocolo' => $dadosPedido['protocolo'],
                'prazo' => date('d/m/Y', strtotime($dadosPedido['prazo_atendimento'])),
                'orgao' => $config['nome_orgao'] ?? 'Órgão'
            ]);
            
            $emailService->send($dadosPedido['email_solicitante'], $assunto, $mensagem);
            
        } catch (\Exception $e) {
            // Log do erro mas não interromper o processo
            error_log("Erro ao enviar email: " . $e->getMessage());
        }
    }
    
    private function enviarEmailRecurso($pedido, $dadosRecurso)
    {
        try {
            $emailService = new EmailService();
            $config = $this->configModel->getConfiguracoes();
            
            $assunto = "E-SIC - Confirmação de Recurso #{$dadosRecurso['protocolo']}";
            $mensagem = $this->renderEmailTemplate('confirmacao_recurso', [
                'nome' => $pedido['nome_solicitante'],
                'protocolo_pedido' => $pedido['protocolo'],
                'protocolo_recurso' => $dadosRecurso['protocolo'],
                'instancia' => $dadosRecurso['instancia'],
                'prazo' => date('d/m/Y', strtotime($dadosRecurso['prazo_resposta'])),
                'orgao' => $config['nome_orgao'] ?? 'Órgão'
            ]);
            
            $emailService->send($pedido['email_solicitante'], $assunto, $mensagem);
            
        } catch (\Exception $e) {
            error_log("Erro ao enviar email de recurso: " . $e->getMessage());
        }
    }
    
    private function renderEmailTemplate($template, $vars)
    {
        extract($vars);
        ob_start();
        include APP_PATH . "/views/emails/{$template}.php";
        return ob_get_clean();
    }
    
    private function logPedidoActivity($tipo, $id, $protocolo)
    {
        // Implementar log de atividades
        $log = [
            'tipo' => $tipo,
            'referencia_id' => $id,
            'protocolo' => $protocolo,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'data' => date('Y-m-d H:i:s')
        ];
        
        // Salvar no banco ou arquivo de log
        file_put_contents(
            LOG_PATH . '/activity.log',
            json_encode($log) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}