# 🔗 Conectar VS Code ao Servidor Hostinger

## 🚀 Opção 1: SSH/SFTP (Recomendado)

### 1.1 Pré-requisitos
- ✅ Extensões instaladas: Remote-SSH, Remote Explorer
- ✅ Conta Hostinger com SSH habilitado (planos Premium+)
- ✅ Credenciais SSH da Hostinger

### 1.2 Obter Credenciais SSH na Hostinger

1. **Acesse o hPanel da Hostinger**
2. **Vá em "Avançado" → "SSH Access"**
3. **Anote as informações:**
   ```
   Hostname: ssh.hostinger.com (ou IP específico)
   Usuário: u123456789 (seu usuário)
   Porta: 65002 (porta padrão Hostinger)
   Senha: sua_senha_hosting
   ```

### 1.3 Configurar SSH no VS Code

#### Passo 1: Abrir Configuração SSH
1. **Pressione** `Ctrl+Shift+P`
2. **Digite:** "Remote-SSH: Open SSH Configuration File"
3. **Selecione:** o arquivo de configuração (geralmente `%USERPROFILE%\.ssh\config`)

#### Passo 2: Adicionar Servidor
```ssh
# Configuração SSH para Hostinger E-SIC
Host hostinger-esic
    HostName ssh.hostinger.com
    User u123456789
    Port 65002
    PreferredAuthentications password
    PubkeyAuthentication no
    PasswordAuthentication yes
    ServerAliveInterval 60
    ServerAliveCountMax 3
```

#### Passo 3: Conectar
1. **Pressione** `Ctrl+Shift+P`
2. **Digite:** "Remote-SSH: Connect to Host"
3. **Selecione:** "hostinger-esic"
4. **Digite a senha** quando solicitado
5. **Aguarde** a conexão ser estabelecida

### 1.4 Navegar até o Projeto
```bash
# Após conectado via SSH
cd /home/u123456789/public_html/esic/
```

---

## 📁 Opção 2: SFTP Sync (Já Instalado)

### 2.1 Configurar SFTP
1. **Abra** a pasta do projeto local no VS Code
2. **Pressione** `Ctrl+Shift+P`
3. **Digite:** "SFTP: Config"
4. **Edite** o arquivo `sftp.json` criado:

```json
{
    "name": "Hostinger E-SIC",
    "host": "ftp.seudominio.com.br",
    "protocol": "sftp",
    "port": 22,
    "username": "u123456789",
    "password": "sua_senha",
    "remotePath": "/public_html/esic/",
    "uploadOnSave": true,
    "useTempFile": false,
    "openSsh": false,
    "ignore": [
        ".vscode",
        ".git",
        ".DS_Store",
        "node_modules",
        "*.log"
    ],
    "watcher": {
        "files": "**/*",
        "autoUpload": true,
        "autoDelete": true
    }
}
```

### 2.2 Sincronizar Arquivos
- **Upload All:** `Ctrl+Shift+P` → "SFTP: Upload Project"
- **Download All:** `Ctrl+Shift+P` → "SFTP: Download Project"  
- **Sync:** `Ctrl+Shift+P` → "SFTP: Sync Both Directions"

---

## 🌐 Opção 3: FTP Simples (Fallback)

### 3.1 Configuração FTP
```json
{
    "name": "Hostinger FTP",
    "host": "ftp.seudominio.com.br",
    "protocol": "ftp",
    "port": 21,
    "username": "u123456789",
    "password": "sua_senha",
    "remotePath": "/public_html/esic/",
    "secure": false,
    "passive": true
}
```

---

## ⚙️ Configuração Avançada

### SSH Key (Mais Seguro)
```bash
# No seu computador local (PowerShell)
ssh-keygen -t rsa -b 4096 -C "seuemail@exemplo.com"

# Copiar chave pública para Hostinger
type $env:USERPROFILE\.ssh\id_rsa.pub | clip

# Na Hostinger, adicionar em: Avançado → SSH Access → Manage SSH Keys
```

