<?php
/**
 * Gerenciamento de Usuários E-SIC
 * Cadastro, login e recuperação de dados
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuração do banco de dados (simulado com arquivo JSON)
$usuarios_file = 'data/usuarios.json';
$log_file = 'data/access_log.json';

// Criar diretório se não existir
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// Função para ler usuários
function lerUsuarios() {
    global $usuarios_file;
    if (file_exists($usuarios_file)) {
        return json_decode(file_get_contents($usuarios_file), true) ?: [];
    }
    return [];
}

// Função para salvar usuários
function salvarUsuarios($usuarios) {
    global $usuarios_file;
    return file_put_contents($usuarios_file, json_encode($usuarios, JSON_PRETTY_PRINT));
}

// Função para log de acesso
function logAccess($acao, $dados = []) {
    global $log_file;
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'acao' => $acao,
        'dados' => $dados
    ];
    
    $logs = [];
    if (file_exists($log_file)) {
        $logs = json_decode(file_get_contents($log_file), true) ?: [];
    }
    
    $logs[] = $log_entry;
    
    // Manter apenas os últimos 1000 logs
    if (count($logs) > 1000) {
        $logs = array_slice($logs, -1000);
    }
    
    file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));
}

// Validação de CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

// Validação de CNPJ
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }
    
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    
    $resto = $soma % 11;
    
    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
        return false;
    }
    
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    
    $resto = $soma % 11;
    
    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

// Função para gerar ID único
function gerarId() {
    return uniqid('user_' . date('Ymd_'));
}

// Processar requisições
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        switch ($action) {
            case 'cadastrar':
                $response = cadastrarUsuario($input);
                break;
                
            case 'login':
                $response = loginUsuario($input);
                break;
                
            case 'recuperar':
                $response = recuperarDados($input);
                break;
                
            default:
                $response = ['success' => false, 'message' => 'Ação não reconhecida'];
        }
        break;
        
    case 'GET':
        switch ($action) {
            case 'verificar_documento':
                $documento = $_GET['documento'] ?? '';
                $response = verificarDocumento($documento);
                break;
                
            default:
                $response = ['success' => false, 'message' => 'Ação GET não reconhecida'];
        }
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Método não permitido'];
}

// Cadastrar usuário
function cadastrarUsuario($dados) {
    // Validações
    $required_fields = ['tipo_documento', 'documento', 'nome', 'email'];
    foreach ($required_fields as $field) {
        if (empty($dados[$field])) {
            return ['success' => false, 'message' => "Campo obrigatório: $field"];
        }
    }
    
    // Validar documento
    $documento = preg_replace('/[^0-9]/', '', $dados['documento']);
    if ($dados['tipo_documento'] === 'cpf') {
        if (!validarCPF($documento)) {
            return ['success' => false, 'message' => 'CPF inválido'];
        }
    } elseif ($dados['tipo_documento'] === 'cnpj') {
        if (!validarCNPJ($documento)) {
            return ['success' => false, 'message' => 'CNPJ inválido'];
        }
    }
    
    // Validar email
    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'E-mail inválido'];
    }
    
    // Verificar se já existe
    $usuarios = lerUsuarios();
    foreach ($usuarios as $usuario) {
        if ($usuario['documento'] === $documento) {
            return ['success' => false, 'message' => 'Documento já cadastrado'];
        }
        if ($usuario['email'] === $dados['email']) {
            return ['success' => false, 'message' => 'E-mail já cadastrado'];
        }
    }
    
    // Criar usuário
    $novo_usuario = [
        'id' => gerarId(),
        'tipo_documento' => $dados['tipo_documento'],
        'documento' => $documento,
        'documento_formatado' => $dados['documento'],
        'nome' => trim($dados['nome']),
        'email' => strtolower(trim($dados['email'])),
        'telefone' => $dados['telefone'] ?? '',
        'cep' => preg_replace('/[^0-9]/', '', $dados['cep'] ?? ''),
        'endereco' => trim($dados['endereco'] ?? ''),
        'data_cadastro' => date('Y-m-d H:i:s'),
        'status' => 'ativo',
        'ultimo_acesso' => null
    ];
    
    $usuarios[] = $novo_usuario;
    
    if (salvarUsuarios($usuarios)) {
        logAccess('cadastro', ['documento' => substr($documento, 0, 3) . '***']);
        return ['success' => true, 'message' => 'Cadastro realizado com sucesso', 'user_id' => $novo_usuario['id']];
    } else {
        return ['success' => false, 'message' => 'Erro ao salvar cadastro'];
    }
}

// Login usuário
function loginUsuario($dados) {
    $required_fields = ['tipo_documento', 'documento', 'nome', 'email'];
    foreach ($required_fields as $field) {
        if (empty($dados[$field])) {
            return ['success' => false, 'message' => "Campo obrigatório: $field"];
        }
    }
    
    $documento = preg_replace('/[^0-9]/', '', $dados['documento']);
    $usuarios = lerUsuarios();
    
    foreach ($usuarios as &$usuario) {
        if ($usuario['documento'] === $documento && 
            strtolower($usuario['email']) === strtolower($dados['email'])) {
            
            // Atualizar último acesso
            $usuario['ultimo_acesso'] = date('Y-m-d H:i:s');
            salvarUsuarios($usuarios);
            
            logAccess('login', ['documento' => substr($documento, 0, 3) . '***']);
            
            return [
                'success' => true, 
                'message' => 'Login realizado com sucesso',
                'usuario' => [
                    'id' => $usuario['id'],
                    'nome' => $usuario['nome'],
                    'email' => $usuario['email'],
                    'tipo_documento' => $usuario['tipo_documento']
                ]
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Dados não encontrados ou incorretos'];
}

// Recuperar dados
function recuperarDados($dados) {
    $tipo = $dados['tipo'] ?? '';
    $valor = $dados['valor'] ?? '';
    
    if (empty($tipo) || empty($valor)) {
        return ['success' => false, 'message' => 'Tipo e valor são obrigatórios'];
    }
    
    $usuarios = lerUsuarios();
    $usuario_encontrado = null;
    
    foreach ($usuarios as $usuario) {
        if ($tipo === 'documento' && $usuario['documento'] === preg_replace('/[^0-9]/', '', $valor)) {
            $usuario_encontrado = $usuario;
            break;
        } elseif ($tipo === 'email' && strtolower($usuario['email']) === strtolower($valor)) {
            $usuario_encontrado = $usuario;
            break;
        }
    }
    
    if ($usuario_encontrado) {
        // Em produção, enviar e-mail real
        logAccess('recuperacao', ['tipo' => $tipo, 'encontrado' => true]);
        
        return [
            'success' => true,
            'message' => 'Dados de acesso enviados para o e-mail cadastrado',
            'dados_parciais' => [
                'nome' => substr($usuario_encontrado['nome'], 0, 10) . '...',
                'email' => substr($usuario_encontrado['email'], 0, 3) . '***@***'
            ]
        ];
    }
    
    logAccess('recuperacao', ['tipo' => $tipo, 'encontrado' => false]);
    return ['success' => false, 'message' => 'Dados não encontrados no sistema'];
}

// Verificar se documento já existe
function verificarDocumento($documento) {
    $documento = preg_replace('/[^0-9]/', '', $documento);
    $usuarios = lerUsuarios();
    
    foreach ($usuarios as $usuario) {
        if ($usuario['documento'] === $documento) {
            return ['exists' => true, 'message' => 'Documento já cadastrado'];
        }
    }
    
    return ['exists' => false, 'message' => 'Documento disponível'];
}

// Enviar resposta
echo json_encode($response);
?>