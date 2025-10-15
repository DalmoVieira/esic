# 🚀 GUIA DE DEPLOY - E-SIC PARA PRODUÇÃO
## Sistema Eletrônico de Informação ao Cidadão
### Prefeitura Municipal de Rio Claro - SP

---

## 📋 **PRÉ-REQUISITOS DO SERVIDOR**

### Requisitos Mínimos:
- **Sistema Operacional:** Linux (Ubuntu 20.04+ / CentOS 7+ / Debian 10+)
- **Servidor Web:** Apache 2.4+ ou Nginx 1.18+
- **PHP:** 8.0 ou superior
- **MySQL/MariaDB:** 8.0+ / 10.5+
- **Memória RAM:** 2GB mínimo (4GB recomendado)
- **Disco:** 10GB livre
- **SSL/TLS:** Certificado válido (Let's Encrypt gratuito)

### Extensões PHP Necessárias:
```bash
php-mysql
php-pdo
php-mbstring
php-json
php-curl
php-gd
php-zip
php-xml
php-fileinfo
php-openssl
```

---

## 🔐 **ETAPA 1: PREPARAÇÃO DO AMBIENTE**

### 1.1. Atualizar o Sistema

```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# CentOS/RHEL
sudo yum update -y
```

### 1.2. Instalar Apache, PHP e MySQL

```bash
# Ubuntu/Debian
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-json \
    php-curl php-gd php-zip php-xml php-fileinfo -y

# CentOS/RHEL
sudo yum install httpd mariadb-server php php-mysql php-mbstring php-json \
    php-curl php-gd php-zip php-xml -y
```

### 1.3. Iniciar e Habilitar Serviços

```bash
# Ubuntu/Debian
sudo systemctl start apache2
sudo systemctl enable apache2
sudo systemctl start mysql
sudo systemctl enable mysql

# CentOS/RHEL
sudo systemctl start httpd
sudo systemctl enable httpd
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

### 1.4. Configurar Firewall

```bash
# UFW (Ubuntu/Debian)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable

# Firewalld (CentOS/RHEL)
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload
```

---

## 📦 **ETAPA 2: TRANSFERIR ARQUIVOS**

### 2.1. Criar Diretório no Servidor

```bash
sudo mkdir -p /var/www/esic
sudo chown -R www-data:www-data /var/www/esic  # Ubuntu/Debian
# OU
sudo chown -R apache:apache /var/www/esic      # CentOS/RHEL
```

### 2.2. Transferir Arquivos via SCP (do seu computador local)

```bash
# Da sua máquina Windows (PowerShell)
scp -r C:\xampp\htdocs\esic\* usuario@servidor.com.br:/var/www/esic/

# Ou via WinSCP, FileZilla, ou outro cliente FTP/SFTP
```

### 2.3. Alternativa: Usar Git (RECOMENDADO)

```bash
# No servidor
cd /var/www/esic
sudo git clone https://github.com/DalmoVieira/esic.git .

# Ou fazer pull se já existir
sudo git pull origin main
```

### 2.4. Definir Permissões Corretas

```bash
cd /var/www/esic

# Permissões de diretórios
sudo find . -type d -exec chmod 755 {} \;

# Permissões de arquivos
sudo find . -type f -exec chmod 644 {} \;

# Diretório de uploads (precisa escrita)
sudo mkdir -p uploads
sudo chmod 775 uploads
sudo chown www-data:www-data uploads  # Ubuntu/Debian
# OU
sudo chown apache:apache uploads      # CentOS/RHEL

# Diretório de logs (se houver)
sudo mkdir -p logs
sudo chmod 775 logs
sudo chown www-data:www-data logs
```

---

## 🗄️ **ETAPA 3: CONFIGURAR BANCO DE DADOS**

### 3.1. Criar Usuário e Banco de Dados

```bash
# Conectar ao MySQL
sudo mysql -u root -p

# Dentro do MySQL
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'esic_user'@'localhost' IDENTIFIED BY 'SenhaForteSegura123!@#';

GRANT ALL PRIVILEGES ON esic_db.* TO 'esic_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

### 3.2. Importar Schema do Banco

```bash
cd /var/www/esic

# Importar schema
sudo mysql -u esic_user -p esic_db < database/schema_novo.sql

# Verificar se as tabelas foram criadas
sudo mysql -u esic_user -p esic_db -e "SHOW TABLES;"
```

### 3.3. Atualizar Credenciais de Conexão

```bash
# Editar arquivo de configuração
sudo nano /var/www/esic/app/config/Database.php
```

**Alterar as constantes:**
```php
<?php
class Database {
    private static $instance = null;
    private $connection;
    
    // CONFIGURAÇÕES DE PRODUÇÃO
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'esic_db';
    private const DB_USER = 'esic_user';
    private const DB_PASS = 'SenhaForteSegura123!@#';
    private const DB_CHARSET = 'utf8mb4';
    
    // ... resto do código
}
```

---

## 🌐 **ETAPA 4: CONFIGURAR APACHE**

### 4.1. Criar Virtual Host

```bash
sudo nano /etc/apache2/sites-available/esic.conf
```

**Conteúdo do arquivo:**
```apache
<VirtualHost *:80>
    ServerName esic.rioclaro.sp.gov.br
    ServerAlias www.esic.rioclaro.sp.gov.br
    
    ServerAdmin admin@rioclaro.sp.gov.br
    DocumentRoot /var/www/esic
    
    <Directory /var/www/esic>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Diretório de uploads (sem listagem)
    <Directory /var/www/esic/uploads>
        Options -Indexes
        AllowOverride All
        Require all denied
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/esic-error.log
    CustomLog ${APACHE_LOG_DIR}/esic-access.log combined
    
    # Limites de upload
    php_value upload_max_filesize 10M
    php_value post_max_size 12M
    php_value max_execution_time 300
    php_value max_input_time 300
</VirtualHost>
```

### 4.2. Habilitar Módulos e Site

```bash
# Habilitar mod_rewrite
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Habilitar o site
sudo a2ensite esic.conf

# Desabilitar site padrão (opcional)
sudo a2dissite 000-default.conf

# Reiniciar Apache
sudo systemctl restart apache2
```

---

## 🔒 **ETAPA 5: CONFIGURAR SSL/HTTPS (OBRIGATÓRIO PARA LAI)**

### 5.1. Instalar Certbot (Let's Encrypt)

```bash
# Ubuntu/Debian
sudo apt install certbot python3-certbot-apache -y

# CentOS/RHEL
sudo yum install certbot python3-certbot-apache -y
```

### 5.2. Obter Certificado SSL

```bash
sudo certbot --apache -d esic.rioclaro.sp.gov.br -d www.esic.rioclaro.sp.gov.br
```

**Responda as perguntas:**
1. Email para notificações
2. Aceitar termos de serviço
3. Redirecionar HTTP para HTTPS: **SIM (recomendado)**

### 5.3. Renovação Automática

```bash
# Testar renovação
sudo certbot renew --dry-run

# Criar cron para renovação automática (já vem configurado)
sudo crontab -e

# Adicionar se não existir:
0 3 * * * certbot renew --quiet
```

---

## ⚙️ **ETAPA 6: CONFIGURAÇÕES DE SEGURANÇA**

### 6.1. Proteger Arquivos Sensíveis

```bash
# Criar/editar .htaccess na raiz
sudo nano /var/www/esic/.htaccess
```

**Conteúdo:**
```apache
# Desabilitar listagem de diretórios
Options -Indexes

# Proteger arquivos de configuração
<FilesMatch "\.(env|json|sql|md|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Proteger diretórios sensíveis
RedirectMatch 404 /\.git
RedirectMatch 404 /database
RedirectMatch 404 /logs

# Forçar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Proteção contra injeção de SQL
<IfModule mod_rewrite.c>
    RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=http:// [OR]
    RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [OR]
    RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC]
    RewriteRule .* - [F]
</IfModule>
```

### 6.2. Configurar php.ini para Produção

```bash
sudo nano /etc/php/8.x/apache2/php.ini
```

**Configurações recomendadas:**
```ini
# Modo de produção
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php/error.log

# Limites
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 300
memory_limit = 256M

# Segurança
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
enable_dl = Off

# Sessões
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

### 6.3. Criar Diretório de Logs

```bash
sudo mkdir -p /var/log/php
sudo chown www-data:www-data /var/log/php
sudo chmod 755 /var/log/php
```

---

## 📧 **ETAPA 7: CONFIGURAR EMAIL (SMTP)**

### 7.1. Opção 1: SMTP Externo (Gmail, SendGrid, etc.)

Configurar via painel administrativo:
```
URL: https://esic.rioclaro.sp.gov.br/admin-configuracoes.php?tipo=administrador

Configurações Gmail:
- Servidor SMTP: smtp.gmail.com
- Porta: 587 (TLS)
- Usuário: seuemail@gmail.com
- Senha: senha de app (não a senha normal)
```

### 7.2. Opção 2: Servidor SMTP Local (Postfix)

```bash
# Instalar Postfix
sudo apt install postfix mailutils -y

# Durante instalação, escolher: "Internet Site"
# Hostname: rioclaro.sp.gov.br

# Configurar
sudo nano /etc/postfix/main.cf
```

Adicionar/modificar:
```
myhostname = mail.rioclaro.sp.gov.br
mydomain = rioclaro.sp.gov.br
myorigin = $mydomain
inet_interfaces = all
```

Reiniciar:
```bash
sudo systemctl restart postfix
sudo systemctl enable postfix
```

---

## ⏰ **ETAPA 8: CONFIGURAR CRON JOBS**

### 8.1. Criar Cron para Notificações

```bash
sudo crontab -e
```

**Adicionar:**
```cron
# E-SIC - Notificações diárias às 8h
0 8 * * * php /var/www/esic/cron/notificacoes.php >> /var/log/esic-cron.log 2>&1

# E-SIC - Backup diário às 2h
0 2 * * * /usr/local/bin/backup-esic.sh >> /var/log/esic-backup.log 2>&1
```

### 8.2. Criar Script de Backup

```bash
sudo nano /usr/local/bin/backup-esic.sh
```

**Conteúdo:**
```bash
#!/bin/bash
# Script de Backup E-SIC

BACKUP_DIR="/backup/esic"
DATE=$(date +%Y%m%d_%H%M%S)
DB_USER="esic_user"
DB_PASS="SenhaForteSegura123!@#"
DB_NAME="esic_db"

# Criar diretório de backup
mkdir -p $BACKUP_DIR

# Backup do banco de dados
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/esic_db_$DATE.sql.gz

# Backup dos arquivos
tar -czf $BACKUP_DIR/esic_files_$DATE.tar.gz /var/www/esic/uploads/

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "esic_*" -mtime +7 -delete

echo "Backup concluído: $DATE"
```

**Dar permissão:**
```bash
sudo chmod +x /usr/local/bin/backup-esic.sh
```

---

## 🔍 **ETAPA 9: MONITORAMENTO E LOGS**

### 9.1. Configurar Logrotate

```bash
sudo nano /etc/logrotate.d/esic
```

**Conteúdo:**
```
/var/log/esic-*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 640 www-data www-data
    sharedscripts
    postrotate
        systemctl reload apache2 > /dev/null 2>&1 || true
    endscript
}
```

### 9.2. Monitorar Logs em Tempo Real

```bash
# Logs do Apache
sudo tail -f /var/log/apache2/esic-error.log
sudo tail -f /var/log/apache2/esic-access.log

