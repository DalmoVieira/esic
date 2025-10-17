#!/bin/bash
# Script de configuração inicial no Mac
# E-SIC v3.0.0 - Sistema Eletrônico de Informação ao Cidadão

clear
echo "╔══════════════════════════════════════════════════════════╗"
echo "║  🔧 E-SIC - Configuração de Desenvolvimento para macOS  ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Verificar Homebrew
echo "1️⃣  Verificando Homebrew..."
if ! command -v brew &> /dev/null; then
    echo -e "${RED}❌ Homebrew não encontrado${NC}"
    echo "Instalando Homebrew..."
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
else
    echo -e "${GREEN}✅ Homebrew instalado: $(brew --version | head -1)${NC}"
fi

# 2. Verificar PHP
echo ""
echo "2️⃣  Verificando PHP..."
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP não encontrado${NC}"
    echo "Instalando PHP 8.2..."
    brew install php@8.2
    brew link php@8.2 --force --overwrite
else
    PHP_VERSION=$(php -v | head -1 | awk '{print $2}')
    echo -e "${GREEN}✅ PHP instalado: $PHP_VERSION${NC}"
    
    # Verificar se é versão >= 8.0
    if [[ $(echo "$PHP_VERSION" | cut -d. -f1) -lt 8 ]]; then
        echo -e "${YELLOW}⚠️  Versão PHP antiga detectada. Recomendado: PHP 8.2+${NC}"
        read -p "Deseja atualizar para PHP 8.2? (s/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Ss]$ ]]; then
            brew install php@8.2
            brew link php@8.2 --force --overwrite
        fi
    fi
fi

# 3. Verificar Composer
echo ""
echo "3️⃣  Verificando Composer..."
if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer não encontrado${NC}"
    echo "Instalando Composer..."
    brew install composer
else
    echo -e "${GREEN}✅ Composer instalado: $(composer --version | head -1)${NC}"
fi

# 4. Verificar MySQL
echo ""
echo "4️⃣  Verificando MySQL..."
if ! command -v mysql &> /dev/null; then
    echo -e "${YELLOW}⚠️  MySQL não encontrado${NC}"
    read -p "Deseja instalar MySQL? (s/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        brew install mysql
        brew services start mysql
        echo -e "${GREEN}✅ MySQL instalado e iniciado${NC}"
    else
        echo "Você pode instalar depois com: brew install mysql"
    fi
else
    echo -e "${GREEN}✅ MySQL instalado${NC}"
    if brew services list | grep mysql | grep started &> /dev/null; then
        echo -e "${GREEN}✅ MySQL em execução${NC}"
    else
        echo -e "${YELLOW}⚠️  MySQL não está em execução${NC}"
        read -p "Deseja iniciar MySQL? (s/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Ss]$ ]]; then
            brew services start mysql
        fi
    fi
fi

# 5. Instalar dependências do projeto
echo ""
echo "5️⃣  Verificando dependências do projeto..."
if [ -f "composer.json" ]; then
    echo "📦 Instalando dependências via Composer..."
    composer install --no-interaction
    echo -e "${GREEN}✅ Dependências instaladas${NC}"
else
    echo -e "${YELLOW}⚠️  composer.json não encontrado${NC}"
fi

# 6. Criar diretórios necessários
echo ""
echo "6️⃣  Criando estrutura de diretórios..."
mkdir -p uploads logs cache tmp database/backups
echo -e "${GREEN}✅ Diretórios criados${NC}"

# 7. Ajustar permissões
echo ""
echo "7️⃣  Ajustando permissões..."
chmod -R 755 .
chmod -R 777 uploads logs cache tmp
echo -e "${GREEN}✅ Permissões ajustadas${NC}"

# 8. Configurar arquivo .env
echo ""
echo "8️⃣  Configurando variáveis de ambiente..."
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}✅ Arquivo .env criado a partir do .env.example${NC}"
    else
        echo "Criando .env padrão..."
        cat > .env << 'EOF'
# Ambiente
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de Dados
DB_HOST=localhost
DB_PORT=3306
DB_NAME=esic_db
DB_USER=esic_user
DB_PASS=senha123

# Email (desenvolvimento)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=
MAIL_PASS=
EOF
        echo -e "${GREEN}✅ Arquivo .env criado${NC}"
    fi
else
    echo -e "${GREEN}✅ Arquivo .env já existe${NC}"
fi

