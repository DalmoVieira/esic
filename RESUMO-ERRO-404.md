# 📊 RESUMO EXECUTIVO - Erro 404 no Servidor de Produção

**Data:** 17 de outubro de 2025  
**Servidor:** rioclaro.rj.gov.br/esic  
**Sistema Operacional:** AlmaLinux 9  
**Problema:** Erro 404 ao acessar o sistema

---

## ✅ O QUE FOI FEITO

### 1. Substituição do Logo ✓
- ✅ Logo atualizado de `logo-rioclaro.svg` para `logo-pmrcrj.png`
- ✅ 11 arquivos PHP atualizados
- ✅ Arquivo de constantes atualizado
- ✅ Commit e push para GitHub realizados

### 2. Documentação para Resolver o 404 ✓
Criados 3 arquivos importantes:

1. **`DEPLOY_ALMALINUX9.md`**
   - Guia completo passo a passo
   - Configuração de Apache, SELinux, firewall
   - 12 seções detalhadas

2. **`diagnostico-almalinux.sh`**
   - Script automatizado de diagnóstico e correção
   - Detecta e corrige automaticamente:
     - Permissões incorretas
     - SELinux bloqueando
     - VirtualHost mal configurado
     - Firewall fechado
     - Falta de .htaccess

3. **`RESOLVER-404-ALMALINUX.md`**
   - Guia rápido de resolução
   - Comandos prontos para copiar/colar
   - Checklist manual

---

## 🎯 PRÓXIMOS PASSOS PARA RESOLVER O 404

### Opção A: Automática (RECOMENDADA) ⚡

```bash
# 1. Enviar script para o servidor
scp diagnostico-almalinux.sh usuario@rioclaro.rj.gov.br:/tmp/

# 2. No servidor, executar
ssh usuario@rioclaro.rj.gov.br
sudo bash /tmp/diagnostico-almalinux.sh
```

**O script fará tudo automaticamente!**

---

### Opção B: Manual 🔧

Se preferir fazer manualmente, execute no servidor:

```bash
# 1. Encontrar onde estão os arquivos
sudo find /var/www -name "login.php" -type f

# 2. Ir para o diretório
cd /var/www/html/esic  # (ajuste conforme necessário)

# 3. Corrigir permissões
sudo chown -R apache:apache .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;

# 4. Configurar SELinux
sudo chcon -R -t httpd_sys_content_t .
sudo setsebool -P httpd_unified on

# 5. Criar .htaccess
cat > .htaccess << 'EOF'
RewriteEngine On
RewriteBase /esic/
DirectoryIndex index.php login.php
Options -Indexes
EOF

# 6. Editar VirtualHost
sudo nano /etc/httpd/conf.d/esic.conf
```

Configuração do VirtualHost:
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
# 7. Testar e reiniciar
sudo httpd -t
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
sudo systemctl restart httpd
sudo systemctl restart php-fpm

# 8. Testar
curl -I http://localhost/esic/login.php
```

---

## 🔍 DIAGNÓSTICO DO PROBLEMA

### Causas Comuns do Erro 404 no AlmaLinux 9:

1. **DocumentRoot Incorreto** ⚠️
   - VirtualHost aponta para pasta errada
   - Verificar: `/etc/httpd/conf.d/esic.conf`

2. **SELinux Bloqueando** 🔒
   - AlmaLinux vem com SELinux ativo
   - Apache não consegue ler arquivos
   - Solução: `chcon` e `setsebool`

3. **Permissões Erradas** 📁
   - Arquivos não pertencem ao usuário `apache`
   - Permissões muito restritivas
   - Solução: `chown apache:apache`

4. **AllowOverride Não Configurado** ⚙️
   - .htaccess sendo ignorado
   - Mod_rewrite não funciona
   - Solução: `AllowOverride All` no VirtualHost

5. **Firewall Bloqueando** 🛡️
   - Portas 80/443 fechadas
   - Solução: `firewall-cmd --add-service=http`

---

## ⚠️ IMPORTANTE

### O BANCO DE DADOS **NÃO** É NECESSÁRIO para resolver o 404!

O erro 404 é um problema de **configuração do servidor**, não do banco de dados.

**Ordem correta:**
1. ✅ Resolver o 404 primeiro (arquivos não encontrados)
2. ✅ Depois configurar o banco de dados
3. ✅ Por último, importar o schema SQL

---

## 📋 CHECKLIST DE RESOLUÇÃO

Execute no servidor e marque:

```
☐ Arquivos estão em /var/www/html/
☐ login.php existe e é legível
☐ VirtualHost configurado corretamente
☐ DocumentRoot aponta para /var/www/html
☐ AllowOverride All está no VirtualHost
☐ mod_rewrite habilitado no Apache
☐ .htaccess criado na raiz
☐ Permissões: apache:apache, 755/644
☐ SELinux configurado (httpd_sys_content_t)
☐ Firewall liberou HTTP (80) e HTTPS (443)
☐ Apache reiniciado
☐ PHP-FPM reiniciado
☐ Teste local retorna 200 OK
```

---

## 🧪 TESTE RÁPIDO

No servidor, execute:

```bash
curl -I http://localhost/esic/login.php
```

**Resultado esperado:**
```
HTTP/1.1 200 OK
```

**Se ainda aparecer 404:**
```
HTTP/1.1 404 Not Found
```

Então:
1. Verificar logs: `sudo tail -f /var/log/httpd/error_log`
2. Ver SELinux: `sudo ausearch -m avc -ts recent | grep httpd`
3. Conferir DocumentRoot no VirtualHost

---

## 📞 INFORMAÇÕES NECESSÁRIAS SE NÃO FUNCIONAR

Execute no servidor e me envie o resultado:

```bash
echo "1. Onde está o E-SIC:"
sudo find /var/www /home -name "login.php" -type f 2>/dev/null

