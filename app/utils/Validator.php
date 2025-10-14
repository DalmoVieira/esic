<?php

namespace App\Utils;

class Validator
{
    private $data;
    private $errors = [];
    private $rules = [];
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    /**
     * Add required fields validation
     */
    public function required($fields)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        
        foreach ($fields as $field) {
            if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
                $this->errors[] = "O campo {$field} é obrigatório";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate email format
     */
    public function email($field)
    {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "O campo {$field} deve ser um email válido";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate CPF format
     */
    public function cpf($field)
    {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $cpf = preg_replace('/\D/', '', $this->data[$field]);
            
            if (!$this->isValidCPF($cpf)) {
                $this->errors[] = "O campo {$field} deve ser um CPF válido";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate CNPJ format
     */
    public function cnpj($field)
    {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $cnpj = preg_replace('/\D/', '', $this->data[$field]);
            
            if (!$this->isValidCNPJ($cnpj)) {
                $this->errors[] = "O campo {$field} deve ser um CNPJ válido";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate minimum length
     */
    public function minLength($field, $length)
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[] = "O campo {$field} deve ter pelo menos {$length} caracteres";
        }
        
        return $this;
    }
    
    /**
     * Validate maximum length
     */
    public function maxLength($field, $length)
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[] = "O campo {$field} deve ter no máximo {$length} caracteres";
        }
        
        return $this;
    }
    
    /**
     * Validate numeric value
     */
    public function numeric($field)
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[] = "O campo {$field} deve ser um número";
        }
        
        return $this;
    }
    
    /**
     * Validate integer value
     */
    public function integer($field)
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->errors[] = "O campo {$field} deve ser um número inteiro";
        }
        
        return $this;
    }
    
    /**
     * Validate date format
     */
    public function date($field, $format = 'Y-m-d')
    {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date = \DateTime::createFromFormat($format, $this->data[$field]);
            if (!$date || $date->format($format) !== $this->data[$field]) {
                $this->errors[] = "O campo {$field} deve ser uma data válida no formato {$format}";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate URL format
     */
    public function url($field)
    {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                $this->errors[] = "O campo {$field} deve ser uma URL válida";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate phone number
     */
    public function phone($field)
    {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $phone = preg_replace('/\D/', '', $this->data[$field]);
            if (strlen($phone) < 10 || strlen($phone) > 11) {
                $this->errors[] = "O campo {$field} deve ser um telefone válido";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate password strength
     */
    public function password($field, $minLength = 8)
    {
        if (isset($this->data[$field])) {
            $password = $this->data[$field];
            
            if (strlen($password) < $minLength) {
                $this->errors[] = "A senha deve ter pelo menos {$minLength} caracteres";
            }
            
            if (!preg_match('/[A-Z]/', $password)) {
                $this->errors[] = "A senha deve conter pelo menos uma letra maiúscula";
            }
            
            if (!preg_match('/[a-z]/', $password)) {
                $this->errors[] = "A senha deve conter pelo menos uma letra minúscula";
            }
            
            if (!preg_match('/[0-9]/', $password)) {
                $this->errors[] = "A senha deve conter pelo menos um número";
            }
            
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                $this->errors[] = "A senha deve conter pelo menos um caractere especial";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate that field matches another field
     */
    public function matches($field, $matchField)
    {
        if (isset($this->data[$field]) && isset($this->data[$matchField])) {
            if ($this->data[$field] !== $this->data[$matchField]) {
                $this->errors[] = "O campo {$field} deve ser igual ao campo {$matchField}";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate that field is in array of values
     */
    public function in($field, $values)
    {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[] = "O campo {$field} deve ser um dos seguintes valores: " . implode(', ', $values);
        }
        
        return $this;
    }
    
    /**
     * Custom validation rule
     */
    public function custom($field, $callback, $message = null)
    {
        if (isset($this->data[$field])) {
            if (!$callback($this->data[$field])) {
                $this->errors[] = $message ?: "O campo {$field} é inválido";
            }
        }
        
        return $this;
    }
    
    /**
     * Check if validation passed
     */
    public function isValid()
    {
        return empty($this->errors);
    }
    
    /**
     * Get validation errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Get first error
     */
    public function getFirstError()
    {
        return $this->errors[0] ?? null;
    }
    
    /**
     * Validate CPF algorithm
     */
    private function isValidCPF($cpf)
    {
        // Remove non-digits
        $cpf = preg_replace('/\D/', '', $cpf);
        
        // Check if has 11 digits
        if (strlen($cpf) !== 11) {
            return false;
        }
        
        // Check for known invalid CPFs
        $invalidCpfs = [
            '00000000000', '11111111111', '22222222222', '33333333333',
            '44444444444', '55555555555', '66666666666', '77777777777',
            '88888888888', '99999999999'
        ];
        
        if (in_array($cpf, $invalidCpfs)) {
            return false;
        }
        
        // Validate first check digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $checkDigit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($cpf[9] != $checkDigit1) {
            return false;
        }
        
        // Validate second check digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $checkDigit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $cpf[10] == $checkDigit2;
    }
    
    /**
     * Validate CNPJ algorithm
     */
    private function isValidCNPJ($cnpj)
    {
        // Remove non-digits
        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        // Check if has 14 digits
        if (strlen($cnpj) !== 14) {
            return false;
        }
        
        // Check for known invalid CNPJs
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Validate first check digit
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $checkDigit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($cnpj[12] != $checkDigit1) {
            return false;
        }
        
        // Validate second check digit
        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $checkDigit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $cnpj[13] == $checkDigit2;
    }
}