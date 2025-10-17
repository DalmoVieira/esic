# ✅ PACOTE E-SIC PARA MAC - PRONTO!

## 📦 Pacote Criado com Sucesso!

**Arquivo:** `C:\xampp\htdocs\esic-dev-macos.zip`
**Tamanho:** 0.94 MB (940 KB)

---

## 📋 **O QUE ESTÁ NO PACOTE**

### ✅ Código Fonte Completo
- Todos os arquivos PHP do sistema E-SIC v3.0.0
- Assets (CSS, JavaScript, imagens)
- Logo atualizado (logo-pmrcrj.png)
- Configurações e constantes

### ✅ Configurações VS Code
- `.vscode/settings.json` - Configurações do editor
- `.vscode/launch.json` - Debug do PHP
- `.vscode/extensions.json` - Extensões recomendadas

### ✅ Scripts de Automação
- `scripts/dev-setup.sh` - Configuração completa automática
- `scripts/start-dev.sh` - Iniciar servidor de desenvolvimento
- `backup-db.sh` - Script de backup do banco

### ✅ Arquivos de Configuração
- `.env.example` - Template de variáveis de ambiente
- `.htaccess` - Configuração do Apache
- `.gitignore` - Arquivos a ignorar no Git

### ✅ Documentação Completa
- `PACOTE-MACOS.md` - Guia completo de instalação no Mac
- `CRIAR-PACOTE.md` - Como criar o pacote
- `README.md` - Documentação geral
- Todos os guias de deployment

### ✅ Estrutura de Diretórios
- `uploads/` - Para arquivos enviados (com .gitkeep)
- `logs/` - Para logs do sistema (com .gitkeep)
- `cache/` - Para cache (com .gitkeep)
- `tmp/` - Para arquivos temporários (com .gitkeep)

---

## 🚀 **COMO USAR NO MAC**

### Passo 1: Transferir o Arquivo

Escolha um método:

**Opção A: USB Drive**
```bash
# Copiar para USB no Windows
# Depois, no Mac:
cp /Volumes/USB/esic-dev-macos.zip ~/Downloads/
```

**Opção B: Email/Cloud**
- Envie o arquivo por email
- Ou use Google Drive / Dropbox
- Baixe no Mac

**Opção C: AirDrop (se disponível)**
- Clique com botão direito no arquivo
- Compartilhar > AirDrop > Selecione o Mac

### Passo 2: Extrair e Configurar no Mac

```bash
# 1. Criar diretório
mkdir -p ~/Projects

# 2. Extrair pacote
unzip ~/Downloads/esic-dev-macos.zip -d ~/Projects/esic

# 3. Ir para o diretório
cd ~/Projects/esic

# 4. Dar permissão aos scripts
chmod +x scripts/*.sh

# 5. Executar configuração automática
./scripts/dev-setup.sh
```

O script `dev-setup.sh` vai:
- ✅ Verificar e instalar Homebrew
- ✅ Verificar e instalar PHP 8.2+
- ✅ Verificar e instalar Composer
- ✅ Verificar e instalar MySQL
- ✅ Criar diretórios necessários
- ✅ Ajustar permissões
- ✅ Criar arquivo .env
- ✅ Configurar banco de dados (opcional)
- ✅ Criar scripts auxiliares

### Passo 3: Iniciar Servidor

```bash
# Iniciar servidor de desenvolvimento
./scripts/start-dev.sh

# Ou manualmente:
php -S localhost:8000
```

### Passo 4: Acessar no Navegador

```
http://localhost:8000/login.php
```

---

## 🔧 **REQUISITOS NO MAC**

O script de configuração vai instalar automaticamente, mas você pode instalar manualmente:

### Homebrew (Gerenciador de Pacotes)
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### PHP 8.2+
```bash
brew install php@8.2
php -v
```

### Composer
```bash
brew install composer
composer --version
```

### MySQL (Opcional)
```bash
brew install mysql
brew services start mysql
```

### VS Code
```bash
brew install --cask visual-studio-code
```

---

## 📁 **ESTRUTURA DO PROJETO NO MAC**

