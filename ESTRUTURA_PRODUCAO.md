# 🗂️ Estrutura de Pastas - Ambiente de Produção E-SIC

## 📋 Visão Geral

Esta é a estrutura completa de diretórios e arquivos do sistema E-SIC v3.0.0 
após a instalação em ambiente de produção (Linux).

**Diretório Base:** `/var/www/esic/`

---

## 🌳 Árvore de Diretórios Completa

```
/var/www/esic/
│
├── 📄 index.php                      # Página principal do sistema
├── 📄 login.php                      # Página de autenticação
├── 📄 logout.php                     # Encerramento de sessão
├── 📄 novo-pedido.php                # Formulário de nova solicitação
├── 📄 acompanhar.php                 # Acompanhamento por protocolo
├── 📄 transparencia.php              # Portal da transparência
├── 📄 recurso.php                    # Sistema de recursos
├── 📄 admin-pedidos.php              # [ADMIN] Gestão de pedidos
├── 📄 admin-recursos.php             # [ADMIN] Gestão de recursos
├── 📄 admin-configuracoes.php        # [ADMIN] Configurações
├── 📄 .htaccess                      # Configurações Apache
├── 📄 deploy.sh                      # Script de deploy automatizado
├── 📄 comandos-rapidos.sh            # Menu de comandos úteis
├── 📄 README.md                      # Documentação principal
├── 📄 DEPLOY_PRODUCAO.md             # Guia de deploy
├── 📄 CHECKLIST_DEPLOY.md            # Checklist de implantação
├── 📄 CHANGELOG.md                   # Histórico de mudanças
├── 📄 LEIA-ME.txt                    # Instruções rápidas
├── 📄 VERSION.txt                    # Informações de versão/build
│
├── 📁 api/                           # APIs REST
│   ├── 📄 pedidos.php                # CRUD de pedidos (cidadãos)
│   ├── 📄 pedidos-admin.php          # Gestão administrativa
│   ├── 📄 recursos.php               # Sistema de recursos
│   ├── 📄 anexos.php                 # Upload/download de arquivos
│   ├── 📄 tramitacoes.php            # Histórico de movimentações
│   └── 📄 configuracoes.php          # Configurações do sistema
│
├── 📁 app/                           # Lógica de negócio
│   ├── 📁 config/                    # Configurações
│   │   └── 📄 Database.php           # Conexão com banco de dados
│   │
│   ├── 📁 classes/                   # Classes do sistema
│   │   └── 📄 EmailNotificacao.php   # Sistema de notificações
│   │
│   ├── 📁 models/                    # Modelos (se houver)
│   │
│   ├── 📁 controllers/               # Controllers (se houver)
│   │
│   └── 📁 views/                     # Views (se houver)
│       └── 📁 auth/
│           └── 📄 login.php          # View de login (se houver)
│
├── 📁 assets/                        # Recursos estáticos
│   ├── 📁 css/                       # Estilos
│   │   ├── 📄 style.css              # Estilos principais
│   │   ├── 📄 admin.css              # Estilos do painel admin
│   │   └── 📄 custom.css             # Personalizações
│   │
│   ├── 📁 js/                        # JavaScript
│   │   ├── 📄 main.js                # Scripts principais
│   │   ├── 📄 app.js                 # Lógica da aplicação
│   │   ├── 📄 anexos.js              # Gestão de anexos
│   │   └── 📄 admin.js               # Scripts do admin
│   │
│   └── 📁 images/                    # Imagens
│       ├── 📄 logo.png               # Logo do sistema
│       ├── 📄 brasao.png             # Brasão do município
│       └── 📄 favicon.ico            # Favicon
│
├── 📁 database/                      # Scripts de banco de dados
│   ├── 📄 schema_novo.sql            # Schema completo (8 tabelas)
│   ├── 📁 migrations/                # Migrações (se houver)
│   └── 📁 seeds/                     # Seeds (se houver)
│
├── 📁 cron/                          # Scripts de automação
│   └── 📄 notificacoes.php           # Notificações automáticas
│
├── 📁 uploads/                       # 🔒 Arquivos enviados
│   ├── 📄 .htaccess                  # Proteção (bloqueia PHP)
│   ├── 📄 .gitkeep                   # Mantém estrutura
│   └── 📁 [dinamico]/                # Arquivos dos pedidos (criados em runtime)
│
└── 📁 logs/                          # 🔒 Logs do sistema
    ├── 📄 .gitkeep                   # Mantém estrutura
    ├── 📁 cron/                      # Logs do cron
    │   └── 📄 .gitkeep
    └── 📁 apache/                    # Logs do Apache
        └── 📄 .gitkeep
```

