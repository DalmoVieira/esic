# 🔍 DIAGNÓSTICO - Problema de Autenticação SSH

## Problema Atual
```
ssh dalmo@rioclaro.rj.gov.br
Permission denied, please try again.
```

---

## 🎯 **POSSÍVEIS CAUSAS E SOLUÇÕES**

### 1️⃣ **Senha Incorreta (Mais Comum)**

**Verificar:**
- ✅ Certifique-se de que está digitando a senha corretamente
- ✅ Verifique Caps Lock
- ✅ Senha pode ter caracteres especiais que precisam ser digitados com atenção

**Solução:**
```powershell
# Tente novamente prestando atenção na senha
ssh dalmo@rioclaro.rj.gov.br
```

---

### 2️⃣ **Usuário Não Existe no Servidor**

**Verificar no servidor (se tiver acesso alternativo):**
```bash
# Verificar se usuário existe
id dalmo
# ou
cat /etc/passwd | grep dalmo
```

**Criar usuário (se necessário):**
```bash
# Como root ou com sudo
sudo useradd -m -s /bin/bash dalmo
sudo passwd dalmo
# Digite a nova senha
```

---

### 3️⃣ **Autenticação por Senha Desabilitada**

O servidor pode estar configurado para aceitar APENAS chaves SSH.

**Verificar no servidor:**
```bash
sudo cat /etc/ssh/sshd_config | grep PasswordAuthentication
```

Se mostrar:
```
PasswordAuthentication no
```

**Solução A - Habilitar senha temporariamente (no servidor):**
```bash
sudo nano /etc/ssh/sshd_config

# Alterar para:
PasswordAuthentication yes

# Salvar e reiniciar SSH
sudo systemctl restart sshd
```

**Solução B - Usar chave SSH (recomendado):**
Veja seção abaixo.

---

### 4️⃣ **Conta Bloqueada ou Expirada**

**Verificar status da conta (no servidor):**
```bash
sudo passwd -S dalmo
sudo chage -l dalmo
```

**Desbloquear conta:**
```bash
sudo passwd -u dalmo
```

---

### 5️⃣ **SELinux Bloqueando**

**Verificar contexto SSH (no servidor):**
```bash
sudo ausearch -m avc -ts recent | grep sshd
```

**Solução temporária:**
```bash
sudo setenforce 0
```

---

### 6️⃣ **Firewall Bloqueando**

**Verificar se SSH está liberado (no servidor):**
```bash
sudo firewall-cmd --list-all
```

**Liberar SSH:**
```bash
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload
```

---

## 🔑 **SOLUÇÃO RECOMENDADA: Usar Chave SSH**

### Passo 1: Gerar Chave no Windows

```powershell
# Gerar chave SSH
ssh-keygen -t ed25519 -C "dalmo@rioclaro.rj.gov.br"

# Pressione Enter 3 vezes para aceitar padrões
```

### Passo 2: Ver Chave Pública

```powershell
# Ver conteúdo da chave pública
type $env:USERPROFILE\.ssh\id_ed25519.pub
```

**Copie todo o conteúdo** (começa com `ssh-ed25519`)

### Passo 3: Adicionar Chave no Servidor

Se você conseguir acessar o servidor de outra forma (como root, ou console direto):

```bash
# Trocar para usuário dalmo
sudo su - dalmo

# Criar diretório SSH
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Adicionar chave pública
nano ~/.ssh/authorized_keys
# [Colar a chave pública aqui]
# [Ctrl+O] [Enter] [Ctrl+X]

# Ajustar permissões
chmod 600 ~/.ssh/authorized_keys
exit
```

### Passo 4: Testar Conexão

```powershell
# Agora deve conectar sem pedir senha
ssh dalmo@rioclaro.rj.gov.br
```

---

## 🆘 **ALTERNATIVAS DE ACESSO**

### Opção 1: Acessar como Root (se souber a senha)

```powershell
ssh root@rioclaro.rj.gov.br
```

Depois dentro do servidor:
```bash
# Redefinir senha do dalmo
sudo passwd dalmo
# Digite nova senha
```

### Opção 2: Acessar por Console Direto

Se tiver acesso físico ou console virtual (como VNC, iDRAC, etc.):
1. Acessar console direto do servidor
2. Login como root
3. Redefinir senha do usuário dalmo

### Opção 3: Verificar com Administrador

Se não souber a senha:
- Contate o administrador do servidor
- Peça para redefinir a senha do usuário `dalmo`
- Ou peça para adicionar sua chave SSH pública

---

