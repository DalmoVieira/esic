# Script para criar pacote de produção do E-SIC
# Versão: 1.0.0

$ErrorActionPreference = "Stop"

# Configurações
$projectRoot = $PSScriptRoot
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$version = "3.0.0"
$outputFile = "esic_v${version}_producao_${timestamp}.zip"
$tempDir = Join-Path $env:TEMP "esic_build_$timestamp"

Write-Host ""
Write-Host "E-SIC - Gerador de Pacote para Producao" -ForegroundColor Blue
Write-Host "Versao: $version" -ForegroundColor Cyan
Write-Host ""

# Criar diretório temporário
Write-Host "Criando diretorio temporario..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path $tempDir -Force | Out-Null
Write-Host "OK" -ForegroundColor Green

# Lista de arquivos/diretórios a incluir
$incluir = @(
    "index.php",
    "login.php",
    "logout.php",
    "novo-pedido.php",
    "acompanhar.php",
    "transparencia.php",
    "recurso.php",
    "admin-pedidos.php",
    "admin-recursos.php",
    "admin-configuracoes.php",
    "api",
    "app",
    "assets",
    "database",
    "cron",
    "deploy.sh",
    "comandos-rapidos.sh",
    "README.md",
    "DEPLOY_PRODUCAO.md",
    "CHECKLIST_DEPLOY.md",
    "CHANGELOG.md",
    ".htaccess"
)

# Copiar arquivos
Write-Host "Copiando arquivos..." -ForegroundColor Yellow
$contador = 0

foreach ($item in $incluir) {
    $origem = Join-Path $projectRoot $item
    $destino = Join-Path $tempDir $item
    
    if (Test-Path $origem) {
        $destinoDir = Split-Path $destino -Parent
        if (-not (Test-Path $destinoDir)) {
            New-Item -ItemType Directory -Path $destinoDir -Force | Out-Null
        }
        
        Copy-Item -Path $origem -Destination $destino -Recurse -Force
        $contador++
    }
}

Write-Host "OK - $contador itens copiados" -ForegroundColor Green

# Criar diretórios vazios
Write-Host "Criando diretorios vazios..." -ForegroundColor Yellow
@("uploads", "logs", "logs\cron", "logs\apache") | ForEach-Object {
    $dir = Join-Path $tempDir $_
    New-Item -ItemType Directory -Path $dir -Force | Out-Null
    "" | Out-File -FilePath (Join-Path $dir ".gitkeep") -Encoding UTF8
}
Write-Host "OK" -ForegroundColor Green

# Criar .htaccess para uploads
Write-Host "Criando .htaccess para uploads..." -ForegroundColor Yellow
$htaccessContent = "<Files *.php>`r`nOrder Deny,Allow`r`nDeny from all`r`n</Files>"
$htaccessPath = Join-Path (Join-Path $tempDir "uploads") ".htaccess"
$htaccessContent | Out-File -FilePath $htaccessPath -Encoding UTF8
Write-Host "OK" -ForegroundColor Green

# Criar arquivo LEIA-ME
Write-Host "Criando LEIA-ME.txt..." -ForegroundColor Yellow
$leiame = @"
E-SIC v$version - Pacote de Producao
Data: $(Get-Date -Format 'dd/MM/yyyy HH:mm:ss')

INSTALACAO

1. Extrair o ZIP no servidor
   unzip esic_v${version}_producao_*.zip -d /var/www/

2. Executar script de deploy
   cd /var/www/esic
   chmod +x deploy.sh
   sudo ./deploy.sh

3. Consultar documentacao
   DEPLOY_PRODUCAO.md
   CHECKLIST_DEPLOY.md

SUPORTE
GitHub: https://github.com/DalmoVieira/esic

LICENCA
MIT License - Prefeitura Municipal de Rio Claro - SP
"@
$leiame | Out-File -FilePath (Join-Path $tempDir "LEIA-ME.txt") -Encoding UTF8
Write-Host "OK" -ForegroundColor Green

# Criar arquivo VERSION
Write-Host "Criando VERSION.txt..." -ForegroundColor Yellow
$versionTxt = @"
E-SIC - Sistema Eletronico de Informacoes ao Cidadao
Versao: $version
Build: $timestamp
Data: $(Get-Date -Format 'dd/MM/yyyy HH:mm:ss')
Tipo: Producao
Status: Production Ready

Desenvolvido por: Dalmo Vieira
Orgao: Prefeitura Municipal de Rio Claro - SP
Licenca: MIT
"@
$versionTxt | Out-File -FilePath (Join-Path $tempDir "VERSION.txt") -Encoding UTF8
Write-Host "OK" -ForegroundColor Green

# Criar ZIP
Write-Host "Criando arquivo ZIP..." -ForegroundColor Yellow
$zipPath = Join-Path $projectRoot $outputFile

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

Add-Type -Assembly System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($tempDir, $zipPath, [System.IO.Compression.CompressionLevel]::Optimal, $false)

$zipSize = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host "OK - $zipSize MB" -ForegroundColor Green

# Limpar
Write-Host "Limpando arquivos temporarios..." -ForegroundColor Yellow
Remove-Item -Path $tempDir -Recurse -Force
Write-Host "OK" -ForegroundColor Green

# Resumo
Write-Host ""
Write-Host "PACOTE CRIADO COM SUCESSO!" -ForegroundColor Green
Write-Host ""
Write-Host "Arquivo: $outputFile" -ForegroundColor Cyan
Write-Host "Tamanho: $zipSize MB" -ForegroundColor Cyan
Write-Host "Local: $zipPath" -ForegroundColor Cyan
Write-Host ""
Write-Host "Proximos passos:" -ForegroundColor Yellow
Write-Host "1. Transferir ZIP para o servidor" -ForegroundColor Gray
Write-Host "2. Extrair: unzip $outputFile -d /var/www/" -ForegroundColor Gray
Write-Host "3. Executar: sudo ./deploy.sh" -ForegroundColor Gray
Write-Host ""
