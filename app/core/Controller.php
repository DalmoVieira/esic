<?php

namespace App\Core;

abstract class Controller
{
    protected $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->startSession();
        $this->generateCSRFToken();
    }
    
    /**
     * Render a view
     */
    protected function view($view, $data = [])
    {
        // Make data available to view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View not found: {$view}");
        }
        
        // Get the content
        $content = ob_get_clean();
        
        // Check if we need a layout
        if (isset($data['layout']) && $data['layout'] === false) {
            echo $content;
        } else {
            // Use default layout
            $layout = $data['layout'] ?? 'main';
            $this->renderLayout($layout, $content, $data);
        }
    }
    
    /**
     * Render layout with content
     */
    private function renderLayout($layout, $content, $data = [])
    {
        extract($data);
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content; // Fallback to content only
        }
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url, $statusCode = 302)
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCSRF()
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    /**
     * Generate CSRF token
     */
    private function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    
    /**
     * Start session if not already started
     */
    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Get current user
     */
    protected function getCurrentUser()
    {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/auth/login');
        }
    }
    
    /**
     * Check user permission
     */
    protected function hasPermission($permission)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // Check specific permissions
        $permissions = $user['permissions'] ?? [];
        return in_array($permission, $permissions);
    }
    
    /**
     * Require specific permission
     */
    protected function requirePermission($permission)
    {
        if (!$this->hasPermission($permission)) {
            $this->json(['error' => 'Permission denied'], 403);
        }
    }
    
    /**
     * Sanitize input data
     */
    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate file upload
     */
    protected function validateUpload($file, $options = [])
    {
        $maxSize = $options['max_size'] ?? 5 * 1024 * 1024; // 5MB default
        $allowedTypes = $options['allowed_types'] ?? ['jpg', 'jpeg', 'png', 'pdf'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Upload error: ' . $file['error']);
        }
        
        if ($file['size'] > $maxSize) {
            throw new \Exception('File too large');
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            throw new \Exception('File type not allowed');
        }
        
        return true;
    }
    
    /**
     * Log activity
     */
    protected function logActivity($action, $details = null)
    {
        $user = $this->getCurrentUser();
        $log = [
            'user_id' => $user['id'] ?? null,
            'action' => $action,
            'details' => $details,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Save to database or file
        $logFile = LOG_PATH . '/activity_' . date('Y-m') . '.log';
        file_put_contents($logFile, json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get flash messages
     */
    protected function getFlashMessages()
    {
        $messages = [];
        
        foreach (['success', 'error', 'warning', 'info'] as $type) {
            $key = "flash_{$type}";
            if (isset($_SESSION[$key])) {
                $messages[$type] = $_SESSION[$key];
                unset($_SESSION[$key]);
            }
        }
        
        return $messages;
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($type, $message)
    {
        $_SESSION["flash_{$type}"] = $message;
    }
    
    /**
     * Paginate results
     */
    protected function paginate($query, $page = 1, $perPage = 20)
    {
        $page = max(1, (int) $page);
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $countQuery = preg_replace('/SELECT.*?FROM/i', 'SELECT COUNT(*) as total FROM', $query);
        $total = $this->db->query($countQuery)->fetch()['total'];
        
        // Add limit to original query
        $query .= " LIMIT {$offset}, {$perPage}";
        $results = $this->db->query($query)->fetchAll();
        
        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
            'has_more' => $page < ceil($total / $perPage)
        ];
    }
}