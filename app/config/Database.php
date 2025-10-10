<?php

/**
 * Sistema E-SIC - Classe de Conexão com Banco de Dados
 * 
 * Esta classe gerencia a conexão PDO com MySQL de forma singleton
 * Inclui tratamento de erros, transações e logs de debug
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class Database {
    
    private static $instance = null;
    private $connection;
    private $host;
    private $database;
    private $username;
    private $password;
    private $port;
    private $charset;
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        $this->loadEnvironment();
        $this->connect();
    }
    
    /**
     * Carrega variáveis de ambiente
     */
    private function loadEnvironment() {
        // Carregar arquivo .env se existir
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }
            }
        }
        
        // Configurações do banco com fallback
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_NAME'] ?? 'esic_db';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->port = $_ENV['DB_PORT'] ?? '3306';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    }
    
    /**
     * Estabelece conexão com o banco de dados
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE {$this->charset}_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
            // Log de conexão bem-sucedida (apenas em desenvolvimento)
            if (($_ENV['APP_DEBUG'] ?? false) && ($_ENV['APP_ENV'] ?? '') === 'development') {
                error_log("Database: Conexão estabelecida com sucesso");
            }
            
        } catch (PDOException $e) {
            // Log do erro sem expor dados sensíveis
            error_log("Database Error: " . $e->getMessage());
            
            // Em produção, mostrar erro genérico
            if (($_ENV['APP_ENV'] ?? '') === 'production') {
                throw new Exception("Erro na conexão com o banco de dados. Tente novamente mais tarde.");
            } else {
                throw new Exception("Erro na conexão: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Retorna instância única da classe (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Retorna conexão PDO
     */
    public function getConnection() {
        // Verifica se conexão ainda está ativa
        if ($this->connection === null) {
            $this->connect();
        }
        
        try {
            $this->connection->query('SELECT 1');
        } catch (PDOException $e) {
            // Reconecta se a conexão foi perdida
            $this->connect();
        }
        
        return $this->connection;
    }
    
    /**
     * Executa query preparada com parâmetros
     * 
     * @param string $query Query SQL
     * @param array $params Parâmetros para bind
     * @return PDOStatement
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute($params);
            
            // Log da query em modo debug
            if (($_ENV['APP_DEBUG'] ?? false) && ($_ENV['LOG_QUERIES'] ?? false)) {
                error_log("Query: " . $query);
                if (!empty($params)) {
                    error_log("Params: " . json_encode($params));
                }
            }
            
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage() . " - Query: " . $query);
            throw new Exception("Erro na execução da consulta: " . $e->getMessage());
        }
    }
    
    /**
     * Executa SELECT e retorna todos os resultados
     */
    public function select($query, $params = []) {
        return $this->query($query, $params)->fetchAll();
    }
    
    /**
     * Executa SELECT e retorna primeiro resultado
     */
    public function selectOne($query, $params = []) {
        return $this->query($query, $params)->fetch();
    }
    
    /**
     * Executa INSERT e retorna ID inserido
     */
    public function insert($query, $params = []) {
        $this->query($query, $params);
        return $this->getConnection()->lastInsertId();
    }
    
    /**
     * Executa UPDATE/DELETE e retorna número de linhas afetadas
     */
    public function execute($query, $params = []) {
        return $this->query($query, $params)->rowCount();
    }
    
    /**
     * Inicia transação
     */
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Confirma transação
     */
    public function commit() {
        return $this->getConnection()->commit();
    }
    
    /**
     * Desfaz transação
     */
    public function rollback() {
        return $this->getConnection()->rollback();
    }
    
    /**
     * Executa função em transação com rollback automático em caso de erro
     */
    public function transaction($callback) {
        $this->beginTransaction();
        
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Escapa string para evitar SQL injection (uso apenas quando necessário)
     */
    public function quote($string) {
        return $this->getConnection()->quote($string);
    }
    
    /**
     * Verifica se tabela existe
     */
    public function tableExists($tableName) {
        $query = "SHOW TABLES LIKE ?";
        $result = $this->selectOne($query, [$tableName]);
        return $result !== false;
    }
    
    /**
     * Retorna informações sobre as colunas de uma tabela
     */
    public function getTableColumns($tableName) {
        $query = "DESCRIBE `{$tableName}`";
        return $this->select($query);
    }
    
    /**
     * Executa múltiplas queries (usar com cuidado)
     */
    public function multiQuery($queries) {
        $results = [];
        foreach ($queries as $query) {
            if (is_array($query)) {
                $results[] = $this->query($query['sql'], $query['params'] ?? []);
            } else {
                $results[] = $this->query($query);
            }
        }
        return $results;
    }
    
    /**
     * Retorna estatísticas da conexão
     */
    public function getStats() {
        $stats = [];
        
        // Informações do servidor
        $serverInfo = $this->selectOne("SELECT VERSION() as version, CONNECTION_ID() as connection_id, DATABASE() as database_name");
        $stats['server'] = $serverInfo;
        
        // Status de variáveis importantes
        $variables = $this->select("SHOW VARIABLES WHERE Variable_name IN ('max_connections', 'max_allowed_packet', 'innodb_buffer_pool_size')");
        $stats['variables'] = $variables;
        
        return $stats;
    }
    
    /**
     * Impede clonagem da instância
     */
    private function __clone() {}
    
    /**
     * Impede deserialização da instância
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Fecha conexão explicitamente
     */
    public function close() {
        $this->connection = null;
    }
    
    /**
     * Destrutor - fecha conexão automaticamente
     */
    public function __destruct() {
        $this->close();
    }
}