---

## 📊 Estatísticas da Estrutura

### **Diretórios**
- **Total de pastas:** 15
- **Pastas com código:** 10
- **Pastas vazias (estrutura):** 5

### **Arquivos**
- **Total de arquivos:** ~55-60
- **PHP (páginas):** 10 páginas principais
- **PHP (admin):** 3 páginas administrativas
- **PHP (APIs):** 6 endpoints REST
- **PHP (classes):** 2+ classes
- **PHP (cron):** 1 script
- **JavaScript:** 4 arquivos
- **CSS:** 3 arquivos
- **SQL:** 1 schema
- **Shell:** 2 scripts
- **Markdown:** 4 documentos
- **Outros:** 4 arquivos (.htaccess, txt, etc)

### **Tamanho Total**
- **Descompactado:** ~1.5 MB
- **Com uploads/logs:** Cresce dinamicamente

---

## 📁 Detalhamento por Diretório

### **1. Raiz (`/var/www/esic/`)**

**Páginas Públicas (Frontend):**
- `index.php` - Landing page com informações sobre e-SIC
- `novo-pedido.php` - Formulário para cidadãos solicitarem informações
- `acompanhar.php` - Consulta de pedidos por protocolo
- `transparencia.php` - Portal da transparência com estatísticas
- `recurso.php` - Formulário de recurso contra decisões

**Autenticação:**
- `login.php` - Sistema de autenticação para servidores
- `logout.php` - Encerramento de sessão

**Painel Administrativo (Backend):**
- `admin-pedidos.php` - Dashboard de gestão de pedidos
- `admin-recursos.php` - Gestão de recursos administrativos
- `admin-configuracoes.php` - Configurações do sistema

**Scripts de Deploy:**
- `deploy.sh` - Instalação automatizada (chmod +x)
- `comandos-rapidos.sh` - Menu interativo de manutenção

**Documentação:**
- `README.md` - Visão geral do projeto
- `DEPLOY_PRODUCAO.md` - Guia completo de deploy
- `CHECKLIST_DEPLOY.md` - Checklist de implantação
- `CHANGELOG.md` - Histórico de versões
- `LEIA-ME.txt` - Instruções rápidas (gerado)
- `VERSION.txt` - Build info (gerado)

**Configuração:**
- `.htaccess` - Regras de reescrita, segurança, CORS

---

### **2. API (`/var/www/esic/api/`)**

**APIs REST (Content-Type: application/json):**

```
api/
├── pedidos.php           # POST /api/pedidos.php - Criar pedido
│                         # GET  /api/pedidos.php?protocolo=XXX - Buscar
│
├── pedidos-admin.php     # GET    /api/pedidos-admin.php - Listar todos
│                         # PUT    /api/pedidos-admin.php - Atualizar status
│                         # DELETE /api/pedidos-admin.php - Excluir
│
├── recursos.php          # POST /api/recursos.php - Criar recurso
│                         # GET  /api/recursos.php - Listar recursos
│
├── anexos.php            # POST /api/anexos.php - Upload de arquivo
│                         # GET  /api/anexos.php?id=X - Download
│
├── tramitacoes.php       # POST /api/tramitacoes.php - Adicionar tramitação
│                         # GET  /api/tramitacoes.php?pedido_id=X - Histórico
│
└── configuracoes.php     # GET /api/configuracoes.php - Obter configs
                          # PUT /api/configuracoes.php - Atualizar configs
```