```
~/Projects/esic/
├── .vscode/              # Configurações do VS Code
│   ├── settings.json
│   ├── launch.json
│   └── extensions.json
├── scripts/              # Scripts de automação
│   ├── dev-setup.sh      # Configuração inicial
│   └── start-dev.sh      # Iniciar servidor
├── app/                  # Código da aplicação
├── assets/               # CSS, JS, imagens
│   └── images/
│       └── logo-pmrcrj.png
├── config/               # Configurações
│   └── constants.php
├── database/             # Schemas SQL
├── uploads/              # Uploads
├── logs/                 # Logs
├── .env                  # Variáveis de ambiente (criado na instalação)
├── .env.example          # Template
├── .htaccess             # Configuração Apache
├── .gitignore            # Ignorar arquivos
└── PACOTE-MACOS.md       # Este guia
```

---

## 🎯 **INÍCIO RÁPIDO - 5 COMANDOS**

```bash
# 1. Extrair
unzip ~/Downloads/esic-dev-macos.zip -d ~/Projects/esic

# 2. Entrar
cd ~/Projects/esic

# 3. Configurar
chmod +x scripts/*.sh && ./scripts/dev-setup.sh

# 4. Iniciar
./scripts/start-dev.sh

# 5. Acessar: http://localhost:8000/login.php
```

---

## 🆘 **SOLUÇÃO DE PROBLEMAS**

### Erro: "PHP não encontrado"
```bash
brew install php@8.2
brew link php@8.2 --force
```

### Erro: "Permission denied" ao executar scripts
```bash
chmod +x scripts/*.sh
chmod +x *.sh
```

### Erro: "Porta 8000 já está em uso"
```bash
# Usar porta 8001
php -S localhost:8001

# Ou matar processo na porta 8000
lsof -ti:8000 | xargs kill -9
```

### MySQL não conecta
```bash
# Iniciar MySQL
brew services start mysql

# Verificar status
brew services list

# Conectar sem senha (primeira vez)
mysql -u root
```

---

## 🔌 **EXTENSÕES VS CODE RECOMENDADAS**

Abra o VS Code no projeto:
```bash
code ~/Projects/esic
```

Instale as extensões recomendadas:
- PHP Intelephense
- PHP Debug
- MySQL
- GitLens
- EditorConfig

O VS Code vai sugerir automaticamente as extensões quando você abrir o projeto!

---

## 🗄️ **CONFIGURAR BANCO DE DADOS**

### Opção 1: MySQL (Completo)

```bash
# 1. Instalar MySQL
brew install mysql
brew services start mysql

# 2. Criar banco
mysql -u root -p << 'SQL'
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'esic_user'@'localhost' IDENTIFIED BY 'senha123';
GRANT ALL PRIVILEGES ON esic_db.* TO 'esic_user'@'localhost';
FLUSH PRIVILEGES;
SQL

# 3. Importar schema
cd ~/Projects/esic
mysql -u esic_user -psenha123 esic_db < database/schema_novo.sql
```

### Opção 2: Docker (Isolado)

```bash
# Instalar Docker
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
docker exec -i esic-mysql mysql -u esic_user -psenha123 esic_db < database/schema_novo.sql
```

---

## ✅ **CHECKLIST DE INSTALAÇÃO**

```
☐ Pacote transferido para o Mac
☐ Extraído em ~/Projects/esic
☐ Homebrew instalado
☐ PHP 8.2+ instalado
☐ Composer instalado
☐ Scripts com permissão de execução
☐ dev-setup.sh executado
☐ MySQL instalado e configurado
☐ Banco de dados criado
☐ Schema importado
☐ Arquivo .env configurado
☐ VS Code instalado e aberto no projeto
☐ Extensões instaladas
☐ Servidor iniciado
☐ Acesso funcionando: http://localhost:8000/login.php
```

---

## 📞 **SUPORTE**

### Documentação
- `PACOTE-MACOS.md` - Este arquivo
- `CRIAR-PACOTE.md` - Como criar o pacote
- `README.md` - Documentação geral

### GitHub
- **Repositório:** https://github.com/DalmoVieira/esic
- **Issues:** https://github.com/DalmoVieira/esic/issues

---

## 🎉 **TUDO PRONTO!**

Seu pacote está em:
```
C:\xampp\htdocs\esic-dev-macos.zip (940 KB)
```

✅ Pacote completo para desenvolvimento
✅ Scripts de automação incluídos
✅ Configurações do VS Code prontas
✅ Documentação completa
✅ Pronto para usar no Mac!

---

**Próximos Passos:**
1. Transfira `esic-dev-macos.zip` para o Mac
2. Siga os passos em "COMO USAR NO MAC"
3. Execute `./scripts/dev-setup.sh`
4. Inicie o servidor com `./scripts/start-dev.sh`
5. Acesse http://localhost:8000/login.php

🍎 Bom desenvolvimento no Mac! 🚀
