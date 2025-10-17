# 🚀 DEPLOY E-SIC - AlmaLinux 9
## Guia Completo para Servidor de Produção
### https://rioclaro.rj.gov.br/esic

---

## 🔍 **DIAGNÓSTICO DO ERRO 404**

O erro 404 em AlmaLinux 9 geralmente ocorre por:
1. ✅ DocumentRoot incorreto no VirtualHost
2. ✅ SELinux bloqueando acesso
3. ✅ Permissões de arquivo/diretório incorretas
4. ✅ Apache não reiniciado após configuração
5. ✅ Módulo mod_rewrite não habilitado
6. ✅ Arquivo .htaccess não sendo lido

---

## 📋 **PASSO 1: VERIFICAR ESTRUTURA DE ARQUIVOS**

### No servidor, verifique onde os arquivos estão:

```bash
# Conectar via SSH
ssh usuario@rioclaro.rj.gov.br

# Verificar estrutura
ls -la /var/www/html/esic
# ou
ls -la /var/www/esic
# ou
ls -la /usr/share/nginx/html/esic
```

**Estrutura esperada:**
```
/var/www/html/esic/
├── index.php
├── login.php
├── transparencia.php
├── assets/
├── app/
├── config/
├── uploads/
└── .htaccess (se Apache)
```

---

## 🔧 **PASSO 2: CORRIGIR CONFIGURAÇÃO DO APACHE/NGINX**

### Se estiver usando APACHE:

#### 2.1. Verificar qual Apache está instalado:
```bash
sudo httpd -v
sudo systemctl status httpd
```

#### 2.2. Editar Virtual Host:
```bash
sudo nano /etc/httpd/conf.d/esic.conf
```

**Configuração correta para AlmaLinux 9:**
```apache
<VirtualHost *:80>
    ServerName rioclaro.rj.gov.br
    ServerAlias www.rioclaro.rj.gov.br
    
    # IMPORTANTE: DocumentRoot deve apontar para onde os arquivos estão
    DocumentRoot /var/www/html/esic
    
    <Directory /var/www/html/esic>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Permitir .htaccess
        AllowOverride All
        
        # Desabilitar listagem de diretórios
        Options -Indexes
    </Directory>
    
    # Logs específicos
    ErrorLog /var/log/httpd/esic-error.log
    CustomLog /var/log/httpd/esic-access.log combined
    
    # PHP Settings
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>

# Redirecionamento HTTPS (adicionar depois de configurar SSL)
<VirtualHost *:443>
    ServerName rioclaro.rj.gov.br
    ServerAlias www.rioclaro.rj.gov.br
    
    DocumentRoot /var/www/html/esic
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/rioclaro.rj.gov.br/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/rioclaro.rj.gov.br/privkey.pem
    
    <Directory /var/www/html/esic>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/esic-ssl-error.log
    CustomLog /var/log/httpd/esic-ssl-access.log combined
</VirtualHost>
```

#### 2.3. Habilitar módulos necessários:
```bash
# Verificar módulos carregados
sudo httpd -M | grep rewrite
sudo httpd -M | grep ssl

# Se não estiverem ativos, editar configuração
sudo nano /etc/httpd/conf.modules.d/00-base.conf
```

Garantir que estas linhas estejam descomentadas:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule ssl_module modules/mod_ssl.so
```

#### 2.4. Testar configuração:
```bash
# Testar sintaxe
sudo httpd -t

# Se OK, reiniciar
sudo systemctl restart httpd
```

---

### Se estiver usando NGINX:

#### 2.1. Editar configuração do site:
```bash
sudo nano /etc/nginx/conf.d/esic.conf
```

**Configuração para AlmaLinux 9:**
```nginx
server {
    listen 80;
    listen [::]:80;
    
    server_name rioclaro.rj.gov.br www.rioclaro.rj.gov.br;
    
    root /var/www/html/esic;
    index index.php index.html;
    
    # Logs
    access_log /var/log/nginx/esic-access.log;
    error_log /var/log/nginx/esic-error.log;
    
    # Bloquear acesso a arquivos sensíveis
    location ~ /\. {
        deny all;
    }
    
    location ~ \.(sql|md|json|lock)$ {
        deny all;
    }
    
    # PHP processing
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Remover .php das URLs
    location / {
        try_files $uri $uri/ $uri.php?$query_string;
    }
    
    # Cache de arquivos estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}

# HTTPS (configurar depois do SSL)
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    
    server_name rioclaro.rj.gov.br www.rioclaro.rj.gov.br;
    
    root /var/www/html/esic;
    index index.php index.html;
    
    ssl_certificate /etc/letsencrypt/live/rioclaro.rj.gov.br/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/rioclaro.rj.gov.br/privkey.pem;
    
    # O restante é igual ao bloco HTTP acima
}
```

#### 2.2. Testar e reiniciar:
```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

## 🔒 **PASSO 3: RESOLVER PROBLEMAS DE SELINUX**

