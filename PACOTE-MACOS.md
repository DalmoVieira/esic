# 📦 PACOTE E-SIC PARA DESENVOLVIMENTO - macOS + VS Code

## Configuração Completa para Desenvolvimento em Mac

---

## 📋 **O QUE ESTÁ INCLUÍDO**

Este pacote contém:
- ✅ Código-fonte completo do E-SIC v3.0.0
- ✅ Configurações do VS Code (extensões recomendadas)
- ✅ Servidor PHP embutido (sem necessidade de XAMPP)
- ✅ Scripts de automação para Mac
- ✅ Documentação completa
- ✅ Configuração de desenvolvimento local

---

## 🖥️ **REQUISITOS NO MAC**

### Instalar Homebrew (se ainda não tiver):

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Instalar PHP 8.2+:

```bash
# Instalar PHP via Homebrew
brew install php@8.2

# Verificar versão
php -v
```

### Instalar Composer (gerenciador de dependências PHP):

```bash
brew install composer
composer --version
```

### Instalar Git (se não tiver):

```bash
brew install git
git --version
```

### Instalar VS Code:

```bash
# Baixar do site
# https://code.visualstudio.com/download

# Ou via Homebrew
brew install --cask visual-studio-code
```

---

## 📦 **CRIAR PACOTE NO WINDOWS**

Execute no PowerShell (no diretório do projeto):

```powershell
# Ir para o diretório do projeto
cd C:\xampp\htdocs\esic

# Criar arquivo ZIP completo (incluindo arquivos ocultos)
Compress-Archive -Path * -DestinationPath "esic-dev-macos.zip" -Force

# Incluir .htaccess e outros arquivos ocultos
Get-ChildItem -Path . -Force | Where-Object { $_.Name -like ".*" } | ForEach-Object {
    Compress-Archive -Path $_.FullName -Update -DestinationPath "esic-dev-macos.zip"
}

# Verificar tamanho
Get-Item esic-dev-macos.zip | Select-Object Name, @{Name="SizeMB";Expression={[math]::Round($_.Length/1MB,2)}}
```

---

## 🚀 **INSTALAR NO MAC**

### 1. Transferir o arquivo

Copie `esic-dev-macos.zip` para o Mac via:
- USB drive
- Email
- Google Drive / Dropbox
- AirDrop

### 2. Extrair no Mac

```bash
# Criar diretório de desenvolvimento
mkdir -p ~/Projects/esic

# Extrair ZIP
unzip ~/Downloads/esic-dev-macos.zip -d ~/Projects/esic

# Ir para o diretório
cd ~/Projects/esic
```

### 3. Instalar dependências (se houver)

```bash
# Se o projeto usar Composer
composer install

# Dar permissões de escrita
chmod -R 755 .
chmod -R 777 uploads logs cache
```

---

## 🔧 **CONFIGURAR VS CODE NO MAC**

### 1. Abrir projeto no VS Code

```bash
# Abrir VS Code no diretório do projeto
code ~/Projects/esic
```

### 2. Instalar extensões recomendadas

Instale estas extensões no VS Code (Cmd+Shift+X):

- **PHP Intelephense** (bmewburn.vscode-intelephense-client)
- **PHP Debug** (xdebug.php-debug)
- **MySQL** (cweijan.vscode-mysql-client2)
- **GitLens** (eamodio.gitlens)
- **EditorConfig** (editorconfig.editorconfig)
- **Better Comments** (aaron-bond.better-comments)
- **Bracket Pair Colorizer** (coenraads.bracket-pair-colorizer-2)
- **Material Icon Theme** (pkief.material-icon-theme)

### 3. Configurar settings.json do VS Code

Criar: `.vscode/settings.json`

```json
{
    "php.validate.executablePath": "/opt/homebrew/bin/php",
    "php.suggest.basic": true,
    "editor.formatOnSave": true,
    "editor.tabSize": 4,
    "editor.insertSpaces": true,
    "files.encoding": "utf8",
    "files.eol": "\n",
    "files.trimTrailingWhitespace": true,
    "files.insertFinalNewline": true,
    "files.exclude": {
        "**/.git": true,
        "**/.DS_Store": true,
        "**/node_modules": true,
        "**/vendor": true,
        "**/*.zip": true
    },
    "search.exclude": {
        "**/node_modules": true,
        "**/vendor": true,
        "**/.git": true
    }
}
```

---

