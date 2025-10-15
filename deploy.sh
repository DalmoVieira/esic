#!/bin/bash
# ====================================================
# Script de Deploy Automatizado - E-SIC
# Sistema Eletrônico de Informação ao Cidadão
# Prefeitura Municipal de Rio Claro - SP
# ====================================================

set -e  # Parar em caso de erro

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variáveis de Configuração
DOMAIN="esic.rioclaro.sp.gov.br"
INSTALL_DIR="/var/www/esic"
DB_NAME="esic_db"
DB_USER="esic_user"
ADMIN_EMAIL="admin@rioclaro.sp.gov.br"

# Funções auxiliares
print_header() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Verificar se é root
if [[ $EUID -ne 0 ]]; then
   print_error "Este script deve ser executado como root (sudo)"
   exit 1
fi

# Detectar distribuição
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
    VER=$VERSION_ID
else
    print_error "Não foi possível detectar a distribuição Linux"
    exit 1
fi

print_header "INÍCIO DO DEPLOY E-SIC"
echo "Sistema Operacional: $OS $VER"
echo "Domínio: $DOMAIN"
echo "Diretório: $INSTALL_DIR"
echo ""

# ====================================================
# ETAPA 1: Atualizar Sistema
# ====================================================
print_header "ETAPA 1: Atualizando Sistema"

if [[ "$OS" == "ubuntu" ]] || [[ "$OS" == "debian" ]]; then
    apt update && apt upgrade -y
    print_success "Sistema atualizado (Debian/Ubuntu)"
elif [[ "$OS" == "centos" ]] || [[ "$OS" == "rhel" ]]; then
    yum update -y
    print_success "Sistema atualizado (CentOS/RHEL)"
fi

# ====================================================
# ETAPA 2: Instalar Dependências
# ====================================================
print_header "ETAPA 2: Instalando Dependências"

if [[ "$OS" == "ubuntu" ]] || [[ "$OS" == "debian" ]]; then
    apt install -y apache2 mysql-server php php-mysql php-mbstring \
        php-json php-curl php-gd php-zip php-xml php-fileinfo git \
        certbot python3-certbot-apache fail2ban
    
    WEB_USER="www-data"
    print_success "Dependências instaladas (Debian/Ubuntu)"
    
elif [[ "$OS" == "centos" ]] || [[ "$OS" == "rhel" ]]; then
    yum install -y httpd mariadb-server php php-mysql php-mbstring \
        php-json php-curl php-gd php-zip php-xml git certbot \
        python3-certbot-apache fail2ban
    
    WEB_USER="apache"
    print_success "Dependências instaladas (CentOS/RHEL)"
fi

# ====================================================
# ETAPA 3: Iniciar Serviços
# ====================================================
print_header "ETAPA 3: Iniciando Serviços"

if [[ "$OS" == "ubuntu" ]] || [[ "$OS" == "debian" ]]; then
    systemctl start apache2
    systemctl enable apache2
    systemctl start mysql
    systemctl enable mysql
    print_success "Apache e MySQL iniciados"
elif [[ "$OS" == "centos" ]] || [[ "$OS" == "rhel" ]]; then
    systemctl start httpd
    systemctl enable httpd
    systemctl start mariadb
    systemctl enable mariadb
    print_success "Apache e MariaDB iniciados"
fi

# ====================================================
# ETAPA 4: Criar Diretório e Clonar Repositório
# ====================================================
print_header "ETAPA 4: Criando Diretório e Clonando Repositório"

mkdir -p $INSTALL_DIR

echo "Você deseja:"
echo "1) Clonar do GitHub"
echo "2) Usar arquivos locais (já transferidos)"
read -p "Escolha (1 ou 2): " CLONE_OPTION

if [ "$CLONE_OPTION" == "1" ]; then
    read -p "URL do repositório GitHub: " REPO_URL
    git clone $REPO_URL $INSTALL_DIR
    print_success "Repositório clonado de $REPO_URL"
elif [ "$CLONE_OPTION" == "2" ]; then
    print_warning "Certifique-se de que os arquivos já estão em $INSTALL_DIR"
    read -p "Pressione ENTER para continuar..."
fi

# ====================================================
# ETAPA 5: Configurar Permissões
# ====================================================
print_header "ETAPA 5: Configurando Permissões"

cd $INSTALL_DIR