AlmaLinux 9 vem com SELinux ativo por padrão, que pode bloquear o Apache.

### 3.1. Verificar status do SELinux:
```bash
sudo getenforce
# Resultado esperado: Enforcing
```

### 3.2. Ajustar permissões do SELinux:
```bash
# Permitir que Apache leia os arquivos
sudo chcon -R -t httpd_sys_content_t /var/www/html/esic

# Permitir escrita em uploads
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/esic/uploads

# Permitir que Apache execute scripts
sudo setsebool -P httpd_can_network_connect on
sudo setsebool -P httpd_can_network_connect_db on
sudo setsebool -P httpd_unified on
sudo setsebool -P httpd_enable_homedirs on

# Se usar envio de email
sudo setsebool -P httpd_can_sendmail on
```

### 3.3. Se continuar com problema, verificar logs do SELinux:
```bash
sudo tail -f /var/log/audit/audit.log | grep denied

# Gerar política automaticamente para permitir
sudo grep httpd /var/log/audit/audit.log | audit2allow -M httpd_esic
sudo semodule -i httpd_esic.pp
```

---

## 📁 **PASSO 4: CORRIGIR PERMISSÕES DE ARQUIVOS**

```bash
cd /var/www/html/esic

# Proprietário correto (Apache no AlmaLinux)
sudo chown -R apache:apache .

# Permissões de diretórios
sudo find . -type d -exec chmod 755 {} \;

# Permissões de arquivos
sudo find . -type f -exec chmod 644 {} \;

# Diretório de uploads (precisa escrita)
sudo chmod 775 uploads
sudo chown apache:apache uploads

# Logs (se houver)
sudo mkdir -p logs
sudo chmod 775 logs
sudo chown apache:apache logs
```

---

## 🌐 **PASSO 5: CRIAR/CORRIGIR .htaccess**

Se estiver usando Apache, crie o arquivo `.htaccess` na raiz:

```bash
sudo nano /var/www/html/esic/.htaccess
```

**Conteúdo básico (sem banco de dados ainda):**
```apache
# Habilitar RewriteEngine
RewriteEngine On
RewriteBase /esic/

# Redirecionar para index.php se arquivo não existir
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Página padrão
DirectoryIndex index.php login.php

# Desabilitar listagem de diretórios
Options -Indexes

# Bloquear acesso a arquivos sensíveis
<FilesMatch "\.(sql|md|env|json|lock)$">
    Require all denied
</FilesMatch>

# Bloquear acesso a pastas do sistema
RedirectMatch 404 /config/
RedirectMatch 404 /database/
RedirectMatch 404 /app/
RedirectMatch 404 /\.git
```

**Salvar e definir permissão:**
```bash
sudo chmod 644 /var/www/html/esic/.htaccess
sudo chown apache:apache /var/www/html/esic/.htaccess
```

---

## 🔥 **PASSO 6: CONFIGURAR FIREWALL**

AlmaLinux 9 usa `firewalld`:

```bash
# Verificar status
sudo firewall-cmd --state

# Permitir HTTP e HTTPS
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https

# Se SSH não estiver aberto
sudo firewall-cmd --permanent --add-service=ssh

# Recarregar firewall
sudo firewall-cmd --reload

# Verificar regras
sudo firewall-cmd --list-all
```

---

## 🧪 **PASSO 7: TESTES E DIAGNÓSTICO**

### 7.1. Verificar se arquivos existem:
```bash
ls -la /var/www/html/esic/index.php
ls -la /var/www/html/esic/login.php
ls -la /var/www/html/esic/transparencia.php
```

### 7.2. Testar acesso direto aos arquivos:
```bash
# No navegador, tente acessar:
http://rioclaro.rj.gov.br/esic/login.php
http://rioclaro.rj.gov.br/esic/transparencia.php
http://rioclaro.rj.gov.br/esic/index.php
```

### 7.3. Verificar logs em tempo real:
```bash
# Apache
sudo tail -f /var/log/httpd/esic-error.log
sudo tail -f /var/log/httpd/error_log

# Nginx
sudo tail -f /var/log/nginx/esic-error.log
sudo tail -f /var/log/nginx/error.log

# SELinux
sudo tail -f /var/log/audit/audit.log | grep denied
```

### 7.4. Testar se PHP está funcionando:
```bash
# Criar arquivo de teste
sudo nano /var/www/html/esic/info.php
```

Conteúdo:
```php
<?php
phpinfo();
?>
```

Acessar no navegador:
```
http://rioclaro.rj.gov.br/esic/info.php
```

**IMPORTANTE:** Deletar depois do teste!
```bash
sudo rm /var/www/html/esic/info.php
```

### 7.5. Verificar PHP-FPM:
```bash
sudo systemctl status php-fpm
sudo systemctl restart php-fpm
```

---

## 🗄️ **PASSO 8: CONFIGURAR BANCO DE DADOS (QUANDO ESTIVER PRONTO)**

### 8.1. Instalar MariaDB:
```bash
sudo dnf install mariadb-server -y
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Configurar segurança
sudo mysql_secure_installation
```

