# 🚨 RESOLUÇÃO RÁPIDA - Erro 404 no AlmaLinux 9

## ⚡ Solução em 5 Minutos

### 1️⃣ Envie o script de diagnóstico para o servidor

```bash
# Do seu computador Windows (PowerShell)
scp C:\xampp\htdocs\esic\diagnostico-almalinux.sh usuario@rioclaro.rj.gov.br:/tmp/
```

### 2️⃣ No servidor, execute o script

```bash
# Conectar ao servidor
ssh usuario@rioclaro.rj.gov.br

# Executar diagnóstico
sudo bash /tmp/diagnostico-almalinux.sh
```

**O script irá automaticamente:**
- ✅ Encontrar onde o E-SIC está instalado
- ✅ Corrigir permissões de arquivos
- ✅ Configurar SELinux corretamente
- ✅ Criar .htaccess se não existir
- ✅ Verificar/criar VirtualHost
- ✅ Liberar portas no firewall
- ✅ Reiniciar serviços
- ✅ Testar acesso

---

## 🔍 Causas Comuns do Erro 404

### 1. DocumentRoot Incorreto
**Problema:** VirtualHost aponta para pasta errada

**Solução:**
```bash
sudo nano /etc/httpd/conf.d/esic.conf
```

Verificar linha:
```apache
DocumentRoot /var/www/html/esic
```

Deve apontar para onde os arquivos estão!

### 2. SELinux Bloqueando
**Problema:** SELinux impede Apache de ler arquivos

**Solução:**
```bash
sudo chcon -R -t httpd_sys_content_t /var/www/html/esic
sudo setsebool -P httpd_unified on
```

### 3. Permissões Erradas
**Problema:** Apache não consegue ler os arquivos

**Solução:**
```bash
cd /var/www/html/esic
sudo chown -R apache:apache .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
```

### 4. AllowOverride Não Configurado
**Problema:** .htaccess sendo ignorado

**Solução:**
```apache
<Directory /var/www/html/esic>
    AllowOverride All
    Require all granted
</Directory>
```

### 5. Firewall Bloqueando
**Problema:** Porta 80/443 fechada

**Solução:**
```bash
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

---

## 🛠️ Comandos Úteis de Diagnóstico

### Verificar onde os arquivos estão:
```bash
sudo find /var/www -name "login.php" -type f 2>/dev/null
sudo find /home -name "login.php" -type f 2>/dev/null
```

### Ver logs de erro em tempo real:
```bash
sudo tail -f /var/log/httpd/error_log
```

### Testar configuração Apache:
```bash
sudo httpd -t
```

### Ver negações SELinux:
```bash
sudo ausearch -m avc -ts recent | grep httpd
```

### Testar acesso local:
```bash
curl -I http://localhost/esic/login.php
```

---

## 📋 Checklist Manual (se script falhar)

Execute na ordem:

```bash
# 1. Ir para diretório
cd /var/www/html/esic  # Ajuste se necessário

# 2. Corrigir permissões
sudo chown -R apache:apache .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;

# 3. Configurar SELinux
sudo chcon -R -t httpd_sys_content_t .
sudo setsebool -P httpd_unified on

# 4. Criar .htaccess
cat > .htaccess << 'EOF'
RewriteEngine On
RewriteBase /esic/
DirectoryIndex index.php login.php
Options -Indexes
EOF

# 5. Criar/Editar VirtualHost
sudo nano /etc/httpd/conf.d/esic.conf
```

Conteúdo do VirtualHost:
```apache
<VirtualHost *:80>
    ServerName rioclaro.rj.gov.br
    
    DocumentRoot /var/www/html/esic
    
    <Directory /var/www/html/esic>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/esic-error.log
    CustomLog /var/log/httpd/esic-access.log combined
</VirtualHost>
```

```bash
# 6. Testar configuração
sudo httpd -t

# 7. Liberar firewall
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload

# 8. Reiniciar serviços
sudo systemctl restart httpd
sudo systemctl restart php-fpm

# 9. Testar
curl -I http://localhost/esic/login.php
```

---

## ✅ Resultado Esperado

Após executar, você deve ver:

```bash
HTTP/1.1 200 OK
Date: ...
Server: Apache/2.4.53 (AlmaLinux)
Content-Type: text/html; charset=UTF-8
```

**Se ainda aparecer 404:**
```bash
HTTP/1.1 404 Not Found
```

Então o problema é:
1. DocumentRoot incorreto no VirtualHost
2. Arquivos não estão onde você pensa
3. SELinux ainda bloqueando

---

## 📞 Informações que Preciso se Ainda Não Funcionar

Execute e me envie o resultado:

```bash
#!/bin/bash
echo "=== INFORMAÇÕES DO SERVIDOR ==="
echo ""
echo "1. Sistema:"
cat /etc/os-release | grep PRETTY_NAME
echo ""
echo "2. Apache:"
httpd -v
echo ""
echo "3. Onde está o E-SIC:"
find /var/www /home -name "login.php" -type f 2>/dev/null
echo ""
echo "4. VirtualHost:"
cat /etc/httpd/conf.d/esic.conf 2>/dev/null || echo "Não existe"
echo ""
echo "5. SELinux:"
getenforce
ls -Z /var/www/html/esic 2>/dev/null | head -5
echo ""
echo "6. Permissões:"
ls -la /var/www/html/esic 2>/dev/null | head -10
echo ""
echo "7. Teste local:"
curl -I http://localhost/esic/login.php
echo ""
echo "8. Últimos erros:"
tail -10 /var/log/httpd/error_log
```

---

## 🎯 Próximos Passos Após Resolver o 404

1. ✅ **Configurar HTTPS:**
   ```bash
   sudo dnf install -y certbot python3-certbot-apache
   sudo certbot --apache -d rioclaro.rj.gov.br
   ```

2. ✅ **Configurar Banco de Dados:**
   ```bash
   sudo dnf install -y mariadb-server
   sudo systemctl start mariadb
   sudo mysql_secure_installation
   ```

3. ✅ **Importar Schema:**
   ```bash
   mysql -u root -p esic_db < /var/www/html/esic/database/schema_novo.sql
   ```

---

## 📚 Documentação Completa

- **Guia Detalhado:** `DEPLOY_ALMALINUX9.md`
- **Guia Geral:** `DEPLOY_PRODUCAO.md`

---

## ⚠️ IMPORTANTE

**O banco de dados NÃO é necessário** para corrigir o erro 404!

O erro 404 acontece porque:
- Apache não encontra os arquivos PHP
- VirtualHost mal configurado
- SELinux bloqueando
- Permissões erradas

**Não tem nada a ver com banco de dados!**

Primeiro resolva o 404, **depois** configure o banco.

---

✅ **Execute o script `diagnostico-almalinux.sh` e o problema será resolvido automaticamente!**
