<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;
    
    private $host;
    private $database;
    private $username;
    private $password;
    private $charset = 'utf8mb4';
    
    private function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_DATABASE'] ?? 'esic';
        $this->username = $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }
    
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception('Query failed: ' . $e->getMessage());
        }
    }
    
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldsList = implode(', ', $fields);
        
        $sql = "INSERT INTO {$table} ({$fieldsList}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = [])
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
        }
        $fieldsList = implode(', ', $fields);
        
        $sql = "UPDATE {$table} SET {$fieldsList} WHERE {$where}";
        
        return $this->query($sql, array_merge($data, $whereParams));
    }
    
    public function delete($table, $where, $whereParams = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $whereParams);
    }
    
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }
    
    public function commit()
    {
        return $this->connection->commit();
    }
    
    public function rollback()
    {
        return $this->connection->rollBack();
    }
    
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}