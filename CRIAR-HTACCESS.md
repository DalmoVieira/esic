# 📋 CRIAR .htaccess NO SERVIDOR

## O arquivo .htaccess está faltando em /var/www/html/

---

## 🚀 **SOLUÇÃO RÁPIDA**

### Opção 1: Copiar do Projeto Local

**No Windows PowerShell:**

```powershell
# Copiar .htaccess para o servidor
scp C:\xampp\htdocs\esic\.htaccess dalmo@rioclaro.rj.gov.br:/tmp/

# Conectar ao servidor
ssh dalmo@rioclaro.rj.gov.br

# No servidor:
sudo mv /tmp/.htaccess /var/www/html/
sudo chown apache:apache /var/www/html/.htaccess
sudo chmod 644 /var/www/html/.htaccess
```

---

### Opção 2: Criar Diretamente no Servidor

**No servidor (como root ou dalmo com sudo):**

```bash
# Criar arquivo .htaccess
sudo nano /var/www/html/.htaccess
```

**Cole este conteúdo:**

```apache
# E-SIC - Configuração Apache para /var/www/html/

# Habilitar RewriteEngine
RewriteEngine On

# Base da aplicação (na raiz do domínio)
RewriteBase /

# Força HTTPS (importante para segurança)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Página inicial padrão
DirectoryIndex index.php login.php

# Desabilitar listagem de diretórios
Options -Indexes

# Bloquear acesso a arquivos sensíveis
<Files "*.sql">
    Require all denied
</Files>

<Files "*.md">
    Require all denied
</Files>

<Files ".env">
    Require all denied
</Files>

<FilesMatch "^\.">
    Require all denied
</FilesMatch>

# Bloquear acesso direto a pastas do sistema
RedirectMatch 404 /config/
RedirectMatch 404 /database/
RedirectMatch 404 /app/
RedirectMatch 404 /\.git/

# Headers de segurança
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "DENY"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' cdn.jsdelivr.net"
</IfModule>

# Cache para performance
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>

# Compressão GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/javascript
</IfModule>

# Limites de upload
php_value upload_max_filesize 10M
php_value post_max_size 12M
php_value max_execution_time 300