## 🌐 **RODAR SERVIDOR DE DESENVOLVIMENTO**

### Opção 1: Servidor PHP Embutido (Mais Simples)

```bash
# Ir para o diretório do projeto
cd ~/Projects/esic

# Iniciar servidor na porta 8000
php -S localhost:8000

# Acessar no navegador
# http://localhost:8000/login.php
```

### Opção 2: Script de Inicialização

Criar arquivo: `start-dev.sh`

```bash
#!/bin/bash

echo "🚀 Iniciando E-SIC em modo desenvolvimento..."
echo ""
echo "📍 Servidor: http://localhost:8000"
echo "📁 Diretório: $(pwd)"
echo ""
echo "⚠️  Pressione Ctrl+C para parar o servidor"
echo ""

# Iniciar servidor PHP
php -S localhost:8000 -t .
```

Dar permissão de execução:

```bash
chmod +x start-dev.sh
./start-dev.sh
```

### Opção 3: Usar MAMP (Alternativa ao XAMPP no Mac)

```bash
# Baixar MAMP
# https://www.mamp.info/en/downloads/

# Instalar e configurar
# Document Root: ~/Projects/esic
# PHP Version: 8.2+
# Apache Port: 8888
```

---

## 🗄️ **CONFIGURAR BANCO DE DADOS NO MAC**

### Opção 1: MySQL via Homebrew

```bash
# Instalar MySQL
brew install mysql

# Iniciar MySQL
brew services start mysql

# Conectar ao MySQL
mysql -u root

# Criar banco e usuário
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'esic_user'@'localhost' IDENTIFIED BY 'senha123';
GRANT ALL PRIVILEGES ON esic_db.* TO 'esic_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Importar schema
mysql -u esic_user -p esic_db < ~/Projects/esic/database/schema_novo.sql
```

### Opção 2: SQLite (Desenvolvimento Local - Mais Simples)

```bash
# SQLite já vem no Mac
sqlite3 --version

# Criar banco de dados
cd ~/Projects/esic
sqlite3 database/esic.db < database/schema_sqlite.sql
```

### Opção 3: Docker (Recomendado para Isolamento)

```bash
# Instalar Docker Desktop para Mac
brew install --cask docker

# Iniciar MySQL em container
docker run -d \
  --name esic-mysql \
  -e MYSQL_ROOT_PASSWORD=root123 \
  -e MYSQL_DATABASE=esic_db \
  -e MYSQL_USER=esic_user \
  -e MYSQL_PASSWORD=senha123 \
  -p 3306:3306 \
  mysql:8.0

# Importar schema
docker exec -i esic-mysql mysql -u esic_user -psenha123 esic_db < ~/Projects/esic/database/schema_novo.sql
```

---

## ⚙️ **CONFIGURAR VARIÁVEIS DE AMBIENTE**

Criar arquivo: `.env` (na raiz do projeto)

```bash
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
```

Atualizar `config/constants.php`:

```php
<?php
// Carregar variáveis do .env se existir
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// Usar variáveis de ambiente ou valores padrão
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'esic_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'esic_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'senha123');
define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? 'true');
```

---

## 📝 **SCRIPTS ÚTEIS PARA MAC**

### Criar: `scripts/dev-setup.sh`

```bash
#!/bin/bash
# Script de configuração inicial no Mac

echo "🔧 Configurando ambiente de desenvolvimento E-SIC..."
echo ""

# Verificar PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP não encontrado. Instalando..."
    brew install php@8.2
else
    echo "✅ PHP instalado: $(php -v | head -1)"
fi

# Verificar Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer não encontrado. Instalando..."
    brew install composer
else
    echo "✅ Composer instalado: $(composer --version)"
fi

# Instalar dependências
if [ -f "composer.json" ]; then
    echo "📦 Instalando dependências..."
    composer install
fi

# Criar diretórios necessários
echo "📁 Criando diretórios..."
mkdir -p uploads logs cache tmp
chmod -R 777 uploads logs cache tmp

# Verificar .env
if [ ! -f ".env" ]; then
    echo "⚙️ Criando arquivo .env..."
    cp .env.example .env
fi

echo ""
echo "✅ Configuração concluída!"
echo ""
echo "🚀 Para iniciar o servidor:"
echo "   ./start-dev.sh"
echo ""
echo "📚 Documentação:"
echo "   - README.md"
echo "   - DEPLOY.md"
echo ""
```

