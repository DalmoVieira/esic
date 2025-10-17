# 🚀 SSH - Guia Rápido

## Conexão Rápida ao Servidor

---

## 1️⃣ **CONECTAR (Primeira Vez)**

```powershell
# No Windows PowerShell
ssh usuario@rioclaro.rj.gov.br
```

Digite a senha quando solicitado.

---

## 2️⃣ **CONFIGURAR CHAVE SSH (Recomendado)**

### No Windows:

```powershell
# 1. Gerar chave
ssh-keygen -t ed25519 -C "seu_email@exemplo.com"
# [Enter] [Enter] [Enter] para aceitar padrões

# 2. Ver chave pública
type $env:USERPROFILE\.ssh\id_ed25519.pub
# Copiar o conteúdo (começa com ssh-ed25519)
```

### No Servidor:

```bash
# 3. Conectar ao servidor
ssh usuario@rioclaro.rj.gov.br

# 4. Adicionar chave
mkdir -p ~/.ssh
chmod 700 ~/.ssh
nano ~/.ssh/authorized_keys
# [Colar a chave pública aqui]
# [Ctrl+O] [Enter] [Ctrl+X]

# 5. Ajustar permissões
chmod 600 ~/.ssh/authorized_keys

# 6. Sair
exit
```

### Testar:

```powershell
# Agora deve conectar SEM pedir senha
ssh usuario@rioclaro.rj.gov.br
```

---

## 3️⃣ **TRANSFERIR ARQUIVOS**

### Enviar projeto para servidor:

```powershell
# Todos os arquivos
scp -r C:\xampp\htdocs\esic\* usuario@rioclaro.rj.gov.br:/var/www/html/

# Arquivo único
scp C:\xampp\htdocs\esic\login.php usuario@rioclaro.rj.gov.br:/var/www/html/

# Script de diagnóstico
scp diagnostico-almalinux.sh usuario@rioclaro.rj.gov.br:/tmp/
```

### Baixar do servidor:

```powershell
# Baixar arquivo
scp usuario@rioclaro.rj.gov.br:/var/www/html/config.php C:\backup\

# Baixar diretório
scp -r usuario@rioclaro.rj.gov.br:/var/www/html C:\backup\
```

---

## 4️⃣ **EXECUTAR COMANDOS REMOTOS**

```powershell
# Reiniciar Apache
ssh usuario@rioclaro.rj.gov.br "sudo systemctl restart httpd"

# Ver logs
ssh usuario@rioclaro.rj.gov.br "sudo tail -20 /var/log/httpd/error_log"

# Executar script local no servidor
ssh usuario@rioclaro.rj.gov.br 'bash -s' < diagnostico-almalinux.sh
```

---

## 5️⃣ **FACILITAR ACESSO**

### Criar arquivo de configuração:

**Arquivo:** `C:\Users\SeuUsuario\.ssh\config`

```
Host esic
    HostName rioclaro.rj.gov.br
    User seu_usuario
    IdentityFile C:\Users\SeuUsuario\.ssh\id_ed25519
```

### Agora conecte simplesmente:

```powershell
ssh esic
scp arquivo.php esic:/var/www/html/
```

---

## 6️⃣ **SEGURANÇA NO SERVIDOR**

```bash
# No servidor, editar configuração SSH
sudo nano /etc/ssh/sshd_config

# Alterar estas linhas:
PermitRootLogin no
PasswordAuthentication no

# Salvar e reiniciar
sudo systemctl restart sshd
```

---

## 🆘 **PROBLEMAS COMUNS**

### "Connection refused"
```bash
# No servidor
sudo systemctl status sshd
sudo systemctl start sshd
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload
```

### "Permission denied (publickey)"
```bash
# Verificar permissões no servidor
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
cat ~/.ssh/authorized_keys  # Ver se chave está lá
```

### "Host key verification failed"
```powershell
# No Windows
ssh-keygen -R rioclaro.rj.gov.br
```

---

## 📊 **COMANDOS ÚTEIS**

```powershell
# Conectar
ssh usuario@servidor

# Conectar com porta diferente
ssh usuario@servidor -p 2222

# Copiar arquivo
scp arquivo.txt usuario@servidor:/caminho/

# Copiar pasta
scp -r pasta/ usuario@servidor:/caminho/

# Executar comando remoto
ssh usuario@servidor "comando"

# SFTP (interface de arquivos)
sftp usuario@servidor

# Ver chave pública
type $env:USERPROFILE\.ssh\id_ed25519.pub

# Testar conexão
ssh -v usuario@servidor
```

---

## ✅ **CHECKLIST RÁPIDO**

```
☐ Conectou ao servidor via SSH
☐ Gerou chave SSH no Windows
☐ Copiou chave para servidor
☐ Testou conexão sem senha
☐ Transferiu arquivos do projeto
☐ Configurou segurança (desabilitar senha)
```

---

## 🎯 **EXEMPLO PRÁTICO COMPLETO**

```powershell
# 1. Gerar chave
ssh-keygen -t ed25519

# 2. Ver chave
type $env:USERPROFILE\.ssh\id_ed25519.pub

# 3. Conectar e configurar servidor
ssh root@rioclaro.rj.gov.br

# No servidor:
mkdir -p ~/.ssh
nano ~/.ssh/authorized_keys
# [Colar chave]
chmod 600 ~/.ssh/authorized_keys
exit

# 4. Testar
ssh root@rioclaro.rj.gov.br

# 5. Enviar projeto
scp -r C:\xampp\htdocs\esic\* root@rioclaro.rj.gov.br:/var/www/html/

# 6. Executar diagnóstico
ssh root@rioclaro.rj.gov.br 'bash -s' < diagnostico-almalinux.sh
```

---

**Para guia completo, veja:** `CONFIGURAR-SSH.md` 📚
