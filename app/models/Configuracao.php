<?php

namespace App\Models;

class Configuracao extends BaseModel
{
    protected $table = 'configuracoes';
    
    protected $fillable = [
        'chave',
        'valor',
        'tipo',
        'grupo',
        'descricao',
        'publico'
    ];
    
    /**
     * Get all configurations as key-value array
     */
    public function getConfiguracoes()
    {
        $sql = "SELECT chave, valor FROM {$this->table}";
        $results = $this->db->fetchAll($sql);
        
        $config = [];
        foreach ($results as $row) {
            $config[$row['chave']] = $row['valor'];
        }
        
        return $config;
    }
    
    /**
     * Get configuration by key
     */
    public function get($chave, $default = null)
    {
        $sql = "SELECT valor FROM {$this->table} WHERE chave = ?";
        $result = $this->db->fetch($sql, [$chave]);
        
        return $result ? $result['valor'] : $default;
    }
    
    /**
     * Set configuration value
     */
    public function set($chave, $valor, $tipo = 'string', $grupo = 'geral')
    {
        $existing = $this->findBy('chave', $chave);
        
        if ($existing) {
            return $this->update($existing['id'], ['valor' => $valor]);
        } else {
            return $this->create([
                'chave' => $chave,
                'valor' => $valor,
                'tipo' => $tipo,
                'grupo' => $grupo
            ]);
        }
    }
    
    /**
     * Get configurations by group
     */
    public function getByGroup($grupo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE grupo = ? ORDER BY chave";
        return $this->db->fetchAll($sql, [$grupo]);
    }
    
    /**
     * Get public configurations (for frontend use)
     */
    public function getPublicas()
    {
        $sql = "SELECT chave, valor FROM {$this->table} WHERE publico = 1";
        $results = $this->db->fetchAll($sql);
        
        $config = [];
        foreach ($results as $row) {
            $config[$row['chave']] = $row['valor'];
        }
        
        return $config;
    }
    
    /**
     * Update multiple configurations
     */
    public function updateMultiple($configs)
    {
        $this->beginTransaction();
        
        try {
            foreach ($configs as $chave => $valor) {
                $this->set($chave, $valor);
            }
            
            $this->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Reset configurations to default values
     */
    public function resetToDefaults()
    {
        $defaults = [
            'nome_orgao' => 'Órgão Público',
            'prazo_resposta' => '20',
            'prazo_recurso' => '10',
            'prazo_analise_recurso' => '5',
            'max_instancias' => '3',
            'permitir_anonimo' => '0',
            'validar_cpf' => '1',
            'smtp_host' => '',
            'smtp_porta' => '587',
            'smtp_ssl' => '1',
            'sessao_timeout' => '60',
            'max_tentativas_login' => '5',
            'bloqueio_tempo' => '15',
            'complexidade_senha' => 'media',
            'force_https' => '0',
            'log_tentativas' => '1',
            'captcha_ativo' => '0',
            'modo_manutencao' => '0',
            'debug_ativo' => '0'
        ];
        
        return $this->updateMultiple($defaults);
    }
}