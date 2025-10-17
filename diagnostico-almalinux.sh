#!/bin/bash
#
# Script de Diagnóstico E-SIC - AlmaLinux 9
# Identifica e resolve problemas de erro 404
#
# Uso: sudo bash diagnostico-almalinux.sh
#

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Cabeçalho
echo -e "${BLUE}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     E-SIC - Diagnóstico AlmaLinux 9                ║${NC}"
echo -e "${BLUE}║     Resolução de Erro 404                          ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════╝${NC}"
echo ""

# Verificar se é root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}✖ Por favor, execute como root (sudo)${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Executando como root${NC}"
echo ""

# Função para verificar status
check_status() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ $1${NC}"
        return 0
    else
        echo -e "${RED}✖ $1${NC}"
        return 1
    fi
}

# 1. VERIFICAR DIRETÓRIO DO PROJETO
echo -e "${BLUE}[1/12] Verificando diretório do projeto...${NC}"

ESIC_PATHS=(
    "/var/www/html/esic"
    "/var/www/esic"
    "/usr/share/nginx/html/esic"
    "/home/*/public_html/esic"
)

FOUND_PATH=""
for path in "${ESIC_PATHS[@]}"; do
    if [ -d "$path" ]; then
        FOUND_PATH=$path
        echo -e "${GREEN}✓ Encontrado em: $path${NC}"
        break
    fi
done

if [ -z "$FOUND_PATH" ]; then
    echo -e "${RED}✖ Diretório E-SIC não encontrado!${NC}"
    echo "Caminhos testados:"
    printf '%s\n' "${ESIC_PATHS[@]}"
    exit 1
fi

ESIC_DIR=$FOUND_PATH
cd "$ESIC_DIR"

# 2. VERIFICAR ARQUIVOS ESSENCIAIS
echo ""
echo -e "${BLUE}[2/12] Verificando arquivos essenciais...${NC}"

FILES=("index.php" "login.php" "transparencia.php" "config/constants.php")
MISSING_FILES=0

for file in "${FILES[@]}"; do
    if [ -f "$ESIC_DIR/$file" ]; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${RED}✖ Faltando: $file${NC}"
        ((MISSING_FILES++))
    fi
done

if [ $MISSING_FILES -gt 0 ]; then
    echo -e "${YELLOW}⚠ Arquivos faltando. Sistema pode não funcionar corretamente.${NC}"
fi

# 3. VERIFICAR SERVIDOR WEB
echo ""
echo -e "${BLUE}[3/12] Verificando servidor web...${NC}"

if systemctl is-active --quiet httpd; then
    echo -e "${GREEN}✓ Apache (httpd) está rodando${NC}"
    WEB_SERVER="httpd"
    WEB_USER="apache"
elif systemctl is-active --quiet nginx; then
    echo -e "${GREEN}✓ Nginx está rodando${NC}"
    WEB_SERVER="nginx"
    WEB_USER="nginx"
else
    echo -e "${RED}✖ Nenhum servidor web ativo!${NC}"
    echo "Instalando Apache..."
    dnf install -y httpd
    systemctl start httpd
    systemctl enable httpd
    WEB_SERVER="httpd"
    WEB_USER="apache"
fi

# 4. VERIFICAR PHP-FPM
echo ""
echo -e "${BLUE}[4/12] Verificando PHP-FPM...${NC}"

if systemctl is-active --quiet php-fpm; then
    echo -e "${GREEN}✓ PHP-FPM está rodando${NC}"
    PHP_VERSION=$(php -v | head -1 | awk '{print $2}')
    echo "  Versão: $PHP_VERSION"
else
    echo -e "${RED}✖ PHP-FPM não está ativo${NC}"
    echo "Iniciando PHP-FPM..."
    systemctl start php-fpm
    systemctl enable php-fpm
fi

# 5. VERIFICAR PERMISSÕES
echo ""
echo -e "${BLUE}[5/12] Verificando e corrigindo permissões...${NC}"

