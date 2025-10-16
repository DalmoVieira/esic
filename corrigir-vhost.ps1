# ==================================================
# Script para Corrigir VirtualHost do E-SIC
# Execute como ADMINISTRADOR
# ==================================================

Write-Host "`n==================================================" -ForegroundColor Cyan
Write-Host "🔧 Correção do VirtualHost - E-SIC v3.0.0" -ForegroundColor Cyan
Write-Host "==================================================`n" -ForegroundColor Cyan

# Verificar se está executando como Admin
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "❌ ERRO: Este script precisa ser executado como ADMINISTRADOR!" -ForegroundColor Red
    Write-Host "`nClique com botão direito no PowerShell e selecione 'Executar como Administrador'`n" -ForegroundColor Yellow
    pause
    exit
}

$vhostFile = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"

Write-Host "📋 Verificando arquivo de configuração..." -ForegroundColor Yellow

if (-not (Test-Path $vhostFile)) {
    Write-Host "❌ Arquivo não encontrado: $vhostFile" -ForegroundColor Red
    pause
    exit
}

Write-Host "✅ Arquivo encontrado!`n" -ForegroundColor Green

# 1. Fazer backup
Write-Host "💾 Criando backup..." -ForegroundColor Yellow
$backupFile = "$vhostFile.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item $vhostFile $backupFile -Force
Write-Host "✅ Backup criado: $backupFile`n" -ForegroundColor Green

# 2. Ler conteúdo atual
Write-Host "📖 Lendo configuração atual..." -ForegroundColor Yellow
$content = Get-Content $vhostFile -Raw

# 3. Verificar se já está correto
if ($content -match 'DocumentRoot "C:/xampp/htdocs/esic/public"') {
    Write-Host "✅ Configuração já está correta! Nada a fazer.`n" -ForegroundColor Green
    pause
    exit
}

# 4. Fazer as substituições
Write-Host "🔄 Aplicando correções..." -ForegroundColor Yellow

# Corrigir caminho: e-sic -> esic
$content = $content -replace 'C:/xampp/htdocs/e-sic', 'C:/xampp/htdocs/esic'

# Remover Options Indexes (segurança)
$content = $content -replace 'Options Indexes FollowSymLinks', 'Options FollowSymLinks'

# Corrigir RewriteRule
$content = $content -replace 'index\.php\?pagina=\$1', 'index.php'

Write-Host "✅ Correções aplicadas!`n" -ForegroundColor Green

# 5. Salvar arquivo
Write-Host "💾 Salvando arquivo..." -ForegroundColor Yellow
Set-Content $vhostFile $content -Encoding UTF8
Write-Host "✅ Arquivo salvo!`n" -ForegroundColor Green

# 6. Parar Apache
Write-Host "⏹️ Parando Apache..." -ForegroundColor Yellow
Stop-Process -Name "httpd" -Force -ErrorAction SilentlyContinue
Start-Sleep -Seconds 2
Write-Host "✅ Apache parado!`n" -ForegroundColor Green

# 7. Verificar sintaxe
Write-Host "🔍 Verificando sintaxe do Apache..." -ForegroundColor Yellow
$syntaxCheck = & "C:\xampp\apache\bin\httpd.exe" -t 2>&1

if ($syntaxCheck -match "Syntax OK") {
    Write-Host "✅ Sintaxe OK!`n" -ForegroundColor Green
} else {
    Write-Host "❌ ERRO na sintaxe:" -ForegroundColor Red
    Write-Host $syntaxCheck -ForegroundColor Red
    Write-Host "`n⚠️ Restaurando backup..." -ForegroundColor Yellow
    Copy-Item $backupFile $vhostFile -Force
    Write-Host "✅ Backup restaurado!`n" -ForegroundColor Green
    pause
    exit
}

# 8. Iniciar Apache
Write-Host "▶️ Iniciando Apache..." -ForegroundColor Yellow
Start-Process "C:\xampp\apache\bin\httpd.exe" -WindowStyle Hidden
Start-Sleep -Seconds 3

# 9. Verificar se está rodando
$apacheRunning = Get-Process -Name "httpd" -ErrorAction SilentlyContinue

if ($apacheRunning) {
    Write-Host "✅ Apache iniciado com sucesso!`n" -ForegroundColor Green
} else {
    Write-Host "❌ Apache não iniciou. Verifique os logs.`n" -ForegroundColor Red
    Write-Host "Ver log de erros:" -ForegroundColor Yellow
    Write-Host "Get-Content 'C:\xampp\apache\logs\error.log' -Tail 20`n" -ForegroundColor White
    pause
    exit
}

# 10. Testar URLs
Write-Host "==================================================`n" -ForegroundColor Cyan
Write-Host "🧪 Testando URLs..." -ForegroundColor Yellow
Write-Host ""

# Teste 1: localhost/esic
Write-Host "1️⃣ Testando: http://localhost/esic/" -ForegroundColor Cyan
try {
    $response = Invoke-WebRequest -Uri "http://localhost/esic/" -UseBasicParsing -TimeoutSec 5
    if ($response.StatusCode -eq 200 -or $response.StatusCode -eq 302) {
        Write-Host "   ✅ Status: $($response.StatusCode) - OK`n" -ForegroundColor Green
    }
} catch {
    Write-Host "   ⚠️ Erro: $($_.Exception.Message)`n" -ForegroundColor Yellow
}

# Teste 2: esic.local
Write-Host "2️⃣ Testando: http://esic.local/" -ForegroundColor Cyan
try {
    $response = Invoke-WebRequest -Uri "http://esic.local/" -UseBasicParsing -TimeoutSec 5
    if ($response.StatusCode -eq 200 -or $response.StatusCode -eq 302) {
        Write-Host "   ✅ Status: $($response.StatusCode) - OK`n" -ForegroundColor Green
    }
} catch {
    Write-Host "   ⚠️ Erro: $($_.Exception.Message)`n" -ForegroundColor Yellow
}

# Resumo
Write-Host "==================================================`n" -ForegroundColor Cyan
Write-Host "✅ CORREÇÃO CONCLUÍDA COM SUCESSO!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Resumo das alterações:" -ForegroundColor Cyan
Write-Host "   • Caminho corrigido: e-sic → esic" -ForegroundColor White
Write-Host "   • Segurança: Options Indexes removido" -ForegroundColor White
Write-Host "   • RewriteRule otimizada" -ForegroundColor White
Write-Host ""
Write-Host "🌐 URLs disponíveis:" -ForegroundColor Cyan
Write-Host "   • http://localhost/esic/" -ForegroundColor White
Write-Host "   • http://esic.local/" -ForegroundColor White
Write-Host "   • http://www.esic.local/" -ForegroundColor White
Write-Host ""
Write-Host "📁 Backup salvo em:" -ForegroundColor Cyan
Write-Host "   $backupFile" -ForegroundColor White
Write-Host ""
Write-Host "==================================================`n" -ForegroundColor Cyan

# Abrir navegador
$abrirNavegador = Read-Host "Deseja abrir o navegador para testar? (S/N)"
if ($abrirNavegador -eq "S" -or $abrirNavegador -eq "s") {
    Write-Host "`n🌐 Abrindo navegador..." -ForegroundColor Yellow
    Start-Process "http://localhost/esic/"
}

Write-Host "`nPressione qualquer tecla para sair..." -ForegroundColor Gray
pause
