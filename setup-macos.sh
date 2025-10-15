#!/bin/bash

##############################################################################
# Script de Instalação Automática do E-SIC para macOS
# Versão: 1.0.0
# Autor: Dalmo Vieira
# Data: Outubro 2025
##############################################################################

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variáveis
PROJECT_DIR="$HOME/Projects/esic"
DB_NAME="esic_db"
DB_USER="esic_dev"
DB_PASS="EsicDev@2025"

##############################################################################
# Funções
##############################################################################

print_header() {
    echo ""
    echo -e "${BLUE}╔══════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║${NC}  ${GREEN}E-SIC - Instalação Automática para macOS${NC}                   ${BLUE}║${NC}"
    echo -e "${BLUE}╚══════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_step() {
    echo -e "\n${BLUE}▶${NC} ${GREEN}$1${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

check_command() {
    if command -v $1 &> /dev/null; then
        print_success "$1 já está instalado"
        return 0
    else
        print_warning "$1 não encontrado"
        return 1
    fi
}

##############################################################################
# Verificação de Sistema
##############################################################################

print_header

print_step "Verificando sistema operacional..."
if [[ "$OSTYPE" != "darwin"* ]]; then
    print_error "Este script é para macOS apenas!"
    exit 1
fi
print_success "macOS detectado"

##############################################################################
# 1. Instalação do Homebrew
##############################################################################

print_step "1/10 - Instalando Homebrew..."

if ! check_command brew; then
    echo "Instalando Homebrew..."
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
    
    # Adicionar ao PATH
    if [[ $(uname -m) == 'arm64' ]]; then
        echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
        eval "$(/opt/homebrew/bin/brew shellenv)"
    fi
    
    print_success "Homebrew instalado"
else
    brew update
fi

##############################################################################
# 2. Instalação do PHP
##############################################################################

print_step "2/10 - Instalando PHP 8.2..."

if ! check_command php; then
    brew install php@8.2
    
    # Adicionar ao PATH
    echo 'export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"' >> ~/.zshrc
    echo 'export PATH="/opt/homebrew/opt/php@8.2/sbin:$PATH"' >> ~/.zshrc
    
    export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"
    
    print_success "PHP instalado"
else
    php_version=$(php -v | head -n 1 | cut -d " " -f 2)
    print_success "PHP $php_version já instalado"
fi

# Configurar PHP
print_step "Configurando PHP..."
PHP_INI="/opt/homebrew/etc/php/8.2/php.ini"

if [ -f "$PHP_INI" ]; then
    cp "$PHP_INI" "$PHP_INI.backup"
    
    sed -i '' 's/upload_max_filesize = .*/upload_max_filesize = 10M/' "$PHP_INI"
    sed -i '' 's/post_max_size = .*/post_max_size = 12M/' "$PHP_INI"
    sed -i '' 's/memory_limit = .*/memory_limit = 256M/' "$PHP_INI"
    sed -i '' 's/max_execution_time = .*/max_execution_time = 300/' "$PHP_INI"
    sed -i '' 's/;date.timezone =.*/date.timezone = America\/Sao_Paulo/' "$PHP_INI"
    
    print_success "PHP configurado"
fi

# Iniciar PHP-FPM
brew services start php@8.2

##############################################################################
# 3. Instalação do MySQL
##############################################################################

print_step "3/10 - Instalando MySQL..."

if ! check_command mysql; then
    brew install mysql
    brew services start mysql
    print_success "MySQL instalado"
    
    print_warning "Execute 'mysql_secure_installation' manualmente após a instalação"
else
    mysql_version=$(mysql --version | awk '{print $3}')
    print_success "MySQL $mysql_version já instalado"
fi

##############################################################################
# 4. Instalação do Apache
##############################################################################

print_step "4/10 - Instalando Apache..."

# Parar Apache nativo se estiver rodando
sudo apachectl stop 2>/dev/null

if ! check_command httpd; then
    brew install httpd
    print_success "Apache instalado"
fi

# Configurar Apache
print_step "Configurando Apache..."
HTTPD_CONF="/opt/homebrew/etc/httpd/httpd.conf"

if [ -f "$HTTPD_CONF" ]; then
    cp "$HTTPD_CONF" "$HTTPD_CONF.backup"
    
    # Habilitar mod_rewrite
    sed -i '' 's/#LoadModule rewrite_module/LoadModule rewrite_module/' "$HTTPD_CONF"
    
    # Habilitar vhosts
    sed -i '' 's/#LoadModule vhost_alias_module/LoadModule vhost_alias_module/' "$HTTPD_CONF"
    sed -i '' 's/#Include \/opt\/homebrew\/etc\/httpd\/extra\/httpd-vhosts.conf/Include \/opt\/homebrew\/etc\/httpd\/extra\/httpd-vhosts.conf/' "$HTTPD_CONF"
    
    print_success "Apache configurado"
fi

##############################################################################
# 5. Instalação do Git
##############################################################################

print_step "5/10 - Verificando Git..."

if ! check_command git; then
    brew install git
    print_success "Git instalado"
fi

##############################################################################
# 6. Clonar Projeto
##############################################################################

print_step "6/10 - Clonando projeto do GitHub..."

if [ -d "$PROJECT_DIR" ]; then
    print_warning "Diretório do projeto já existe. Pulando clone."
else
    mkdir -p "$HOME/Projects"
    cd "$HOME/Projects"
    
    git clone https://github.com/DalmoVieira/esic.git
    
    if [ $? -eq 0 ]; then
        print_success "Projeto clonado"
    else
        print_error "Erro ao clonar projeto"
        exit 1
    fi
fi

# Criar diretórios necessários
cd "$PROJECT_DIR"
mkdir -p uploads logs
chmod 775 uploads logs

##############################################################################
# 7. Configurar Banco de Dados
##############################################################################

print_step "7/10 - Configurando banco de dados..."

# Criar usuário e banco
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
USE $DB_NAME;
SOURCE $PROJECT_DIR/database/schema_novo.sql;
EOF

if [ $? -eq 0 ]; then
    print_success "Banco de dados configurado"
    echo ""
    echo -e "${YELLOW}Credenciais do banco:${NC}"
    echo "  Database: $DB_NAME"
    echo "  Username: $DB_USER"
    echo "  Password: $DB_PASS"
    echo ""
else
    print_error "Erro ao configurar banco de dados"
    print_warning "Configure manualmente seguindo SETUP_MACOS.md"
fi

##############################################################################
# 8. Configurar Virtual Host
##############################################################################

print_step "8/10 - Configurando Virtual Host..."

VHOSTS_CONF="/opt/homebrew/etc/httpd/extra/httpd-vhosts.conf"
CURRENT_USER=$(whoami)

cat >> "$VHOSTS_CONF" << EOF

# E-SIC Virtual Host
<VirtualHost *:8080>
    ServerName esic.local
    DocumentRoot "$PROJECT_DIR"
    
    <Directory "$PROJECT_DIR">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "$PROJECT_DIR/logs/error.log"
    CustomLog "$PROJECT_DIR/logs/access.log" common
</VirtualHost>
EOF

print_success "Virtual Host configurado"

# Adicionar ao /etc/hosts
print_step "Adicionando entrada ao /etc/hosts..."
if ! grep -q "esic.local" /etc/hosts; then
    echo "127.0.0.1    esic.local" | sudo tee -a /etc/hosts > /dev/null
    print_success "Entrada adicionada ao /etc/hosts"
fi

##############################################################################
# 9. Criar arquivo .htaccess
##############################################################################

print_step "9/10 - Criando .htaccess..."

cat > "$PROJECT_DIR/.htaccess" << 'EOF'
# Habilitar mod_rewrite
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
</IfModule>

# Configurações de segurança
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# Desabilitar listagem de diretórios
Options -Indexes

# Configurações de upload
php_value upload_max_filesize 10M
php_value post_max_size 12M
php_value memory_limit 256M
php_value max_execution_time 300
EOF

print_success ".htaccess criado"

##############################################################################
# 10. Reiniciar Serviços
##############################################################################

print_step "10/10 - Reiniciando serviços..."

# Verificar configuração do Apache
apachectl configtest

if [ $? -eq 0 ]; then
    brew services restart httpd
    brew services restart php@8.2
    brew services restart mysql
    
    print_success "Serviços reiniciados"
else
    print_error "Erro na configuração do Apache"
    print_warning "Verifique os logs: apachectl configtest"
fi

##############################################################################
# Finalização
##############################################################################

echo ""
echo -e "${BLUE}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║${NC}  ${GREEN}✓ Instalação Concluída!${NC}                                      ${BLUE}║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""

print_success "E-SIC instalado com sucesso!"
echo ""
echo -e "${YELLOW}Próximos passos:${NC}"
echo ""
echo "1. Edite as configurações do banco:"
echo "   ${BLUE}nano $PROJECT_DIR/app/config/Database.php${NC}"
echo ""
echo "2. Acesse o sistema no navegador:"
echo "   ${BLUE}http://esic.local:8080/${NC}"
echo ""
echo "3. Teste a conexão com o banco:"
echo "   ${BLUE}http://esic.local:8080/test_db.php${NC}"
echo ""
echo "4. Abra o projeto no VS Code:"
echo "   ${BLUE}code $PROJECT_DIR${NC}"
echo ""
echo -e "${YELLOW}Credenciais do MySQL:${NC}"
echo "   Database: ${GREEN}$DB_NAME${NC}"
echo "   Username: ${GREEN}$DB_USER${NC}"
echo "   Password: ${GREEN}$DB_PASS${NC}"
echo ""
echo -e "${YELLOW}Comandos úteis:${NC}"
echo "   ${BLUE}brew services list${NC}              - Ver status dos serviços"
echo "   ${BLUE}brew services restart httpd${NC}     - Reiniciar Apache"
echo "   ${BLUE}tail -f $PROJECT_DIR/logs/error.log${NC} - Ver logs"
echo ""
echo -e "${YELLOW}Documentação completa:${NC}"
echo "   ${BLUE}$PROJECT_DIR/SETUP_MACOS.md${NC}"
echo ""
echo "Desenvolvido com ❤️  para macOS"
echo ""
