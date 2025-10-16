# 📦 Documentação do Pacote de Produção E-SIC

## 📋 Informações do Pacote

**Versão:** 3.0.0  
**Tipo:** Produção (Production Ready)  
**Script Gerador:** `criar-pacote.ps1`  
**Formato:** ZIP compactado  

---

## 📁 Conteúdo do Pacote

### ✅ Arquivos Incluídos

#### **Páginas Principais** (Frontend Público)
- `index.php` - Página principal do sistema
- `novo-pedido.php` - Formulário de nova solicitação
- `acompanhar.php` - Acompanhamento por protocolo
- `transparencia.php` - Portal da transparência
- `recurso.php` - Sistema de recursos

#### **Autenticação** (Sistema de Login)
- `login.php` - Página de autenticação
- `logout.php` - Encerramento de sessão

#### **Painel Administrativo** (Backend)
- `admin-pedidos.php` - Gestão de pedidos
- `admin-recursos.php` - Gestão de recursos
- `admin-configuracoes.php` - Configurações do sistema

#### **APIs REST** (`api/`)
- `api/pedidos.php` - CRUD de pedidos (cidadãos)
- `api/pedidos-admin.php` - Gestão administrativa
- `api/recursos.php` - Sistema de recursos
- `api/anexos.php` - Upload/download de arquivos
- `api/tramitacoes.php` - Histórico de movimentações
- `api/configuracoes.php` - Configurações

#### **Classes e Configurações** (`app/`)
- `app/config/Database.php` - Conexão com banco de dados
- `app/classes/EmailNotificacao.php` - Sistema de notificações

#### **Assets** (`assets/`)
- `assets/css/` - Estilos CSS personalizados
- `assets/js/main.js` - Scripts principais
- `assets/js/app.js` - Lógica da aplicação
- `assets/js/anexos.js` - Gestão de anexos
- `assets/images/` - Imagens do sistema

#### **Banco de Dados** (`database/`)
- `database/schema_novo.sql` - Schema completo (8 tabelas)

#### **Automação** (`cron/`)
- `cron/notificacoes.php` - Script de notificações automáticas

#### **Scripts de Deploy**
- `deploy.sh` - Script automatizado de instalação (Linux)
- `comandos-rapidos.sh` - Menu de comandos úteis

#### **Documentação**
- `README.md` - Visão geral do projeto
- `DEPLOY_PRODUCAO.md` - Guia completo de deploy (12 etapas)
- `CHECKLIST_DEPLOY.md` - Checklist de implantação
- `CHANGELOG.md` - Histórico de mudanças
- `LEIA-ME.txt` - Instruções rápidas (gerado automaticamente)
- `VERSION.txt` - Informações de versão (gerado automaticamente)

#### **Configuração**
- `.htaccess` - Configurações do Apache
- `.htaccess` (uploads/) - Proteção do diretório de uploads

---

## ❌ Arquivos Excluídos (Não Incluídos)

### **Arquivos de Desenvolvimento**
- `teste-*.php` - Arquivos de teste
- `exemplo-*.php` - Exemplos de código
- `*-v2.php` - Versões antigas de páginas
- `diagnostico.php` - Diagnóstico do sistema
- `test_*.php` - Scripts de teste
- `projeto-completo.html` - Página de demonstração

### **Documentação de Desenvolvimento**
- `CONTRIBUTING.md` - Guia de contribuição
- `README_FASE*.md` - Documentação de fases
- `RELEASE_NOTES.md` - Notas de release detalhadas
- `SUMARIO_EXECUTIVO.md` - Resumo executivo
- `PROJETO_STATUS.txt` - Status do projeto (ASCII art)
- `SETUP_MACOS.md` - Guia de setup para macOS
- `setup-macos.sh` - Script de setup para macOS

### **Diretórios de Desenvolvimento**
- `.git/` - Repositório Git
- `.vscode/` - Configurações do VS Code
- `node_modules/` - Dependências Node.js
- `vendor/` - Dependências Composer
- `logs/*.log` - Arquivos de log existentes
- `uploads/*` - Arquivos de upload existentes

### **Arquivos Temporários**
- `*.log` - Logs
- `*.tmp` - Temporários
- `.DS_Store` - macOS
- `thumbs.db` - Windows
- `*.backup` - Backups

---

## 📂 Diretórios Criados Vazios

Os seguintes diretórios são criados vazios no pacote:

- `uploads/` - Para armazenar arquivos enviados (permissão 775)
- `logs/` - Para logs do sistema (permissão 775)
- `logs/cron/` - Para logs do cron
- `logs/apache/` - Para logs do Apache

Cada diretório contém um arquivo `.gitkeep` para manter a estrutura.

---

## 🔧 Como o Pacote é Gerado

### **Script PowerShell** (`criar-pacote.ps1`)

```powershell
# Executar no Windows
powershell -ExecutionPolicy Bypass -File criar-pacote.ps1
```

### **Processo de Criação**

