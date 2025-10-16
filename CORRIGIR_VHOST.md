# ==================================================
# CORREÇÃO DO VIRTUALHOST - E-SIC
# ==================================================

## PROBLEMA IDENTIFICADO:
O arquivo httpd-vhosts.conf está configurado para:
  DocumentRoot "C:/xampp/htdocs/e-sic/public"
  
Mas a pasta real é:
  C:/xampp/htdocs/esic  (SEM hífen)

## SOLUÇÃO:

Execute os comandos abaixo no PowerShell como ADMINISTRADOR:

### 1. Parar o Apache
Stop-Process -Name "httpd" -Force -ErrorAction SilentlyContinue

### 2. Fazer backup do arquivo de configuração
Copy-Item "C:\xampp\apache\conf\extra\httpd-vhosts.conf" "C:\xampp\apache\conf\extra\httpd-vhosts.conf.backup"

### 3. Editar o arquivo (cole este conteúdo):

notepad "C:\xampp\apache\conf\extra\httpd-vhosts.conf"

### 4. Substitua o VirtualHost do E-SIC por:

# Virtual Host para E-SIC
<VirtualHost *:80>
    ServerName esic.local
    ServerAlias www.esic.local
    DocumentRoot "C:/xampp/htdocs/esic/public"

    <Directory "C:/xampp/htdocs/esic/public">
        Options FollowSymLinks
        AllowOverride All
        Require all granted

        # Rewrite para Front Controller
        RewriteEngine On

        # Redirecionar tudo para index.php (exceto arquivos/diretórios reais)
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>

    # Logs específicos
    ErrorLog "C:/xampp/apache/logs/esic_error.log"
    CustomLog "C:/xampp/apache/logs/esic_access.log" combined
</VirtualHost>

# Manter localhost padrão
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs"
    ServerName localhost
</VirtualHost>

### 5. Salvar e fechar o Notepad

### 6. Reiniciar o Apache
Start-Process "C:\xampp\apache\bin\httpd.exe"

## OU USE O XAMPP CONTROL PANEL:
1. Abra o XAMPP Control Panel
2. Clique em "Stop" no Apache
3. Aguarde 2 segundos
4. Clique em "Start" no Apache

## APÓS REINICIAR O APACHE, TESTE:

### Opção 1: Usar localhost/esic (recomendado para desenvolvimento)
http://localhost/esic/

### Opção 2: Usar esic.local (domínio local)
http://esic.local/

### Opção 3: Usar www.esic.local
http://www.esic.local/

## VERIFICAR SE FUNCIONOU:

# Testar se Apache reiniciou
Test-NetConnection -ComputerName localhost -Port 80

# Testar URL localhost
Invoke-WebRequest -Uri "http://localhost/esic/" -UseBasicParsing | Select-Object StatusCode

# Testar URL esic.local
Invoke-WebRequest -Uri "http://esic.local/" -UseBasicParsing | Select-Object StatusCode

## NOTAS IMPORTANTES:

1. **Remoção do "Options Indexes"**: Isso evita listagem de diretórios
2. **Caminho corrigido**: De "e-sic" para "esic"
3. **RewriteRule simplificada**: Removido "?pagina=$1" pois o sistema usa roteamento próprio
4. **AllowOverride All**: Permite que .htaccess funcione

## EM CASO DE ERRO:

Se o Apache não iniciar após as mudanças:

1. Restaurar backup:
   Copy-Item "C:\xampp\apache\conf\extra\httpd-vhosts.conf.backup" "C:\xampp\apache\conf\extra\httpd-vhosts.conf" -Force

2. Verificar erros de sintaxe:
   C:\xampp\apache\bin\httpd.exe -t

3. Ver log de erros:
   Get-Content "C:\xampp\apache\logs\error.log" -Tail 20

## SCRIPT AUTOMÁTICO (Execute como Administrador):

# Para corrigir automaticamente, salve este bloco em: corrigir-vhost.ps1

$vhostFile = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"

# Backup
Copy-Item $vhostFile "$vhostFile.backup" -Force

# Ler conteúdo
$content = Get-Content $vhostFile -Raw

# Substituir e-sic por esic
$content = $content -replace 'e-sic', 'esic'

# Remover Options Indexes (segurança)
$content = $content -replace 'Options Indexes FollowSymLinks', 'Options FollowSymLinks'

# Corrigir RewriteRule
$content = $content -replace '\^\\(\.\*\\)\$ index\.php\?pagina=\$1', '^(.*)$ index.php'

# Salvar
Set-Content $vhostFile $content -Encoding UTF8

Write-Host "✅ Arquivo corrigido!" -ForegroundColor Green
Write-Host "⚠️ Reinicie o Apache no XAMPP Control Panel" -ForegroundColor Yellow

---
Data: 16/10/2025
Sistema: E-SIC v3.0.0
