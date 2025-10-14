# 🚀 Script de Deploy Automático - E-SIC

## Para Windows (PowerShell)

### deploy-hostinger.ps1
```powershell
# Script de Deploy E-SIC para Hostinger
# Execute: .\deploy-hostinger.ps1

param(
    [string]$ftpHost = "ftp.seudominio.com.br",
    [string]$ftpUser = "u123456789",
    [string]$ftpPass = "sua_senha_ftp",
    [string]$remotePath = "/public_html/esic/"
)

Write-Host "🚀 Iniciando deploy do E-SIC para Hostinger..." -ForegroundColor Green

# 1. Preparar arquivos
Write-Host "📁 Preparando arquivos..." -ForegroundColor Yellow
Copy-Item ".htaccess-production" ".htaccess" -Force
Write-Host "✅ .htaccess copiado" -ForegroundColor Green

# 2. Compactar projeto (sem arquivos desnecessários)
Write-Host "📦 Compactando projeto..." -ForegroundColor Yellow
$excludeFiles = @(
    "*.md",
    "logs/*",
    "cache/*",
    "test_*",
    "debug*",
    "diagnostico.php",
    "info.php",
    ".git/*",
    "node_modules/*"
)

Compress-Archive -Path "." -DestinationPath "esic-deploy.zip" -Force -CompressionLevel Optimal
Write-Host "✅ Projeto compactado" -ForegroundColor Green

# 3. Upload via FTP (usando WinSCP se disponível)
Write-Host "📤 Fazendo upload..." -ForegroundColor Yellow
if (Get-Command "WinSCP.com" -ErrorAction SilentlyContinue) {
    & WinSCP.com /command `
        "open ftp://$ftpUser`:$ftpPass@$ftpHost" `
        "cd $remotePath" `
        "put esic-deploy.zip" `
        "call unzip esic-deploy.zip" `
        "rm esic-deploy.zip" `
        "exit"
    Write-Host "✅ Upload concluído via WinSCP" -ForegroundColor Green
} else {
    Write-Host "⚠️  WinSCP não encontrado. Upload manual necessário." -ForegroundColor Red
    Write-Host "📋 Faça upload de esic-deploy.zip para $remotePath" -ForegroundColor Yellow
}

# 4. Limpeza
Remove-Item "esic-deploy.zip" -Force
Write-Host "🧹 Limpeza concluída" -ForegroundColor Green

Write-Host "🎉 Deploy concluído!" -ForegroundColor Green
Write-Host "🌐 Acesse: https://seudominio.com.br/esic/" -ForegroundColor Cyan
Write-Host "🧪 Teste: https://seudominio.com.br/esic/test-production.php" -ForegroundColor Cyan
```

## Para Linux/Mac (Bash)

### deploy-hostinger.sh
```bash
#!/bin/bash
# Script de Deploy E-SIC para Hostinger
# Uso: ./deploy-hostinger.sh

# Configurações (ALTERE AQUI)
FTP_HOST="ftp.seudominio.com.br"
FTP_USER="u123456789"
FTP_PASS="sua_senha_ftp"
REMOTE_PATH="/public_html/esic/"
PROJECT_NAME="esic-deploy"

echo "🚀 Iniciando deploy do E-SIC para Hostinger..."

# 1. Verificar dependências
command -v zip >/dev/null 2>&1 || { echo "❌ zip não instalado. Execute: sudo apt install zip" >&2; exit 1; }
command -v lftp >/dev/null 2>&1 || { echo "❌ lftp não instalado. Execute: sudo apt install lftp" >&2; exit 1; }

# 2. Preparar arquivos
echo "📁 Preparando arquivos..."
cp .htaccess-production .htaccess
echo "✅ .htaccess copiado"

# 3. Criar arquivo de exclusão
cat > .deployignore << EOF
*.md
logs/
cache/
test_*
debug*
diagnostico.php
info.php
.git/
node_modules/
.deployignore
deploy-*.sh
deploy-*.ps1
EOF

# 4. Compactar projeto
echo "📦 Compactando projeto..."
zip -r ${PROJECT_NAME}.zip . -x@.deployignore
echo "✅ Projeto compactado"

# 5. Upload via LFTP
echo "📤 Fazendo upload..."
lftp -c "
set ftp:ssl-allow no;
open -u $FTP_USER,$FTP_PASS $FTP_HOST;
cd $REMOTE_PATH;
put ${PROJECT_NAME}.zip;
quit
"
echo "✅ Upload concluído"

# 6. Descompactar no servidor (se possível)
echo "📂 Descompactando no servidor..."
lftp -c "
set ftp:ssl-allow no;
open -u $FTP_USER,$FTP_PASS $FTP_HOST;
cd $REMOTE_PATH;
quote SITE UNZIP ${PROJECT_NAME}.zip;
rm ${PROJECT_NAME}.zip;
quit
"

# 7. Limpeza local
rm ${PROJECT_NAME}.zip
rm .deployignore
echo "🧹 Limpeza concluída"

echo "🎉 Deploy concluído!"
echo "🌐 Acesse: https://seudominio.com.br/esic/"
echo "🧪 Teste: https://seudominio.com.br/esic/test-production.php"
```