## 🔍 **DIAGNÓSTICO DETALHADO**

Execute estes comandos para mais informações:

### No Windows (modo verbose):

```powershell
# Tentar conexão com modo verbose
ssh -v dalmo@rioclaro.rj.gov.br

# Isso mostrará detalhes do que está acontecendo
```

**Procure por estas mensagens:**
- `debug1: Authentications that can continue: publickey` → Só aceita chave
- `debug1: Authentications that can continue: password` → Aceita senha
- `Permission denied (publickey)` → Precisa de chave SSH
- `Permission denied (password)` → Senha incorreta

### No Servidor (ver logs):

```bash
# Ver últimas tentativas de login
sudo tail -f /var/log/secure

# Ver tentativas falhadas
sudo grep "Failed password" /var/log/secure | tail -20

# Ver se usuário tentou login
sudo grep "dalmo" /var/log/secure | tail -20
```

---

## ✅ **PASSOS PARA RESOLVER AGORA**

### Opção A: Se você tem acesso root ao servidor

```powershell
# 1. Conectar como root
ssh root@rioclaro.rj.gov.br

# No servidor:
# 2. Verificar se usuário dalmo existe
id dalmo

# 3. Se não existir, criar
sudo useradd -m -s /bin/bash dalmo

# 4. Redefinir senha
sudo passwd dalmo
# Digite uma senha forte

# 5. Sair e testar
exit

# 6. Tentar novamente
ssh dalmo@rioclaro.rj.gov.br
```

### Opção B: Configurar chave SSH via root

```powershell
# 1. No Windows, gerar chave
ssh-keygen -t ed25519

# 2. Ver chave pública
type $env:USERPROFILE\.ssh\id_ed25519.pub
# Copiar o conteúdo

# 3. Conectar como root
ssh root@rioclaro.rj.gov.br

# No servidor:
# 4. Configurar chave para dalmo
sudo mkdir -p /home/dalmo/.ssh
sudo nano /home/dalmo/.ssh/authorized_keys
# [Colar chave pública]

# 5. Ajustar permissões
sudo chmod 700 /home/dalmo/.ssh
sudo chmod 600 /home/dalmo/.ssh/authorized_keys
sudo chown -R dalmo:dalmo /home/dalmo/.ssh

# 6. Sair
exit

# 7. Testar
ssh dalmo@rioclaro.rj.gov.br
```

---

## 📊 **CHECKLIST DE VERIFICAÇÃO**

```
☐ Senha está correta (sem Caps Lock)
☐ Usuário 'dalmo' existe no servidor
☐ Conta não está bloqueada
☐ PasswordAuthentication está habilitada no sshd_config
☐ SSH está rodando (systemctl status sshd)
☐ Firewall permite SSH (porta 22)
☐ SELinux não está bloqueando
☐ Logs não mostram outros erros (/var/log/secure)
```

---

## 🎯 **TESTE RÁPIDO**

Execute este comando para diagnóstico completo:

```powershell
ssh -vvv dalmo@rioclaro.rj.gov.br 2>&1 | Select-String "debug1"
```

**Copie a saída** e analise para ver onde está falhando.

---

## 💡 **RECOMENDAÇÃO IMEDIATA**

**Se você tem acesso root:**

1. Conecte como root:
```powershell
ssh root@rioclaro.rj.gov.br
```

2. Execute este script no servidor:
```bash
#!/bin/bash
# Script de diagnóstico do usuário dalmo

echo "=== Verificando usuário dalmo ==="
echo ""

echo "1. Usuário existe?"
id dalmo 2>/dev/null && echo "✓ Sim" || echo "✗ Não"

echo ""
echo "2. Home directory existe?"
ls -ld /home/dalmo 2>/dev/null && echo "✓ Sim" || echo "✗ Não"

echo ""
echo "3. Status da conta:"
passwd -S dalmo 2>/dev/null

echo ""
echo "4. SSH config aceita senha?"
grep "^PasswordAuthentication" /etc/ssh/sshd_config

echo ""
echo "5. Últimas tentativas de login de dalmo:"
grep "dalmo" /var/log/secure | tail -5

echo ""
echo "6. SSH está rodando?"
systemctl is-active sshd
```

---

## 📞 **PRÓXIMO PASSO**

**Execute no Windows:**
```powershell
ssh -vvv dalmo@rioclaro.rj.gov.br
```

**E me envie a saída completa** para eu identificar o problema específico.

Ou tente:
```powershell
ssh root@rioclaro.rj.gov.br
```

Se funcionar, podemos configurar o usuário dalmo corretamente.
