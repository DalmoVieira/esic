# Script para criar pacote de produção do E-SIC
# Versão: 1.0.0
# Data: Outubro 2025

Write-Host ""
Write-Host "╔══════════════════════════════════════════════════════════════════╗" -ForegroundColor Blue
Write-Host "║  E-SIC - Gerador de Pacote para Produção                        ║" -ForegroundColor Blue
Write-Host "╚══════════════════════════════════════════════════════════════════╝" -ForegroundColor Blue
Write-Host ""

# Variáveis
$projectRoot = $PSScriptRoot
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$version = "3.0.0"
$outputFile = "esic_v${version}_producao_${timestamp}.zip"
$tempDir = Join-Path $env:TEMP "esic_build_$timestamp"

Write-Host "📦 Criando pacote de produção..." -ForegroundColor Green
Write-Host "Versão: $version" -ForegroundColor Cyan
Write-Host "Diretório: $projectRoot" -ForegroundColor Cyan
Write-Host ""

# Criar diretório temporário
Write-Host "► Criando diretório temporário..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path $tempDir -Force | Out-Null
Write-Host "✓ Diretório criado: $tempDir" -ForegroundColor Green
Write-Host ""

# Arquivos e diretórios a incluir
Write-Host "► Copiando arquivos para produção..." -ForegroundColor Yellow

$includePaths = @(
    # Páginas principais
    "index.php",
    "novo-pedido.php",
    "acompanhar.php",
    "transparencia.php",
    "recurso.php",
    
    # Painel administrativo
    "admin-pedidos.php",
    "admin-recursos.php",
    "admin-configuracoes.php",
    
    # API
    "api/",
    
    # Classes e configurações
    "app/",
    
    # Assets
    "assets/",
    
    # Banco de dados
    "database/",
    
    # Cron jobs
    "cron/",
    
    # Scripts de deploy
    "deploy.sh",
    "comandos-rapidos.sh",
    
    # Documentação essencial
    "README.md",
    "DEPLOY_PRODUCAO.md",
    "CHECKLIST_DEPLOY.md",
    "CHANGELOG.md",
    "LICENSE",
    
    # Configuração
    ".htaccess"
)

# Arquivos e diretórios a excluir
$excludePatterns = @(
    "*.git*",
    ".vscode",
    ".DS_Store",
    "node_modules",
    "vendor",
    "composer.lock",
    "package-lock.json",
    "*.log",
    "*.tmp",
    "thumbs.db",
    
    # Arquivos de desenvolvimento
    "teste-*.php",
    "exemplo-*.php",
    "*-v2.php",
    "diagnostico.php",
    "test_*.php",
    
    # Documentação de desenvolvimento
    "CONTRIBUTING.md",
    "README_FASE*.md",
    "RELEASE_NOTES.md",
    "SUMARIO_EXECUTIVO.md",
    "PROJETO_STATUS.txt",
    "projeto-completo.html",
    "SETUP_MACOS.md",
    "setup-macos.sh",
    
    # Diretórios temporários
    "logs",
    "uploads"
)