**Autenticação:**
- Endpoints admin requerem sessão ativa
- Headers: `Content-Type: application/json`
- Respostas em formato JSON padrão

---

### **3. Aplicação (`/var/www/esic/app/`)**

**Estrutura MVC (parcial):**

```
app/
├── config/
│   └── Database.php              # Singleton PDO, credenciais, conexão
│
├── classes/
│   └── EmailNotificacao.php      # PHPMailer, templates, envio SMTP
│
├── models/                       # (Reservado para expansão)
│   ├── Pedido.php
│   ├── Usuario.php
│   └── Recurso.php
│
├── controllers/                  # (Reservado para expansão)
│   ├── PedidoController.php
│   └── AuthController.php
│
└── views/                        # (Parcialmente usado)
    └── auth/
        └── login.php             # View isolada de login
```

**Classes Principais:**

**Database.php:**
```php
class Database {
    private static $instance = null;
    private $conn;
    
    public static function getInstance()
    public function getConnection()
}
```

**EmailNotificacao.php:**
```php
class EmailNotificacao {
    private $mailer;
    
    public function enviarConfirmacao($pedido)
    public function enviarAtualizacao($pedido)
    public function enviarRecurso($recurso)
}
```

---

### **4. Assets (`/var/www/esic/assets/`)**

**Recursos Estáticos:**

```
assets/
├── css/
│   ├── style.css                 # ~150 KB - Bootstrap + custom
│   ├── admin.css                 # ~20 KB - Estilos admin
│   └── custom.css                # ~10 KB - Personalizações
│
├── js/
│   ├── main.js                   # ~30 KB - jQuery, Bootstrap
│   ├── app.js                    # ~15 KB - Lógica geral
│   ├── anexos.js                 # ~10 KB - Upload/download
│   └── admin.js                  # ~20 KB - Dashboard admin
│
└── images/
    ├── logo.png                  # Logo da prefeitura
    ├── brasao.png                # Brasão municipal
    ├── favicon.ico               # Favicon 16x16
    ├── bg-header.jpg             # Background header
    └── icons/                    # Ícones do sistema
        ├── pedido.svg
        ├── recurso.svg
        └── transparencia.svg
```

**CDNs Utilizados:**
- Bootstrap 5.3.0 (CSS/JS)
- jQuery 3.6.0
- Font Awesome 6.4.0
- Chart.js 3.9.1 (transparência)

---

### **5. Database (`/var/www/esic/database/`)**

**Scripts SQL:**

```
database/
├── schema_novo.sql               # Schema completo (8 tabelas)
│   ├── pedidos
│   ├── usuarios
│   ├── recursos
│   ├── tramitacoes
│   ├── anexos
│   ├── configuracoes
│   ├── notificacoes
│   └── logs_acesso
│
├── migrations/                   # (Futuro)
│   └── 001_initial_schema.sql
│
└── seeds/                        # (Futuro)
    └── default_configs.sql
```

**Importar Schema:**
```bash
mysql -u esic_user -p esic_db < database/schema_novo.sql
```

---

### **6. Cron (`/var/www/esic/cron/`)**

**Scripts de Automação:**

```
cron/
└── notificacoes.php              # Envia notificações pendentes
```

**Configuração Crontab:**
```bash
# Editar crontab
crontab -e

# Adicionar linha (executa a cada hora)
0 * * * * /usr/bin/php /var/www/esic/cron/notificacoes.php >> /var/www/esic/logs/cron/notificacoes.log 2>&1
```

**Funcionalidades:**
- Envia e-mails de pedidos vencendo prazo
- Notifica sobre atualizações de status
- Alertas de recursos pendentes
- Log em `/var/www/esic/logs/cron/`