# Logs do PHP
sudo tail -f /var/log/php/error.log

# Logs do MySQL
sudo tail -f /var/log/mysql/error.log

# Logs do Cron
sudo tail -f /var/log/esic-cron.log
```

---

## ✅ **ETAPA 10: TESTES DE PRODUÇÃO**

### 10.1. Checklist de Testes

```bash
# 1. Testar conexão com banco
curl https://esic.rioclaro.sp.gov.br/test_db.php

# 2. Verificar SSL
openssl s_client -connect esic.rioclaro.sp.gov.br:443 -servername esic.rioclaro.sp.gov.br

# 3. Testar permissões de upload
# Acessar: https://esic.rioclaro.sp.gov.br/novo-pedido-v2.php
# Tentar fazer upload de arquivo

# 4. Testar envio de email
# Acessar: https://esic.rioclaro.sp.gov.br/admin-configuracoes.php
# Aba "Testar Email"

# 5. Verificar cron
sudo grep CRON /var/log/syslog | tail -20
```

### 10.2. Testes Funcionais

- ✅ Cadastrar novo usuário
- ✅ Criar novo pedido
- ✅ Fazer upload de anexo
- ✅ Acompanhar protocolo
- ✅ Responder pedido (admin)
- ✅ Interpor recurso
- ✅ Receber notificação por email
- ✅ Gerar relatórios

---

## 🛡️ **ETAPA 11: HARDENING E SEGURANÇA ADICIONAL**

### 11.1. Instalar ModSecurity (WAF)

```bash
sudo apt install libapache2-mod-security2 -y
sudo a2enmod security2
sudo systemctl restart apache2
```

### 11.2. Configurar Fail2Ban

```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 11.3. Atualizar Base URL no Banco