### Criar: `scripts/reset-db.sh`

```bash
#!/bin/bash
# Script para resetar banco de dados

echo "🗄️ Resetando banco de dados..."

# Dropar e recriar banco
mysql -u root -p -e "DROP DATABASE IF EXISTS esic_db;"
mysql -u root -p -e "CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar schema
mysql -u esic_user -p esic_db < database/schema_novo.sql

echo "✅ Banco de dados resetado!"
```

Dar permissão:

```bash
chmod +x scripts/*.sh
```

---

## 🔄 **SINCRONIZAR COM GIT**

### Configurar repositório

```bash
# Clonar diretamente do GitHub
git clone https://github.com/DalmoVieira/esic.git ~/Projects/esic
cd ~/Projects/esic

# Ou inicializar novo repositório
cd ~/Projects/esic
git init
git remote add origin https://github.com/DalmoVieira/esic.git
git pull origin main
```

### Configurar .gitignore

Criar/atualizar: `.gitignore`

```
# Dependências
/vendor/
/node_modules/

# Ambiente
.env
.env.local

# Arquivos de sistema
.DS_Store
Thumbs.db
*.log

# IDEs
.vscode/
.idea/
*.swp
*.swo

# Uploads e cache
/uploads/*
!/uploads/.gitkeep
/logs/*
!/logs/.gitkeep
/cache/*
!/cache/.gitkeep
/tmp/*
!/tmp/.gitkeep

# Banco de dados
*.sql.gz
*.db

# Pacotes
*.zip
*.tar.gz
```

---

## 🐛 **DEBUGGING NO VS CODE**

### Instalar Xdebug

```bash
# Instalar Xdebug via PECL
pecl install xdebug

# Verificar localização do php.ini
php --ini

# Adicionar ao php.ini
echo "[xdebug]
zend_extension=xdebug.so
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_port=9003" >> $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")

# Reiniciar PHP
brew services restart php@8.2
```

### Configurar VS Code: `.vscode/launch.json`

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            }
        },
        {
            "name": "Launch currently open script",
            "type": "php",
            "request": "launch",
            "program": "${file}",
            "cwd": "${fileDirname}",
            "port": 9003
        }
    ]
}
```

---

## 📚 **ESTRUTURA DO PACOTE**

```
esic-dev-macos/
├── .vscode/              # Configurações do VS Code
│   ├── settings.json
│   ├── launch.json
│   └── extensions.json
├── scripts/              # Scripts de automação
│   ├── dev-setup.sh
│   ├── start-dev.sh
│   └── reset-db.sh
├── app/                  # Código da aplicação
├── assets/               # CSS, JS, imagens
├── config/               # Configurações
├── database/             # Schemas SQL
├── uploads/              # Uploads (com .gitkeep)
├── logs/                 # Logs
├── .env.example          # Exemplo de variáveis
├── .gitignore           # Ignorar arquivos
├── composer.json        # Dependências PHP
├── README-MAC.md        # Este arquivo
└── DESENVOLVIMENTO.md   # Guia de desenvolvimento
```

---

## ✅ **CHECKLIST DE INSTALAÇÃO NO MAC**

```
☐ Homebrew instalado
☐ PHP 8.2+ instalado (php -v)
☐ Composer instalado (composer --version)
☐ MySQL ou Docker instalado
☐ VS Code instalado
☐ Projeto extraído em ~/Projects/esic
☐ Extensões do VS Code instaladas
☐ Banco de dados criado e schema importado
☐ Arquivo .env configurado
☐ Permissões ajustadas (uploads, logs)
☐ Servidor de desenvolvimento funcionando
☐ Acesso: http://localhost:8000/login.php
```

---

## 🚀 **INÍCIO RÁPIDO**

```bash
# 1. Extrair projeto
unzip esic-dev-macos.zip -d ~/Projects/esic

# 2. Ir para o diretório
cd ~/Projects/esic

# 3. Configurar ambiente
./scripts/dev-setup.sh

# 4. Iniciar servidor
./start-dev.sh

# 5. Acessar no navegador
# http://localhost:8000/login.php
```

---

## 📞 **SUPORTE**

- **GitHub:** https://github.com/DalmoVieira/esic
- **Documentação:** README.md, DEPLOY.md
- **Issues:** https://github.com/DalmoVieira/esic/issues

---

✅ **Pacote pronto para desenvolvimento no macOS com VS Code!** 🍎
