# 🔧 CRIAR USUÁRIO DALMO NO SERVIDOR

## Comandos para Executar no Servidor (como root)

Você já está conectado como root, agora execute:

---

## 1️⃣ **CRIAR USUÁRIO DALMO**

```bash
# Criar usuário dalmo com home directory
useradd -m -s /bin/bash -c "Dalmo Vieira" dalmo

# Verificar se foi criado
id dalmo

# Ver informações
cat /etc/passwd | grep dalmo
```

---

## 2️⃣ **DEFINIR SENHA**

```bash
# Definir senha para o usuário dalmo
passwd dalmo

# Digite a nova senha duas vezes
# Exemplo: Dalmo@2025!RioClaro (use uma senha forte)
```

---

## 3️⃣ **ADICIONAR AO GRUPO WHEEL (Permissões sudo)**

```bash
# Adicionar dalmo ao grupo wheel (para usar sudo)
usermod -aG wheel dalmo

# Verificar grupos do usuário
groups dalmo
```

---

## 4️⃣ **CONFIGURAR DIRETÓRIO SSH**

```bash
# Criar diretório .ssh para o usuário dalmo
mkdir -p /home/dalmo/.ssh
chmod 700 /home/dalmo/.ssh

# Criar arquivo authorized_keys (para chaves SSH futuras)
touch /home/dalmo/.ssh/authorized_keys
chmod 600 /home/dalmo/.ssh/authorized_keys

# Ajustar proprietário
chown -R dalmo:dalmo /home/dalmo/.ssh
```

---

## 5️⃣ **CONFIGURAR SELINUX (se necessário)**

```bash
# Ajustar contexto SELinux para o diretório SSH
chcon -R -t ssh_home_t /home/dalmo/.ssh
restorecon -R -v /home/dalmo/.ssh
```

---

## 6️⃣ **VERIFICAR CONFIGURAÇÃO**

```bash
# Verificar home directory
ls -la /home/dalmo

# Verificar .ssh
ls -la /home/dalmo/.ssh

# Verificar permissões
stat /home/dalmo/.ssh
stat /home/dalmo/.ssh/authorized_keys
```

---

## 7️⃣ **TESTAR TROCA DE USUÁRIO**

```bash
# Trocar para usuário dalmo
su - dalmo

# Verificar se está funcionando
whoami
pwd

# Ver grupos
groups

# Testar sudo (deve pedir senha)
sudo ls /root

# Voltar para root
exit
```

---

## 8️⃣ **SAIR DO SERVIDOR**

```bash
exit
```

---

## ✅ **TESTAR CONEXÃO DO WINDOWS**

Agora no **Windows PowerShell**:

```powershell
# Conectar com o usuário dalmo
ssh dalmo@rioclaro.rj.gov.br

# Digite a senha que você definiu
```

**Deve funcionar agora!** ✅

---

## 🔑 **CONFIGURAR CHAVE SSH (Opcional mas Recomendado)**

### No Windows:

```powershell
# 1. Gerar chave SSH (se ainda não tiver)
ssh-keygen -t ed25519 -C "dalmo@rioclaro.rj.gov.br"
# Pressione Enter 3 vezes

# 2. Ver chave pública
type $env:USERPROFILE\.ssh\id_ed25519.pub
```

**Copie todo o conteúdo** (linha completa começando com `ssh-ed25519`)

### No Servidor (como root):

```bash
# Adicionar chave SSH para dalmo
echo "ssh-ed25519 AAAA... dalmo@rioclaro.rj.gov.br" >> /home/dalmo/.ssh/authorized_keys

# Ajustar permissões
chmod 600 /home/dalmo/.ssh/authorized_keys
chown dalmo:dalmo /home/dalmo/.ssh/authorized_keys

# Verificar
cat /home/dalmo/.ssh/authorized_keys
```

### Testar no Windows:

```powershell
# Agora deve conectar SEM pedir senha
ssh dalmo@rioclaro.rj.gov.br
```

---

## 📋 **SCRIPT COMPLETO PARA COPIAR E COLAR**

Execute tudo de uma vez no servidor (como root):

```bash
#!/bin/bash
# Script para criar e configurar usuário dalmo

echo "🔧 Criando usuário dalmo..."

# Criar usuário
useradd -m -s /bin/bash -c "Dalmo Vieira" dalmo

# Definir senha (você será solicitado a digitar)
echo "📝 Defina a senha para o usuário dalmo:"
passwd dalmo

# Adicionar ao grupo wheel (sudo)
usermod -aG wheel dalmo

# Criar diretório SSH
mkdir -p /home/dalmo/.ssh
chmod 700 /home/dalmo/.ssh
touch /home/dalmo/.ssh/authorized_keys
chmod 600 /home/dalmo/.ssh/authorized_keys
chown -R dalmo:dalmo /home/dalmo/.ssh

# Ajustar SELinux
chcon -R -t ssh_home_t /home/dalmo/.ssh 2>/dev/null
restorecon -R -v /home/dalmo/.ssh 2>/dev/null

echo "✅ Usuário dalmo criado com sucesso!"
echo ""
echo "📊 Informações do usuário:"
id dalmo
echo ""
echo "📁 Home directory:"
ls -la /home/dalmo
echo ""
echo "🔐 Diretório SSH:"
ls -la /home/dalmo/.ssh
echo ""
echo "✅ Pronto! Teste a conexão do Windows com:"
echo "   ssh dalmo@rioclaro.rj.gov.br"
```

---

## 🎯 **RESUMO DOS COMANDOS PRINCIPAIS**

```bash
# 1. Criar usuário
useradd -m -s /bin/bash dalmo

# 2. Definir senha
passwd dalmo

# 3. Adicionar ao grupo wheel
usermod -aG wheel dalmo

# 4. Configurar SSH
mkdir -p /home/dalmo/.ssh
chmod 700 /home/dalmo/.ssh
chown -R dalmo:dalmo /home/dalmo/.ssh

# 5. Sair
exit
```

---

## 📝 **INFORMAÇÕES CRIADAS**

Após executar, você terá:

- ✅ **Usuário:** `dalmo`
- ✅ **Home:** `/home/dalmo`
- ✅ **Shell:** `/bin/bash`
- ✅ **Grupos:** `dalmo`, `wheel` (pode usar sudo)
- ✅ **SSH:** Pronto para receber chaves SSH
- ✅ **Senha:** Definida por você

---

## 🔐 **PERMISSÕES SUDO (Opcional)**

Se quiser que dalmo execute comandos como root sem senha:

```bash
# Editar sudoers
visudo

# Adicionar esta linha:
dalmo ALL=(ALL) NOPASSWD: ALL
```

**Ou** para exigir senha (mais seguro):

```bash
# A linha já existe para o grupo wheel:
%wheel ALL=(ALL) ALL
```

Como dalmo já está no grupo wheel, ele pode usar `sudo` digitando sua senha.

---

✅ **Execute os comandos acima no servidor e teste a conexão!**