```bash
mysql -u esic_user -p esic_db

UPDATE configuracoes 
SET valor = 'https://esic.rioclaro.sp.gov.br' 
WHERE chave = 'base_url';

EXIT;
```

---

## 📊 **ETAPA 12: OTIMIZAÇÃO DE DESEMPENHO**

### 12.1. Habilitar Compressão

```bash
sudo a2enmod deflate
sudo systemctl restart apache2
```

### 12.2. Habilitar Cache

```bash
sudo a2enmod expires
sudo a2enmod headers
sudo systemctl restart apache2
```

### 12.3. Otimizar MySQL

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Adicionar:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
query_cache_size = 64M
query_cache_limit = 2M
```

```bash
sudo systemctl restart mysql
```

---

## 📋 **CHECKLIST FINAL DE DEPLOY**

```
☐ Servidor atualizado
☐ Apache/PHP/MySQL instalados e configurados
☐ Arquivos transferidos via Git ou SCP
☐ Permissões corretas (755/644)
☐ Banco de dados criado e importado
☐ Credenciais de conexão atualizadas
☐ Virtual Host configurado
☐ SSL/HTTPS ativo (Let's Encrypt)
☐ .htaccess configurado
☐ php.ini otimizado para produção
☐ SMTP configurado e testado
☐ Cron jobs ativos
☐ Backup automático configurado
☐ Logs configurados (logrotate)
☐ Fail2Ban e ModSecurity instalados
☐ Base URL atualizada no banco
☐ Todos os testes funcionais passando
☐ Monitoramento ativo
```

---

## 🚨 **TROUBLESHOOTING COMUM**

### Problema: "Permission denied" no upload

```bash
sudo chmod 775 /var/www/esic/uploads
sudo chown -R www-data:www-data /var/www/esic/uploads
```

### Problema: Emails não são enviados

```bash
# Verificar logs
sudo tail -f /var/log/mail.log

# Testar SMTP
telnet smtp.gmail.com 587
```

### Problema: Erro 500

```bash
# Ver logs
sudo tail -f /var/log/apache2/esic-error.log
sudo tail -f /var/log/php/error.log
```

### Problema: Banco não conecta

```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Testar conexão
mysql -u esic_user -p -h localhost esic_db
```

---

## 📞 **SUPORTE E MANUTENÇÃO**

### Comandos Úteis:

```bash
# Reiniciar serviços
sudo systemctl restart apache2
sudo systemctl restart mysql

# Ver status
sudo systemctl status apache2
sudo systemctl status mysql

# Espaço em disco
df -h

# Uso de memória
free -h

# Processos ativos
top
htop

# Conexões MySQL
mysql -u root -p -e "SHOW PROCESSLIST;"
```

---

## 📚 **DOCUMENTAÇÃO ADICIONAL**

- **Lei 12.527/2011:** http://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm
- **Apache Docs:** https://httpd.apache.org/docs/2.4/
- **PHP Manual:** https://www.php.net/manual/pt_BR/
- **Let's Encrypt:** https://letsencrypt.org/docs/

---

## ✅ **SISTEMA PRONTO PARA PRODUÇÃO!**

Após seguir todos os passos, seu E-SIC estará:
- ✅ Seguro (SSL, firewall, hardening)
- ✅ Performático (cache, compressão)
- ✅ Monitorado (logs, backups)
- ✅ Conforme LAI (Lei 12.527/2011)
- ✅ Profissional e estável

**Acesse:** https://esic.rioclaro.sp.gov.br

🎉 **Parabéns! Sistema em produção!**