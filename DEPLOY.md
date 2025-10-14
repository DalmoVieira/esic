# 🚀 Guia de Deploy - E-SIC para Hostinger

## 📋 Checklist Pré-Deploy

### ✅ Preparação Local
- [ ] Projeto funcionando localmente
- [ ] Banco de dados testado
- [ ] Arquivos de configuração ajustados
- [ ] Backup do projeto atual

### ✅ Preparação Hostinger
- [ ] Conta Hostinger ativa
- [ ] Painel de controle acessível
- [ ] Domínio configurado
- [ ] SSL/HTTPS configurado

## 🔧 Passo 1: Preparar o Projeto

### 1.1 Criar arquivo .htaccess para produção
```apache
# Arquivo: .htaccess (raiz do projeto)
RewriteEngine On

# Força HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove extensão .php das URLs
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^\.]+)$ $1.php [NC,L]

# Segurança
<Files "*.sql">
    Require all denied
</Files>

<Files "config/*">
    Require all denied
</Files>

# Cache para arquivos estáticos
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>
```

### 1.2 Criar configuração de produção
```php
<?php
// Arquivo: config/production.php
return [
    'database' => [
        'host' => 'localhost', // ou IP fornecido pela Hostinger
        'dbname' => 'u123456789_esic', // Nome do banco na Hostinger
        'username' => 'u123456789_user', // Usuário do banco
        'password' => 'SUA_SENHA_SEGURA', // Senha do banco
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => 'E-SIC - Produção',
        'url' => 'https://seudominio.com.br',
        'debug' => false,
        'environment' => 'production'
    ],
    'email' => [
        'smtp_host' => 'smtp.hostinger.com',
        'smtp_port' => 587,
        'smtp_user' => 'noreply@seudominio.com.br',
        'smtp_pass' => 'senha_email',
        'from_name' => 'E-SIC Sistema'
    ],
    'security' => [
        'jwt_secret' => 'chave_super_secreta_256_bits',
        'session_lifetime' => 7200,
        'max_login_attempts' => 5
    ]
];
```

### 1.3 Ajustar arquivos de configuração
```php
<?php
// Arquivo: config/database.php - Atualizar para produção
$environment = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (strpos($environment, 'localhost') !== false || strpos($environment, '127.0.0.1') !== false) {
    // Desenvolvimento
    return [
        'host' => 'localhost',
        'dbname' => 'esic_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
} else {
    // Produção
    return include 'production.php';
}
```

## 📊 Passo 2: Configurar Banco de Dados na Hostinger

### 2.1 Criar Banco via Painel
1. **Acesse o hPanel da Hostinger**
2. **Vá em "Banco de Dados" > "MySQL"**
3. **Clique em "Criar novo banco"**
4. **Nome:** `u123456789_esic` (exemplo)
5. **Usuário:** `u123456789_user`
6. **Senha:** Crie uma senha forte
7. **Anote as credenciais!**

### 2.2 Importar Schema
```sql
-- Conecte via phpMyAdmin da Hostinger
-- Importe o arquivo: database/esic_schema.sql
-- Ou execute manualmente:

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'operador', 'cidadao') DEFAULT 'cidadao',
    ativo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    protocolo VARCHAR(20) UNIQUE NOT NULL,
    usuario_id INT,
    assunto VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('recebido', 'em_analise', 'respondido', 'recurso', 'arquivado') DEFAULT 'recebido',
    prazo_resposta DATE,
    resposta TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Continue com outras tabelas...
```

## 📁 Passo 3: Upload dos Arquivos

### 3.1 Opção A: Via Painel da Hostinger
1. **Acesse "Gerenciador de Arquivos"**
2. **Navegue até `/public_html/`**
3. **Crie pasta `/public_html/esic/`** (se quiser em subdiretório)
4. **Upload todos os arquivos do projeto**
5. **Extraia se necessário**

### 3.2 Opção B: Via FTP/SFTP
```bash
# Configurações FTP da Hostinger (exemplo)
Host: ftp.seudominio.com.br
Usuário: u123456789
Senha: sua_senha_ftp
Porta: 21 (FTP) ou 22 (SFTP)
Diretório: /public_html/

# Usando FileZilla ou WinSCP
# Conecte e faça upload da pasta completa
```

### 3.3 Opção C: Via Git (Recomendado)
```bash
# SSH na Hostinger (se disponível no plano)
cd /public_html/
git clone https://github.com/DalmoVieira/esic.git
cd esic
git checkout main
```

## ⚙️ Passo 4: Configurar Permissões