CURRENT_OWNER=$(stat -c '%U' "$ESIC_DIR")
if [ "$CURRENT_OWNER" != "$WEB_USER" ]; then
    echo -e "${YELLOW}⚠ Proprietário incorreto: $CURRENT_OWNER (deveria ser $WEB_USER)${NC}"
    echo "  Corrigindo..."
    chown -R $WEB_USER:$WEB_USER "$ESIC_DIR"
    check_status "Proprietário corrigido para $WEB_USER"
else
    echo -e "${GREEN}✓ Proprietário correto: $WEB_USER${NC}"
fi

echo "  Ajustando permissões de arquivos e diretórios..."
find "$ESIC_DIR" -type d -exec chmod 755 {} \;
find "$ESIC_DIR" -type f -exec chmod 644 {} \;

if [ -d "$ESIC_DIR/uploads" ]; then
    chmod 775 "$ESIC_DIR/uploads"
    echo -e "${GREEN}✓ Permissões de uploads ajustadas${NC}"
fi

# 6. VERIFICAR E CONFIGURAR SELINUX
echo ""
echo -e "${BLUE}[6/12] Verificando SELinux...${NC}"

SELINUX_STATUS=$(getenforce)
echo "  Status: $SELINUX_STATUS"

if [ "$SELINUX_STATUS" == "Enforcing" ]; then
    echo "  Configurando contextos SELinux..."
    
    chcon -R -t httpd_sys_content_t "$ESIC_DIR"
    check_status "Contexto httpd_sys_content_t aplicado"
    
    if [ -d "$ESIC_DIR/uploads" ]; then
        chcon -R -t httpd_sys_rw_content_t "$ESIC_DIR/uploads"
        check_status "Contexto de escrita em uploads aplicado"
    fi
    
    echo "  Configurando booleanos SELinux..."
    setsebool -P httpd_unified on 2>/dev/null
    setsebool -P httpd_can_network_connect_db on 2>/dev/null
    setsebool -P httpd_can_sendmail on 2>/dev/null
    
    echo -e "${GREEN}✓ SELinux configurado${NC}"
elif [ "$SELINUX_STATUS" == "Permissive" ]; then
    echo -e "${YELLOW}⚠ SELinux está em modo Permissivo${NC}"
else
    echo -e "${GREEN}✓ SELinux está desabilitado${NC}"
fi

# 7. VERIFICAR MÓDULOS APACHE
echo ""
echo -e "${BLUE}[7/12] Verificando módulos do Apache...${NC}"

if [ "$WEB_SERVER" == "httpd" ]; then
    if httpd -M 2>/dev/null | grep -q rewrite_module; then
        echo -e "${GREEN}✓ mod_rewrite habilitado${NC}"
    else
        echo -e "${RED}✖ mod_rewrite não encontrado${NC}"
    fi
    
    if httpd -M 2>/dev/null | grep -q ssl_module; then
        echo -e "${GREEN}✓ mod_ssl habilitado${NC}"
    else
        echo -e "${YELLOW}⚠ mod_ssl não encontrado (necessário para HTTPS)${NC}"
    fi
fi

# 8. VERIFICAR/CRIAR .htaccess
echo ""
echo -e "${BLUE}[8/12] Verificando arquivo .htaccess...${NC}"

if [ "$WEB_SERVER" == "httpd" ]; then
    if [ -f "$ESIC_DIR/.htaccess" ]; then
        echo -e "${GREEN}✓ .htaccess existe${NC}"
        echo "  Tamanho: $(stat -c%s "$ESIC_DIR/.htaccess") bytes"
    else
        echo -e "${YELLOW}⚠ .htaccess não encontrado. Criando...${NC}"
        
        cat > "$ESIC_DIR/.htaccess" << 'EOF'
# E-SIC - Configuração Apache
RewriteEngine On
RewriteBase /esic/

# Página inicial padrão
DirectoryIndex index.php login.php

# Desabilitar listagem de diretórios
Options -Indexes

# Bloquear arquivos sensíveis
<FilesMatch "\.(sql|md|env|json|lock)$">
    Require all denied
</FilesMatch>

# Redirecionar para index.php se arquivo não existir
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
EOF
        
        chmod 644 "$ESIC_DIR/.htaccess"
        chown $WEB_USER:$WEB_USER "$ESIC_DIR/.htaccess"
        echo -e "${GREEN}✓ .htaccess criado${NC}"
    fi
