# 🚀 CHECKLIST RÁPIDO DE DEPLOY - E-SIC

## PRÉ-DEPLOY (No seu computador)

### 1. Preparar Arquivos
```powershell
# Criar arquivo .gitignore se não existir
cd C:\xampp\htdocs\esic
echo "uploads/*" >> .gitignore
echo "!uploads/.htaccess" >> .gitignore
echo "logs/*" >> .gitignore

# Commit final
git add .
git commit -m "Preparação para deploy em produção"
git push origin main
```

### 2. Testar Localmente
- [ ] Todos os pedidos funcionam
- [ ] Upload de anexos OK
- [ ] Emails sendo enviados (teste)
- [ ] Recursos funcionando
- [ ] Painel admin acessível

---

## DEPLOY NO SERVIDOR

### Opção A: Deploy Automático (Recomendado)
```bash
# 1. Conectar no servidor
ssh usuario@servidor.com.br

# 2. Baixar e executar script
wget https://github.com/DalmoVieira/esic/raw/main/deploy.sh
chmod +x deploy.sh
sudo ./deploy.sh

# 3. Seguir instruções na tela
```

### Opção B: Deploy Manual (Passo a Passo)

#### 1️⃣ Preparar Servidor
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar pacotes
sudo apt install apache2 mysql-server php php-mysql php-mbstring \
    php-json php-curl php-gd php-zip php-xml php-fileinfo git -y
```

#### 2️⃣ Clonar Repositório
```bash
sudo mkdir -p /var/www/esic
cd /var/www/esic
sudo git clone https://github.com/DalmoVieira/esic.git .
```

#### 3️⃣ Configurar Banco
```bash
sudo mysql -u root -p

# Dentro do MySQL:
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'esic_user'@'localhost' IDENTIFIED BY 'SuaSenhaForte123!';
GRANT ALL PRIVILEGES ON esic_db.* TO 'esic_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Importar schema
sudo mysql -u esic_user -p esic_db < database/schema_novo.sql
```

#### 4️⃣ Configurar Permissões
```bash
cd /var/www/esic
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
sudo mkdir -p uploads logs
sudo chmod 775 uploads logs
sudo chown -R www-data:www-data uploads logs
```

#### 5️⃣ Configurar Apache
```bash
sudo nano /etc/apache2/sites-available/esic.conf
```

Copiar conteúdo do arquivo de configuração (ver DEPLOY_PRODUCAO.md)

```bash
sudo a2enmod rewrite ssl headers
sudo a2ensite esic.conf
sudo systemctl restart apache2
```

#### 6️⃣ Configurar SSL
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d esic.rioclaro.sp.gov.br
```

#### 7️⃣ Configurar Cron
```bash
sudo crontab -e

# Adicionar:
0 8 * * * php /var/www/esic/cron/notificacoes.php >> /var/log/esic-cron.log 2>&1
```

---

## PÓS-DEPLOY

### Testes Obrigatórios

#### 1. Testar Acesso Básico
```bash
# Verificar se site carrega
curl -I https://esic.rioclaro.sp.gov.br

# Deve retornar: HTTP/2 200
```

#### 2. Testar Banco de Dados
- [ ] Acessar: https://esic.rioclaro.sp.gov.br/dashboard.php?tipo=cidadao
- [ ] Página carrega sem erro
- [ ] Estatísticas aparecem

#### 3. Testar Upload
- [ ] Criar novo pedido
- [ ] Fazer upload de arquivo PDF
- [ ] Verificar se arquivo foi salvo

#### 4. Testar Email
- [ ] Acessar painel admin
- [ ] Ir em Configurações > Testar Email
- [ ] Enviar email de teste
- [ ] Verificar recebimento

#### 5. Testar HTTPS
```bash
# SSL Labs Test
# Acessar: https://www.ssllabs.com/ssltest/
# Inserir: esic.rioclaro.sp.gov.br
# Nota mínima esperada: A
```

---

## CONFIGURAÇÕES INICIAIS

### 1. Atualizar Configurações no Banco
```sql
USE esic_db;

UPDATE configuracoes SET valor = 'https://esic.rioclaro.sp.gov.br' 
WHERE chave = 'base_url';

UPDATE configuracoes SET valor = 'E-SIC Rio Claro' 
WHERE chave = 'sistema_nome';

UPDATE configuracoes SET valor = 'esic@rioclaro.sp.gov.br' 
WHERE chave = 'sistema_email';
```

