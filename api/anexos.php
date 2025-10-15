<?php
header('Content-Type: application/json');
require_once '../app/config/Database.php';

// Permitir CORS para desenvolvimento
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Configurações de upload
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'odt', 'xls', 'xlsx']);
define('ALLOWED_MIMETYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png',
    'text/plain',
    'application/vnd.oasis.opendocument.text',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
]);

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $action = $_REQUEST['action'] ?? '';
    
    switch ($action) {
        case 'upload':
            uploadAnexo($pdo);
            break;
            
        case 'listar':
            listarAnexos($pdo);
            break;
            
        case 'download':
            downloadAnexo($pdo);
            break;
            
        case 'deletar':
            deletarAnexo($pdo);
            break;
            
        case 'validar':
            validarArquivo();
            break;
            
        default:
            throw new Exception('Ação não especificada ou inválida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Upload de anexo
 */
function uploadAnexo($pdo) {
    // Validar se há arquivo
    if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Nenhum arquivo foi enviado ou houve erro no upload');
    }
    
    $arquivo = $_FILES['arquivo'];
    $tipo_entidade = $_POST['tipo_entidade'] ?? null; // 'pedido' ou 'recurso'
    $entidade_id = $_POST['entidade_id'] ?? null;
    $descricao = $_POST['descricao'] ?? '';
    
    // Validações
    if (!$tipo_entidade || !$entidade_id) {
        throw new Exception('Tipo de entidade e ID são obrigatórios');
    }
    
    if (!in_array($tipo_entidade, ['pedido', 'recurso'])) {
        throw new Exception('Tipo de entidade inválido');
    }
    
    // Validar tamanho do arquivo
    if ($arquivo['size'] > MAX_FILE_SIZE) {
        throw new Exception('Arquivo excede o tamanho máximo de 10MB');
    }
    
    // Validar extensão
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, ALLOWED_EXTENSIONS)) {
        throw new Exception('Extensão de arquivo não permitida. Permitidas: ' . implode(', ', ALLOWED_EXTENSIONS));
    }
    
    // Validar MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_MIMETYPES)) {
        throw new Exception('Tipo de arquivo não permitido');
    }
    
    // Verificar se entidade existe
    if ($tipo_entidade === 'pedido') {
        $stmt = $pdo->prepare("SELECT id FROM pedidos WHERE id = :id");
    } else {
        $stmt = $pdo->prepare("SELECT id FROM recursos WHERE id = :id");
    }
    $stmt->execute([':id' => $entidade_id]);
    if (!$stmt->fetch()) {
        throw new Exception(ucfirst($tipo_entidade) . ' não encontrado');
    }
    
    // Criar diretório de upload se não existir
    $upload_path = UPLOAD_DIR;
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    // Gerar nome único para o arquivo
    $nome_original = $arquivo['name'];
    $nome_hash = bin2hex(random_bytes(16));
    $nome_arquivo = $nome_hash . '.' . $extensao;
    $caminho_completo = $upload_path . $nome_arquivo;
    
    // Mover arquivo
    if (!move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
        throw new Exception('Erro ao salvar arquivo no servidor');
    }
    
    // Registrar no banco de dados
    $pdo->beginTransaction();
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO anexos (
                tipo_entidade,
                entidade_id,
                nome_original,
                nome_arquivo,
                caminho,
                tamanho,
                tipo_mime,
                descricao,
                data_upload
            ) VALUES (
                :tipo_entidade,
                :entidade_id,
                :nome_original,
                :nome_arquivo,
                :caminho,
                :tamanho,
                :tipo_mime,
                :descricao,
                NOW()
            )
        ");
        
        $stmt->execute([
            ':tipo_entidade' => $tipo_entidade,
            ':entidade_id' => $entidade_id,
            ':nome_original' => $nome_original,
            ':nome_arquivo' => $nome_arquivo,
            ':caminho' => $caminho_completo,
            ':tamanho' => $arquivo['size'],
            ':tipo_mime' => $mime_type,
            ':descricao' => $descricao
        ]);
        
        $anexo_id = $pdo->lastInsertId();
        
        // Registrar tramitação
        if ($tipo_entidade === 'pedido') {
            $stmt = $pdo->prepare("
                INSERT INTO tramitacoes (pedido_id, descricao, usuario_id, data_tramitacao)
                VALUES (:entidade_id, :descricao, 1, NOW())
            ");
            
            $stmt->execute([
                ':entidade_id' => $entidade_id,
                ':descricao' => "Anexo adicionado: {$nome_original}"
            ]);
        }
        
        // Log do sistema
        $stmt = $pdo->prepare("
            INSERT INTO logs_sistema (usuario_id, acao, detalhes, data_log)
            VALUES (1, 'upload_anexo', :detalhes, NOW())
        ");
        
        $detalhes = json_encode([
            'anexo_id' => $anexo_id,
            'tipo_entidade' => $tipo_entidade,
            'entidade_id' => $entidade_id,
            'nome_arquivo' => $nome_original,
            'tamanho' => $arquivo['size']
        ]);
        
        $stmt->execute([':detalhes' => $detalhes]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Arquivo enviado com sucesso',
            'data' => [
                'anexo_id' => $anexo_id,
                'nome_original' => $nome_original,
                'tamanho' => $arquivo['size'],
                'tipo_mime' => $mime_type
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        // Remover arquivo se houver erro no banco
        if (file_exists($caminho_completo)) {
            unlink($caminho_completo);
        }
        throw $e;
    }
}

/**
 * Listar anexos de uma entidade
 */
function listarAnexos($pdo) {
    $tipo_entidade = $_GET['tipo_entidade'] ?? null;
    $entidade_id = $_GET['entidade_id'] ?? null;
    
    if (!$tipo_entidade || !$entidade_id) {
        throw new Exception('Tipo de entidade e ID são obrigatórios');
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            id,
            nome_original,
            nome_arquivo,
            tamanho,
            tipo_mime,
            descricao,
            data_upload,
            ROUND(tamanho / 1024, 2) as tamanho_kb,
            ROUND(tamanho / 1024 / 1024, 2) as tamanho_mb
        FROM anexos
        WHERE tipo_entidade = :tipo_entidade
        AND entidade_id = :entidade_id
        ORDER BY data_upload DESC
    ");
    
    $stmt->execute([
        ':tipo_entidade' => $tipo_entidade,
        ':entidade_id' => $entidade_id
    ]);
    
    $anexos = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $anexos
    ]);
}

/**
 * Download de anexo
 */
function downloadAnexo($pdo) {
    $anexo_id = $_GET['anexo_id'] ?? null;
    
    if (!$anexo_id) {
        throw new Exception('ID do anexo é obrigatório');
    }
    
    // Buscar anexo
    $stmt = $pdo->prepare("SELECT * FROM anexos WHERE id = :id");
    $stmt->execute([':id' => $anexo_id]);
    $anexo = $stmt->fetch();
    
    if (!$anexo) {
        throw new Exception('Anexo não encontrado');
    }
    
    // Verificar se arquivo existe
    if (!file_exists($anexo['caminho'])) {
        throw new Exception('Arquivo não encontrado no servidor');
    }
    
    // Registrar log de download
    $stmt = $pdo->prepare("
        INSERT INTO logs_sistema (usuario_id, acao, detalhes, data_log)
        VALUES (1, 'download_anexo', :detalhes, NOW())
    ");
    
    $detalhes = json_encode([
        'anexo_id' => $anexo_id,
        'nome_arquivo' => $anexo['nome_original']
    ]);
    
    $stmt->execute([':detalhes' => $detalhes]);
    
    // Headers para download
    header('Content-Type: ' . $anexo['tipo_mime']);
    header('Content-Disposition: attachment; filename="' . $anexo['nome_original'] . '"');
    header('Content-Length: ' . $anexo['tamanho']);
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    // Enviar arquivo
    readfile($anexo['caminho']);
    exit;
}

/**
 * Deletar anexo
 */
function deletarAnexo($pdo) {
    $anexo_id = $_POST['anexo_id'] ?? null;
    
    if (!$anexo_id) {
        throw new Exception('ID do anexo é obrigatório');
    }
    
    $pdo->beginTransaction();
    
    try {
        // Buscar anexo
        $stmt = $pdo->prepare("SELECT * FROM anexos WHERE id = :id");
        $stmt->execute([':id' => $anexo_id]);
        $anexo = $stmt->fetch();
        
        if (!$anexo) {
            throw new Exception('Anexo não encontrado');
        }
        
        // Deletar do banco
        $stmt = $pdo->prepare("DELETE FROM anexos WHERE id = :id");
        $stmt->execute([':id' => $anexo_id]);
        
        // Deletar arquivo físico
        if (file_exists($anexo['caminho'])) {
            unlink($anexo['caminho']);
        }
        
        // Registrar tramitação
        if ($anexo['tipo_entidade'] === 'pedido') {
            $stmt = $pdo->prepare("
                INSERT INTO tramitacoes (pedido_id, descricao, usuario_id, data_tramitacao)
                VALUES (:entidade_id, :descricao, 1, NOW())
            ");
            
            $stmt->execute([
                ':entidade_id' => $anexo['entidade_id'],
                ':descricao' => "Anexo removido: {$anexo['nome_original']}"
            ]);
        }
        
        // Log do sistema
        $stmt = $pdo->prepare("
            INSERT INTO logs_sistema (usuario_id, acao, detalhes, data_log)
            VALUES (1, 'deletar_anexo', :detalhes, NOW())
        ");
        
        $detalhes = json_encode([
            'anexo_id' => $anexo_id,
            'tipo_entidade' => $anexo['tipo_entidade'],
            'entidade_id' => $anexo['entidade_id'],
            'nome_arquivo' => $anexo['nome_original']
        ]);
        
        $stmt->execute([':detalhes' => $detalhes]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Anexo deletado com sucesso'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Validar arquivo antes do upload
 */
function validarArquivo() {
    if (!isset($_FILES['arquivo'])) {
        throw new Exception('Nenhum arquivo foi enviado');
    }
    
    $arquivo = $_FILES['arquivo'];
    $validacoes = [];
    
    // Validar tamanho
    if ($arquivo['size'] > MAX_FILE_SIZE) {
        $validacoes['tamanho'] = false;
        $validacoes['tamanho_msg'] = 'Arquivo muito grande (máx: 10MB)';
    } else {
        $validacoes['tamanho'] = true;
    }
    
    // Validar extensão
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, ALLOWED_EXTENSIONS)) {
        $validacoes['extensao'] = false;
        $validacoes['extensao_msg'] = 'Extensão não permitida';
    } else {
        $validacoes['extensao'] = true;
    }
    
    // Validar MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_MIMETYPES)) {
        $validacoes['mime'] = false;
        $validacoes['mime_msg'] = 'Tipo de arquivo não permitido';
    } else {
        $validacoes['mime'] = true;
    }
    
    $validacoes['valido'] = $validacoes['tamanho'] && $validacoes['extensao'] && $validacoes['mime'];
    
    echo json_encode([
        'success' => true,
        'data' => $validacoes
    ]);
}

/**
 * Formatar tamanho de arquivo
 */
function formatarTamanho($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>