/**
 * Função helper para acesso rápido ao banco
 */
function db() {
    return Database::getInstance();
}

/**
 * Classe para Query Builder (opcional - facilita construção de queries)
 */
class QueryBuilder {
    
    private $db;
    private $table;
    private $select = ['*'];
    private $where = [];
    private $joins = [];
    private $orderBy = [];
    private $groupBy = [];
    private $having = [];
    private $limit = null;
    private $offset = null;
    private $params = [];
    
    public function __construct($table = null) {
        $this->db = Database::getInstance();
        $this->table = $table;
    }
    
    public static function table($table) {
        return new self($table);
    }
    
    public function select($columns = ['*']) {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
    
    public function where($column, $operator = null, $value = null) {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $placeholder = ':where_' . count($this->where);
        $this->where[] = "{$column} {$operator} {$placeholder}";
        $this->params[$placeholder] = $value;
        
        return $this;
    }
    
    public function whereIn($column, $values) {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = ':wherein_' . $i;
            $placeholders[] = $placeholder;
            $this->params[$placeholder] = $value;
        }
        
        $this->where[] = "{$column} IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }
    
    public function join($table, $first, $operator = null, $second = null) {
        if (func_num_args() === 3) {
            $second = $operator;
            $operator = '=';
        }
        
        $this->joins[] = "JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }
    
    public function leftJoin($table, $first, $operator = null, $second = null) {
        if (func_num_args() === 3) {
            $second = $operator;
            $operator = '=';
        }
        
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }
    
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }
    
    public function groupBy($columns) {
        $this->groupBy = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
    
    public function limit($limit, $offset = null) {
        $this->limit = $limit;
        if ($offset !== null) {
            $this->offset = $offset;
        }
        return $this;
    }
    
    public function get() {
        $query = $this->buildSelectQuery();
        return $this->db->select($query, $this->params);
    }
    
    public function first() {
        $this->limit(1);
        $query = $this->buildSelectQuery();
        return $this->db->selectOne($query, $this->params);
    }
    
    public function count() {
        $originalSelect = $this->select;
        $this->select = ['COUNT(*) as count'];
        $query = $this->buildSelectQuery();
        $result = $this->db->selectOne($query, $this->params);
        $this->select = $originalSelect;
        return $result['count'] ?? 0;
    }
    
    private function buildSelectQuery() {
        $query = "SELECT " . implode(', ', $this->select) . " FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $query .= " " . implode(" ", $this->joins);
        }
        
        if (!empty($this->where)) {
            $query .= " WHERE " . implode(" AND ", $this->where);
        }
        
        if (!empty($this->groupBy)) {
            $query .= " GROUP BY " . implode(", ", $this->groupBy);
        }
        
        if (!empty($this->having)) {
            $query .= " HAVING " . implode(" AND ", $this->having);
        }
        
        if (!empty($this->orderBy)) {
            $query .= " ORDER BY " . implode(", ", $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $query .= " LIMIT {$this->limit}";
            if ($this->offset !== null) {
                $query .= " OFFSET {$this->offset}";
            }
        }
        
        return $query;
    }
}