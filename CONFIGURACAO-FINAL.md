# ✅ CONFIGURAÇÃO FINAL CONFIRMADA

## 🎯 Especificações do Servidor de Produção

### 📁 **Diretório:**
```
/var/www/html/
```

### 🌐 **URL de Acesso:**
```
rioclaro.rj.gov.br
```

**SEM subdiretório `/esic`**

---

## 🔧 Configuração do VirtualHost

### Arquivo: `/etc/httpd/conf.d/rioclaro.conf`

```apache
<VirtualHost *:80>
    ServerName rioclaro.rj.gov.br
    ServerAlias www.rioclaro.rj.gov.br
    
    # Sistema na raiz do servidor web
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/rioclaro-error.log
    CustomLog /var/log/httpd/rioclaro-access.log combined
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>

# HTTPS (após configurar SSL)
<VirtualHost *:443>
    ServerName rioclaro.rj.gov.br
    ServerAlias www.rioclaro.rj.gov.br
    
    DocumentRoot /var/www/html
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/rioclaro.rj.gov.br/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/rioclaro.rj.gov.br/privkey.pem
    
    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/rioclaro-ssl-error.log
    CustomLog /var/log/httpd/rioclaro-ssl-access.log combined
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

---

## 📋 Estrutura de Arquivos

```
/var/www/html/
├── index.php                 → redireciona para login.php
├── login.php                 → página de login
├── transparencia.php         → portal da transparência
├── dashboard.php             → dashboard do cidadão
├── admin.php                 → painel administrativo
├── novo-pedido.php           → formulário de pedido
├── acompanhar.php            → acompanhar protocolo
├── recurso.php               → interpor recurso
├── home.php                  → página inicial
├── .htaccess                 → regras Apache
├── bootstrap.php             → autoloader
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│       ├── logo-pmrcrj.png   → logo da prefeitura
│       └── logo-pmrcrj.svg
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
├── config/
│   └── constants.php
├── database/
│   └── schema_novo.sql
├── uploads/                  → (permissão 775)
└── logs/                     → (permissão 775)
```

---

## 🌐 URLs de Acesso

### Páginas Públicas:
- **Home:** `https://rioclaro.rj.gov.br/`
- **Login:** `https://rioclaro.rj.gov.br/login.php`
- **Novo Pedido:** `https://rioclaro.rj.gov.br/novo-pedido.php`
- **Acompanhar:** `https://rioclaro.rj.gov.br/acompanhar.php`
- **Transparência:** `https://rioclaro.rj.gov.br/transparencia.php`

### Páginas Administrativas:
- **Dashboard:** `https://rioclaro.rj.gov.br/dashboard.php`
- **Admin:** `https://rioclaro.rj.gov.br/admin.php`

---

## ⚙️ Configuração .htaccess

O arquivo `.htaccess` na raiz (`/var/www/html/.htaccess`) deve ter:

```apache
RewriteEngine On

# Força HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove extensão .php das URLs
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^\.]+)$ $1.php [NC,L]

# Página padrão
DirectoryIndex index.php login.php

# Desabilitar listagem de diretórios
Options -Indexes

# Bloquear arquivos sensíveis
<FilesMatch "\.(sql|md|env|json|lock)$">
    Require all denied
</FilesMatch>

# Bloquear pastas do sistema
RedirectMatch 404 /config/
RedirectMatch 404 /database/
RedirectMatch 404 /app/
RedirectMatch 404 /\.git
```

---

## ✅ Comandos de Configuração

### 1. Transferir arquivos para o servidor:

```bash
# Via SCP
scp -r * usuario@rioclaro.rj.gov.br:/var/www/html/

# Ou via Git (recomendado)
ssh usuario@rioclaro.rj.gov.br
cd /var/www/html
git clone https://github.com/DalmoVieira/esic.git .
```

### 2. Configurar permissões:

```bash
cd /var/www/html
sudo chown -R apache:apache .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
sudo chmod 775 uploads logs
```

### 3. Configurar SELinux:

```bash
sudo chcon -R -t httpd_sys_content_t /var/www/html
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/uploads
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/logs
sudo setsebool -P httpd_unified on
sudo setsebool -P httpd_can_network_connect_db on
sudo setsebool -P httpd_can_sendmail on
```

### 4. Criar VirtualHost:

```bash
sudo nano /etc/httpd/conf.d/rioclaro.conf
# (colar a configuração acima)

# Testar configuração
sudo httpd -t

# Reiniciar Apache
sudo systemctl restart httpd
```

### 5. Configurar Firewall:

```bash
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

### 6. Configurar SSL (Let's Encrypt):

```bash
sudo dnf install -y certbot python3-certbot-apache
sudo certbot --apache -d rioclaro.rj.gov.br -d www.rioclaro.rj.gov.br
```

### 7. Testar:

```bash
# Teste local
curl -I http://localhost/login.php

# Teste externo
curl -I http://rioclaro.rj.gov.br/login.php
```

**Resultado esperado:**
```
HTTP/1.1 200 OK
```

---

## 🗄️ Configuração do Banco de Dados

### 1. Criar banco e usuário:

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'esic_user'@'localhost' IDENTIFIED BY 'SenhaForteSegura123!';
GRANT ALL PRIVILEGES ON esic_db.* TO 'esic_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Importar schema:

```bash
mysql -u esic_user -p esic_db < /var/www/html/database/schema_novo.sql
```

### 3. Atualizar configuração:

```bash
sudo nano /var/www/html/config/constants.php
```

Ajustar:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'esic_db');
define('DB_USER', 'esic_user');
define('DB_PASS', 'SenhaForteSegura123!');
define('BASE_URL', 'https://rioclaro.rj.gov.br');
```

---

## 📊 Checklist de Deploy

```
☐ Arquivos em /var/www/html/
☐ Permissões: apache:apache, 755/644
☐ SELinux configurado
☐ VirtualHost criado (/etc/httpd/conf.d/rioclaro.conf)
☐ DocumentRoot = /var/www/html
☐ .htaccess na raiz
☐ Firewall liberado (80, 443)
☐ Apache reiniciado
☐ PHP-FPM rodando
☐ SSL configurado (Let's Encrypt)
☐ Banco de dados criado
☐ Schema importado
☐ config/constants.php atualizado
☐ Teste: curl -I https://rioclaro.rj.gov.br/login.php
☐ Resultado: HTTP/1.1 200 OK
```

---

## 🚨 Importante

### ❌ **NÃO é:**
- URL: `rioclaro.rj.gov.br/esic`
- Diretório: `/var/www/esic`
- Diretório: `/var/www/html/esic`

### ✅ **É:**
- URL: `rioclaro.rj.gov.br`
- Diretório: `/var/www/html`

---

## 📞 Suporte

Se encontrar problemas, execute o script de diagnóstico:

```bash
sudo bash /var/www/html/diagnostico-almalinux.sh
```

Ou verifique logs:

```bash
sudo tail -f /var/log/httpd/rioclaro-error.log
sudo tail -f /var/log/httpd/error_log
```

---

## 📚 Documentação Completa

- **DEPLOY_ALMALINUX9.md** - Guia completo detalhado
- **RESOLVER-404-ALMALINUX.md** - Solução de problemas
- **CAMINHO-CORRETO-ALMALINUX.md** - Especificações de caminho
- **diagnostico-almalinux.sh** - Script automatizado

---

✅ **Configuração confirmada e documentada!**

**Diretório:** `/var/www/html/`  
**URL:** `rioclaro.rj.gov.br`  
**Sistema pronto para deploy!** 🚀