fi

# 9. VERIFICAR VIRTUALHOST
echo ""
echo -e "${BLUE}[9/12] Verificando VirtualHost...${NC}"

if [ "$WEB_SERVER" == "httpd" ]; then
    VHOST_FILE="/etc/httpd/conf.d/esic.conf"
    
    if [ -f "$VHOST_FILE" ]; then
        echo -e "${GREEN}✓ VirtualHost existe: $VHOST_FILE${NC}"
        
        # Verificar DocumentRoot
        DOC_ROOT=$(grep -i "DocumentRoot" "$VHOST_FILE" | head -1 | awk '{print $2}')
        echo "  DocumentRoot: $DOC_ROOT"
        
        if [ "$DOC_ROOT" != "$ESIC_DIR" ]; then
            echo -e "${YELLOW}⚠ DocumentRoot incorreto!${NC}"
            echo "  Esperado: $ESIC_DIR"
            echo "  Atual: $DOC_ROOT"
        fi
        
        # Verificar AllowOverride
        if grep -q "AllowOverride All" "$VHOST_FILE"; then
            echo -e "${GREEN}✓ AllowOverride All configurado${NC}"
        else
            echo -e "${RED}✖ AllowOverride All não encontrado!${NC}"
        fi
    else
        echo -e "${YELLOW}⚠ VirtualHost não encontrado. Criando...${NC}"
        
        cat > "$VHOST_FILE" << EOF
<VirtualHost *:80>
    ServerName rioclaro.rj.gov.br
    ServerAlias www.rioclaro.rj.gov.br
    
    DocumentRoot $ESIC_DIR
    
    <Directory $ESIC_DIR>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/esic-error.log
    CustomLog /var/log/httpd/esic-access.log combined
</VirtualHost>
EOF
        
        echo -e "${GREEN}✓ VirtualHost criado${NC}"
    fi
    
    # Testar configuração Apache
    echo "  Testando configuração do Apache..."
    if httpd -t 2>/dev/null; then
        echo -e "${GREEN}✓ Configuração do Apache válida${NC}"
    else
        echo -e "${RED}✖ Erro na configuração do Apache!${NC}"
        httpd -t
    fi
fi

# 10. VERIFICAR FIREWALL
echo ""
echo -e "${BLUE}[10/12] Verificando firewall...${NC}"

if systemctl is-active --quiet firewalld; then
    echo -e "${GREEN}✓ Firewalld está ativo${NC}"
    
    if firewall-cmd --list-services | grep -q http; then
        echo -e "${GREEN}✓ Serviço HTTP permitido${NC}"
    else
        echo -e "${YELLOW}⚠ Liberando HTTP no firewall...${NC}"
        firewall-cmd --permanent --add-service=http
        firewall-cmd --reload
        echo -e "${GREEN}✓ HTTP liberado${NC}"
    fi
    
    if firewall-cmd --list-services | grep -q https; then
        echo -e "${GREEN}✓ Serviço HTTPS permitido${NC}"
    else
        echo -e "${YELLOW}⚠ Liberando HTTPS no firewall...${NC}"
        firewall-cmd --permanent --add-service=https
        firewall-cmd --reload
        echo -e "${GREEN}✓ HTTPS liberado${NC}"
    fi
else
    echo -e "${YELLOW}⚠ Firewalld não está ativo${NC}"
fi

# 11. VERIFICAR LOGS RECENTES
echo ""
echo -e "${BLUE}[11/12] Verificando logs recentes...${NC}"

if [ "$WEB_SERVER" == "httpd" ]; then
    ERROR_LOG="/var/log/httpd/error_log"
    
    if [ -f "$ERROR_LOG" ]; then
        RECENT_ERRORS=$(tail -50 "$ERROR_LOG" | grep -i error | wc -l)
        echo "  Erros recentes: $RECENT_ERRORS"
        
        if [ $RECENT_ERRORS -gt 0 ]; then
            echo -e "${YELLOW}  Últimos 3 erros:${NC}"
            tail -50 "$ERROR_LOG" | grep -i error | tail -3
        fi
    fi