1. **Criar diretório temporário** em `%TEMP%`
2. **Copiar arquivos selecionados** da lista de inclusão
3. **Criar diretórios vazios** (uploads, logs)
4. **Criar .htaccess de proteção** para uploads
5. **Gerar LEIA-ME.txt** com instruções
6. **Gerar VERSION.txt** com informações de build
7. **Comprimir em ZIP** com compressão otimizada
8. **Limpar arquivos temporários**

### **Saída**

Arquivo ZIP gerado no formato:
```
esic_v3.0.0_producao_YYYYMMDD_HHMMSS.zip
```

Exemplo:
```
esic_v3.0.0_producao_20251016_140553.zip
```

---

## 📊 Estatísticas do Pacote

### **Tamanho Aproximado**
- **Compactado:** ~0.3 MB
- **Descompactado:** ~1.5 MB

### **Quantidade de Arquivos**
- **Total:** ~50-60 arquivos
- **PHP:** ~22 arquivos (incluindo login.php e logout.php)
- **JavaScript:** ~4 arquivos
- **CSS:** ~3 arquivos
- **SQL:** 1 arquivo
- **Markdown:** ~5 arquivos
- **Shell:** 2 scripts

### **Diretórios**
- **Total:** ~15 diretórios
- **Com arquivos:** ~10
- **Vazios (estrutura):** ~5

---

## 🚀 Como Usar o Pacote

### **1. Transferir para o Servidor**

```bash
# Via SCP
scp esic_v3.0.0_producao_*.zip usuario@servidor:/tmp/

# Via FTP/SFTP
# Use seu cliente FTP favorito (FileZilla, WinSCP, etc)
```

### **2. Extrair no Servidor**

```bash
# Conectar via SSH
ssh usuario@servidor

# Extrair
cd /tmp
unzip esic_v3.0.0_producao_*.zip -d /var/www/

# Verificar
ls -la /var/www/esic/
```

### **3. Executar Deploy Automatizado**

```bash
cd /var/www/esic
chmod +x deploy.sh
sudo ./deploy.sh
```

### **4. OU Deploy Manual**

Consulte o arquivo `DEPLOY_PRODUCAO.md` dentro do pacote para instruções passo a passo.

---

## ✅ Validação do Pacote

### **Verificar Integridade**

Após extrair, verifique se todos os arquivos essenciais estão presentes:

```bash
cd /var/www/esic

# Verificar estrutura
ls -la

# Verificar arquivos principais
test -f index.php && echo "✓ index.php OK"
test -f deploy.sh && echo "✓ deploy.sh OK"
test -d api && echo "✓ api/ OK"
test -d app && echo "✓ app/ OK"
test -d database && echo "✓ database/ OK"
test -d cron && echo "✓ cron/ OK"

# Verificar permissões
ls -ld uploads
ls -ld logs
```

### **Checklist de Validação**

- [ ] Arquivo ZIP extraído sem erros
- [ ] Todos os diretórios presentes
- [ ] Arquivo `deploy.sh` executável
- [ ] Arquivo `database/schema_novo.sql` presente
- [ ] Diretórios `uploads/` e `logs/` criados
- [ ] Arquivo `.htaccess` na raiz e em uploads/
- [ ] Documentação presente (README.md, DEPLOY_PRODUCAO.md)
- [ ] APIs presentes no diretório `api/`
- [ ] Classes presentes em `app/classes/`

---

## 🔒 Segurança do Pacote

### **Arquivos de Configuração Não Incluídos**

Por segurança, os seguintes arquivos **NÃO** estão incluídos:

- Credenciais do banco de dados
- Chaves de API
- Senhas SMTP
- Certificados SSL
- Arquivos `.env` com dados sensíveis

**Estes devem ser configurados APÓS a instalação!**

### **Proteções Incluídas**

- ✅ `.htaccess` de proteção em uploads/
- ✅ Bloqueio de execução de PHP em uploads/
- ✅ Desabilitação de listagem de diretórios
- ✅ Headers de segurança

---

## 📞 Suporte

### **Problemas com o Pacote?**

1. **Verificar checksums** (se disponível)
2. **Re-extrair o ZIP** em caso de erro
3. **Verificar permissões** do usuário no servidor
4. **Consultar logs** de extração

### **Contato**

- **GitHub Issues:** https://github.com/DalmoVieira/esic/issues
- **Documentação:** Incluída no pacote
- **Email:** ti@rioclaro.sp.gov.br

---

## 📄 Licença

MIT License - Copyright (c) 2025 Prefeitura Municipal de Rio Claro - SP

---

## 🎉 Histórico de Versões

### **v3.0.0** (Atual)
- Sistema completo de produção
- Todas as 3 fases implementadas
- Documentação completa
- Scripts automatizados
- Segurança em produção

### **v2.0.0**
- Painel administrativo
- Sistema de recursos
- Gestão de usuários

### **v1.0.0**
- Sistema base
- Pedidos e acompanhamento
- Portal da transparência

---

**Desenvolvido com ❤️ para a Prefeitura Municipal de Rio Claro - SP**
