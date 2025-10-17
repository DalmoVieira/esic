# ⚠️ IMPORTANTE - Caminho Correto dos Arquivos

## 📁 Estrutura de Diretórios - AlmaLinux 9

### Caminho Padrão no Servidor de Produção:

```
/var/www/esic/
```

**NÃO é:**
- ❌ `/var/www/html/esic/` (este é usado em algumas distros Ubuntu/Debian)
- ❌ `/var/www/html/`
- ❌ `/usr/share/nginx/html/esic/`

**É exatamente:**
- ✅ `/var/www/esic/`

---

## 🔧 Configuração Correta do VirtualHost

### Arquivo: `/etc/httpd/conf.d/esic.conf`

```apache
<VirtualHost *:80>
    ServerName rioclaro.rj.gov.br
    ServerAlias www.rioclaro.rj.gov.br
    
    # CAMINHO CORRETO - AlmaLinux 9
    DocumentRoot /var/www/esic
    
    <Directory /var/www/esic>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/esic-error.log
    CustomLog /var/log/httpd/esic-access.log combined
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

---

## 📋 Estrutura Completa dos Arquivos

```
/var/www/esic/
├── index.php
├── login.php
├── transparencia.php
├── dashboard.php
├── admin.php
├── novo-pedido.php
├── acompanhar.php
├── recurso.php
├── home.php
├── .htaccess
├── bootstrap.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│       ├── logo-pmrcrj.png
│       └── logo-pmrcrj.svg
├── app/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── ...
├── config/
│   ├── constants.php
│   └── ...
├── database/
│   └── schema_novo.sql
├── uploads/
├── logs/
└── public/
```

---

## ✅ Comandos Corretos para AlmaLinux 9

### 1. Criar o diretório:
```bash
sudo mkdir -p /var/www/esic
```

### 2. Definir permissões:
```bash
sudo chown -R apache:apache /var/www/esic
sudo find /var/www/esic -type d -exec chmod 755 {} \;
sudo find /var/www/esic -type f -exec chmod 644 {} \;
sudo chmod 775 /var/www/esic/uploads
```

### 3. Configurar SELinux:
```bash
sudo chcon -R -t httpd_sys_content_t /var/www/esic
sudo chcon -R -t httpd_sys_rw_content_t /var/www/esic/uploads
sudo setsebool -P httpd_unified on
```

### 4. Testar acesso:
```bash
curl -I http://localhost/esic/login.php
```

---

## 🔍 Como Descobrir Onde Estão os Arquivos

Se você não tiver certeza onde os arquivos foram colocados, execute:

```bash
# Procurar login.php em todo o sistema
sudo find /var/www -name "login.php" -type f 2>/dev/null
sudo find /home -name "login.php" -type f 2>/dev/null
sudo find /usr/share -name "login.php" -type f 2>/dev/null

# Listar conteúdo dos possíveis diretórios
ls -la /var/www/esic/
ls -la /var/www/html/esic/
ls -la /var/www/html/
```

---

## 📝 Resumo Rápido

**No servidor de produção AlmaLinux 9:**

1. **Caminho dos arquivos:** `/var/www/esic/`
2. **Usuário/Grupo:** `apache:apache`
3. **Permissões diretórios:** `755`
4. **Permissões arquivos:** `644`
5. **Permissões uploads:** `775`
6. **VirtualHost:** DocumentRoot `/var/www/esic`
7. **SELinux context:** `httpd_sys_content_t`

---

## ⚡ Script de Verificação Rápida

Execute este comando no servidor para verificar:

```bash
#!/bin/bash
echo "=== VERIFICAÇÃO DO CAMINHO E-SIC ==="
echo ""

echo "1. Procurando login.php:"
find /var/www -name "login.php" -type f 2>/dev/null

echo ""
echo "2. Conteúdo de /var/www/esic:"
ls -la /var/www/esic/ 2>/dev/null | head -10 || echo "Diretório não existe"

echo ""
echo "3. VirtualHost configurado:"
grep -i DocumentRoot /etc/httpd/conf.d/esic.conf 2>/dev/null || echo "Arquivo não existe"

echo ""
echo "4. Permissões:"
ls -ld /var/www/esic 2>/dev/null || echo "Diretório não existe"

echo ""
echo "5. SELinux context:"
ls -Zd /var/www/esic 2>/dev/null || echo "Diretório não existe"
```

**Salvar como** `verificar-caminho.sh` e executar:
```bash
sudo bash verificar-caminho.sh
```

---

## 🚨 Correção de Caminho Errado

Se os arquivos estiverem em `/var/www/html/esic/`, você tem duas opções:

### Opção A: Mover os arquivos
```bash
sudo mv /var/www/html/esic /var/www/esic
sudo chown -R apache:apache /var/www/esic
```

### Opção B: Ajustar o VirtualHost
```bash
sudo nano /etc/httpd/conf.d/esic.conf
```

Mudar:
```apache
DocumentRoot /var/www/html/esic

<Directory /var/www/html/esic>
    ...
</Directory>
```

---

## ✅ Confirmação Final

Após ajustar o caminho, teste:

```bash
# 1. Verificar se arquivo existe
ls -la /var/www/esic/login.php

# 2. Verificar permissões
stat /var/www/esic/login.php

# 3. Verificar VirtualHost
sudo httpd -t
grep DocumentRoot /etc/httpd/conf.d/esic.conf

# 4. Reiniciar Apache
sudo systemctl restart httpd

# 5. Testar acesso
curl -I http://localhost/esic/login.php
```

**Resultado esperado:**
```
HTTP/1.1 200 OK
```

---

**Caminho correto confirmado:** `/var/www/esic/` ✅