# Copiar arquivos
$fileCount = 0
foreach ($path in $includePaths) {
    $sourcePath = Join-Path $projectRoot $path
    
    if (Test-Path $sourcePath) {
        $destPath = Join-Path $tempDir $path
        
        if (Test-Path $sourcePath -PathType Container) {
            # É um diretório
            Write-Host "  Copiando diretório: $path" -ForegroundColor Gray
            
            # Criar diretório de destino
            $destDir = Split-Path $destPath -Parent
            if (-not (Test-Path $destDir)) {
                New-Item -ItemType Directory -Path $destDir -Force | Out-Null
            }
            
            # Copiar recursivamente excluindo padrões
            Copy-Item -Path $sourcePath -Destination $destPath -Recurse -Force
            
            # Remover arquivos excluídos
            foreach ($pattern in $excludePatterns) {
                Get-ChildItem -Path $destPath -Recurse -Force -Include $pattern | Remove-Item -Force -Recurse -ErrorAction SilentlyContinue
            }
            
            $count = (Get-ChildItem -Path $destPath -Recurse -File).Count
            $fileCount += $count
            Write-Host "    ✓ $count arquivos copiados" -ForegroundColor Green
        }
        else {
            # É um arquivo
            Write-Host "  Copiando arquivo: $path" -ForegroundColor Gray
            
            $destDir = Split-Path $destPath -Parent
            if (-not (Test-Path $destDir)) {
                New-Item -ItemType Directory -Path $destDir -Force | Out-Null
            }
            
            Copy-Item -Path $sourcePath -Destination $destPath -Force
            $fileCount++
            Write-Host "    ✓ Arquivo copiado" -ForegroundColor Green
        }
    }
    else {
        Write-Host "  ⚠ Não encontrado: $path" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "✓ Total de $fileCount arquivos copiados" -ForegroundColor Green
Write-Host ""

# Criar diretórios vazios necessários
Write-Host "► Criando diretórios vazios necessários..." -ForegroundColor Yellow

$emptyDirs = @(
    "uploads",
    "logs",
    "logs/cron",
    "logs/apache"
)

foreach ($dir in $emptyDirs) {
    $dirPath = Join-Path $tempDir $dir
    New-Item -ItemType Directory -Path $dirPath -Force | Out-Null
    
    # Criar .gitkeep para manter o diretório no Git
    $gitkeepPath = Join-Path $dirPath ".gitkeep"
    "" | Out-File -FilePath $gitkeepPath -Encoding UTF8
    
    Write-Host "  ✓ Criado: $dir" -ForegroundColor Green
}

Write-Host ""

# Criar arquivo de proteção para uploads
Write-Host "► Criando arquivo de proteção para uploads..." -ForegroundColor Yellow
$htaccessUploadsContent = @'
# Proteção do diretório de uploads
# Bloqueia acesso direto a arquivos PHP

<Files *.php>
    Order Deny,Allow
    Deny from all
</Files>

# Permitir apenas tipos de arquivo específicos
<FilesMatch "\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|zip)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
'@

$htaccessUploadsPath = Join-Path $tempDir "uploads"
$htaccessUploadsPath = Join-Path $htaccessUploadsPath ".htaccess"
$htaccessUploadsContent | Out-File -FilePath $htaccessUploadsPath -Encoding UTF8
Write-Host "  ✓ .htaccess criado em uploads/" -ForegroundColor Green
Write-Host ""

# Criar arquivo README para produção
Write-Host "► Criando README de instalação..." -ForegroundColor Yellow
$readmeContent = @"
# E-SIC v$version - Pacote de Produção

Data de geração: $(Get-Date -Format "dd/MM/yyyy HH:mm:ss")

## Conteúdo do Pacote

Este pacote contém todos os arquivos necessários para deploy em produção do sistema E-SIC.

## Instalação Rápida

### Opção 1: Script Automatizado (Recomendado)

bash
# 1. Extrair o ZIP no servidor
unzip esic_v${version}_producao_*.zip -d /var/www/

# 2. Entrar no diretório
cd /var/www/esic

# 3. Executar script de deploy
chmod +x deploy.sh
sudo ./deploy.sh


### Opção 2: Manual

Consulte o arquivo DEPLOY_PRODUCAO.md para instruções detalhadas.

## Checklist Pós-Instalação

Verifique todos os itens após a instalação.

## Estrutura de Diretórios

Confira a organização dos arquivos no sistema.

## Configurações Necessárias

### 1. Banco de Dados
Edite o arquivo app/config/Database.php

### 2. Importar Schema
Use o arquivo database/schema_novo.sql

### 3. Configurar Permissões
Configure as permissões corretas

### 4. Configurar Cron
Configure o agendamento de tarefas

## Segurança

Certifique-se de configurar SSL/TLS e firewall.

## Suporte

- Documentação: DEPLOY_PRODUCAO.md
- Checklist: CHECKLIST_DEPLOY.md
- GitHub: https://github.com/DalmoVieira/esic

## Licença

MIT License - Copyright (c) 2025 Prefeitura Municipal de Rio Claro - SP

Desenvolvido com amor para a transparência pública
"@

$readmeProducaoPath = Join-Path $tempDir "LEIA-ME.txt"
$readmeContent | Out-File -FilePath $readmeProducaoPath -Encoding UTF8
Write-Host "  ✓ LEIA-ME.txt criado" -ForegroundColor Green
Write-Host ""

# Criar arquivo de versão
Write-Host "► Criando arquivo de versão..." -ForegroundColor Yellow
$versionInfo = @"
E-SIC - Sistema Eletrônico de Informações ao Cidadão
Versão: $version
Build: $timestamp
Data: $(Get-Date -Format "dd/MM/yyyy HH:mm:ss")
Tipo: Produção
Status: Production Ready

Conteúdo:
- Sistema completo de pedidos
- Sistema de anexos
- Notificações por email
- Painel administrativo
- Sistema de recursos
- Documentação completa
- Scripts de deploy

Requisitos:
- PHP 8.0+
- MySQL 8.0+
- Apache 2.4+ ou Nginx 1.18+
- SSL/TLS
- 2GB RAM mínimo
- 10GB disco mínimo

Desenvolvido por: Dalmo Vieira
Órgão: Prefeitura Municipal de Rio Claro - SP
Licença: MIT
"@

$versionPath = Join-Path $tempDir "VERSION.txt"
$versionInfo | Out-File -FilePath $versionPath -Encoding UTF8
Write-Host "  ✓ VERSION.txt criado" -ForegroundColor Green
Write-Host ""

# Criar arquivo .env.example
Write-Host "► Criando arquivo .env.example..." -ForegroundColor Yellow
$envExample = @"
# Configurações do Banco de Dados
DB_HOST=localhost
DB_NAME=esic_db
DB_USER=esic_user
DB_PASS=senha_segura_aqui

# Configurações de Email (SMTP)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=seu-email@gmail.com
SMTP_PASS=sua-senha-aqui
SMTP_FROM=noreply@rioclaro.sp.gov.br
SMTP_FROM_NAME=E-SIC Rio Claro

# Configurações do Sistema
BASE_URL=https://esic.rioclaro.sp.gov.br
TIMEZONE=America/Sao_Paulo
DEBUG=false

# Configurações de Upload
UPLOAD_MAX_SIZE=10485760
UPLOAD_ALLOWED_TYPES=pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip

# Configurações de Segurança
SESSION_LIFETIME=7200
CSRF_TOKEN_ENABLED=true
"@

$envExamplePath = Join-Path $tempDir ".env.example"
$envExample | Out-File -FilePath $envExamplePath -Encoding UTF8
Write-Host "  ✓ .env.example criado" -ForegroundColor Green
Write-Host ""

# Criar arquivo de checksums
Write-Host "► Gerando checksums MD5..." -ForegroundColor Yellow
$checksums = @()
Get-ChildItem -Path $tempDir -Recurse -File | ForEach-Object {
    $hash = (Get-FileHash -Path $_.FullName -Algorithm MD5).Hash
    $relativePath = $_.FullName.Replace($tempDir + "\", "")
    $checksums += "$hash  $relativePath"
}

$checksumsPath = Join-Path $tempDir "CHECKSUMS.md5"
$checksums | Out-File -FilePath $checksumsPath -Encoding UTF8
Write-Host "  ✓ CHECKSUMS.md5 gerado com $($checksums.Count) arquivos" -ForegroundColor Green
Write-Host ""

# Criar arquivo ZIP
Write-Host "► Criando arquivo ZIP..." -ForegroundColor Yellow
$zipPath = Join-Path $projectRoot $outputFile

# Remover ZIP anterior se existir
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

# Criar ZIP
Add-Type -Assembly System.IO.Compression.FileSystem
$compressionLevel = [System.IO.Compression.CompressionLevel]::Optimal
[System.IO.Compression.ZipFile]::CreateFromDirectory($tempDir, $zipPath, $compressionLevel, $false)

Write-Host "  ✓ ZIP criado: $outputFile" -ForegroundColor Green
Write-Host ""

# Obter tamanho do arquivo
$zipSize = (Get-Item $zipPath).Length
$zipSizeMB = [math]::Round($zipSize / 1MB, 2)

# Limpar diretório temporário
Write-Host "► Limpando arquivos temporários..." -ForegroundColor Yellow
Remove-Item -Path $tempDir -Recurse -Force
Write-Host "  ✓ Diretório temporário removido" -ForegroundColor Green
Write-Host ""

# Resumo final
Write-Host "╔══════════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║  ✓ Pacote de Produção Criado com Sucesso!                       ║" -ForegroundColor Green
Write-Host "╚══════════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "📦 Arquivo: " -NoNewline -ForegroundColor Cyan
Write-Host "$outputFile" -ForegroundColor White
Write-Host "📊 Tamanho: " -NoNewline -ForegroundColor Cyan
Write-Host "$zipSizeMB MB" -ForegroundColor White
Write-Host "📁 Localização: " -NoNewline -ForegroundColor Cyan
Write-Host "$zipPath" -ForegroundColor White
Write-Host "📝 Arquivos: " -NoNewline -ForegroundColor Cyan
Write-Host "$fileCount arquivos incluídos" -ForegroundColor White
Write-Host ""

Write-Host "📋 Conteúdo do pacote:" -ForegroundColor Yellow
Write-Host "  Sistema completo E-SIC" -ForegroundColor Gray
Write-Host "  APIs REST" -ForegroundColor Gray
Write-Host "  Painel administrativo" -ForegroundColor Gray
Write-Host "  Sistema de anexos" -ForegroundColor Gray
Write-Host "  Notificações por email" -ForegroundColor Gray
Write-Host "  Scripts de deploy" -ForegroundColor Gray
Write-Host "  Documentação essencial" -ForegroundColor Gray
Write-Host "  Schema do banco de dados" -ForegroundColor Gray
Write-Host ""

Write-Host "🚀 Próximos passos:" -ForegroundColor Yellow
Write-Host "  1. Transfira o arquivo ZIP para o servidor" -ForegroundColor Gray
Write-Host "  2. Extraia: unzip $outputFile -d /var/www/" -ForegroundColor Gray
Write-Host "  3. Execute: sudo ./deploy.sh" -ForegroundColor Gray
Write-Host "  4. Consulte: DEPLOY_PRODUCAO.md" -ForegroundColor Gray
Write-Host ""

Write-Host "Desenvolvido com amor para a Prefeitura de Rio Claro - SP" -ForegroundColor Cyan
Write-Host ""
