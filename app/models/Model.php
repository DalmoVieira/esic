<?php

/**
 * Sistema E-SIC - Model Base
 * 
 * Classe base para todos os models com funcionalidades comuns
 * Implementa padrão Active Record simplificado
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

abstract class Model {
    
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar todos os registros
     */
    public function all($orderBy = null) {
        $query = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $query .= " ORDER BY {$orderBy}";
        }
        
        return $this->db->select($query);
    }
    
    /**
     * Buscar por ID
     */
    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->selectOne($query, [$id]);
    }
    
    /**
     * Buscar primeiro registro com condições
     */
    public function where($column, $operator = null, $value = null) {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $query = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?";
        return $this->db->select($query, [$value]);
    }
    
    /**
     * Buscar primeiro registro
     */
    public function first($column = null, $operator = null, $value = null) {
        if ($column) {
            if (func_num_args() === 2) {
                $value = $operator;
                $operator = '=';
            }
            $query = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ? LIMIT 1";
            return $this->db->selectOne($query, [$value]);
        }
        
        $query = "SELECT * FROM {$this->table} LIMIT 1";
        return $this->db->selectOne($query);
    }
    
    /**
     * Criar novo registro
     */
    public function create($data) {
        // Filtrar apenas campos permitidos
        $data = $this->filterFillable($data);
        
        // Adicionar timestamps se habilitado
        if ($this->timestamps) {
            $now = date($this->dateFormat);
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
        }
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $id = $this->db->insert($query, array_values($data));
        
        // Retornar registro criado
        return $this->find($id);
    }
    
    /**
     * Atualizar registro
     */
    public function update($id, $data) {
        // Filtrar apenas campos permitidos
        $data = $this->filterFillable($data);
        
        // Adicionar updated_at se habilitado
        if ($this->timestamps && !isset($data['updated_at'])) {
            $data['updated_at'] = date($this->dateFormat);
        }
        
        $columns = array_keys($data);
        $setClause = implode(' = ?, ', $columns) . ' = ?';
        
        $query = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        $this->db->execute($query, $values);
        
        // Retornar registro atualizado
        return $this->find($id);
    }
    
    /**
     * Deletar registro
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($query, [$id]);
    }
    
    /**
     * Contar registros
     */
    public function count($filters = []) {
        // Se for chamada antiga com parâmetros individuais, converter para formato de filtros
        if (func_num_args() > 1 || (func_num_args() === 1 && !is_array($filters))) {
            $column = $filters;
            $operator = func_get_arg(1) ?? '=';
            $value = func_get_arg(2) ?? $operator;
            
            if (func_num_args() === 2) {
                $value = $operator;
                $operator = '=';
            }
            
            $filters = [$column => $value];
        }
        
        $conditions = [];
        $params = [];
        
        foreach ($filters as $key => $value) {
            if ($key === 'q') {
                // Busca por texto em múltiplos campos
                $searchFields = $this->getSearchFields();
                if (!empty($searchFields)) {
                    $searchConditions = [];
                    foreach ($searchFields as $field) {
                        $searchConditions[] = "{$field} LIKE ?";
                        $params[] = "%{$value}%";
                    }
                    $conditions[] = "(" . implode(' OR ', $searchConditions) . ")";
                }
            } elseif ($key === 'data_inicio') {
                $conditions[] = "data_criacao >= ?";
                $params[] = $value . ' 00:00:00';
            } elseif ($key === 'data_fim') {
                $conditions[] = "data_criacao <= ?";
                $params[] = $value . ' 23:59:59';
            } elseif (is_array($value)) {
                // Para arrays, usar IN
                $placeholders = str_repeat('?,', count($value) - 1) . '?';
                $conditions[] = "{$key} IN ({$placeholders})";
                $params = array_merge($params, $value);
            } else {
                $conditions[] = "{$key} = ?";
                $params[] = $value;
            }
        }
        
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $result = $this->db->selectOne($query, $params);
        return (int) $result['count'];
    }
    
    /**
     * Verificar se registro existe
     */
    public function exists($id) {
        return $this->find($id) !== false;
    }
    
    /**
     * Busca paginada
     */
    public function paginate($page = 1, $perPage = 10, $where = null, $params = []) {
        $offset = ($page - 1) * $perPage;
        
        // Query para contar total
        $countQuery = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($where) {
            $countQuery .= " WHERE {$where}";
        }
        
        $total = $this->db->selectOne($countQuery, $params)['count'];
        
        // Query para dados
        $dataQuery = "SELECT * FROM {$this->table}";
        if ($where) {
            $dataQuery .= " WHERE {$where}";
        }
        $dataQuery .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->db->select($dataQuery, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    /**
     * Executar query customizada
     */
    public function query($sql, $params = []) {
        return $this->db->select($sql, $params);
    }
    
    /**
     * Filtrar apenas campos permitidos
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            // Se fillable está vazio, remover apenas os guarded
            foreach ($this->guarded as $field) {
                unset($data[$field]);
            }
            return $data;
        }
        
        // Filtrar apenas campos fillable
        $filtered = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $filtered[$field] = $data[$field];
            }
        }
        
        return $filtered;
    }
    
    /**
     * Validar dados antes de salvar
     */
    protected function validate($data, $rules = []) {
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
                    
                    case 'unique':
                        if ($value) {
                            $table = $ruleValue ?: $this->table;
                            $existing = $this->db->selectOne("SELECT id FROM {$table} WHERE {$field} = ?", [$value]);
                            if ($existing) {
                                $errors[$field][] = "O valor do campo {$field} já está em uso.";
                            }
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
     * Começar transação
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Confirmar transação
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Cancelar transação
     */
    public function rollback() {
        return $this->db->rollback();
    }
    
    /**
     * Alias para o método find
     */
    public function findById($id) {
        return $this->find($id);
    }
    
    /**
     * Buscar todos com filtros
     */
    public function findAll($filters = [], $orderBy = null, $limit = null) {
        $conditions = [];
        $params = [];
        
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                // Para arrays, usar IN
                $placeholders = str_repeat('?,', count($value) - 1) . '?';
                $conditions[] = "{$key} IN ({$placeholders})";
                $params = array_merge($params, $value);
            } else {
                $conditions[] = "{$key} = ?";
                $params[] = $value;
            }
        }
        
        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        
        if ($orderBy) {
            $query .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $query .= " LIMIT {$limit}";
        }
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Buscar todos com paginação e filtros
     */
    public function findAllWithPagination($filters = [], $page = 1, $perPage = 10, $orderBy = null) {
        $conditions = [];
        $params = [];
        
        foreach ($filters as $key => $value) {
            if ($key === 'q') {
                // Busca por texto em múltiplos campos
                $searchFields = $this->getSearchFields();
                if (!empty($searchFields)) {
                    $searchConditions = [];
                    foreach ($searchFields as $field) {
                        $searchConditions[] = "{$field} LIKE ?";
                        $params[] = "%{$value}%";
                    }
                    $conditions[] = "(" . implode(' OR ', $searchConditions) . ")";
                }
            } elseif ($key === 'data_inicio') {
                $conditions[] = "data_criacao >= ?";
                $params[] = $value . ' 00:00:00';
            } elseif ($key === 'data_fim') {
                $conditions[] = "data_criacao <= ?";
                $params[] = $value . ' 23:59:59';
            } elseif (is_array($value)) {
                // Para arrays, usar IN
                $placeholders = str_repeat('?,', count($value) - 1) . '?';
                $conditions[] = "{$key} IN ({$placeholders})";
                $params = array_merge($params, $value);
            } else {
                $conditions[] = "{$key} = ?";
                $params[] = $value;
            }
        }
        
        // Contar total
        $countQuery = "SELECT COUNT(*) as count FROM {$this->table}";
        if (!empty($conditions)) {
            $countQuery .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $total = $this->db->selectOne($countQuery, $params)['count'];
        
        // Buscar dados
        $offset = ($page - 1) * $perPage;
        $dataQuery = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $dataQuery .= " WHERE " . implode(' AND ', $conditions);
        }
        
        if ($orderBy) {
            $dataQuery .= " ORDER BY {$orderBy}";
        } else {
            $dataQuery .= " ORDER BY created_at DESC";
        }
        
        $dataQuery .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->db->select($dataQuery, $params);
        
        return $data;
    }
    
    /**
     * Obter campos para busca por texto
     * Deve ser sobrescrito nas classes filhas
     */
    protected function getSearchFields() {
        return [];
    }
}

/**
 * Exceção de validação
 */
class ValidationException extends Exception {
    
    private $errors;
    
    public function __construct($message = "", $errors = [], $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }
    
    public function getErrors() {
        return $this->errors;
    }
}