---

### **7. Uploads (`/var/www/esic/uploads/`) 🔒**

**Armazenamento de Arquivos:**

```
uploads/
├── .htaccess                     # CRÍTICO: Bloqueia execução PHP
├── .gitkeep                      # Mantém estrutura no Git
├── 2025/                         # Organizados por ano/mês
│   ├── 01/
│   │   ├── pedido_123_doc.pdf
│   │   └── pedido_124_foto.jpg
│   ├── 02/
│   └── 03/
```

**Segurança (.htaccess):**
```apache
# Bloqueia execução de PHP
<FilesMatch "\.(php|php3|php4|php5|phtml)$">
    Deny from all
</FilesMatch>

# Permite apenas certos tipos
<FilesMatch "\.(pdf|jpg|jpeg|png|doc|docx|xls|xlsx)$">
    Allow from all
</FilesMatch>

# Desabilita listagem
Options -Indexes
```

**Permissões:**
```bash
chown -R www-data:www-data /var/www/esic/uploads/
chmod 775 /var/www/esic/uploads/
chmod 664 /var/www/esic/uploads/.htaccess
```

**Tamanho Máximo (php.ini):**
- `upload_max_filesize = 20M`
- `post_max_size = 20M`
- `max_file_uploads = 5`

---

### **8. Logs (`/var/www/esic/logs/`) 🔒**

**Sistema de Logging:**

```
logs/
├── .gitkeep                      # Mantém estrutura
├── app.log                       # Logs gerais da aplicação
├── error.log                     # Erros PHP
├── access.log                    # Acessos (se configurado)
├── sql.log                       # Queries SQL (dev/debug)
│
├── cron/                         # Logs de cron jobs
│   ├── .gitkeep
│   └── notificacoes.log          # Saída do cron
│
└── apache/                       # Logs do Apache (symlink)
    ├── .gitkeep
    ├── access.log -> /var/log/apache2/esic-access.log
    └── error.log  -> /var/log/apache2/esic-error.log
```

**Permissões:**
```bash
chown -R www-data:www-data /var/www/esic/logs/
chmod 775 /var/www/esic/logs/
chmod 664 /var/www/esic/logs/*.log
```

**Rotação de Logs (logrotate):**
```bash
# /etc/logrotate.d/esic
/var/www/esic/logs/*.log {
    daily
    rotate 30
    compress
    missingok
    notifempty
    create 0664 www-data www-data
}
```

---

## 🔒 Permissões e Propriedade

### **Estrutura de Permissões Padrão**

```bash
# Propriedade
chown -R www-data:www-data /var/www/esic/

# Diretórios (775 = rwxrwxr-x)
find /var/www/esic/ -type d -exec chmod 775 {} \;

# Arquivos PHP (644 = rw-r--r--)
find /var/www/esic/ -type f -name "*.php" -exec chmod 644 {} \;

# Scripts executáveis (755 = rwxr-xr-x)
chmod 755 /var/www/esic/deploy.sh
chmod 755 /var/www/esic/comandos-rapidos.sh

# Diretórios sensíveis (775 = rwxrwxr-x)
chmod 775 /var/www/esic/uploads/
chmod 775 /var/www/esic/logs/

# Proteções .htaccess (644 = rw-r--r--)
chmod 644 /var/www/esic/.htaccess
chmod 644 /var/www/esic/uploads/.htaccess
```

### **Verificação de Permissões**

```bash
# Verificar propriedade
ls -la /var/www/esic/

# Verificar diretórios críticos
ls -ld /var/www/esic/uploads/
ls -ld /var/www/esic/logs/

# Verificar scripts
ls -l /var/www/esic/*.sh
```

---

## 🌐 Configuração do Apache

### **Virtual Host**