# 9. Configurar banco de dados
echo ""
echo "9️⃣  Configurando banco de dados..."
if command -v mysql &> /dev/null; then
    read -p "Deseja criar o banco de dados agora? (s/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        echo "Digite a senha do root do MySQL (ou pressione Enter se não houver):"
        mysql -u root -p << 'MYSQL_SCRIPT'
CREATE DATABASE IF NOT EXISTS esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'esic_user'@'localhost' IDENTIFIED BY 'senha123';
GRANT ALL PRIVILEGES ON esic_db.* TO 'esic_user'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✅ Banco de dados criado${NC}"
            
            # Importar schema
            if [ -f "database/schema_novo.sql" ]; then
                read -p "Deseja importar o schema? (s/n) " -n 1 -r
                echo
                if [[ $REPLY =~ ^[Ss]$ ]]; then
                    mysql -u esic_user -psenha123 esic_db < database/schema_novo.sql
                    echo -e "${GREEN}✅ Schema importado${NC}"
                fi
            fi
        else
            echo -e "${RED}❌ Erro ao criar banco de dados${NC}"
        fi
    fi
fi

# 10. Verificar extensões PHP necessárias
echo ""
echo "🔟 Verificando extensões PHP..."
PHP_EXTENSIONS=("mysqli" "pdo" "pdo_mysql" "mbstring" "json" "curl" "gd" "zip")
for ext in "${PHP_EXTENSIONS[@]}"; do
    if php -m | grep -i "^$ext$" &> /dev/null; then
        echo -e "${GREEN}✅ $ext${NC}"
    else
        echo -e "${RED}❌ $ext (não instalada)${NC}"
    fi
done

# 11. Criar scripts auxiliares
echo ""
echo "1️⃣1️⃣  Criando scripts auxiliares..."

# Script start-dev.sh
cat > start-dev.sh << 'EOF'
#!/bin/bash

clear
echo "╔══════════════════════════════════════════════════════════╗"
echo "║         🚀 E-SIC - Servidor de Desenvolvimento          ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""
echo "📍 Servidor: http://localhost:8000"
echo "📁 Diretório: $(pwd)"
echo "🔗 URL principal: http://localhost:8000/login.php"
echo ""
echo "⚠️  Pressione Ctrl+C para parar o servidor"
echo ""

# Iniciar servidor PHP
php -S localhost:8000 -t .
EOF

chmod +x start-dev.sh

# Script stop-dev.sh
cat > stop-dev.sh << 'EOF'
#!/bin/bash
echo "🛑 Parando servidores..."
pkill -f "php -S localhost:8000"
echo "✅ Servidor parado"
EOF

chmod +x stop-dev.sh

# Script backup-db.sh
cat > backup-db.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="database/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/esic_db_$TIMESTAMP.sql"

mkdir -p $BACKUP_DIR

echo "📦 Fazendo backup do banco de dados..."
mysqldump -u esic_user -psenha123 esic_db > $BACKUP_FILE

if [ $? -eq 0 ]; then
    gzip $BACKUP_FILE
    echo "✅ Backup criado: $BACKUP_FILE.gz"
else
    echo "❌ Erro ao criar backup"
fi
EOF

chmod +x backup-db.sh

echo -e "${GREEN}✅ Scripts criados: start-dev.sh, stop-dev.sh, backup-db.sh${NC}"

# 12. Resumo final
echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║              ✅ CONFIGURAÇÃO CONCLUÍDA! ✅               ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""
echo "🚀 Para iniciar o servidor de desenvolvimento:"
echo "   ${GREEN}./start-dev.sh${NC}"
echo ""
echo "📚 Documentação disponível:"
echo "   - README.md"
echo "   - PACOTE-MACOS.md"
echo "   - DEPLOY.md"
echo ""
echo "🔧 Scripts úteis criados:"
echo "   - ${GREEN}./start-dev.sh${NC}  → Inicia servidor"
echo "   - ${GREEN}./stop-dev.sh${NC}   → Para servidor"
echo "   - ${GREEN}./backup-db.sh${NC}  → Backup do banco"
echo ""
echo "🌐 URLs de acesso:"
echo "   - Login: ${GREEN}http://localhost:8000/login.php${NC}"
echo "   - Dashboard: ${GREEN}http://localhost:8000/dashboard.php${NC}"
echo ""
echo "💡 Dica: Use VS Code com as extensões recomendadas"
echo "   ${YELLOW}code .${NC}"
echo ""
