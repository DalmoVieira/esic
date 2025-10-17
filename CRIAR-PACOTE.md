# 📦 CRIAR PACOTE PARA MAC

## PowerShell (Windows) - Criar arquivo ZIP completo

```powershell
# Ir para o diretório do projeto
cd C:\xampp\htdocs\esic

# Limpar arquivos temporários
Remove-Item -Path "cache\*" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "logs\*" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "uploads\*" -Recurse -Force -ErrorAction SilentlyContinue

# Criar diretório temporário para o pacote
$tempDir = "C:\temp\esic-pacote"
if (Test-Path $tempDir) {
    Remove-Item -Path $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir -Force

# Copiar todos os arquivos (exceto os desnecessários)
Get-ChildItem -Path . -Exclude @("node_modules", "vendor", ".git", "*.zip", "*.tar.gz") |
    Copy-Item -Destination $tempDir -Recurse -Force

# Criar arquivo .gitkeep nos diretórios vazios
New-Item -ItemType File -Path "$tempDir\uploads\.gitkeep" -Force
New-Item -ItemType File -Path "$tempDir\logs\.gitkeep" -Force
New-Item -ItemType File -Path "$tempDir\cache\.gitkeep" -Force
New-Item -ItemType File -Path "$tempDir\tmp\.gitkeep" -Force

# Criar arquivo ZIP
$zipPath = "C:\xampp\htdocs\esic-dev-macos.zip"
Compress-Archive -Path "$tempDir\*" -DestinationPath $zipPath -Force

# Verificar tamanho
Get-Item $zipPath | Select-Object Name, @{Name="SizeMB";Expression={[math]::Round($_.Length/1MB,2)}}

# Limpar diretório temporário
Remove-Item -Path $tempDir -Recurse -Force

Write-Host ""
Write-Host "✅ Pacote criado: $zipPath" -ForegroundColor Green
Write-Host ""
Write-Host "📦 Conteúdo do pacote:" -ForegroundColor Cyan
Write-Host "   - Código fonte completo"
Write-Host "   - Configurações do VS Code (.vscode/)"
Write-Host "   - Scripts de automação (scripts/)"
Write-Host "   - Documentação (*.md)"
Write-Host "   - Arquivo .env.example"
Write-Host "   - Arquivo .htaccess"
Write-Host ""
Write-Host "🚀 Próximos passos:" -ForegroundColor Yellow
Write-Host "   1. Copiar esic-dev-macos.zip para o Mac"
Write-Host "   2. Extrair: unzip esic-dev-macos.zip -d ~/Projects/esic"
Write-Host "   3. Executar: cd ~/Projects/esic && ./scripts/dev-setup.sh"
Write-Host ""
```

## Bash (Mac) - Extrair e configurar

```bash
# 1. Extrair pacote
mkdir -p ~/Projects
unzip ~/Downloads/esic-dev-macos.zip -d ~/Projects/esic

# 2. Ir para o diretório
cd ~/Projects/esic

# 3. Dar permissão aos scripts
chmod +x scripts/*.sh
chmod +x *.sh 2>/dev/null

# 4. Executar configuração
./scripts/dev-setup.sh

# 5. Iniciar servidor
./scripts/start-dev.sh
```

## Verificação Pós-Instalação

```bash
# Verificar estrutura
ls -la ~/Projects/esic

# Verificar scripts
ls -la ~/Projects/esic/scripts

# Verificar permissões
ls -la ~/Projects/esic/uploads ~/Projects/esic/logs

# Testar PHP
php -v

# Testar conexão MySQL
mysql -u esic_user -psenha123 -e "SHOW DATABASES;"

# Abrir VS Code
code ~/Projects/esic
```

## Tamanho Estimado do Pacote

- **Código fonte:** ~5 MB
- **Assets (imagens, CSS, JS):** ~2 MB
- **Documentação:** ~500 KB
- **Total:** ~7-8 MB

## ✅ Checklist de Empacotamento

- [x] Código fonte completo
- [x] Configurações VS Code (.vscode/)
- [x] Scripts de automação (scripts/)
- [x] Documentação (.md)
- [x] Arquivo .env.example
- [x] Arquivo .htaccess
- [x] Arquivo .gitignore
- [x] Arquivo .editorconfig
- [x] Diretórios vazios com .gitkeep
- [x] README-MAC.md (renomeado de PACOTE-MACOS.md)

## 🚀 Pronto!

O pacote está pronto para ser transferido para o Mac e usado com VS Code!