### 2. Configurar SMTP (via painel web)
- URL: https://esic.rioclaro.sp.gov.br/admin-configuracoes.php?tipo=administrador
- Servidor: smtp.gmail.com (ou servidor SMTP da prefeitura)
- Porta: 587
- Usuário: seuemail@gmail.com
- Senha: senha de aplicativo

### 3. Criar Usuários Administrativos
```sql
USE esic_db;

-- Criar admin principal
INSERT INTO usuarios (nome, email, cpf_cnpj, tipo_usuario, senha_hash, ativo, email_verificado) 
VALUES 
('João Silva', 'joao.silva@rioclaro.sp.gov.br', '123.456.789-00', 'administrador', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Senha padrão acima é: password
-- IMPORTANTE: Trocar no primeiro login!
```

---

## MONITORAMENTO

### Comandos Úteis

```bash
# Ver logs em tempo real
sudo tail -f /var/log/apache2/esic-error.log

# Status dos serviços
sudo systemctl status apache2
sudo systemctl status mysql

# Uso de recursos
htop

# Espaço em disco
df -h

# Conexões MySQL ativas
mysql -u root -p -e "SHOW PROCESSLIST;"

# Últimos erros PHP
sudo tail -50 /var/log/php/error.log

# Verificar cron
sudo grep CRON /var/log/syslog | tail -20
```

### Arquivos de Log Importantes
```
/var/log/apache2/esic-access.log  - Acessos HTTP
/var/log/apache2/esic-error.log   - Erros Apache
/var/log/php/error.log            - Erros PHP
/var/log/mysql/error.log          - Erros MySQL
/var/log/esic-cron.log            - Execução do cron
```

---

## BACKUP E RECUPERAÇÃO

### Backup Manual
```bash
# Criar backup completo
sudo /usr/local/bin/backup-esic.sh

# Verificar backups
ls -lh /backup/esic/
```

### Restaurar Backup
```bash
# Restaurar banco
gunzip < /backup/esic/esic_db_20251015_080000.sql.gz | mysql -u esic_user -p esic_db

# Restaurar arquivos
tar -xzf /backup/esic/esic_files_20251015_080000.tar.gz -C /
```

---

## TROUBLESHOOTING

### Problema: Site não carrega
```bash
sudo systemctl status apache2
sudo tail -50 /var/log/apache2/esic-error.log
```

### Problema: Erro 500
```bash
# Verificar logs
sudo tail -50 /var/log/apache2/esic-error.log
sudo tail -50 /var/log/php/error.log

# Verificar permissões
ls -la /var/www/esic/
```

### Problema: Banco não conecta
```bash
# Testar conexão
mysql -u esic_user -p -h localhost esic_db

# Verificar se MySQL está rodando
sudo systemctl status mysql
```

### Problema: SSL não funciona
```bash
# Renovar certificado
sudo certbot renew --force-renewal

# Verificar configuração
sudo apache2ctl -t
```

### Problema: Upload não funciona
```bash
# Verificar permissões
sudo chmod 775 /var/www/esic/uploads
sudo chown -R www-data:www-data /var/www/esic/uploads

# Verificar limites PHP
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

---

## SEGURANÇA

### Checklist de Segurança
- [ ] SSL/HTTPS ativo e válido
- [ ] Firewall configurado (portas 80, 443, 22)
- [ ] Fail2Ban instalado e ativo
- [ ] Senhas fortes no banco de dados
- [ ] .htaccess protegendo arquivos sensíveis
- [ ] display_errors = Off no php.ini
- [ ] Backups automáticos funcionando
- [ ] Logs sendo rotacionados
- [ ] Sistema atualizado (apt update)

### Atualizar Sistema
```bash
# Atualizar pacotes
sudo apt update && sudo apt upgrade -y

# Reiniciar serviços
sudo systemctl restart apache2
sudo systemctl restart mysql
```

---

## CONTATOS IMPORTANTES

**Suporte Técnico:**
- Email: ti@rioclaro.sp.gov.br
- Telefone: (19) 3522-7600

**Documentação:**
- Lei 12.527/2011: http://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm
- Manual E-SIC: /var/www/esic/README_FASE3.md
- Deploy Guide: /var/www/esic/DEPLOY_PRODUCAO.md

---

## ✅ DEPLOY COMPLETO!

Quando todos os checkboxes estiverem marcados, o sistema está pronto para uso em produção! 🎉