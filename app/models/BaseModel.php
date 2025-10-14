<?php

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all records
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Find record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Find record by column
     */
    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetch($sql, [$value]);
    }
    
    /**
     * Find multiple records by column
     */
    public function findAllBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchAll($sql, [$value]);
    }
    
    /**
     * Create new record
     */
    public function create($data)
    {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Update record
     */
    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $where = "{$this->primaryKey} = ?";
        return $this->db->update($this->table, $data, $where, [$id]);
    }
    
    /**
     * Delete record
     */
    public function delete($id)
    {
        $where = "{$this->primaryKey} = ?";
        return $this->db->delete($this->table, $where, [$id]);
    }
    
    /**
     * Count records
     */
    public function count($where = '', $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        $result = $this->db->fetch($sql, $params);
        return (int) $result['total'];
    }
    
    /**
     * Check if record exists
     */
    public function exists($column, $value, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = ?";
        $params = [$value];
        
        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'] > 0;
    }
    
    /**
     * Get paginated results
     */
    public function paginate($page = 1, $perPage = 20, $where = '', $params = [])
    {
        $page = max(1, (int) $page);
        $offset = ($page - 1) * $perPage;
        
        // Count total records
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($where)) {
            $countSql .= " WHERE {$where}";
        }
        $total = $this->db->fetch($countSql, $params)['total'];
        
        // Get paginated data
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " LIMIT {$offset}, {$perPage}";
        
        $data = $this->db->fetchAll($sql, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
            'has_more' => $page < ceil($total / $perPage)
        ];
    }
    
    /**
     * Execute raw query
     */
    public function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }
    
    /**
     * Execute raw select query
     */
    public function select($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Filter data to only include fillable fields
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Begin database transaction
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit database transaction
     */
    public function commit()
    {
        return $this->db->commit();
    }
    
    /**
     * Rollback database transaction
     */
    public function rollback()
    {
        return $this->db->rollback();
    }
    
    /**
     * Get table name
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Get primary key
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    
    /**
     * Soft delete (mark as deleted)
     */
    public function softDelete($id)
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * Restore soft deleted record
     */
    public function restore($id)
    {
        return $this->update($id, ['deleted_at' => null]);
    }
    
    /**
     * Get only non-deleted records
     */
    public function withoutDeleted()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
        return $this->db->fetchAll($sql);
    }
}