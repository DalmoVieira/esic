<?php
/**
 * Sistema E-SIC - API de Pedidos de Informação
 * 
 * Gerencia operações CRUD para pedidos de informação
 * 
 * @author Sistema E-SIC Rio Claro
 * @version 2.0
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tratar requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir dependências
require_once '../app/config/Database.php';

session_start();

// Função para validar CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) return false;
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;
    
    // Calcula primeiro dígito verificador
    for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--) {
        $soma += $cpf[$i] * $j;
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula segundo dígito verificador
    for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--) {
        $soma += $cpf[$i] * $j;
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    
    return ($cpf[9] == $dv1 && $cpf[10] == $dv2);
}

// Função para validar CNPJ
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    if (strlen($cnpj) != 14) return false;
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{13}/', $cnpj)) return false;
    
    // Calcula primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    
    return ($cnpj[12] == $dv1 && $cnpj[13] == $dv2);
}

// Função para calcular data limite
function calcularDataLimite($orgaoId = null, $prazoBase = 20) {
    try {
        $db = Database::getInstance();
        
        // Buscar prazo específico do órgão
        if ($orgaoId) {
            $orgao = $db->selectOne("SELECT prazo_resposta FROM orgaos_setores WHERE id = ?", [$orgaoId]);
            if ($orgao) {
                $prazoBase = $orgao['prazo_resposta'];
            }
        }
        
        // Calcular data limite (dias úteis)
        $dataLimite = new DateTime();
        $diasAdicionados = 0;
        
        while ($diasAdicionados < $prazoBase) {
            $dataLimite->add(new DateInterval('P1D'));
            
            // Se não for final de semana, conta como dia útil
            if ($dataLimite->format('N') < 6) { // 1-5 = segunda a sexta
                $diasAdicionados++;
            }
        }
        
        return $dataLimite->format('Y-m-d');
        
    } catch (Exception $e) {
        // Em caso de erro, usar prazo padrão
        return date('Y-m-d', strtotime("+{$prazoBase} days"));
    }
}

// Função para processar upload de anexos
function processarAnexos($pedidoId, $files) {
    $anexosProcessados = [];
    $uploadDir = '../uploads/pedidos/' . date('Y/m/');
    
    // Criar diretório se não existir
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Tipos de arquivo permitidos
    $tiposPermitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt'];
    $tamanhoMaximo = 10 * 1024 * 1024; // 10MB
    
    $db = Database::getInstance();
    
    foreach ($files['name'] as $index => $nomeOriginal) {
        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        // Validar tamanho
        if ($files['size'][$index] > $tamanhoMaximo) {
            throw new Exception("Arquivo '{$nomeOriginal}' excede o tamanho máximo de 10MB");
        }
        
        // Validar tipo
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        if (!in_array($extensao, $tiposPermitidos)) {
            throw new Exception("Tipo de arquivo não permitido: {$extensao}");
        }
        
        // Gerar nome único
        $nomeArquivo = uniqid() . '_' . time() . '.' . $extensao;
        $caminhoCompleto = $uploadDir . $nomeArquivo;
        
        // Mover arquivo
        if (move_uploaded_file($files['tmp_name'][$index], $caminhoCompleto)) {
            // Salvar no banco
            $dadosAnexo = [
                'pedido_id' => $pedidoId,
                'nome_original' => $nomeOriginal,
                'nome_arquivo' => $nomeArquivo,
                'mime_type' => $files['type'][$index],
                'tamanho' => $files['size'][$index],
                'hash_arquivo' => hash_file('sha256', $caminhoCompleto),
                'tipo' => in_array($extensao, ['jpg', 'jpeg', 'png']) ? 'imagem' : 'documento',
                'usuario_upload_id' => 1 // Temporário - usar ID do usuário logado
            ];
            
            $anexoId = $db->insert('anexos', $dadosAnexo);
            $anexosProcessados[] = $anexoId;
        }
    }
    
    return $anexosProcessados;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        
        case 'criar':
            // Validar dados obrigatórios
            $requiredFields = ['nome', 'email', 'cpf_cnpj', 'orgao_id', 'assunto', 'descricao', 'forma_recebimento'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Campo obrigatório não preenchido: {$field}");
                }
            }
            
            // Validar e-mail
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("E-mail inválido");
            }
            
            // Validar CPF/CNPJ
            $cpfCnpj = preg_replace('/[^0-9]/', '', $_POST['cpf_cnpj']);
            if (strlen($cpfCnpj) === 11) {
                if (!validarCPF($cpfCnpj)) {
                    throw new Exception("CPF inválido");
                }
                $tipoPessoa = 'fisica';
            } elseif (strlen($cpfCnpj) === 14) {
                if (!validarCNPJ($cpfCnpj)) {
                    throw new Exception("CNPJ inválido");
                }
                $tipoPessoa = 'juridica';
            } else {
                throw new Exception("CPF ou CNPJ inválido");
            }
            
            $pdo->beginTransaction();
            
            try {
                // Verificar/criar usuário
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE cpf_cnpj = ?");
                $stmt->execute([$cpfCnpj]);
                $usuario = $stmt->fetch();
                
                if (!$usuario) {
                    // Criar novo usuário
                    $stmt = $pdo->prepare("
                        INSERT INTO usuarios (nome, email, cpf_cnpj, telefone, tipo_pessoa, tipo_usuario, senha_hash, ativo, email_verificado)
                        VALUES (?, ?, ?, ?, ?, 'cidadao', ?, 1, 0)
                    ");
                    $stmt->execute([
                        $_POST['nome'],
                        $_POST['email'],
                        $cpfCnpj,
                        $_POST['telefone'] ?? null,
                        $tipoPessoa,
                        password_hash(substr($cpfCnpj, -6), PASSWORD_DEFAULT)
                    ]);
                    $usuarioId = $pdo->lastInsertId();
                } else {
                    $usuarioId = $usuario['id'];
                    
                    // Atualizar dados do usuário se necessário
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ?");
                    $stmt->execute([$_POST['nome'], $_POST['email'], $_POST['telefone'] ?? null, $usuarioId]);
                }
                
                // Gerar protocolo simples
                $ano = date('Y');
                $stmt = $pdo->prepare("SELECT COUNT(*) + 1 as proximo FROM pedidos WHERE YEAR(data_cadastro) = ?");
                $stmt->execute([$ano]);
                $resultado = $stmt->fetch();
                $protocolo = sprintf('P%d%06d', $ano, $resultado['proximo']);
                
                // Calcular data limite
                $dataLimite = calcularDataLimite($_POST['orgao_id']);
                
                // Criar pedido
                $stmt = $pdo->prepare("
                    INSERT INTO pedidos (protocolo, requerente_id, orgao_id, assunto, descricao, forma_recebimento, 
                                        status, prioridade, data_limite, informacao_classificada, grau_sigilo, ip_origem, user_agent)
                    VALUES (?, ?, ?, ?, ?, ?, 'aguardando', 'normal', ?, 0, 'publico', ?, ?)
                ");
                $stmt->execute([
                    $protocolo,
                    $usuarioId,
                    $_POST['orgao_id'],
                    $_POST['assunto'],
                    $_POST['descricao'],
                    $_POST['forma_recebimento'],
                    $dataLimite,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                ]);
                
                $pedidoId = $pdo->lastInsertId();
                
                // Criar tramitação inicial
                $stmt = $pdo->prepare("
                    INSERT INTO tramitacoes (pedido_id, status_novo, observacoes, usuario_id)
                    VALUES (?, 'aguardando', 'Pedido criado', ?)
                ");
                $stmt->execute([$pedidoId, $usuarioId]);
                
                $pdo->commit();
                
                // Retornar sucesso
                echo json_encode([
                    'success' => true,
                    'message' => 'Pedido criado com sucesso!',
                    'data' => [
                        'protocolo' => $protocolo,
                        'pedido_id' => $pedidoId,
                        'data_limite' => date('d/m/Y', strtotime($dataLimite)),
                        'redirect' => "acompanhar.php?protocolo={$protocolo}"
                    ]
                ]);
                
            } catch (Exception $e) {
                $pdo->rollback();
                throw $e;
            }
            break;
            
        case 'listar':
            // Listar pedidos básico
            $limit = min(intval($_GET['limit'] ?? 50), 100);
            $offset = max(intval($_GET['offset'] ?? 0), 0);
            
            $stmt = $pdo->prepare("
                SELECT 
                    p.*,
                    u.nome as requerente_nome,
                    u.email as requerente_email,
                    o.nome as orgao_nome,
                    o.sigla as orgao_sigla
                FROM pedidos p
                LEFT JOIN usuarios u ON p.requerente_id = u.id
                LEFT JOIN orgaos_setores o ON p.orgao_id = o.id
                ORDER BY p.data_cadastro DESC
                LIMIT {$limit} OFFSET {$offset}
            ");
            $stmt->execute();
            $pedidos = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $pedidos
            ]);
            break;
            
        case 'buscar':
            // Buscar pedido por protocolo
            $protocolo = $_GET['protocolo'] ?? '';
            if (empty($protocolo)) {
                throw new Exception("Protocolo é obrigatório");
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    p.*,
                    u.nome as requerente_nome,
                    u.email as requerente_email,
                    u.cpf_cnpj as requerente_documento,
                    o.nome as orgao_nome,
                    o.sigla as orgao_sigla
                FROM pedidos p
                LEFT JOIN usuarios u ON p.requerente_id = u.id
                LEFT JOIN orgaos_setores o ON p.orgao_id = o.id
                WHERE p.protocolo = ?
            ");
            $stmt->execute([$protocolo]);
            $pedido = $stmt->fetch();
            
            if (!$pedido) {
                throw new Exception("Pedido não encontrado");
            }
            
            // Buscar tramitações
            $stmt = $pdo->prepare("
                SELECT t.*, u.nome as usuario_nome
                FROM tramitacoes t
                LEFT JOIN usuarios u ON t.usuario_id = u.id
                WHERE t.pedido_id = ?
                ORDER BY t.data_tramitacao ASC
            ");
            $stmt->execute([$pedido['id']]);
            $tramitacoes = $stmt->fetchAll();
            
            $pedido['tramitacoes'] = $tramitacoes;
            $pedido['data_cadastro_formatada'] = date('d/m/Y H:i', strtotime($pedido['data_cadastro']));
            $pedido['data_limite_formatada'] = date('d/m/Y', strtotime($pedido['data_limite']));
            
            echo json_encode([
                'success' => true,
                'data' => $pedido
            ]);
            break;
            
        default:
            throw new Exception("Ação não reconhecida: {$action}");
    }
    
} catch (Exception $e) {
    // Em caso de erro, fazer rollback se necessário
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
}
?>