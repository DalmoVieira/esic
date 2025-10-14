# Script PowerShell para Preparar Deploy E-SIC
# Uso: .\prepare-deploy.ps1

param(
    [string]$OutputPath = ".\esic-production"
)

Write-Host "🚀 Preparando arquivos para deploy em produção..." -ForegroundColor Green

# Criar pasta de output
if (Test-Path $OutputPath) {
    Remove-Item $OutputPath -Recurse -Force
}
New-Item -ItemType Directory -Path $OutputPath | Out-Null

Write-Host "📁 Copiando arquivos essenciais..." -ForegroundColor Yellow

# Arquivos principais
$mainFiles = @(
    "index.php",
    "novo-pedido.php", 
    "acompanhar.php",
    "transparencia.php",
    "bootstrap.php",
    "test-production.php"
)

foreach ($file in $mainFiles) {
    if (Test-Path $file) {
        Copy-Item $file -Destination $OutputPath
        Write-Host "✅ $file" -ForegroundColor Green
    } else {
        Write-Host "⚠️  $file não encontrado" -ForegroundColor Yellow
    }
}

# Configurações
Write-Host "⚙️ Copiando configurações..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path "$OutputPath\config" -Force | Out-Null
Copy-Item "config\production.php" -Destination "$OutputPath\config\" -ErrorAction SilentlyContinue
Copy-Item "config\constants.php" -Destination "$OutputPath\config\" -ErrorAction SilentlyContinue

# .htaccess para produção
if (Test-Path ".htaccess-production") {
    Copy-Item ".htaccess-production" -Destination "$OutputPath\.htaccess"
    Write-Host "✅ .htaccess configurado" -ForegroundColor Green
}

# Database
Write-Host "🗄️ Copiando scripts de banco..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path "$OutputPath\database" -Force | Out-Null
Copy-Item "database\esic_schema.sql" -Destination "$OutputPath\database\" -ErrorAction SilentlyContinue
Copy-Item "database\install.php" -Destination "$OutputPath\database\" -ErrorAction SilentlyContinue
Copy-Item "database\install_complete.php" -Destination "$OutputPath\database\" -ErrorAction SilentlyContinue

# Sistema MVC completo
Write-Host "🏗️ Copiando sistema MVC..." -ForegroundColor Yellow
if (Test-Path "app") {
    Copy-Item "app" -Destination "$OutputPath\app" -Recurse -Force
    Write-Host "✅ Sistema MVC copiado" -ForegroundColor Green
}

# Criar pastas necessárias
Write-Host "📂 Criando pastas necessárias..." -ForegroundColor Yellow
$folders = @("uploads", "logs", "cache", "tmp")
foreach ($folder in $folders) {
    New-Item -ItemType Directory -Path "$OutputPath\$folder" -Force | Out-Null
    # Criar .htaccess de proteção
    @"
# Proteção da pasta $folder
<Files "*">
    Require all denied
</Files>
"@ | Out-File "$OutputPath\$folder\.htaccess" -Encoding UTF8
    Write-Host "✅ Pasta $folder criada" -ForegroundColor Green
}

# Criar arquivo de informações do deploy
$deployInfo = @"
# Deploy E-SIC - $(Get-Date -Format "dd/MM/yyyy HH:mm:ss")

## Arquivos incluídos neste deploy:
$(Get-ChildItem $OutputPath -Recurse | Select-Object Name, Length | Format-Table | Out-String)

## Próximos passos:
1. Editar config/production.php com suas credenciais
2. Fazer upload dos arquivos para /public_html/esic/
3. Importar database/esic_schema.sql no MySQL
4. Configurar permissões (chmod 755 para pastas, 644 para arquivos)
5. Testar em: https://seudominio.com.br/esic/test-production.php

## Estrutura no servidor:
/public_html/esic/
$(Get-ChildItem $OutputPath -Recurse | ForEach-Object { "  " + $_.FullName.Replace((Get-Location).Path + "\$OutputPath", "") })
"@

$deployInfo | Out-File "$OutputPath\DEPLOY-INFO.txt" -Encoding UTF8

# Estatísticas
$totalFiles = (Get-ChildItem $OutputPath -Recurse -File).Count
$totalSize = (Get-ChildItem $OutputPath -Recurse -File | Measure-Object -Property Length -Sum).Sum
$sizeInMB = [math]::Round($totalSize / 1MB, 2)

Write-Host "`n🎊 Deploy preparado com sucesso!" -ForegroundColor Green
Write-Host "📊 Estatísticas:" -ForegroundColor Cyan
Write-Host "   📁 Pasta: $OutputPath" -ForegroundColor White
Write-Host "   📄 Arquivos: $totalFiles" -ForegroundColor White  
Write-Host "   💾 Tamanho: $sizeInMB MB" -ForegroundColor White

Write-Host "`n📋 Próximos passos:" -ForegroundColor Yellow
Write-Host "1. 📝 Editar $OutputPath\config\production.php" -ForegroundColor White
Write-Host "2. 📤 Fazer upload para servidor" -ForegroundColor White
Write-Host "3. 🗄️ Importar banco de dados" -ForegroundColor White
Write-Host "4. 🧪 Testar sistema" -ForegroundColor White

# Oferecer compactação
$compress = Read-Host "`n💾 Deseja criar arquivo ZIP para upload? (s/N)"
if ($compress -eq 's' -or $compress -eq 'S') {
    $zipPath = "esic-production-$(Get-Date -Format 'yyyyMMdd-HHmmss').zip"
    Compress-Archive -Path "$OutputPath\*" -DestinationPath $zipPath -Force
    Write-Host "✅ Arquivo ZIP criado: $zipPath" -ForegroundColor Green
}

Write-Host "`n🌐 URLs de teste após deploy:" -ForegroundColor Cyan
Write-Host "   https://seudominio.com.br/esic/" -ForegroundColor White
Write-Host "   https://seudominio.com.br/esic/test-production.php" -ForegroundColor White