fi

# Verificar SELinux denials
if [ "$SELINUX_STATUS" == "Enforcing" ]; then
    SELINUX_DENIALS=$(grep denied /var/log/audit/audit.log 2>/dev/null | grep httpd | tail -50 | wc -l)
    if [ $SELINUX_DENIALS -gt 0 ]; then
        echo -e "${YELLOW}⚠ $SELINUX_DENIALS negações SELinux encontradas para httpd${NC}"
        echo "  Execute: ausearch -m avc -ts recent | grep httpd"
    else
        echo -e "${GREEN}✓ Sem negações SELinux recentes${NC}"
    fi
fi

# 12. REINICIAR SERVIÇOS
echo ""
echo -e "${BLUE}[12/12] Reiniciando serviços...${NC}"

systemctl restart php-fpm
check_status "PHP-FPM reiniciado"

systemctl restart $WEB_SERVER
check_status "$WEB_SERVER reiniciado"

# RESUMO FINAL
echo ""
echo -e "${BLUE}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║              RESUMO DO DIAGNÓSTICO                 ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${GREEN}Diretório E-SIC:${NC} $ESIC_DIR"
echo -e "${GREEN}Servidor Web:${NC} $WEB_SERVER"
echo -e "${GREEN}Usuário Web:${NC} $WEB_USER"
echo -e "${GREEN}PHP-FPM:${NC} $(systemctl is-active php-fpm)"
echo -e "${GREEN}SELinux:${NC} $SELINUX_STATUS"
echo ""

# TESTES FINAIS
echo -e "${BLUE}Executando testes finais...${NC}"
echo ""

# Teste 1: Arquivo existe e é legível?
if [ -r "$ESIC_DIR/login.php" ]; then
    echo -e "${GREEN}✓ login.php é legível${NC}"
else
    echo -e "${RED}✖ login.php não é legível!${NC}"
fi

# Teste 2: Curl local
echo "Testando acesso local..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/esic/login.php)
echo "  HTTP Code: $HTTP_CODE"

if [ "$HTTP_CODE" == "200" ]; then
    echo -e "${GREEN}✓ Login.php responde com 200 OK${NC}"
elif [ "$HTTP_CODE" == "404" ]; then
    echo -e "${RED}✖ Ainda retornando 404${NC}"
    echo ""
    echo -e "${YELLOW}Possíveis causas:${NC}"
    echo "  1. DocumentRoot incorreto no VirtualHost"
    echo "  2. Arquivo não existe no caminho especificado"
    echo "  3. SELinux bloqueando acesso"
    echo "  4. Permissões incorretas"
else
    echo -e "${YELLOW}⚠ Código inesperado: $HTTP_CODE${NC}"
fi

# PRÓXIMOS PASSOS
echo ""
echo -e "${BLUE}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║               PRÓXIMOS PASSOS                      ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════╝${NC}"
echo ""

if [ "$HTTP_CODE" == "200" ]; then
    echo -e "${GREEN}✓ Sistema funcionando! Acesse:${NC}"
    echo "  http://rioclaro.rj.gov.br/esic/login.php"
    echo ""
    echo -e "${YELLOW}Ainda precisa:${NC}"
    echo "  1. Configurar SSL/HTTPS (certbot)"
    echo "  2. Configurar banco de dados"
    echo "  3. Importar schema SQL"
else
    echo -e "${YELLOW}Para resolver o 404, verifique:${NC}"
    echo ""
    echo "1. Editar VirtualHost:"
    echo "   sudo nano /etc/httpd/conf.d/esic.conf"
    echo "   Garantir: DocumentRoot $ESIC_DIR"
    echo ""
    echo "2. Ver logs de erro:"
    echo "   sudo tail -f /var/log/httpd/esic-error.log"
    echo ""
    echo "3. Testar configuração:"
    echo "   sudo httpd -t"
    echo ""
    echo "4. Ver negações SELinux:"
    echo "   sudo ausearch -m avc -ts recent | grep httpd"
fi

echo ""
echo -e "${GREEN}Diagnóstico concluído!${NC}"
echo ""
