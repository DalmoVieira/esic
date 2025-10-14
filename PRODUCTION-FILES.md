# 📦 Lista de Arquivos para Deploy em Produção

## ✅ ARQUIVOS OBRIGATÓRIOS (Enviar)

### 🏠 **Arquivos Principais do Sistema**
```
✅ index.php                    # Página principal do E-SIC
✅ novo-pedido.php              # Formulário de nova solicitação
✅ acompanhar.php               # Consulta de protocolo
✅ transparencia.php            # Portal da transparência
✅ bootstrap.php                # Inicializador do sistema MVC
```

### ⚙️ **Configurações Essenciais**
```
✅ .htaccess-production         # Renomear para .htaccess no servidor
✅ config/production.php        # Configurações de produção
✅ config/constants.php         # Constantes do sistema
```

### 🗄️ **Banco de Dados**
```
✅ database/esic_schema.sql     # Schema principal do banco
✅ database/install.php         # Script de instalação
✅ database/install_complete.php
```

### 🏗️ **Sistema MVC Completo**
```
✅ app/config/                  # Configurações do sistema
✅ app/controllers/             # Controladores (Admin, Auth, etc.)
✅ app/core/                    # Classes centrais do framework
✅ app/libraries/               # Bibliotecas auxiliares
✅ app/middleware/              # Middlewares de autenticação
✅ app/models/                  # Modelos de dados
✅ app/utils/                   # Utilitários e helpers
✅ app/views/                   # Templates e layouts
```

### 📁 **Pastas Necessárias (criar vazias se não existirem)**
```
✅ uploads/                     # Arquivos enviados pelos usuários
✅ logs/                        # Logs do sistema
✅ cache/                       # Cache de dados
✅ tmp/                         # Arquivos temporários
```

### 🧪 **Arquivo de Teste (Opcional mas Recomendado)**
```
✅ test-production.php          # Teste automático do sistema
```

---

## ❌ ARQUIVOS QUE NÃO DEVEM SER ENVIADOS

### 🚫 **Arquivos de Desenvolvimento**
```
❌ debug.php                    # Debug local
❌ debug_routes.php             # Debug de rotas
❌ diagnostico.php              # Diagnóstico local
❌ info.php                     # phpinfo() - SEGURANÇA!
❌ teste.php                    # Teste local
❌ teste_rapido.php             # Teste rápido
❌ test_*.php                   # Todos os arquivos test_
```

### 🚫 **Backups e Versões Antigas**
```
❌ index_backup_*.php           # Backups antigos
❌ index_estatico.php           # Versão estática
❌ index_limpo.php              # Versão limpa
❌ index_original.php           # Versão original
❌ index_problematico*.php      # Versões problemáticas
❌ index_simple.php             # Versão simples
```

### 🚫 **Documentação e Configurações Locais**
```
❌ README.md                    # Documentação
❌ DEPLOY.md                    # Guia de deploy
❌ DEPLOY-SCRIPTS.md            # Scripts de deploy
❌ VSCODE-REMOTE.md             # Configuração VS Code
❌ LICENSE                      # Licença
❌ .gitignore                   # Git ignore
❌ hosts_padrao.txt             # Hosts padrão
```

### 🚫 **Configurações de Desenvolvimento**
```
❌ .git/                       # Repositório Git
❌ .vscode/                     # Configurações VS Code
❌ .env                         # Variáveis de ambiente local
❌ .env.example                 # Exemplo de .env
❌ composer.json                # Dependências PHP (se não usar)
❌ composer.lock                # Lock do Composer
❌ sftp-config-example.json     # Exemplo SFTP
❌ ssh-config-example           # Exemplo SSH
```

---

## 📋 CHECKLIST DE DEPLOY

### ✅ **Pré-Deploy**
- [ ] **Editar** `config/production.php` com suas credenciais
- [ ] **Testar** sistema localmente uma última vez
- [ ] **Fazer backup** do servidor (se já existe algo)
- [ ] **Verificar** credenciais do banco de dados

### ✅ **Durante o Deploy**
- [ ] **Criar pastas** necessárias no servidor:
  ```bash
  mkdir -p uploads logs cache tmp
  chmod 755 uploads logs cache tmp
  ```