# Permissões de diretórios
find . -type d -exec chmod 755 {} \;

# Permissões de arquivos
find . -type f -exec chmod 644 {} \;

# Diretórios especiais
mkdir -p uploads logs
chmod 775 uploads logs
chown -R $WEB_USER:$WEB_USER uploads logs

print_success "Permissões configuradas"

# ====================================================
# ETAPA 6: Configurar Banco de Dados
# ====================================================
print_header "ETAPA 6: Configurando Banco de Dados"

echo "Gerando senha segura para o banco de dados..."
DB_PASS=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)

echo ""
print_warning "IMPORTANTE! Guarde estas credenciais:"
echo "Banco de Dados: $DB_NAME"
echo "Usuário: $DB_USER"
echo "Senha: $DB_PASS"
echo ""
read -p "Pressione ENTER para continuar..."

# Criar banco e usuário
mysql -u root <<MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

print_success "Banco de dados criado"

# Importar schema
if [ -f "$INSTALL_DIR/database/schema_novo.sql" ]; then
    mysql -u root $DB_NAME < $INSTALL_DIR/database/schema_novo.sql
    print_success "Schema importado"
else
    print_warning "Arquivo schema_novo.sql não encontrado. Import manual necessário."
fi

# Atualizar credenciais no código
sed -i "s/private const DB_NAME = .*/private const DB_NAME = '$DB_NAME';/" $INSTALL_DIR/app/config/Database.php
sed -i "s/private const DB_USER = .*/private const DB_USER = '$DB_USER';/" $INSTALL_DIR/app/config/Database.php
sed -i "s/private const DB_PASS = .*/private const DB_PASS = '$DB_PASS';/" $INSTALL_DIR/app/config/Database.php

print_success "Credenciais atualizadas no código"

# Atualizar base_url no banco
mysql -u $DB_USER -p$DB_PASS $DB_NAME <<SQL
UPDATE configuracoes SET valor = 'https://$DOMAIN' WHERE chave = 'base_url';
SQL

# ====================================================
# ETAPA 7: Configurar Apache Virtual Host
# ====================================================
print_header "ETAPA 7: Configurando Apache Virtual Host"

if [[ "$OS" == "ubuntu" ]] || [[ "$OS" == "debian" ]]; then
    VHOST_FILE="/etc/apache2/sites-available/esic.conf"
elif [[ "$OS" == "centos" ]] || [[ "$OS" == "rhel" ]]; then
    VHOST_FILE="/etc/httpd/conf.d/esic.conf"
fi

cat > $VHOST_FILE <<VHOST
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    
    ServerAdmin $ADMIN_EMAIL
    DocumentRoot $INSTALL_DIR
    
    <Directory $INSTALL_DIR>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    <Directory $INSTALL_DIR/uploads>
        Options -Indexes
        AllowOverride All
        Require all denied
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/esic-error.log
    CustomLog \${APACHE_LOG_DIR}/esic-access.log combined
    
    php_value upload_max_filesize 10M
    php_value post_max_size 12M
    php_value max_execution_time 300
</VirtualHost>
VHOST

if [[ "$OS" == "ubuntu" ]] || [[ "$OS" == "debian" ]]; then
    a2enmod rewrite ssl headers
    a2ensite esic.conf
    a2dissite 000-default.conf
    systemctl restart apache2
elif [[ "$OS" == "centos" ]] || [[ "$OS" == "rhel" ]]; then
    systemctl restart httpd
fi

print_success "Virtual Host configurado"

# ====================================================
# ETAPA 8: Configurar SSL com Let's Encrypt
# ====================================================
print_header "ETAPA 8: Configurando SSL"

echo "Configurar SSL com Let's Encrypt?"
read -p "(s/n): " SSL_OPTION

if [ "$SSL_OPTION" == "s" ] || [ "$SSL_OPTION" == "S" ]; then
    certbot --apache -d $DOMAIN -d www.$DOMAIN \
        --non-interactive --agree-tos --email $ADMIN_EMAIL \
        --redirect
    
    print_success "SSL configurado com sucesso!"
else
    print_warning "SSL não configurado. Configure manualmente depois."
fi

# ====================================================
# ETAPA 9: Configurar Cron Jobs
# ====================================================
print_header "ETAPA 9: Configurando Cron Jobs"

