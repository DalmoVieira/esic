<?php

require_once 'Model.php';

/**
 * Sistema E-SIC - Model Usuario
 * 
 * Gerencia usuários administrativos do sistema
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class Usuario extends Model {
    
    protected $table = 'usuarios';
    protected $fillable = [
        'nome', 'email', 'senha', 'telefone', 'cargo', 'unidade', 
        'nivel_acesso', 'ativo', 'google_id', 'govbr_id', 'foto'
    ];
    
    /**
     * Buscar usuário por email
     */
    public function findByEmail($email) {
        return $this->first('email', $email);
    }
    
    /**
     * Criar novo usuário com validação
     */
    public function createUser($data) {
        // Validar dados
        $this->validate($data, [
            'nome' => 'required|min:3|max:100',
            'email' => 'required|email|unique:usuarios',
            'senha' => 'required|min:6',
            'nivel_acesso' => 'required'
        ]);
        
        // Hash da senha
        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        // Definir valores padrão
        $data['ativo'] = $data['ativo'] ?? 1;
        $data['email_verificado'] = false;
        $data['tentativas_login'] = 0;
        
        return $this->create($data);
    }
    
    /**
     * Atualizar usuário
     */
    public function updateUser($id, $data) {
        // Remover senha vazia
        if (isset($data['senha']) && empty($data['senha'])) {
            unset($data['senha']);
        } elseif (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        // Validar email único (excluindo o próprio usuário)
        if (isset($data['email'])) {
            $existing = $this->db->selectOne(
                "SELECT id FROM usuarios WHERE email = ? AND id != ?", 
                [$data['email'], $id]
            );
            if ($existing) {
                throw new ValidationException("Email já está em uso", ['email' => ['Este email já está sendo usado por outro usuário.']]);
            }
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Verificar senha
     */
    public function verifyPassword($hashedPassword, $plainPassword) {
        return password_verify($plainPassword, $hashedPassword);
    }
    
    /**
     * Listar usuários ativos
     */
    public function getActiveUsers() {
        return $this->where('ativo', 1);
    }
    
    /**
     * Buscar usuários por nível de acesso
     */
    public function getUsersByRole($role) {
        return $this->where('nivel_acesso', $role);
    }
    
    /**
     * Atualizar último login
     */
    public function updateLastLogin($id) {
        return $this->update($id, [
            'ultimo_login' => date('Y-m-d H:i:s'),
            'tentativas_login' => 0,
            'bloqueado_ate' => null
        ]);
    }
    
    /**
     * Incrementar tentativas de login
     */
    public function incrementLoginAttempts($id) {
        $user = $this->find($id);
        if (!$user) return false;
        
        $tentativas = $user['tentativas_login'] + 1;
        $updateData = ['tentativas_login' => $tentativas];
        
        // Bloquear se excedeu tentativas
        if ($tentativas >= 5) {
            $updateData['bloqueado_ate'] = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        }
        
        return $this->update($id, $updateData);
    }
    
    /**
     * Verificar se usuário está bloqueado
     */
    public function isBlocked($id) {
        $user = $this->find($id);
        if (!$user || !$user['bloqueado_ate']) {
            return false;
        }
        
        return new DateTime($user['bloqueado_ate']) > new DateTime();
    }
    
    /**
     * Desbloquear usuário
     */
    public function unblock($id) {
        return $this->update($id, [
            'bloqueado_ate' => null,
            'tentativas_login' => 0
        ]);
    }

    /**
     * Buscar usuário por CPF
     */
    public function findByCpf($cpf) {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE cpf = ?",
            [$cpf]
        );
    }
    
    /**
     * Gerar token de verificação de email
     */
    public function generateEmailVerificationToken($id) {
        $token = bin2hex(random_bytes(32));
        $this->update($id, ['token_verificacao' => $token]);
        return $token;
    }
    
    /**
     * Verificar email com token
     */
    public function verifyEmailWithToken($token) {
        $user = $this->first('token_verificacao', $token);
        if (!$user) {
            return false;
        }
        
        $this->update($user['id'], [
            'email_verificado' => true,
            'token_verificacao' => null
        ]);
        
        return $user;
    }
    
    /**
     * Gerar token de reset de senha
     */
    public function generatePasswordResetToken($email) {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->update($user['id'], [
            'token_reset_senha' => $token,
            'reset_senha_expira' => $expiry
        ]);
        
        return $token;
    }
    
    /**
     * Verificar token de reset de senha
     */
    public function verifyPasswordResetToken($token) {
        $user = $this->db->selectOne(
            "SELECT * FROM usuarios WHERE token_reset_senha = ? AND reset_senha_expira > NOW()",
            [$token]
        );
        
        return $user;
    }
    
    /**
     * Resetar senha com token
     */
    public function resetPasswordWithToken($token, $newPassword) {
        $user = $this->verifyPasswordResetToken($token);
        if (!$user) {
            return false;
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $this->update($user['id'], [
            'senha' => $hashedPassword,
            'token_reset_senha' => null,
            'reset_senha_expira' => null
        ]);
        
        return true;
    }
    
    /**
     * Estatísticas de usuários
     */
    public function getStats() {
        $stats = [];
        
        // Total de usuários
        $stats['total'] = $this->count();
        
        // Usuários ativos
        $stats['ativos'] = $this->count('ativo', 1);
        
        // Por nível de acesso
        $stats['por_nivel'] = $this->db->select(
            "SELECT nivel_acesso, COUNT(*) as total FROM usuarios WHERE ativo = 1 GROUP BY nivel_acesso"
        );
        
        // Usuários bloqueados
        $stats['bloqueados'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM usuarios WHERE bloqueado_ate IS NOT NULL AND bloqueado_ate > NOW()"
        )['count'];
        
        // Login nos últimos 30 dias
        $stats['ativos_30_dias'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM usuarios WHERE ultimo_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        )['count'];
        
        return $stats;
    }
    
    /**
     * Buscar usuários com filtros
     */
    public function search($filters = []) {
        $query = "SELECT * FROM usuarios WHERE 1=1";
        $params = [];
        
        // Filtro por nome/email
        if (!empty($filters['search'])) {
            $query .= " AND (nome LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Filtro por nível
        if (!empty($filters['nivel_acesso'])) {
            $query .= " AND nivel_acesso = ?";
            $params[] = $filters['nivel_acesso'];
        }
        
        // Filtro por status
        if (isset($filters['ativo'])) {
            $query .= " AND ativo = ?";
            $params[] = $filters['ativo'];
        }
        
        // Filtro por unidade
        if (!empty($filters['unidade'])) {
            $query .= " AND unidade = ?";
            $params[] = $filters['unidade'];
        }
        
        $query .= " ORDER BY nome";
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Obter dados públicos do usuário (sem informações sensíveis)
     */
    public function getPublicData($id) {
        $user = $this->find($id);
        if (!$user) return null;
        
        return [
            'id' => $user['id'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'cargo' => $user['cargo'],
            'unidade' => $user['unidade'],
            'nivel_acesso' => $user['nivel_acesso'],
            'ultimo_login' => $user['ultimo_login'],
            'ativo' => $user['ativo']
        ];
    }
    
    /**
     * Listar todas as unidades cadastradas
     */
    public function getUnidades() {
        $result = $this->db->select(
            "SELECT DISTINCT unidade FROM usuarios WHERE unidade IS NOT NULL AND unidade != '' ORDER BY unidade"
        );
        
        return array_column($result, 'unidade');
    }
    
    /**
     * Listar todos os cargos cadastrados
     */
    public function getCargos() {
        $result = $this->db->select(
            "SELECT DISTINCT cargo FROM usuarios WHERE cargo IS NOT NULL AND cargo != '' ORDER BY cargo"
        );
        
        return array_column($result, 'cargo');
    }
}