- [ ] **Copiar** `.htaccess-production` como `.htaccess`
- [ ] **Importar** `database/esic_schema.sql` no MySQL
- [ ] **Configurar permissões**:
  ```bash
  chmod 644 *.php
  chmod 755 .
  chmod -R 755 app/
  chmod 600 config/production.php
  ```

### ✅ **Pós-Deploy**
- [ ] **Executar** `test-production.php` para verificar
- [ ] **Testar** todas as páginas principais
- [ ] **Verificar** logs de erro do servidor
- [ ] **Configurar SSL/HTTPS**
- [ ] **Ativar monitoramento**

---

## 🚀 COMANDOS PARA DEPLOY

### **Via Upload Manual (FTP/Painel)**
```bash
# Arquivos para upload:
/esic/
├── index.php
├── novo-pedido.php  
├── acompanhar.php
├── transparencia.php
├── bootstrap.php
├── .htaccess (copiar de .htaccess-production)
├── config/
│   ├── production.php
│   └── constants.php
├── database/
│   ├── esic_schema.sql
│   └── install.php
├── app/ (pasta completa)
├── uploads/ (pasta vazia)
├── logs/ (pasta vazia)
├── cache/ (pasta vazia)
└── test-production.php
```

### **Via Git (Recomendado)**
```bash
# No servidor:
git clone https://github.com/DalmoVieira/esic.git
cd esic
cp .htaccess-production .htaccess
cp config/production.php config/database.php
mkdir -p uploads logs cache tmp
chmod -R 755 uploads logs cache tmp
```

### **Via Script Automatizado**
```bash
# Use os scripts em DEPLOY-SCRIPTS.md
./deploy-hostinger.sh  # Linux/Mac
./deploy-hostinger.ps1 # Windows
```

---

## 🎯 ESTRUTURA FINAL NO SERVIDOR

```
/public_html/esic/
├── 📄 index.php              # Página principal
├── 📄 novo-pedido.php        # Nova solicitação
├── 📄 acompanhar.php         # Acompanhamento
├── 📄 transparencia.php      # Portal transparência
├── 📄 bootstrap.php          # Bootstrap MVC
├── 📄 .htaccess              # Config Apache
├── 📄 test-production.php    # Teste sistema
├── 📂 app/                   # Sistema MVC
│   ├── 📂 controllers/       # Controladores
│   ├── 📂 models/            # Modelos
│   ├── 📂 views/             # Views/Templates
│   ├── 📂 core/              # Classes centrais
│   ├── 📂 middleware/        # Middlewares
│   ├── 📂 libraries/         # Bibliotecas
│   └── 📂 utils/             # Utilitários
├── 📂 config/                # Configurações
│   ├── 📄 production.php     # Config produção
│   └── 📄 constants.php      # Constantes
├── 📂 database/              # Scripts BD
│   ├── 📄 esic_schema.sql    # Schema
│   └── 📄 install.php        # Instalador
├── 📂 uploads/               # Arquivos usuários
├── 📂 logs/                  # Logs sistema
└── 📂 cache/                 # Cache dados
```

---

## 🚨 IMPORTANTE

### ⚠️ **NUNCA Enviar:**
- Arquivos com senhas ou credenciais hardcoded
- Arquivos de debug ou teste (exceto test-production.php)
- Documentação (.md files)
- Configurações de desenvolvimento

### ✅ **SEMPRE Fazer:**
- Backup antes do deploy
- Teste após deploy
- Verificar permissões
- Monitorar logs iniciais
- Configurar SSL

### 📞 **URLs de Teste Após Deploy:**
- **Sistema:** `https://seudominio.com.br/esic/`
- **Teste:** `https://seudominio.com.br/esic/test-production.php`
- **Nova Solicitação:** `https://seudominio.com.br/esic/novo-pedido`
- **Acompanhar:** `https://seudominio.com.br/esic/acompanhar`
- **Transparência:** `https://seudominio.com.br/esic/transparencia`

---

**🎊 Lista completa para deploy em produção criada!**

**📁 Total de arquivos essenciais: ~50-60 arquivos (incluindo estrutura MVC)**
**💾 Tamanho estimado: ~2-5 MB**