### 4.1 Permissões de Arquivos
```bash
# Via SSH ou Gerenciador de Arquivos
chmod 755 /public_html/esic/
chmod 644 *.php
chmod 755 uploads/
chmod 644 uploads/.htaccess
chmod 600 config/production.php
```

### 4.2 Verificar Propriedade
- **Usuário:** Deve ser o usuário do hosting
- **Grupo:** www-data ou similar
- **Uploads:** Pasta deve ter permissão de escrita

## 🔒 Passo 5: Segurança e SSL

### 5.1 Configurar SSL/HTTPS
1. **No hPanel: "SSL" > "Gerenciar"**
2. **Ativar "Let's Encrypt" gratuito**
3. **Forçar HTTPS** nas configurações
4. **Testar:** https://seudominio.com.br

### 5.2 Arquivo de Segurança
```php
<?php
// Arquivo: security.php
// Verificações de segurança

// Bloquear acesso direto
if (!defined('ESIC_SECURE')) {
    die('Acesso negado');
}

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Configurações de sessão segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
```

## 🧪 Passo 6: Testes de Produção

### 6.1 Criar página de teste
```php
<?php
// Arquivo: test-production.php
define('ESIC_SECURE', true);

echo "<h1>🧪 Teste E-SIC Produção</h1>";

// Teste 1: Conexão com banco
try {
    $config = include 'config/database.php';
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
        $config['username'],
        $config['password']
    );
    echo "✅ Banco de dados: CONECTADO<br>";
} catch (Exception $e) {
    echo "❌ Banco de dados: ERRO - " . $e->getMessage() . "<br>";
}

// Teste 2: Permissões
if (is_writable('uploads/')) {
    echo "✅ Uploads: GRAVÁVEL<br>";
} else {
    echo "❌ Uploads: SEM PERMISSÃO<br>";
}

// Teste 3: SSL
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    echo "✅ SSL: ATIVO<br>";
} else {
    echo "⚠️ SSL: INATIVO<br>";
}

// Teste 4: PHP
echo "✅ PHP: " . phpversion() . "<br>";

// Teste 5: Extensões
$extensions = ['mysqli', 'pdo', 'mbstring', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext: INSTALADO<br>";
    } else {
        echo "❌ $ext: FALTANDO<br>";
    }
}

echo "<hr>";
echo "<a href='index.php'>🏠 Ir para E-SIC</a>";
?>
```

### 6.2 Lista de Verificação
- [ ] **URL Principal:** https://seudominio.com.br/esic/
- [ ] **Nova Solicitação:** /novo-pedido funcionando
- [ ] **Acompanhar:** /acompanhar funcionando  
- [ ] **Transparência:** /transparencia funcionando
- [ ] **Banco de dados** conectando
- [ ] **SSL** ativo (cadeado verde)
- [ ] **Formulários** enviando dados
- [ ] **Uploads** funcionando
- [ ] **Email** enviando (se configurado)

## 🚨 Passo 7: Monitoramento

### 7.1 Logs de Erro
```php
<?php
// Arquivo: logs.php (apenas para admin)
if (!isset($_SESSION['admin'])) die('Acesso negado');

$logFile = '/home/usuario/logs/error.log'; // Caminho da Hostinger
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    echo "<pre>$logs</pre>";
} else {
    echo "Nenhum log encontrado";
}
?>
```

### 7.2 Monitoramento Automático
```php
<?php
// Arquivo: monitor.php
// Para executar via cron job

$url = 'https://seudominio.com.br/esic/';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    // Enviar alerta por email
    mail('admin@seudominio.com.br', 'E-SIC Offline', "Status: $httpCode");
}
?>
```

## 🎯 Checklist Final

### ✅ Pré-Deploy
- [ ] Código testado localmente
- [ ] Configurações de produção criadas
- [ ] Credenciais de banco anotadas
- [ ] Backup do projeto feito

### ✅ Deploy
- [ ] Arquivos enviados para servidor
- [ ] Banco de dados criado e populado
- [ ] Permissões configuradas
- [ ] SSL ativado

### ✅ Pós-Deploy
- [ ] Testes de produção executados
- [ ] Todas as páginas funcionando
- [ ] Formulários testados
- [ ] Monitoramento ativo
- [ ] Backup automático configurado

## 📞 Suporte

### Hostinger
- **Painel:** https://hpanel.hostinger.com.br
- **Suporte:** 24/7 via chat
- **Documentação:** https://support.hostinger.com.br

### Projeto E-SIC
- **Issues:** https://github.com/DalmoVieira/esic/issues
- **Email:** suporte@seudominio.com.br

---

**🚀 Boa sorte com o deploy!** 

Lembre-se de fazer backup antes de qualquer alteração em produção!