(crontab -l 2>/dev/null; echo "0 8 * * * php $INSTALL_DIR/cron/notificacoes.php >> /var/log/esic-cron.log 2>&1") | crontab -

print_success "Cron jobs configurados"

# ====================================================
# ETAPA 10: Criar Script de Backup
# ====================================================
print_header "ETAPA 10: Criando Script de Backup"

cat > /usr/local/bin/backup-esic.sh <<'BACKUP'
#!/bin/bash
BACKUP_DIR="/backup/esic"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR
mysqldump -u DB_USER -pDB_PASS DB_NAME | gzip > $BACKUP_DIR/esic_db_$DATE.sql.gz
tar -czf $BACKUP_DIR/esic_files_$DATE.tar.gz INSTALL_DIR/uploads/
find $BACKUP_DIR -name "esic_*" -mtime +7 -delete
BACKUP

sed -i "s/DB_USER/$DB_USER/" /usr/local/bin/backup-esic.sh
sed -i "s/DB_PASS/$DB_PASS/" /usr/local/bin/backup-esic.sh
sed -i "s/DB_NAME/$DB_NAME/" /usr/local/bin/backup-esic.sh
sed -i "s|INSTALL_DIR|$INSTALL_DIR|" /usr/local/bin/backup-esic.sh

chmod +x /usr/local/bin/backup-esic.sh

(crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/backup-esic.sh >> /var/log/esic-backup.log 2>&1") | crontab -

print_success "Script de backup criado"

# ====================================================
# ETAPA 11: Configurar Firewall
# ====================================================
print_header "ETAPA 11: Configurando Firewall"

if command -v ufw &> /dev/null; then
    ufw allow 80/tcp
    ufw allow 443/tcp
    ufw allow 22/tcp
    ufw --force enable
    print_success "Firewall UFW configurado"
elif command -v firewall-cmd &> /dev/null; then
    firewall-cmd --permanent --add-service=http
    firewall-cmd --permanent --add-service=https
    firewall-cmd --permanent --add-service=ssh
    firewall-cmd --reload
    print_success "Firewall firewalld configurado"
fi

# ====================================================
# ETAPA 12: Configurar Fail2Ban
# ====================================================
print_header "ETAPA 12: Configurando Fail2Ban"

systemctl enable fail2ban
systemctl start fail2ban

print_success "Fail2Ban ativado"

# ====================================================
# FINALIZAÇÃO
# ====================================================
print_header "DEPLOY CONCLUÍDO COM SUCESSO!"

echo ""
echo -e "${GREEN}==================================================${NC}"
echo -e "${GREEN}           E-SIC INSTALADO COM SUCESSO!          ${NC}"
echo -e "${GREEN}==================================================${NC}"
echo ""
echo "📋 INFORMAÇÕES IMPORTANTES:"
echo ""
echo "🌐 URL: https://$DOMAIN"
echo "📁 Diretório: $INSTALL_DIR"
echo ""
echo "🗄️  BANCO DE DADOS:"
echo "   Nome: $DB_NAME"
echo "   Usuário: $DB_USER"
echo "   Senha: $DB_PASS"
echo ""
echo "👤 PRIMEIRO ACESSO:"
echo "   Email: admin@rioclaro.rj.gov.br"
echo "   Senha: (definir no primeiro login)"
echo ""
echo "📧 Configurar SMTP em:"
echo "   https://$DOMAIN/admin-configuracoes.php?tipo=administrador"
echo ""
echo "✅ PRÓXIMOS PASSOS:"
echo "   1. Testar acesso ao sistema"
echo "   2. Configurar SMTP para emails"
echo "   3. Criar usuários administrativos"
echo "   4. Testar upload de anexos"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANTE: Salve as credenciais do banco em local seguro!${NC}"
echo ""

# Salvar credenciais em arquivo
cat > /root/.esic-credentials <<CREDS
E-SIC - Credenciais de Deploy
==============================
Data: $(date)
Domínio: $DOMAIN
Diretório: $INSTALL_DIR

Banco de Dados:
- Nome: $DB_NAME
- Usuário: $DB_USER
- Senha: $DB_PASS

Primeira configuração: https://$DOMAIN/admin-configuracoes.php
CREDS

chmod 600 /root/.esic-credentials

print_success "Credenciais salvas em /root/.esic-credentials"

echo ""
echo "🎉 Sistema pronto para uso!"
echo ""