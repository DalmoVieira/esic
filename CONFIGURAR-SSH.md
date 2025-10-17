# 🔐 Guia Completo - Configuração SSH

## Acesso Seguro ao Servidor AlmaLinux 9

---

## 📋 **ÍNDICE**

1. [Configurar SSH no Servidor](#1-configurar-ssh-no-servidor)
2. [Conectar do Windows](#2-conectar-do-windows)
3. [Configurar Chaves SSH](#3-configurar-chaves-ssh)
4. [Hardening de Segurança](#4-hardening-de-segurança)
5. [Transferir Arquivos via SCP/SFTP](#5-transferir-arquivos)
6. [Troubleshooting](#6-troubleshooting)

---

## 1️⃣ **CONFIGURAR SSH NO SERVIDOR**

### 1.1. Verificar se SSH está instalado:

```bash
# Verificar status do SSH
sudo systemctl status sshd

# Se não estiver instalado:
sudo dnf install -y openssh-server

# Iniciar e habilitar SSH
sudo systemctl start sshd
sudo systemctl enable sshd
```

### 1.2. Configurar Firewall:

```bash
# Liberar porta SSH (22)
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload

# Verificar se está liberado
sudo firewall-cmd --list-all
```

### 1.3. Descobrir IP do Servidor:

```bash
# Ver IP do servidor
ip addr show

# Ou
hostname -I

# Ver IP público (se tiver)
curl ifconfig.me
```

### 1.4. Testar Conexão Local:

```bash
# No próprio servidor, testar SSH
ssh localhost
```

---

## 2️⃣ **CONECTAR DO WINDOWS**

### Opção A: PowerShell (Recomendado)

Windows 10/11 já vem com cliente SSH integrado.

```powershell
# Conectar ao servidor
ssh usuario@rioclaro.rj.gov.br

# Ou usando IP
ssh usuario@192.168.1.100

# Com porta específica (se não for 22)
ssh usuario@rioclaro.rj.gov.br -p 2222
```

**Primeira conexão:**
```
The authenticity of host 'rioclaro.rj.gov.br' can't be established.
ECDSA key fingerprint is SHA256:xxxxxxxxxxxxx.
Are you sure you want to continue connecting (yes/no/[fingerprint])? 
```

Digite: **yes**

### Opção B: PuTTY

1. **Baixar PuTTY:**
   - https://www.putty.org/

2. **Configurar:**
   - Host Name: `rioclaro.rj.gov.br` ou IP
   - Port: `22`
   - Connection type: `SSH`
   - Click: **Open**

3. **Login:**
   - login as: `seu_usuario`
   - password: `sua_senha`

### Opção C: Windows Terminal (Moderno)

1. Instalar da Microsoft Store
2. Abrir e usar comando SSH normal:

```powershell
ssh usuario@rioclaro.rj.gov.br
```

---

## 3️⃣ **CONFIGURAR CHAVES SSH (Mais Seguro)**

### 3.1. Gerar Par de Chaves no Windows:

```powershell
# Abrir PowerShell
# Gerar chave SSH (RSA 4096 bits)
ssh-keygen -t rsa -b 4096 -C "seu_email@exemplo.com"

# Ou chave Ed25519 (mais moderna)
ssh-keygen -t ed25519 -C "seu_email@exemplo.com"
```

**Perguntas durante geração:**
```
Enter file in which to save the key (C:\Users\SeuUsuario/.ssh/id_rsa): 
[Enter para aceitar padrão]

Enter passphrase (empty for no passphrase): 
[Digite senha ou deixe vazio]

Enter same passphrase again: 
[Repita a senha]
```

**Arquivos criados:**
- `C:\Users\SeuUsuario\.ssh\id_rsa` - Chave privada (NUNCA compartilhar!)
- `C:\Users\SeuUsuario\.ssh\id_rsa.pub` - Chave pública (copiar para servidor)

### 3.2. Copiar Chave Pública para o Servidor:

**Método A: ssh-copy-id (se disponível)**
```powershell
ssh-copy-id usuario@rioclaro.rj.gov.br
```

**Método B: Manual (mais comum no Windows)**

1. Ver conteúdo da chave pública:
```powershell
type C:\Users\SeuUsuario\.ssh\id_rsa.pub
```

2. Copiar o conteúdo (começa com `ssh-rsa` ou `ssh-ed25519`)

3. Conectar ao servidor:
```powershell
ssh usuario@rioclaro.rj.gov.br
```

4. No servidor, adicionar a chave:
```bash
# Criar diretório .ssh se não existir
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Adicionar chave pública
echo "ssh-rsa AAA... seu_email@exemplo.com" >> ~/.ssh/authorized_keys

# Ajustar permissões
chmod 600 ~/.ssh/authorized_keys
```

### 3.3. Testar Conexão com Chave:

```powershell
# Agora deve conectar sem pedir senha
ssh usuario@rioclaro.rj.gov.br
```

### 3.4. Configurar SSH Config (Facilita Acesso):

Criar arquivo: `C:\Users\SeuUsuario\.ssh\config`

```
# Servidor E-SIC
Host esic
    HostName rioclaro.rj.gov.br
    User seu_usuario
    Port 22
    IdentityFile C:\Users\SeuUsuario\.ssh\id_rsa

# Atalho para servidor de produção
Host producao
    HostName rioclaro.rj.gov.br
    User root
    Port 22
    IdentityFile C:\Users\SeuUsuario\.ssh\id_rsa

# Servidor por IP
Host servidor-ip
    HostName 192.168.1.100
    User admin
    Port 22
```

**Agora pode conectar simplesmente:**
```powershell
ssh esic
ssh producao
ssh servidor-ip
```

---

## 4️⃣ **HARDENING DE SEGURANÇA NO SERVIDOR**

### 4.1. Editar Configuração SSH:

```bash
sudo nano /etc/ssh/sshd_config
```

### 4.2. Configurações Recomendadas:

```bash
# Porta SSH (alterar para aumentar segurança)
Port 22
# Port 2222  # Descomentar para mudar porta

# Desabilitar login root via SSH (IMPORTANTE!)
PermitRootLogin no

# Permitir apenas autenticação por chave (depois de configurar)
PasswordAuthentication yes
# PasswordAuthentication no  # Alterar para 'no' depois de testar chave

# Desabilitar autenticação por senha vazia
PermitEmptyPasswords no

# Permitir apenas usuários específicos
AllowUsers seu_usuario admin

# Ou grupos específicos
# AllowGroups sshusers

# Tempo de timeout (em segundos)
ClientAliveInterval 300
ClientAliveCountMax 2

# Limitar tentativas de login
MaxAuthTries 3
MaxSessions 5

# Desabilitar X11 Forwarding (se não usar interface gráfica)
X11Forwarding no

# Usar protocolo 2 apenas (mais seguro)
Protocol 2

# Arquivo de log
SyslogFacility AUTHPRIV
LogLevel INFO

# Banner de aviso (opcional)
Banner /etc/ssh/banner.txt
```

### 4.3. Criar Banner de Aviso (Opcional):

```bash
sudo nano /etc/ssh/banner.txt
```

Conteúdo:
```
*******************************************************************
*                                                                 *
*  ACESSO AUTORIZADO APENAS                                       *
*  Prefeitura Municipal de Rio Claro - RJ                         *
*  Sistema E-SIC - Lei de Acesso à Informação                     *
*                                                                 *
*  Todas as atividades são monitoradas e registradas.             *
*  Uso não autorizado será processado legalmente.                 *
*                                                                 *
*******************************************************************
```

### 4.4. Aplicar Mudanças:

```bash
# Testar configuração
sudo sshd -t

# Se OK, reiniciar SSH
sudo systemctl restart sshd

# Verificar status
sudo systemctl status sshd
```

### 4.5. Configurar Fail2Ban (Proteção contra Brute Force):

```bash
# Instalar Fail2Ban
sudo dnf install -y fail2ban fail2ban-systemd

# Copiar configuração padrão
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Editar configuração
sudo nano /etc/fail2ban/jail.local
```

Configurar:
```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3
destemail = admin@rioclaro.rj.gov.br
sendername = Fail2Ban
action = %(action_mwl)s

[sshd]
enabled = true
port = 22
logpath = /var/log/secure
maxretry = 3
bantime = 3600
```

Iniciar Fail2Ban:
```bash
sudo systemctl start fail2ban
sudo systemctl enable fail2ban

# Verificar status
sudo fail2ban-client status
sudo fail2ban-client status sshd
```

---

## 5️⃣ **TRANSFERIR ARQUIVOS**

### 5.1. SCP (Secure Copy) - PowerShell:

**Do Windows para Servidor:**
```powershell
# Copiar arquivo único
scp C:\xampp\htdocs\esic\login.php usuario@rioclaro.rj.gov.br:/var/www/html/

# Copiar diretório completo
scp -r C:\xampp\htdocs\esic\* usuario@rioclaro.rj.gov.br:/var/www/html/

# Com porta específica
scp -P 2222 arquivo.php usuario@rioclaro.rj.gov.br:/var/www/html/

# Múltiplos arquivos
scp *.php usuario@rioclaro.rj.gov.br:/var/www/html/
```

**Do Servidor para Windows:**
```powershell
# Baixar arquivo
scp usuario@rioclaro.rj.gov.br:/var/www/html/config.php C:\backup\

# Baixar diretório
scp -r usuario@rioclaro.rj.gov.br:/var/www/html C:\backup\
```

### 5.2. SFTP (SSH File Transfer Protocol):

**PowerShell:**
```powershell
sftp usuario@rioclaro.rj.gov.br
```

**Comandos SFTP:**
```bash
# Listar diretório remoto
ls

# Listar diretório local
lls

# Mudar diretório remoto
cd /var/www/html

# Mudar diretório local
lcd C:\xampp\htdocs\esic

# Upload arquivo
put arquivo.php

# Upload diretório
put -r pasta/

# Download arquivo
get arquivo.php

# Download diretório
get -r pasta/

# Sair
exit
```

### 5.3. WinSCP (Interface Gráfica):

1. **Baixar:** https://winscp.net/

2. **Configurar:**
   - File protocol: `SFTP`
   - Host name: `rioclaro.rj.gov.br`
   - Port: `22`
   - User name: `seu_usuario`
   - Password: `sua_senha`
   - Private key: `C:\Users\SeuUsuario\.ssh\id_rsa`

3. **Usar:** Arrastar e soltar arquivos entre janelas

### 5.4. VS Code Remote SSH:

1. **Instalar Extensão:**
   - Remote - SSH (da Microsoft)

2. **Conectar:**
   - `Ctrl+Shift+P`
   - "Remote-SSH: Connect to Host"
   - Selecionar ou adicionar host

3. **Editar Diretamente:**
   - File > Open Folder
   - Escolher `/var/www/html`
   - Editar arquivos remotos como se fossem locais

---

## 6️⃣ **TROUBLESHOOTING**

### Problema: "Connection refused"

```bash
# Verificar se SSH está rodando no servidor
sudo systemctl status sshd

# Verificar porta
sudo ss -tlnp | grep ssh

# Verificar firewall
sudo firewall-cmd --list-all
```

### Problema: "Permission denied (publickey)"

```bash
# No servidor, verificar permissões
ls -la ~/.ssh
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

# Verificar conteúdo
cat ~/.ssh/authorized_keys

# Ver logs
sudo tail -f /var/log/secure
```

### Problema: "Host key verification failed"

```powershell
# Limpar chave antiga
ssh-keygen -R rioclaro.rj.gov.br

# Ou remover manualmente
notepad C:\Users\SeuUsuario\.ssh\known_hosts
```

### Problema: Conexão muito lenta

```bash
# Desabilitar DNS reverso (no servidor)
sudo nano /etc/ssh/sshd_config

# Adicionar/modificar:
UseDNS no

# Reiniciar SSH
sudo systemctl restart sshd
```

### Ver Logs em Tempo Real:

```bash
# Logs de SSH
sudo tail -f /var/log/secure

# Logs de autenticação
sudo journalctl -u sshd -f

# Últimas tentativas de login
sudo lastlog

# Tentativas falhadas
sudo cat /var/log/secure | grep "Failed password"
```

---

## 📊 **CHECKLIST DE CONFIGURAÇÃO SSH**

```
☐ SSH instalado e rodando no servidor
☐ Firewall liberou porta 22
☐ Testou conexão do Windows
☐ Gerou par de chaves SSH
☐ Copiou chave pública para servidor
☐ Testou conexão sem senha
☐ Criou arquivo ~/.ssh/config
☐ Desabilitou login root (PermitRootLogin no)
☐ Mudou porta SSH (opcional, segurança adicional)
☐ Configurou Fail2Ban
☐ Testou transferência de arquivos (SCP)
☐ Documentou credenciais de forma segura
```

---

## 🎯 **COMANDOS RÁPIDOS**

### Conectar:
```powershell
ssh usuario@rioclaro.rj.gov.br
```

### Transferir Projeto:
```powershell
scp -r C:\xampp\htdocs\esic\* usuario@rioclaro.rj.gov.br:/var/www/html/
```

### Executar Comando Remoto:
```powershell
ssh usuario@rioclaro.rj.gov.br "sudo systemctl restart httpd"
```

### Túnel SSH (Port Forwarding):
```powershell
# Acessar MySQL remotamente via túnel
ssh -L 3306:localhost:3306 usuario@rioclaro.rj.gov.br
```

### Executar Script Localmente no Servidor:
```powershell
ssh usuario@rioclaro.rj.gov.br 'bash -s' < diagnostico-almalinux.sh
```

---

## 🔒 **BOAS PRÁTICAS DE SEGURANÇA**

1. ✅ **Sempre use chaves SSH** em vez de senhas
2. ✅ **Nunca compartilhe** sua chave privada (`id_rsa`)
3. ✅ **Use senhas fortes** nas chaves SSH
4. ✅ **Desabilite login root** via SSH
5. ✅ **Mude a porta padrão** (22) se possível
6. ✅ **Instale Fail2Ban** para proteção contra brute force
7. ✅ **Monitore logs** regularmente (`/var/log/secure`)
8. ✅ **Mantenha SSH atualizado** (`sudo dnf update openssh-server`)
9. ✅ **Use firewall** (firewalld)
10. ✅ **Faça backup** das chaves SSH

---

## 📚 **REFERÊNCIAS**

- OpenSSH Official: https://www.openssh.com/
- AlmaLinux Docs: https://wiki.almalinux.org/
- SSH Security Best Practices: https://www.ssh.com/academy/ssh/security

---

## 🆘 **EXEMPLO COMPLETO - SETUP INICIAL**

```powershell
# No Windows PowerShell

# 1. Gerar chave SSH
ssh-keygen -t ed25519 -C "admin@rioclaro.rj.gov.br"

# 2. Ver chave pública
type $env:USERPROFILE\.ssh\id_ed25519.pub

# 3. Conectar ao servidor (primeira vez, com senha)
ssh root@rioclaro.rj.gov.br

# No servidor:
# 4. Criar usuário admin (se não existir)
sudo useradd -m -G wheel admin
sudo passwd admin

# 5. Adicionar chave SSH para admin
sudo mkdir -p /home/admin/.ssh
sudo nano /home/admin/.ssh/authorized_keys
# [Colar a chave pública aqui]

# 6. Ajustar permissões
sudo chmod 700 /home/admin/.ssh
sudo chmod 600 /home/admin/.ssh/authorized_keys
sudo chown -R admin:admin /home/admin/.ssh

# 7. Configurar SSH
sudo nano /etc/ssh/sshd_config
# PermitRootLogin no
# PasswordAuthentication no

# 8. Reiniciar SSH
sudo systemctl restart sshd

# 9. Sair
exit

# De volta ao Windows:
# 10. Testar conexão com chave
ssh admin@rioclaro.rj.gov.br

# 11. Transferir projeto
scp -r C:\xampp\htdocs\esic\* admin@rioclaro.rj.gov.br:/var/www/html/

# 12. Executar script de diagnóstico
ssh admin@rioclaro.rj.gov.br 'bash -s' < diagnostico-almalinux.sh
```

---

✅ **SSH configurado e pronto para uso seguro!** 🔐