### Configuração SSH com Chave
```ssh
Host hostinger-esic-key
    HostName ssh.hostinger.com
    User u123456789
    Port 65002
    IdentityFile ~/.ssh/id_rsa
    PreferredAuthentications publickey
    PubkeyAuthentication yes
    PasswordAuthentication no
```

---

## 🛠️ Comandos Úteis no Servidor

### Navegação Básica
```bash
# Ir para pasta do projeto
cd /home/u123456789/public_html/esic/

# Listar arquivos
ls -la

# Verificar espaço em disco
df -h

# Ver logs de erro
tail -f /home/u123456789/logs/error.log
```

### Git no Servidor
```bash
# Clonar projeto (primeira vez)
git clone https://github.com/DalmoVieira/esic.git

# Atualizar projeto
git pull origin main

# Ver status
git status
```

### Permissões
```bash
# Ajustar permissões
chmod 755 .
chmod 644 *.php
chmod -R 755 uploads/
chmod -R 755 logs/
```

---

## 🔧 Troubleshooting

### Erro: "Connection Timeout"
```bash
# Verificar porta SSH da Hostinger
# Hostinger usa porta 65002, não 22
# Verificar se SSH está habilitado no plano
```

### Erro: "Permission Denied"
```bash
# Verificar credenciais
# Tentar reset da senha no hPanel
# Verificar se conta não está suspensa
```

### Erro: "Host Key Verification Failed"
```bash
# Limpar chaves antigas
ssh-keygen -R ssh.hostinger.com
ssh-keygen -R [ssh.hostinger.com]:65002
```

### Upload Lento
```json
// Ajustar configurações SFTP
{
    "concurrency": 2,
    "connectTimeout": 20000,
    "uploadOnSave": false,
    "watcher": {
        "autoUpload": false
    }
}
```

---

## 📋 Workflow Recomendado

### 1. Desenvolvimento Local
```bash
# Trabalhar no projeto local
# Testar em localhost/esic/
# Fazer commits no Git
```

### 2. Deploy via Git (Recomendado)
```bash
# No servidor SSH
cd /home/u123456789/public_html/esic/
git pull origin main
```

### 3. Deploy via SFTP (Alternativo)  
```bash
# No VS Code local
# Ctrl+Shift+P → "SFTP: Upload Project"
```

### 4. Edição Direta (Emergência)
```bash
# Conectar via SSH
# Editar arquivos diretamente no servidor
# Fazer backup antes de qualquer alteração
```

---

## 🚨 Importante: Segurança

### ⚠️ Nunca Fazer:
- ❌ Salvar senhas em arquivos versionados
- ❌ Editar arquivos de produção sem backup
- ❌ Commit credenciais no Git
- ❌ Usar FTP sem criptografia para dados sensíveis

### ✅ Sempre Fazer:
- ✅ Usar SSH quando disponível
- ✅ Fazer backup antes de alterações
- ✅ Testar localmente antes do deploy
- ✅ Monitorar logs após alterações
- ✅ Usar chaves SSH ao invés de senhas

---

## 📞 Suporte Específico Hostinger

### SSH não Funciona?
1. **Verificar Plano:** SSH só funciona em planos Premium+
2. **Ativar SSH:** hPanel → Avançado → SSH Access
3. **Porta Correta:** 65002 (não 22)
4. **IP Permitido:** Verificar se seu IP está liberado

### Credenciais FTP/SFTP
- **Host:** ftp.seudominio.com.br
- **Usuário:** Mesmo do cPanel (u123456789)
- **Senha:** Mesma do cPanel
- **Porta FTP:** 21
- **Porta SFTP:** 22

### Suporte Hostinger
- **Chat:** 24/7 no hPanel
- **Documentação:** https://support.hostinger.com.br
- **SSH Guide:** https://support.hostinger.com/en/articles/1583227

---

## 🎯 Status de Conexão

Após configurar, você deve conseguir:
- [ ] Conectar via SSH/SFTP no VS Code
- [ ] Ver arquivos do servidor remotamente  
- [ ] Editar arquivos diretamente no servidor
- [ ] Sincronizar arquivos automaticamente
- [ ] Executar comandos no terminal remoto
- [ ] Fazer deploy via Git ou upload direto

**🎊 Boa sorte com a conexão remota!**