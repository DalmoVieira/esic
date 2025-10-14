<?php

namespace App\Utils;

class FileUpload
{
    private $uploadPath;
    private $maxSize;
    private $allowedTypes;
    private $errors = [];
    
    public function __construct($config = [])
    {
        $this->uploadPath = $config['upload_path'] ?? UPLOAD_PATH;
        $this->maxSize = $config['max_size'] ?? MAX_UPLOAD_SIZE;
        $this->allowedTypes = $config['allowed_types'] ?? ALLOWED_FILE_TYPES;
    }
    
    /**
     * Process single file upload
     */
    public function upload($file, $options = [])
    {
        try {
            // Merge options with defaults
            $uploadPath = $options['upload_path'] ?? $this->uploadPath;
            $maxSize = $options['max_size'] ?? $this->maxSize;
            $allowedTypes = $options['allowed_types'] ?? $this->allowedTypes;
            $prefix = $options['prefix'] ?? '';
            
            // Validate file
            $this->validateFile($file, $maxSize, $allowedTypes);
            
            // Generate unique filename
            $originalName = $file['name'];
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $filename = $prefix . uniqid() . '_' . time() . '.' . $extension;
            $filePath = $uploadPath . '/' . $filename;
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception('Falha ao mover o arquivo');
            }
            
            // Return file info
            return [
                'original_name' => $originalName,
                'filename' => $filename,
                'path' => $filePath,
                'size' => $file['size'],
                'type' => $file['type'],
                'extension' => $extension,
                'url' => $this->getFileUrl($filename)
            ];
            
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Process multiple file uploads
     */
    public function processMultiple($files, $options = [])
    {
        $results = [];
        
        // Normalize files array structure
        $normalizedFiles = $this->normalizeFilesArray($files);
        
        foreach ($normalizedFiles as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $result = $this->upload($file, $options);
                if ($result) {
                    $results[] = $result;
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($file, $maxSize, $allowedTypes)
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception($this->getUploadErrorMessage($file['error']));
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $maxSizeMB = round($maxSize / 1024 / 1024, 2);
            throw new \Exception("Arquivo muito grande. Tamanho máximo: {$maxSizeMB}MB");
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            throw new \Exception("Tipo de arquivo não permitido. Tipos aceitos: " . implode(', ', $allowedTypes));
        }
        
        // Check MIME type for security
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = $this->getAllowedMimes($allowedTypes);
        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception("Tipo MIME não permitido: {$mimeType}");
        }
        
        // Additional security checks
        $this->performSecurityChecks($file);
    }
    
    /**
     * Perform additional security checks
     */
    private function performSecurityChecks($file)
    {
        // Check for executable files
        $executable = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'asp', 'aspx'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($extension, $executable)) {
            throw new \Exception("Tipo de arquivo executável não permitido");
        }
        
        // Check file content for malicious patterns
        $content = file_get_contents($file['tmp_name']);
        $maliciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new \Exception("Arquivo contém código potencialmente malicioso");
            }
        }
    }
    
    /**
     * Get allowed MIME types for extensions
     */
    private function getAllowedMimes($extensions)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed'
        ];
        
        $allowed = [];
        foreach ($extensions as $ext) {
            if (isset($mimeTypes[$ext])) {
                $allowed[] = $mimeTypes[$ext];
            }
        }
        
        return $allowed;
    }
    
    /**
     * Normalize files array for multiple uploads
     */
    private function normalizeFilesArray($files)
    {
        $normalized = [];
        
        if (is_array($files['name'])) {
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                $normalized[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
            }
        } else {
            $normalized[] = $files;
        }
        
        return $normalized;
    }
    
    /**
     * Get upload error message
     */
    private function getUploadErrorMessage($errorCode)
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Arquivo excede o tamanho máximo permitido pelo servidor',
            UPLOAD_ERR_FORM_SIZE => 'Arquivo excede o tamanho máximo permitido pelo formulário',
            UPLOAD_ERR_PARTIAL => 'Upload foi interrompido',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado',
            UPLOAD_ERR_NO_TMP_DIR => 'Diretório temporário não encontrado',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo no disco',
            UPLOAD_ERR_EXTENSION => 'Upload foi bloqueado por extensão'
        ];
        
        return $messages[$errorCode] ?? 'Erro desconhecido no upload';
    }
    
    /**
     * Delete uploaded file
     */
    public function delete($filePath)
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    /**
     * Get file URL
     */
    private function getFileUrl($filename)
    {
        return '/uploads/' . $filename;
    }
    
    /**
     * Get upload errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Check if file exists
     */
    public function exists($filePath)
    {
        return file_exists($filePath);
    }
    
    /**
     * Get file size in human readable format
     */
    public function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Generate thumbnail for images
     */
    public function generateThumbnail($imagePath, $width = 150, $height = 150)
    {
        try {
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                return false;
            }
            
            // Create image resource
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $source = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $source = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($imagePath);
                    break;
                default:
                    return false;
            }
            
            if (!$source) {
                return false;
            }
            
            // Get original dimensions
            $originalWidth = imagesx($source);
            $originalHeight = imagesy($source);
            
            // Calculate new dimensions maintaining aspect ratio
            $ratio = min($width / $originalWidth, $height / $originalHeight);
            $newWidth = (int) ($originalWidth * $ratio);
            $newHeight = (int) ($originalHeight * $ratio);
            
            // Create thumbnail
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG and GIF
            if ($extension === 'png' || $extension === 'gif') {
                imagecolortransparent($thumbnail, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
            
            // Resize image
            imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            
            // Generate thumbnail filename
            $thumbnailPath = dirname($imagePath) . '/thumb_' . basename($imagePath);
            
            // Save thumbnail
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $result = imagejpeg($thumbnail, $thumbnailPath, 90);
                    break;
                case 'png':
                    $result = imagepng($thumbnail, $thumbnailPath, 9);
                    break;
                case 'gif':
                    $result = imagegif($thumbnail, $thumbnailPath);
                    break;
                default:
                    $result = false;
            }
            
            // Clean up
            imagedestroy($source);
            imagedestroy($thumbnail);
            
            return $result ? $thumbnailPath : false;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}