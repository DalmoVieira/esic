# 🍎 Configuração do Ambiente de Desenvolvimento - macOS

Guia completo para configurar o ambiente de desenvolvimento do E-SIC em um Mac com VS Code.

---

## 📋 Índice

1. [Pré-requisitos](#pré-requisitos)
2. [Instalação do Homebrew](#instalação-do-homebrew)
3. [Instalação do PHP](#instalação-do-php)
4. [Instalação do MySQL](#instalação-do-mysql)
5. [Instalação do Apache](#instalação-do-apache)
6. [Clonar o Projeto](#clonar-o-projeto)
7. [Configurar o Banco de Dados](#configurar-o-banco-de-dados)
8. [Configurar o Apache](#configurar-o-apache)
9. [Instalar VS Code e Extensões](#instalar-vs-code-e-extensões)
10. [Testar a Instalação](#testar-a-instalação)
11. [Troubleshooting](#troubleshooting)

---

## 🎯 Pré-requisitos

- **macOS:** 11 Big Sur ou superior
- **Homebrew:** Gerenciador de pacotes
- **Git:** Controle de versão
- **Terminal:** Acesso ao terminal
- **Conexão com internet**

---

## 1️⃣ Instalação do Homebrew

Homebrew é o gerenciador de pacotes mais popular para macOS.

### Instalar Homebrew

```bash
# Abra o Terminal (Command + Space, digite "Terminal")
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Verificar Instalação

```bash
brew --version
# Deve retornar: Homebrew 4.x.x
```

### Atualizar Homebrew

```bash
brew update
brew upgrade
```

---

## 2️⃣ Instalação do PHP

### Instalar PHP 8.2

```bash
# Instalar PHP 8.2
brew install php@8.2

# Adicionar ao PATH
echo 'export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"' >> ~/.zshrc
echo 'export PATH="/opt/homebrew/opt/php@8.2/sbin:$PATH"' >> ~/.zshrc

# Recarregar configuração
source ~/.zshrc
```

### Verificar Instalação

```bash
php -v
# Deve retornar: PHP 8.2.x

php -m | grep -E "pdo|mysqli|mbstring|json|curl|gd|zip|xml|fileinfo"
# Deve mostrar as extensões instaladas
```

### Configurar PHP

```bash
# Editar php.ini
nano /opt/homebrew/etc/php/8.2/php.ini

# Alterar as seguintes linhas:
upload_max_filesize = 10M
post_max_size = 12M
memory_limit = 256M
max_execution_time = 300
date.timezone = America/Sao_Paulo

# Salvar: Ctrl+O, Enter
# Sair: Ctrl+X
```

### Iniciar PHP-FPM

```bash
# Iniciar serviço
brew services start php@8.2

# Verificar status
brew services list | grep php
```

---

## 3️⃣ Instalação do MySQL

### Instalar MySQL 8.0

```bash
# Instalar MySQL
brew install mysql

# Iniciar MySQL
brew services start mysql

# Executar script de segurança
mysql_secure_installation
```

### Configuração Inicial do MySQL

Durante o `mysql_secure_installation`:

1. **Set root password?** → Yes → Digite uma senha forte
2. **Remove anonymous users?** → Yes
3. **Disallow root login remotely?** → Yes
4. **Remove test database?** → Yes
5. **Reload privilege tables?** → Yes

### Verificar Instalação

```bash
mysql -u root -p
# Digite a senha criada

# No console MySQL:
SELECT VERSION();
EXIT;
```

### Criar Usuário para Desenvolvimento

```bash
mysql -u root -p

# No console MySQL:
CREATE USER 'esic_dev'@'localhost' IDENTIFIED BY 'senha_segura_123';
GRANT ALL PRIVILEGES ON *.* TO 'esic_dev'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 4️⃣ Instalação do Apache

O macOS já vem com Apache instalado, mas vamos configurá-lo.

### Verificar Apache Nativo

```bash
# Verificar versão
apachectl -v
# Deve retornar: Apache/2.4.x

# Iniciar Apache
sudo apachectl start

# Verificar se está rodando
curl http://localhost
# Deve retornar HTML
```

### OU Instalar Apache via Homebrew (Recomendado)

```bash
# Parar Apache nativo (se estiver rodando)
sudo apachectl stop
sudo launchctl unload -w /System/Library/LaunchDaemons/org.apache.httpd.plist 2>/dev/null

# Instalar Apache via Homebrew
brew install httpd

# Iniciar Apache
brew services start httpd

# Verificar
curl http://localhost:8080
```

---

## 5️⃣ Clonar o Projeto

### Instalar Git (se não tiver)

```bash
# Verificar se Git está instalado
git --version

# Se não estiver, instalar:
brew install git
```

### Clonar o Repositório

```bash
# Criar diretório de projetos
mkdir -p ~/Projects
cd ~/Projects

# Clonar o projeto
git clone https://github.com/DalmoVieira/esic.git

# Entrar no diretório
cd esic

# Verificar branch
git branch
# Deve estar em 'main'
```

### Estrutura de Diretórios

```bash
# Criar diretório de uploads
mkdir -p uploads
chmod 775 uploads

# Criar diretório de logs
mkdir -p logs
chmod 775 logs
```

---

## 6️⃣ Configurar o Banco de Dados

### Criar Banco de Dados

```bash
# Conectar ao MySQL
mysql -u esic_dev -p

# No console MySQL:
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE esic_db;

# Importar schema
SOURCE ~/Projects/esic/database/schema_novo.sql;

# Verificar tabelas
SHOW TABLES;
# Deve mostrar 8 tabelas

# Sair
EXIT;
```

### Verificar Importação

```bash
mysql -u esic_dev -p esic_db -e "SELECT COUNT(*) FROM usuarios;"
# Deve retornar um número
```

---

## 7️⃣ Configurar o Apache

### Opção A: Configurar Virtual Host (Recomendado)

```bash
# Editar arquivo de configuração do Apache
sudo nano /opt/homebrew/etc/httpd/httpd.conf

# Descomentar (remover #) as seguintes linhas:
LoadModule rewrite_module lib/httpd/modules/mod_rewrite.so
LoadModule vhost_alias_module lib/httpd/modules/mod_vhost_alias.so

# Incluir virtual hosts
Include /opt/homebrew/etc/httpd/extra/httpd-vhosts.conf

# Salvar e sair
```

### Criar Virtual Host

```bash
# Editar arquivo de virtual hosts
sudo nano /opt/homebrew/etc/httpd/extra/httpd-vhosts.conf

# Adicionar no final:
<VirtualHost *:8080>
    ServerName esic.local
    DocumentRoot "/Users/SEU_USUARIO/Projects/esic"
    
    <Directory "/Users/SEU_USUARIO/Projects/esic">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "/Users/SEU_USUARIO/Projects/esic/logs/error.log"
    CustomLog "/Users/SEU_USUARIO/Projects/esic/logs/access.log" common
</VirtualHost>

# Substituir SEU_USUARIO pelo seu nome de usuário do Mac!
# Para descobrir: whoami
```

### Configurar Arquivo Hosts

```bash
# Editar arquivo hosts
sudo nano /etc/hosts

# Adicionar linha:
127.0.0.1    esic.local

# Salvar e sair
```

### Reiniciar Apache

```bash
# Testar configuração
apachectl configtest
# Deve retornar: Syntax OK

# Reiniciar Apache
brew services restart httpd

# OU (se usando Apache nativo):
sudo apachectl restart
```

### Opção B: Usar Diretório Padrão

```bash
# Copiar projeto para o diretório do Apache
sudo cp -r ~/Projects/esic /opt/homebrew/var/www/

# Ajustar permissões
sudo chmod -R 755 /opt/homebrew/var/www/esic
sudo chown -R _www:_www /opt/homebrew/var/www/esic/uploads
```

---

## 8️⃣ Configurar Aplicação

### Editar Arquivo de Configuração do Banco

```bash
# Abrir arquivo de configuração
nano ~/Projects/esic/app/config/Database.php

# Verificar/ajustar as credenciais:
private $host = "localhost";
private $db_name = "esic_db";
private $username = "esic_dev";
private $password = "senha_segura_123";  # A senha que você criou
```

### Criar Arquivo .htaccess (se não existir)

```bash
# Criar .htaccess na raiz do projeto
cat > ~/Projects/esic/.htaccess << 'EOF'
# Habilitar mod_rewrite
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirecionar HTTP para HTTPS (apenas em produção)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
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
```

---

## 9️⃣ Instalar VS Code e Extensões

### Instalar VS Code

```bash
# Opção 1: Download direto
# Acesse: https://code.visualstudio.com/download

# Opção 2: Homebrew Cask
brew install --cask visual-studio-code
```

### Abrir Projeto no VS Code

```bash
# Navegar até o projeto
cd ~/Projects/esic

# Abrir no VS Code
code .
```

### Extensões Recomendadas

Instale as seguintes extensões no VS Code:

#### **1. PHP Essenciais**
- **PHP Intelephense** (`bmewburn.vscode-intelephense-client`)
- **PHP Debug** (`xdebug.php-debug`)
- **PHP Namespace Resolver** (`MehediDracula.php-namespace-resolver`)

#### **2. Web Development**
- **HTML CSS Support** (`ecmel.vscode-html-css`)
- **Auto Rename Tag** (`formulahendry.auto-rename-tag`)
- **Auto Close Tag** (`formulahendry.auto-close-tag`)

#### **3. Database**
- **MySQL** (`cweijan.vscode-mysql-client2`)
- **SQLTools** (`mtxr.sqltools`)

#### **4. Git**
- **GitLens** (`eamodio.gitlens`)
- **Git Graph** (`mhutchie.git-graph`)

#### **5. Produtividade**
- **Path Intellisense** (`christian-kohler.path-intellisense`)
- **Better Comments** (`aaron-bond.better-comments`)
- **Bracket Pair Colorizer** (`CoenraadS.bracket-pair-colorizer-2`)

#### **6. Formatação**
- **Prettier** (`esbenp.prettier-vscode`)
- **EditorConfig** (`EditorConfig.EditorConfig`)

### Instalar Extensões via Linha de Comando

```bash
# PHP
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension xdebug.php-debug

# Web
code --install-extension ecmel.vscode-html-css
code --install-extension formulahendry.auto-rename-tag
code --install-extension formulahendry.auto-close-tag

# Database
code --install-extension cweijan.vscode-mysql-client2

# Git
code --install-extension eamodio.gitlens
code --install-extension mhutchie.git-graph

# Produtividade
code --install-extension christian-kohler.path-intellisense
code --install-extension esbenp.prettier-vscode
```

### Configurar VS Code

Criar arquivo de configurações do projeto:

```bash
# Criar diretório .vscode
mkdir -p ~/Projects/esic/.vscode

# Criar settings.json
cat > ~/Projects/esic/.vscode/settings.json << 'EOF'
{
    "php.validate.executablePath": "/opt/homebrew/bin/php",
    "php.suggest.basic": true,
    "intelephense.files.maxSize": 5000000,
    "files.autoSave": "afterDelay",
    "files.autoSaveDelay": 1000,
    "editor.formatOnSave": true,
    "editor.tabSize": 4,
    "editor.insertSpaces": true,
    "files.exclude": {
        "**/.git": true,
        "**/.DS_Store": true,
        "**/node_modules": true
    },
    "[php]": {
        "editor.defaultFormatter": "bmewburn.vscode-intelephense-client"
    }
}
EOF
```

### Configurar Conexão com MySQL no VS Code

1. Abra a extensão **MySQL** (ícone de banco de dados na barra lateral)
2. Clique em "+" para adicionar conexão
3. Preencha:
   - **Host:** localhost
   - **Port:** 3306
   - **Username:** esic_dev
   - **Password:** senha_segura_123
   - **Database:** esic_db
4. Teste a conexão

---

## 🔟 Testar a Instalação

### Teste 1: Verificar Serviços

```bash
# Verificar Apache
curl http://esic.local:8080
# Deve retornar HTML

# Verificar MySQL
mysql -u esic_dev -p esic_db -e "SELECT 'OK' as status;"
# Deve retornar: OK

# Verificar PHP
php -r "echo 'PHP OK';"
# Deve retornar: PHP OK
```

### Teste 2: Acessar Sistema

Abra o navegador e acesse:

```
http://esic.local:8080/
```

Você deve ver a página inicial do E-SIC.

### Teste 3: Testar Conexão com Banco

Crie um arquivo de teste:

```bash
cat > ~/Projects/esic/test_db.php << 'EOF'
<?php
require_once 'app/config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>Teste de Conexão</h1>";
    echo "<p style='color:green;'>✅ Conexão com banco estabelecida com sucesso!</p>";
    
    // Testar query
    $query = "SELECT COUNT(*) as total FROM usuarios";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>Total de usuários: " . $result['total'] . "</p>";
    
} catch (Exception $e) {
    echo "<h1>Erro de Conexão</h1>";
    echo "<p style='color:red;'>❌ " . $e->getMessage() . "</p>";
}
?>
EOF

# Acessar: http://esic.local:8080/test_db.php
```

### Teste 4: Testar Upload

```bash
# Verificar permissões do diretório uploads
ls -la ~/Projects/esic/ | grep uploads

# Deve mostrar: drwxrwxr-x  (775)

# Se não estiver correto:
chmod 775 ~/Projects/esic/uploads
```

Acesse: `http://esic.local:8080/exemplo-anexos.php`

### Teste 5: Criar Pedido

1. Acesse: `http://esic.local:8080/novo-pedido.php`
2. Preencha o formulário
3. Clique em "Enviar Solicitação"
4. Deve gerar um protocolo

---

## 🔧 Troubleshooting

### Problema 1: "Connection refused" ao acessar localhost

**Causa:** Apache não está rodando

**Solução:**
```bash
# Verificar status
brew services list | grep httpd

# Se não estiver rodando, iniciar:
brew services start httpd

# Verificar logs
tail -f /opt/homebrew/var/log/httpd/error_log
```

### Problema 2: "Access denied" no MySQL

**Causa:** Credenciais incorretas

**Solução:**
```bash
# Resetar senha do usuário
mysql -u root -p

# No console MySQL:
ALTER USER 'esic_dev'@'localhost' IDENTIFIED BY 'nova_senha';
FLUSH PRIVILEGES;
EXIT;

# Atualizar app/config/Database.php
```

### Problema 3: Erro 500 (Internal Server Error)

**Causa:** Erro no PHP ou .htaccess

**Solução:**
```bash
# Ver logs do Apache
tail -f ~/Projects/esic/logs/error.log

# Verificar sintaxe do .htaccess
apachectl configtest

# Verificar permissões
chmod -R 755 ~/Projects/esic
chmod 775 ~/Projects/esic/uploads
```

### Problema 4: "Call to undefined function mysqli_connect"

**Causa:** Extensão MySQL não está instalada

**Solução:**
```bash
# Verificar extensões instaladas
php -m | grep -i mysql

# Se não aparecer, reinstalar PHP:
brew reinstall php@8.2
brew services restart php@8.2
```

### Problema 5: Upload não funciona

**Causa:** Permissões incorretas no diretório

**Solução:**
```bash
# Ajustar permissões
chmod 775 ~/Projects/esic/uploads
sudo chown -R $(whoami):_www ~/Projects/esic/uploads

# Verificar php.ini
php -i | grep upload_max_filesize
# Deve ser: 10M
```

### Problema 6: mod_rewrite não funciona

**Causa:** Módulo não está habilitado

**Solução:**
```bash
# Editar httpd.conf
nano /opt/homebrew/etc/httpd/httpd.conf

# Descomentar linha:
LoadModule rewrite_module lib/httpd/modules/mod_rewrite.so

# Reiniciar Apache
brew services restart httpd
```

### Problema 7: Portas em conflito

**Causa:** Porta 8080 já está em uso

**Solução:**
```bash
# Verificar o que está usando a porta
lsof -i :8080

# Matar processo (se necessário)
kill -9 PID

# OU alterar porta do Apache
nano /opt/homebrew/etc/httpd/httpd.conf
# Alterar: Listen 8080 para Listen 8081

# Reiniciar
brew services restart httpd
```

---

## 📝 Comandos Úteis

### Apache

```bash
# Iniciar
brew services start httpd

# Parar
brew services stop httpd

# Reiniciar
brew services restart httpd

# Verificar configuração
apachectl configtest

# Ver logs em tempo real
tail -f /opt/homebrew/var/log/httpd/error_log
```

### MySQL

```bash
# Iniciar
brew services start mysql

# Parar
brew services stop mysql

# Reiniciar
brew services restart mysql

# Conectar
mysql -u esic_dev -p esic_db

# Backup
mysqldump -u esic_dev -p esic_db > backup.sql

# Restaurar
mysql -u esic_dev -p esic_db < backup.sql
```

### PHP

```bash
# Ver versão
php -v

# Ver extensões
php -m

# Ver configuração
php -i

# Localizar php.ini
php --ini

# Reiniciar PHP-FPM
brew services restart php@8.2
```

### Git

```bash
# Status
git status

# Pull (atualizar)
git pull origin main

# Ver diferenças
git diff

# Log
git log --oneline
```

---

## 🚀 Próximos Passos

Após configurar o ambiente:

1. ✅ **Familiarize-se com o código**
   - Leia o README.md
   - Explore a estrutura de pastas
   - Veja a documentação em DEPLOY_PRODUCAO.md

2. ✅ **Configure o Git**
   ```bash
   git config --global user.name "Seu Nome"
   git config --global user.email "seu@email.com"
   ```

3. ✅ **Crie uma branch de desenvolvimento**
   ```bash
   git checkout -b dev
   ```

4. ✅ **Teste todas as funcionalidades**
   - Criar pedido
   - Upload de anexo
   - Acompanhamento
   - Painel admin

5. ✅ **Comece a desenvolver!**

---

## 📚 Recursos Adicionais

### Documentação Oficial
- **PHP:** https://www.php.net/manual/pt_BR/
- **MySQL:** https://dev.mysql.com/doc/
- **Apache:** https://httpd.apache.org/docs/
- **Homebrew:** https://brew.sh/

### Tutoriais
- **PHP no macOS:** https://getgrav.org/blog/macos-ventura-apache-mysql-vhost-apc
- **MySQL Workbench:** https://www.mysql.com/products/workbench/

### Ferramentas Úteis
- **Sequel Ace:** Cliente MySQL para Mac (https://sequel-ace.com/)
- **Postman:** Testar APIs REST (https://www.postman.com/)
- **iTerm2:** Terminal avançado (https://iterm2.com/)

---

## ✅ Checklist de Instalação

Marque conforme concluir:

- [ ] Homebrew instalado
- [ ] PHP 8.2 instalado e configurado
- [ ] MySQL instalado e configurado
- [ ] Apache instalado e rodando
- [ ] Projeto clonado do GitHub
- [ ] Banco de dados criado e populado
- [ ] Virtual host configurado
- [ ] VS Code instalado
- [ ] Extensões instaladas
- [ ] Conexão MySQL funcionando
- [ ] Sistema acessível pelo navegador
- [ ] Upload de arquivos funcionando
- [ ] Git configurado

---

## 📞 Suporte

**Problemas com instalação?**

1. **Verifique os logs:**
   - Apache: `tail -f ~/Projects/esic/logs/error.log`
   - MySQL: `tail -f /opt/homebrew/var/mysql/*.err`
   - PHP: `tail -f /opt/homebrew/var/log/php-fpm.log`

2. **Consulte a documentação:**
   - README.md
   - DEPLOY_PRODUCAO.md
   - CONTRIBUTING.md

3. **Issues no GitHub:**
   - https://github.com/DalmoVieira/esic/issues

---

**Desenvolvido com ❤️ para macOS**

**Versão:** 1.0.0  
**Data:** Outubro 2025  
**Compatível com:** macOS 11+ (Big Sur, Monterey, Ventura, Sonoma)
