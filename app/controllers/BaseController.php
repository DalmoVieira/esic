<?php

/**
 * Sistema E-SIC - Controller Base
 * 
 * Classe base para todos os controllers com funcionalidades comuns
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

abstract class BaseController {
    
    protected $db;
    protected $auth;
    protected $data = [];
    protected $title = 'Sistema E-SIC';
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->auth = Auth::getInstance();
        $this->initializeData();
    }
    
    /**
     * Inicializar dados comuns
     */
    protected function initializeData() {
        $this->data = [
            'title' => $this->title,
            'user' => $this->auth->user(),
            'isLoggedIn' => $this->auth->check(),
            'currentUrl' => $_SERVER['REQUEST_URI'],
            'baseUrl' => url(),
            'csrfToken' => $this->auth->generateCSRFToken(),
            'messages' => $this->getFlashMessages(),
            'config' => $this->getSystemConfig()
        ];
    }
    
    /**
     * Renderizar view
     */
    protected function render($view, $data = []) {
        // Mesclar dados
        $viewData = array_merge($this->data, $data);
        
        // Extrair variáveis para o escopo da view
        extract($viewData);
        
        // Determinar caminho da view
        $viewPath = $this->getViewPath($view);
        
        if (!file_exists($viewPath)) {
            throw new Exception("View não encontrada: {$view}");
        }
        
        // Renderizar
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        return $content;
    }
    
    /**
     * Renderizar view com layout
     */
    protected function renderWithLayout($view, $data = [], $layout = 'main') {
        // Renderizar conteúdo da view
        $content = $this->render($view, $data);
        
        // Renderizar layout com conteúdo
        return $this->render("layouts/{$layout}", array_merge($data, ['content' => $content]));
    }
    
    /**
     * Renderizar view administrativa
     */
    protected function renderAdmin($view, $data = [], $layout = 'admin') {
        // Verificar autenticação
        AuthMiddleware::requireAuth();
        
        // Renderizar conteúdo da view admin
        $content = $this->render("admin/{$view}", $data);
        
        // Renderizar layout admin com conteúdo
        return $this->render("layouts/{$layout}", array_merge($data, ['content' => $content]));
    }
    
    /**
     * Retornar resposta JSON
     */
    protected function json($data, $status = 200) {
        return Response::json($data, $status);
    }
    
    /**
     * Redirecionar
     */
    protected function redirect($url, $message = null, $type = 'info') {
        if ($message) {
            $this->setFlashMessage($message, $type);
        }
        
        return Response::redirect(url($url));
    }
    
    /**
     * Validar dados de entrada
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $fieldRules);
            
            foreach ($rulesArray as $rule) {
                $ruleName = $rule;
                $ruleValue = null;
                
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $ruleValue) = explode(':', $rule, 2);
                }
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = "O campo {$field} é obrigatório.";
                        }
                        break;
                    
                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "O campo {$field} deve ser um email válido.";
                        }
                        break;
                    
                    case 'min':
                        if ($value && strlen($value) < $ruleValue) {
                            $errors[$field][] = "O campo {$field} deve ter pelo menos {$ruleValue} caracteres.";
                        }
                        break;
                    
                    case 'max':
                        if ($value && strlen($value) > $ruleValue) {
                            $errors[$field][] = "O campo {$field} deve ter no máximo {$ruleValue} caracteres.";
                        }
                        break;
                    
                    case 'numeric':
                        if ($value && !is_numeric($value)) {
                            $errors[$field][] = "O campo {$field} deve ser numérico.";
                        }
                        break;
                }
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Erro de validação", $errors);
        }
        
        return true;
    }
    
    /**
     * Definir mensagem flash
     */
    protected function setFlashMessage($message, $type = 'info') {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        
        $_SESSION['flash_messages'][] = [
            'message' => $message,
            'type' => $type,
            'timestamp' => time()
        ];
    }
    
    /**
     * Obter mensagens flash
     */
    protected function getFlashMessages() {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
    
    /**
     * Upload de arquivo
     */
    protected function uploadFile($file, $allowedTypes = null, $maxSize = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erro no upload do arquivo");
        }
        
        // Configurações padrão
        $allowedTypes = $allowedTypes ?: ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt'];
        $maxSize = $maxSize ?: (10 * 1024 * 1024); // 10MB
        
        // Verificar tamanho
        if ($file['size'] > $maxSize) {
            throw new Exception("Arquivo muito grande. Tamanho máximo: " . formatBytes($maxSize));
        }
        
        // Verificar tipo
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            throw new Exception("Tipo de arquivo não permitido. Tipos aceitos: " . implode(', ', $allowedTypes));
        }
        
        // Gerar nome único
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
        
        // Criar diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . $fileName;
        
        // Mover arquivo
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception("Erro ao salvar arquivo");
        }
        
        return $fileName;
    }
    
    /**
     * Paginar resultados
     */
    protected function paginate($data, $page, $perPage, $total) {
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => (($page - 1) * $perPage) + 1,
                'to' => min($page * $perPage, $total),
                'has_pages' => $total > $perPage,
                'has_previous' => $page > 1,
                'has_next' => $page < ceil($total / $perPage),
                'previous_page' => $page > 1 ? $page - 1 : null,
                'next_page' => $page < ceil($total / $perPage) ? $page + 1 : null
            ]
        ];
    }
    
    /**
     * Obter configurações do sistema
     */
    protected function getSystemConfig() {
        static $config = null;
        
        if ($config === null) {
            try {
                $configRows = $this->db->select("SELECT chave, valor, tipo FROM configuracoes");
                $config = [];
                
                foreach ($configRows as $row) {
                    $value = $row['valor'];
                    
                    // Converter tipo
                    switch ($row['tipo']) {
                        case 'boolean':
                            $value = in_array(strtolower($value), ['true', '1', 'on', 'yes']);
                            break;
                        case 'number':
                            $value = is_numeric($value) ? (float) $value : 0;
                            break;
                        case 'json':
                            $value = json_decode($value, true) ?: [];
                            break;
                    }
                    
                    $config[$row['chave']] = $value;
                }
            } catch (Exception $e) {
                $config = [];
            }
        }
        
        return $config;
    }
    
    /**
     * Obter path da view
     */
    protected function getViewPath($view) {
        $basePath = dirname(__DIR__) . '/views/';
        
        // Remover extensão se fornecida
        $view = str_replace('.php', '', $view);
        
        return $basePath . $view . '.php';
    }
    
    /**
     * Sanitizar entrada HTML
     */
    protected function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Verificar se requisição é POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verificar se requisição é AJAX
     */
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Obter dados POST sanitizados
     */
    protected function getPost($key = null, $default = null) {
        $data = $_POST;
        
        if ($key === null) {
            return $this->sanitize($data);
        }
        
        return $this->sanitize($data[$key] ?? $default);
    }
    
    /**
     * Obter dados GET sanitizados
     */
    protected function getGet($key = null, $default = null) {
        $data = $_GET;
        
        if ($key === null) {
            return $this->sanitize($data);
        }
        
        return $this->sanitize($data[$key] ?? $default);
    }
    
    /**
     * Log de ação do usuário
     */
    protected function logAction($action, $details = null) {
        try {
            $user = $this->auth->user();
            
            $this->db->execute(
                "INSERT INTO historico_pedidos (usuario_id, acao, observacoes, created_at) VALUES (?, ?, ?, NOW())",
                [
                    $user['id'] ?? null,
                    $action,
                    $details
                ]
            );
        } catch (Exception $e) {
            // Log silencioso - não quebrar a aplicação
            error_log("Erro ao registrar ação: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar permissões
     */
    protected function checkPermission($permission) {
        if (!$this->auth->check()) {
            throw new Exception("Usuário não autenticado");
        }
        
        $user = $this->auth->user();
        
        // Administradores têm acesso total
        if ($user['nivel_acesso'] === 'admin') {
            return true;
        }
        
        // Verificar permissões específicas baseadas no nível
        switch ($permission) {
            case 'gerenciar_usuarios':
                return $user['nivel_acesso'] === 'admin';
            case 'gerenciar_configuracoes':
                return $user['nivel_acesso'] === 'admin';
            case 'responder_pedidos':
                return in_array($user['nivel_acesso'], ['admin', 'operador', 'gestor']);
            case 'visualizar_relatorios':
                return in_array($user['nivel_acesso'], ['admin', 'gestor']);
            default:
                return false;
        }
    }
    
    /**
     * Middleware de verificação CSRF
     */
    protected function verifyCsrf() {
        if ($this->isPost()) {
            AuthMiddleware::verifyCSRF();
        }
    }
    
    /**
     * Requerer autenticação
     */
    protected function requireAuth() {
        if (!$this->auth->check()) {
            $this->redirect('/auth/login');
            exit;
        }
    }
    
    /**
     * Requerer roles específicos
     */
    protected function requireRole($roles) {
        if (!$this->auth->check()) {
            $this->redirect('/auth/login');
            exit;
        }
        
        $user = $this->auth->user();
        $userRole = $user['tipo'] ?? 'cidadao';
        
        if (!in_array($userRole, (array)$roles)) {
            $this->error('Acesso negado', 403);
            exit;
        }
    }
    
    /**
     * Exibir mensagem de erro
     */
    protected function error($message, $code = 500) {
        $this->setFlashMessage($message, 'error');
        
        if ($this->isAjax()) {
            return $this->json(['error' => $message], $code);
        }
        
        // Para requisições normais, redirecionar ou renderizar página de erro
        if ($code === 404) {
            $this->render('errors/404', ['message' => $message]);
        } else {
            $_SESSION['error_message'] = $message;
            if (isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                header('Location: /');
            }
        }
        exit;
    }
    
    /**
     * Exibir mensagem de sucesso
     */
    protected function success($message) {
        $this->setFlashMessage($message, 'success');
        
        if ($this->isAjax()) {
            return $this->json(['success' => $message]);
        }
    }
    
    /**
     * Obter filtros da requisição
     */
    protected function getFilters() {
        $filters = [];
        
        // Status
        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        
        // Data inicial
        if (!empty($_GET['data_inicio'])) {
            $filters['data_inicio'] = $_GET['data_inicio'];
        }
        
        // Data final
        if (!empty($_GET['data_fim'])) {
            $filters['data_fim'] = $_GET['data_fim'];
        }
        
        // Busca por texto
        if (!empty($_GET['q'])) {
            $filters['q'] = $_GET['q'];
        }
        
        // Ordenação
        if (!empty($_GET['order_by'])) {
            $filters['order_by'] = $_GET['order_by'];
        }
        
        if (!empty($_GET['order_dir'])) {
            $filters['order_dir'] = $_GET['order_dir'];
        }
        
        return $filters;
    }
    
    /**
     * Calcular paginação
     */
    protected function calculatePagination($total, $currentPage, $perPage) {
        $totalPages = ceil($total / $perPage);
        
        return [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total_items' => $total,
            'total_pages' => $totalPages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage > 1 ? $currentPage - 1 : null,
            'next_page' => $currentPage < $totalPages ? $currentPage + 1 : null,
            'from' => (($currentPage - 1) * $perPage) + 1,
            'to' => min($currentPage * $perPage, $total)
        ];
    }
    
    /**
     * Processar upload de múltiplos arquivos
     */
    protected function handleFileUploads($files, $folder = 'general') {
        $uploadedFiles = [];
        
        if (empty($files['name'][0])) {
            return $uploadedFiles;
        }
        
        $uploadDir = dirname(__DIR__, 2) . "/public/uploads/{$folder}/";
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $originalName = $files['name'][$i];
                $tmpName = $files['tmp_name'][$i];
                $fileSize = $files['size'][$i];
                
                // Validar arquivo
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
                
                if (!in_array($extension, $allowedExtensions)) {
                    continue; // Pular arquivo inválido
                }
                
                if ($fileSize > 10 * 1024 * 1024) { // 10MB
                    continue; // Pular arquivo muito grande
                }
                
                // Gerar nome único
                $fileName = uniqid() . '_' . time() . '.' . $extension;
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    $uploadedFiles[] = [
                        'original_name' => $originalName,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_size' => $fileSize,
                        'upload_date' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Acessar propriedades do usuário logado
     */
    public function __get($property) {
        if ($property === 'user') {
            return $this->auth->user();
        }
        
        return null;
    }
}