```apache
# /etc/apache2/sites-available/esic.conf
<VirtualHost *:80>
    ServerName esic.rioclaro.sp.gov.br
    ServerAlias www.esic.rioclaro.sp.gov.br
    
    DocumentRoot /var/www/esic
    
    <Directory /var/www/esic>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/esic-error.log
    CustomLog ${APACHE_LOG_DIR}/esic-access.log combined
    
    # PHP
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

**Ativar Site:**
```bash
sudo a2ensite esic.conf
sudo systemctl reload apache2
```

---

## 📦 Tamanho e Espaço em Disco

### **Estimativa de Espaço Necessário**

| Componente | Tamanho Inicial | Crescimento |
|------------|----------------|-------------|
| **Código-fonte** | ~1.5 MB | Estático |
| **Assets** | ~0.5 MB | Estático |
| **Uploads** | ~0 MB | +5-50 MB/mês |
| **Logs** | ~0 MB | +1-10 MB/mês |
| **Banco de dados** | ~1 MB | +10-100 MB/ano |
| **TOTAL INICIAL** | **~3 MB** | - |
| **Após 1 ano** | - | **~100-500 MB** |

**Recomendação:** 
- **Mínimo:** 1 GB de espaço livre
- **Recomendado:** 5 GB de espaço livre
- **Ideal:** 10 GB+ com rotação de logs

---

## 🔍 Verificação da Instalação

### **Script de Verificação**

```bash
#!/bin/bash
# verificar-estrutura.sh

echo "Verificando estrutura do E-SIC..."

# Verificar diretórios principais
for dir in api app assets database cron uploads logs; do
    if [ -d "/var/www/esic/$dir" ]; then
        echo "✓ $dir/"
    else
        echo "✗ $dir/ - FALTANDO!"
    fi
done

# Verificar arquivos críticos
for file in index.php login.php deploy.sh database/schema_novo.sql; do
    if [ -f "/var/www/esic/$file" ]; then
        echo "✓ $file"
    else
        echo "✗ $file - FALTANDO!"
    fi
done

# Verificar permissões
echo ""
echo "Permissões de diretórios críticos:"
ls -ld /var/www/esic/uploads/
ls -ld /var/www/esic/logs/

# Contar arquivos
echo ""
echo "Total de arquivos:"
find /var/www/esic -type f | wc -l

# Tamanho total
echo ""
echo "Tamanho total:"
du -sh /var/www/esic/
```

**Executar:**
```bash
chmod +x verificar-estrutura.sh
./verificar-estrutura.sh
```

---

## 📚 Documentação Adicional

- **Setup Completo:** `DEPLOY_PRODUCAO.md`
- **Checklist:** `CHECKLIST_DEPLOY.md`
- **Mudanças:** `CHANGELOG.md`
- **Visão Geral:** `README.md`
- **Empacotamento:** `PACOTE_PRODUCAO.md`
- **Setup macOS:** `SETUP_MACOS.md`

---

## 🆘 Comandos Úteis

### **Navegação**
```bash
cd /var/www/esic/                 # Ir para raiz
cd /var/www/esic/api/             # Ver APIs
cd /var/www/esic/uploads/         # Ver uploads
cd /var/www/esic/logs/            # Ver logs
```

### **Verificação**
```bash
# Listar estrutura
tree /var/www/esic/ -L 2

# Tamanho de diretórios
du -sh /var/www/esic/*/

# Contar arquivos por tipo
find /var/www/esic/ -name "*.php" | wc -l
find /var/www/esic/ -name "*.js" | wc -l
```

### **Manutenção**
```bash
# Limpar logs antigos
find /var/www/esic/logs/ -name "*.log" -mtime +30 -delete

# Limpar uploads temporários (se houver)
find /var/www/esic/uploads/temp/ -mtime +7 -delete

# Verificar espaço
df -h /var/www/esic/
```

---

**Versão do Documento:** 1.0.0  
**Última Atualização:** 16/10/2025  
**Sistema:** E-SIC v3.0.0  

---

**Desenvolvido com ❤️ para a Prefeitura Municipal de Rio Claro - SP**