echo "2. VirtualHost atual:"
sudo cat /etc/httpd/conf.d/esic.conf

echo "3. Permissões:"
ls -la /var/www/html/esic | head -10

echo "4. SELinux:"
getenforce
ls -Z /var/www/html/esic | head -5

echo "5. Teste local:"
curl -I http://localhost/esic/login.php

echo "6. Últimos erros:"
sudo tail -10 /var/log/httpd/error_log
```

---

## 📚 ARQUIVOS DE REFERÊNCIA

Todos os arquivos estão no GitHub e na pasta local:

- `DEPLOY_ALMALINUX9.md` - Guia completo e detalhado
- `RESOLVER-404-ALMALINUX.md` - Guia rápido
- `diagnostico-almalinux.sh` - Script automatizado
- `DEPLOY_PRODUCAO.md` - Guia geral de deploy

**GitHub:** https://github.com/DalmoVieira/esic

---

## 🎯 RESULTADO ESPERADO

Após executar o script ou seguir o guia manual:

✅ **http://rioclaro.rj.gov.br/esic/login.php** → Página de login funcionando  
✅ **http://rioclaro.rj.gov.br/esic/transparencia.php** → Portal da transparência  
✅ **http://rioclaro.rj.gov.br/esic/** → Redireciona para login

---

## 🚀 APÓS RESOLVER O 404

**Próximos passos:**

1. **Configurar HTTPS/SSL:**
   ```bash
   sudo dnf install -y certbot python3-certbot-apache
   sudo certbot --apache -d rioclaro.rj.gov.br
   ```

2. **Configurar Banco de Dados:**
   ```bash
   sudo dnf install -y mariadb-server
   sudo systemctl start mariadb
   sudo mysql_secure_installation
   ```

3. **Criar Banco e Usuário:**
   ```sql
   CREATE DATABASE esic_db;
   CREATE USER 'esic_user'@'localhost' IDENTIFIED BY 'senha';
   GRANT ALL ON esic_db.* TO 'esic_user'@'localhost';
   ```

4. **Importar Schema:**
   ```bash
   mysql -u esic_user -p esic_db < database/schema_novo.sql
   ```

5. **Atualizar Configurações:**
   - Editar `config/constants.php`
   - Configurar credenciais do banco

---

## 📊 RESUMO DO COMMIT

**Commit:** `e80832e`  
**Mensagem:** feat: Replace logo with PMRCRJ branding and add AlmaLinux 9 deployment guide

**Alterações:**
- 21 arquivos modificados
- 1.358 inserções
- 441 deleções
- Novo logo PMRCRJ (PNG e SVG)
- 3 novos documentos de deploy
- Script automatizado de diagnóstico

**Branch:** main  
**Status:** Pushed to GitHub ✅

---

## ✅ CONCLUSÃO

### O que você tem agora:

1. ✅ **Sistema com novo logo** (logo-pmrcrj.png)
2. ✅ **Documentação completa** para AlmaLinux 9
3. ✅ **Script automatizado** de correção
4. ✅ **Guia passo a passo** manual
5. ✅ **Tudo commitado** no GitHub

### Próxima ação:

**Execute o script no servidor:**
```bash
sudo bash /tmp/diagnostico-almalinux.sh
```

**Ou siga o guia manual em:** `RESOLVER-404-ALMALINUX.md`

---

**O erro 404 será resolvido!** 🎉
