# 📦 LISTA SIMPLES - Arquivos para Produção

## ✅ ARQUIVOS PRINCIPAIS (6 arquivos)
```
✅ index.php               # Página principal do E-SIC
✅ novo-pedido.php         # Formulário de nova solicitação  
✅ acompanhar.php          # Consulta de protocolo
✅ transparencia.php       # Portal da transparência
✅ bootstrap.php           # Sistema MVC
✅ test-production.php     # Teste do sistema
```

## ⚙️ CONFIGURAÇÕES (3 arquivos)
```
✅ .htaccess-production    # Copiar como .htaccess no servidor
✅ config/production.php   # EDITAR suas credenciais antes!
✅ config/constants.php    # Constantes do sistema
```

## 🗄️ BANCO DE DADOS (2 arquivos)
```
✅ database/esic_schema.sql   # Schema do banco - IMPORTAR no MySQL
✅ database/install.php       # Script de instalação
```

## 🏗️ SISTEMA MVC (pastas completas)
```
✅ app/controllers/     # Todos os controladores
✅ app/models/          # Todos os modelos  
✅ app/views/           # Todos os templates
✅ app/core/            # Classes centrais
✅ app/middleware/      # Middlewares
✅ app/libraries/       # Bibliotecas
✅ app/utils/           # Utilitários
✅ app/config/          # Configurações do sistema
```

## 📁 PASTAS VAZIAS (criar no servidor)
```
📂 uploads/    # Arquivos dos usuários
📂 logs/       # Logs do sistema  
📂 cache/      # Cache
📂 tmp/        # Temporários
```

---

## 🚫 NÃO ENVIAR ESTES ARQUIVOS:

### ❌ Arquivos de Teste/Debug
```
❌ debug.php
❌ debug_routes.php  
❌ diagnostico.php
❌ info.php
❌ teste.php
❌ teste_rapido.php
❌ test_*.php (exceto test-production.php)
```

### ❌ Backups e Versões Antigas
```
❌ index_backup_*.php
❌ index_estatico.php
❌ index_limpo.php
❌ index_original.php
❌ index_problematico*.php
❌ index_simple.php
```

### ❌ Documentação
```
❌ README.md
❌ DEPLOY.md
❌ DEPLOY-SCRIPTS.md
❌ VSCODE-REMOTE.md
❌ PRODUCTION-FILES.md
❌ LICENSE
```

### ❌ Configurações Locais
```
❌ .git/ (pasta)
❌ .vscode/ (pasta)
❌ .gitignore
❌ ssh-config-example
❌ sftp-config-example.json
❌ prepare-deploy.ps1
❌ hosts_padrao.txt
```

---

## 🎯 CHECKLIST RÁPIDO:

### ✅ Antes do Upload:
1. [ ] **Editar** `config/production.php` com suas credenciais da Hostinger
2. [ ] **Testar** sistema localmente uma última vez
3. [ ] **Copiar** `.htaccess-production` como `.htaccess`

### ✅ No Servidor:
1. [ ] **Fazer upload** dos arquivos para `/public_html/esic/`
2. [ ] **Criar pastas:** uploads, logs, cache, tmp
3. [ ] **Importar** `database/esic_schema.sql` no MySQL via phpMyAdmin
4. [ ] **Configurar permissões:**
   ```bash
   chmod 644 *.php
   chmod 755 . app/ uploads/ logs/ cache/ tmp/
   chmod 600 config/production.php
   ```

### ✅ Testar:
1. [ ] **Acessar:** `https://seudominio.com.br/esic/`
2. [ ] **Executar:** `https://seudominio.com.br/esic/test-production.php`
3. [ ] **Verificar** todas as páginas funcionando

---

## 💾 TAMANHO TOTAL ESTIMADO:
- **Arquivos:** ~60 arquivos
- **Tamanho:** ~3-5 MB
- **Tempo upload:** 2-5 minutos (dependendo da conexão)

## 🌐 URLs DE TESTE:
```
🏠 https://seudominio.com.br/esic/
🧪 https://seudominio.com.br/esic/test-production.php
📝 https://seudominio.com.br/esic/novo-pedido
🔍 https://seudominio.com.br/esic/acompanhar  
👁️ https://seudominio.com.br/esic/transparencia
```

---

**🚀 Esta é a lista essencial para colocar o E-SIC em produção!**