### 8.2. Criar banco e usuário:
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

### 8.3. Importar schema:
```bash
sudo mysql -u esic_user -p esic_db < /var/www/html/esic/database/schema_novo.sql
```

### 8.4. Atualizar configurações:
```bash
sudo nano /var/www/html/esic/config/database.php
```

---

## 📊 **PASSO 9: COMANDOS DE DIAGNÓSTICO RÁPIDO**

Execute este script para diagnóstico completo:

```bash
#!/bin/bash
echo "=== DIAGNÓSTICO E-SIC ==="
echo ""

echo "1. Arquivos existem?"
ls -la /var/www/html/esic/*.php | head -5

echo ""
echo "2. Permissões corretas?"
ls -ld /var/www/html/esic

echo ""
echo "3. Apache/Nginx rodando?"
sudo systemctl status httpd 2>/dev/null || sudo systemctl status nginx

echo ""
echo "4. PHP-FPM rodando?"
sudo systemctl status php-fpm

echo ""
echo "5. SELinux contexto correto?"
ls -Z /var/www/html/esic | head -3

echo ""
echo "6. Firewall configurado?"
sudo firewall-cmd --list-services

echo ""
echo "7. Últimos erros Apache:"
sudo tail -5 /var/log/httpd/error_log 2>/dev/null || sudo tail -5 /var/log/nginx/error.log

echo ""
echo "8. Últimas negações SELinux:"
sudo grep denied /var/log/audit/audit.log | tail -3

echo ""
echo "=== FIM DO DIAGNÓSTICO ==="
```

**Salvar como:**
```bash
sudo nano /root/diagnostico-esic.sh
sudo chmod +x /root/diagnostico-esic.sh
sudo /root/diagnostico-esic.sh
```

---

## ✅ **CHECKLIST DE RESOLUÇÃO DO 404**

```
☐ Arquivos estão em /var/www/html/esic/
☐ VirtualHost configurado corretamente
☐ DocumentRoot aponta para /var/www/html/esic
☐ AllowOverride All está definido
☐ mod_rewrite habilitado (Apache)
☐ .htaccess criado e com permissões corretas
☐ SELinux configurado (httpd_sys_content_t)
☐ Permissões: apache:apache, 755/644
☐ Firewall liberou HTTP (80) e HTTPS (443)
☐ Apache/Nginx reiniciado após mudanças
☐ PHP-FPM ativo e funcionando
☐ Logs não mostram erros críticos
```

---

## 🔧 **SOLUÇÃO RÁPIDA - COMANDOS EM SEQUÊNCIA**

Execute estes comandos em ordem (ajuste o caminho se necessário):

```bash
# 1. Ir para o diretório
cd /var/www/html/esic

# 2. Corrigir permissões
sudo chown -R apache:apache .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
sudo chmod 775 uploads

# 3. Ajustar SELinux
sudo chcon -R -t httpd_sys_content_t .
sudo chcon -R -t httpd_sys_rw_content_t uploads
sudo setsebool -P httpd_unified on

# 4. Criar .htaccess básico
cat > .htaccess << 'EOF'
RewriteEngine On
RewriteBase /esic/
DirectoryIndex index.php login.php
Options -Indexes
EOF

# 5. Reiniciar serviços
sudo systemctl restart httpd
sudo systemctl restart php-fpm

# 6. Liberar firewall
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload

# 7. Testar
curl -I http://rioclaro.rj.gov.br/esic/login.php
```

---

## 📞 **AINDA COM ERRO 404?**

Se ainda estiver com erro, envie-me a saída destes comandos:

```bash
# Executar todos e copiar resultado:
pwd
ls -la /var/www/html/esic/*.php | head -10
sudo cat /etc/httpd/conf.d/esic.conf
sudo httpd -t
sudo httpd -M | grep rewrite
sudo getenforce
ls -Z /var/www/html/esic | head -5
sudo tail -20 /var/log/httpd/error_log
sudo firewall-cmd --list-all
curl -I http://rioclaro.rj.gov.br/esic/login.php
```

---

## 🎯 **RESULTADO ESPERADO**

Após seguir estes passos, você deve conseguir acessar:

- ✅ **http://rioclaro.rj.gov.br/esic/login.php** → Página de login
- ✅ **http://rioclaro.rj.gov.br/esic/transparencia.php** → Portal da transparência
- ✅ **http://rioclaro.rj.gov.br/esic/** → Redireciona para login

**O banco de dados NÃO é necessário** para corrigir o erro 404. O erro 404 é problema de configuração do servidor, não do banco!

---

## 📚 **DOCUMENTAÇÃO ALMALINUX 9**

- Apache: https://httpd.apache.org/docs/2.4/
- SELinux: https://access.redhat.com/documentation/en-us/red_hat_enterprise_linux/9/html/using_selinux/
- Firewalld: https://firewalld.org/documentation/

---

✅ **Siga este guia passo a passo e o erro 404 será resolvido!**