## Via Git (Recomendado)

### 1. Configurar Deploy Key na Hostinger
```bash
# No servidor Hostinger (via SSH)
ssh-keygen -t rsa -b 4096 -C "deploy@seudominio.com.br"
cat ~/.ssh/id_rsa.pub
# Copie a chave pública e adicione no GitHub > Settings > Deploy Keys
```

### 2. Script de deploy via Git
```bash
#!/bin/bash
# deploy-git.sh

echo "🚀 Deploy via Git..."

# No servidor Hostinger
cd /public_html/esic/
git pull origin main

# Aplicar configurações de produção
cp .htaccess-production .htaccess
cp config/production.php config/database.php

# Definir permissões
chmod 755 .
chmod 644 *.php
chmod -R 755 uploads/
chmod -R 755 logs/
chmod -R 755 cache/

echo "✅ Deploy concluído via Git!"
```

## Deploy Manual (Passo-a-passo)

### 1. Preparar Arquivos Localmente
- [ ] Copiar `.htaccess-production` para `.htaccess`
- [ ] Configurar `config/production.php` com suas credenciais
- [ ] Testar localmente uma última vez
- [ ] Fazer backup do projeto

### 2. Upload via Painel Hostinger
- [ ] Acessar hPanel da Hostinger
- [ ] Ir em "Gerenciador de Arquivos"
- [ ] Navegar para `/public_html/`
- [ ] Criar pasta `esic/` (se necessário)
- [ ] Fazer upload de todos os arquivos
- [ ] Extrair se compactado

### 3. Configurar Banco de Dados
- [ ] Criar banco via hPanel > MySQL
- [ ] Anotar credenciais (host, nome, usuário, senha)
- [ ] Importar `database/esic_schema.sql` via phpMyAdmin
- [ ] Atualizar `config/production.php`

### 4. Configurar Permissões
```bash
# Via Gerenciador de Arquivos ou SSH
chmod 755 /public_html/esic/
chmod 644 *.php
chmod -R 755 uploads/
chmod -R 755 logs/
chmod -R 755 cache/
chmod 600 config/production.php
```

### 5. Ativar SSL/HTTPS
- [ ] hPanel > SSL > Gerenciar
- [ ] Ativar "Let's Encrypt"
- [ ] Forçar redirecionamento HTTPS
- [ ] Testar certificado

### 6. Testar Sistema
- [ ] Acessar: `https://seudominio.com.br/esic/`
- [ ] Testar: `https://seudominio.com.br/esic/test-production.php`
- [ ] Verificar todas as páginas
- [ ] Testar formulários
- [ ] Verificar logs de erro

## Troubleshooting

### Erro 500 - Internal Server Error
```bash
# Verificar logs do servidor
tail -f /home/usuario/logs/error.log

# Possíveis causas:
# 1. Erro no .htaccess - remover temporariamente
# 2. Erro PHP - verificar syntax
# 3. Permissões incorretas - ajustar chmod
# 4. Configuração banco - verificar credenciais
```

### Erro de Conexão com Banco
```php
# Verificar credenciais em config/production.php
# Testar conexão:
try {
    $pdo = new PDO("mysql:host=localhost;dbname=u123456_esic", "u123456_user", "senha");
    echo "Conectado!";
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage();
}
```

### Site não carrega CSS/JS
```apache
# Verificar .htaccess, adicionar se necessário:
<IfModule mod_mime.c>
    AddType text/css .css
    AddType application/javascript .js
</IfModule>
```

## Monitoramento Pós-Deploy

### 1. Configurar Cron Jobs
```bash
# Backup diário às 2:00
0 2 * * * /usr/bin/php /home/usuario/public_html/esic/scripts/backup.php

# Limpeza de logs semanalmente
0 0 * * 0 /usr/bin/find /home/usuario/public_html/esic/logs/ -type f -mtime +30 -delete
```

### 2. Monitoramento de Uptime
```bash
# Verificar se site está online
*/5 * * * * curl -f https://seudominio.com.br/esic/ || mail -s "Site offline" admin@seudominio.com.br
```

### 3. Logs de Acesso
```bash
# Analisar logs
tail -f /home/usuario/logs/access.log | grep esic
```

---

**✅ Checklist Final de Deploy**
- [ ] Arquivos enviados
- [ ] Banco configurado
- [ ] SSL ativado  
- [ ] Permissões ajustadas
- [ ] Testes passando
- [ ] Monitoramento ativo
- [ ] Backup configurado

**🎯 URLs Importantes**
- **Site:** https://seudominio.com.br/esic/
- **Teste:** https://seudominio.com.br/esic/test-production.php
- **Admin:** https://seudominio.com.br/esic/admin/ (futuro)