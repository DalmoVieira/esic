# ⚠️ IMPORTANTE - Caminho Correto dos Arquivos

## 📁 Estrutura de Diretórios - AlmaLinux 9

### Caminho Padrão no Servidor de Produção:

```
/var/www/html/
```

**Sistema na raiz do servidor web:**
- ✅ `/var/www/html/` (arquivos do E-SIC na raiz)
- ✅ URL: `rioclaro.rj.gov.br` (sem `/esic`)

**NÃO é:**
- ❌ `/var/www/esic/`
- ❌ `/var/www/html/esic/`
- ❌ URL: `rioclaro.rj.gov.br/esic`

---

## 🔧 Configuração Correta do VirtualHost

### Arquivo: `/etc/httpd/conf.d/rioclaro.conf`

```apache
<VirtualHost *:80>
    ServerName rioclaro.rj.gov.br
    ServerAlias www.rioclaro.rj.gov.br
    
    # Sistema na raiz do servidor web
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/rioclaro-error.log
    CustomLog /var/log/httpd/rioclaro-access.log combined
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

---

## 📋 Estrutura Completa dos Arquivos

```
/var/www/html/
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

### 1. Ir para o diretório:
```bash
cd /var/www/html
```

### 2. Definir permissões:
```bash
sudo chown -R apache:apache /var/www/html
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo find /var/www/html -type f -exec chmod 644 {} \;
sudo chmod 775 /var/www/html/uploads
```

### 3. Configurar SELinux:
```bash
sudo chcon -R -t httpd_sys_content_t /var/www/html
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/uploads
sudo setsebool -P httpd_unified on
```

### 4. Testar acesso:
```bash
curl -I http://localhost/login.php
```

---

## 🔍 Como Descobrir Onde Estão os Arquivos

Se você não tiver certeza onde os arquivos foram colocados, execute:

```bash
# Procurar login.php em todo o sistema
sudo find /var/www -name "login.php" -type f 2>/dev/null

# Verificar o diretório padrão
ls -la /var/www/html/

# Ver se existe login.php na raiz
ls -la /var/www/html/login.php
```

---

## 📝 Resumo Rápido

**No servidor de produção AlmaLinux 9:**

1. **Caminho dos arquivos:** `/var/www/html/`
2. **URL de acesso:** `rioclaro.rj.gov.br` (sem `/esic`)
3. **Usuário/Grupo:** `apache:apache`
4. **Permissões diretórios:** `755`
5. **Permissões arquivos:** `644`
6. **Permissões uploads:** `775`
7. **VirtualHost:** DocumentRoot `/var/www/html`
8. **SELinux context:** `httpd_sys_content_t`

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
echo "2. Conteúdo de /var/www/html:"
ls -la /var/www/html/ 2>/dev/null | head -10 || echo "Diretório não existe"

echo ""
echo "3. VirtualHost configurado:"
grep -i DocumentRoot /etc/httpd/conf.d/rioclaro.conf 2>/dev/null || echo "Arquivo não existe"

echo ""
echo "4. Permissões:"
ls -ld /var/www/html 2>/dev/null || echo "Diretório não existe"

echo ""
echo "5. SELinux context:"
ls -Zd /var/www/html 2>/dev/null || echo "Diretório não existe"
```

**Salvar como** `verificar-caminho.sh` e executar:
```bash
sudo bash verificar-caminho.sh
```

---

## 🚨 Correção de Caminho Errado

Se os arquivos estiverem em subdiretório `/var/www/html/esic/`, você precisa:

### Opção A: Mover os arquivos para a raiz
```bash
sudo mv /var/www/html/esic/* /var/www/html/
sudo mv /var/www/html/esic/.htaccess /var/www/html/
sudo rmdir /var/www/html/esic
sudo chown -R apache:apache /var/www/html
```

### Opção B: Ajustar o VirtualHost e .htaccess
```bash
sudo nano /etc/httpd/conf.d/rioclaro.conf
```

Ajustar:
```apache
DocumentRoot /var/www/html/esic

<Directory /var/www/html/esic>
    ...
</Directory>
```

E ajustar `.htaccess` com:
```apache
RewriteBase /esic/
```

---

## ✅ Confirmação Final

Após ajustar o caminho, teste:

```bash
# 1. Verificar se arquivo existe
ls -la /var/www/html/login.php

# 2. Verificar permissões
stat /var/www/html/login.php

# 3. Verificar VirtualHost
sudo httpd -t
grep DocumentRoot /etc/httpd/conf.d/rioclaro.conf

# 4. Reiniciar Apache
sudo systemctl restart httpd

# 5. Testar acesso
curl -I http://localhost/login.php
```

**Resultado esperado:**
```
HTTP/1.1 200 OK
```

---

## 🌐 URLs de Acesso

Após configurar corretamente:

- ✅ **http://rioclaro.rj.gov.br/login.php** → Página de login
- ✅ **http://rioclaro.rj.gov.br/transparencia.php** → Portal da transparência
- ✅ **http://rioclaro.rj.gov.br/** → Home do sistema

**NÃO é:**
- ❌ `http://rioclaro.rj.gov.br/esic/login.php`

---

**Caminho correto confirmado:** `/var/www/html/` ✅  
**URL correta:** `rioclaro.rj.gov.br` (sem `/esic`) ✅
