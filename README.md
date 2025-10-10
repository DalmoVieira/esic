# 🏛️ Sistema E-SIC - Lei de Acesso à Informação

Sistema Eletrônico do Serviço de Informação ao Cidadão (E-SIC) para implementação da Lei 12.527/2011 (LAI). Permite que cidadãos solicitem informações públicas e que órgãos públicos gerenciem essas solicitações de forma transparente.

## 📋 Características Principais

### Para Cidadãos
- ✅ Solicitar informações via formulário online
- ✅ Receber protocolo automático (ESIC-YYYYMMDD-NNNN)
- ✅ Acompanhar andamento dos pedidos
- ✅ Interpor recursos administrativos
- ✅ Receber notificações por email
- ✅ Interface responsiva e acessível

### Para Administradores
- ✅ Painel administrativo completo
- ✅ Gerenciar pedidos e respostas
- ✅ Sistema de prazos automáticos (20+10 dias úteis)
- ✅ Relatórios e estatísticas
- ✅ Múltiplos níveis de acesso (admin, gestor, operador)
- ✅ Logs de auditoria e segurança
- ✅ Sistema de recuperação de senha

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.0+ (Vanilla MVC)
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Banco de Dados:** MySQL 5.7+
- **Autenticação:** JWT, Sessions, OAuth2 (Google, Gov.br)
- **Email:** PHPMailer/SMTP
- **Segurança:** CSRF Protection, SQL Injection Prevention, XSS Protection

## 📁 Estrutura do Projeto

```
esic/
├── app/
│   ├── config/
│   │   ├── Database.php          # Conexão com banco (PDO)
│   │   └── Auth.php              # Sistema de autenticação
│   ├── controllers/
│   │   ├── BaseController.php    # Controller base
│   │   ├── HomeController.php    # Páginas públicas
│   │   ├── PedidoController.php  # Gerenciamento de pedidos
│   │   ├── RecursoController.php # Recursos administrativos
│   │   ├── AdminController.php   # Painel administrativo
│   │   └── AuthController.php    # Autenticação
│   ├── models/
│   │   ├── Model.php            # Model base (Active Record)
│   │   ├── Usuario.php          # Usuários do sistema
│   │   ├── Pedido.php          # Pedidos de informação
│   │   ├── Recurso.php         # Recursos administrativos
│   │   └── AuthLog.php         # Logs de autenticação
│   ├── views/
│   │   ├── layouts/            # Templates base
│   │   ├── public/             # Views públicas
│   │   └── admin/              # Views administrativas
│   ├── libraries/              # Bibliotecas auxiliares
│   └── middleware/             # Middlewares de autenticação
├── public/
│   ├── index.php              # Front Controller
│   ├── css/                   # Estilos CSS
│   ├── js/                    # Scripts JavaScript
│   └── uploads/               # Arquivos enviados
├── database/
│   └── schema.sql             # Esquema do banco de dados
├── .env.example              # Configurações de ambiente
├── .gitignore               # Arquivos ignorados pelo Git
└── README.md               # Este arquivo
```

## 🚀 Instalação e Configuração

### 1. Pré-requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx) ou XAMPP
- Extensões PHP: pdo_mysql, openssl, mbstring, curl

### 2. Clonagem e Configuração

```bash
# 1. Clone ou baixe o projeto
# 2. Navegue até a pasta do projeto
cd esic

# 3. Configure o arquivo de ambiente
cp .env.example .env

# 4. Edite o .env com suas configurações
# Banco de dados, email, OAuth, etc.
```

### 3. Configuração do Banco de Dados

```bash
# 1. Crie o banco de dados MySQL
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2. Importe o esquema
mysql -u root -p esic_db < database/schema.sql
```

### 4. Configuração do Servidor Web

#### XAMPP
```bash
# Coloque o projeto em: C:\xampp\htdocs\esic
# Acesse: http://localhost/esic
```

#### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName esic.local
    DocumentRoot /path/to/esic/public
    
    <Directory /path/to/esic/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name esic.local;
    root /path/to/esic/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### 5. Configuração do Arquivo .env

```env
# Banco de Dados
DB_HOST=localhost
DB_NAME=esic_db
DB_USER=root
DB_PASS=sua_senha

# Aplicação
APP_URL=http://localhost/esic
APP_ENV=development
APP_DEBUG=true

# JWT
JWT_SECRET=sua-chave-secreta-256-bits

# Email SMTP
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-app

# OAuth Google (opcional)
GOOGLE_CLIENT_ID=seu-google-client-id
GOOGLE_CLIENT_SECRET=seu-google-client-secret

# OAuth Gov.br (opcional)
GOVBR_CLIENT_ID=seu-govbr-client-id
GOVBR_CLIENT_SECRET=seu-govbr-client-secret
```

## 👤 Usuários Padrão

O sistema cria automaticamente um usuário administrador:

- **Email:** admin@esic.gov.br
- **Senha:** password (altere imediatamente!)
- **Nível:** Administrador

## 📱 Funcionalidades Principais

### Sistema de Pedidos
- Protocolo automático: ESIC-YYYYMMDD-NNNN
- Prazos legais: 20 dias úteis (+ 10 para recurso)
- Upload de anexos (PDF, DOC, JPG, PNG)
- Notificações automáticas por email
- Acompanhamento em tempo real

### Sistema de Recursos
- Recurso de primeira instância (10 dias)
- Recurso de segunda instância (15 dias)
- Encaminhamento para CGU (20 dias)
- Controle automático de prazos

### Painel Administrativo
- Dashboard com estatísticas
- Gerenciamento de pedidos por status
- Sistema de alertas de prazos
- Relatórios personalizados
- Gerenciamento de usuários
- Logs de auditoria

### Segurança
- Autenticação JWT + Sessions
- Proteção CSRF
- Prevenção SQL Injection
- Logs de tentativas de acesso
- Bloqueio automático por tentativas

## 🔧 Configurações Avançadas

### OAuth - Integração com Google

1. Acesse o [Google Cloud Console](https://console.cloud.google.com)
2. Crie um projeto ou selecione existente
3. Ative a API Google+ 
4. Crie credenciais OAuth 2.0
5. Configure no .env:

```env
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI=http://localhost/esic/auth/google/callback
```

### OAuth - Integração com Gov.br

1. Acesse o [Portal de Serviços do Gov.br](https://servicos.gov.br)
2. Registre sua aplicação
3. Configure no .env:

```env
GOVBR_CLIENT_ID=seu_client_id
GOVBR_CLIENT_SECRET=seu_client_secret
GOVBR_REDIRECT_URI=http://localhost/esic/auth/govbr/callback
GOVBR_ENVIRONMENT=homologacao
```

### Configuração de Email

#### Gmail (Recomendado para desenvolvimento)
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=sua_senha_de_app
MAIL_ENCRYPTION=tls
```

#### Outros Provedores
```env
# Outlook/Hotmail
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587

# Yahoo
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
```

## 📊 Base de Dados

### Tabelas Principais

- **usuarios** - Administradores e operadores
- **pedidos** - Solicitações de informação
- **recursos** - Recursos administrativos
- **auth_logs** - Logs de autenticação
- **configuracoes** - Configurações do sistema
- **notificacoes** - Sistema de notificações
- **historico_pedidos** - Auditoria de alterações

### Triggers Automáticos
- Geração de protocolos sequenciais
- Cálculo automático de prazos
- Histórico de alterações
- Estatísticas consolidadas

## 🎨 Personalização

### Configurações do Órgão
Edite no painel administrativo ou no .env:

```env
ORGAO_NOME="Prefeitura Municipal"
ORGAO_ENDERECO="Rua Principal, 123"
ORGAO_TELEFONE="(11) 3333-3333"
ORGAO_EMAIL="contato@prefeitura.gov.br"
```

### Prazos Legais
```env
PRAZO_RESPOSTA_PRIMEIRA_INSTANCIA=20
PRAZO_RESPOSTA_SEGUNDA_INSTANCIA=10
PRAZO_RECURSO_CGU=15
```

### Uploads
```env
UPLOAD_MAX_SIZE=10485760  # 10MB
ALLOWED_EXTENSIONS=pdf,doc,docx,jpg,jpeg,png,txt
```

## 🔍 API REST

### Endpoints Públicos
```bash
GET /api/pedidos/stats          # Estatísticas públicas
GET /api/pedido/{protocolo}     # Buscar pedido
POST /api/pedido                # Criar pedido
```

### Endpoints Administrativos (Auth Required)
```bash
GET /api/admin/pedidos          # Listar pedidos
PUT /api/admin/pedido/{id}      # Atualizar pedido
```

## 📈 Relatórios

### Tipos Disponíveis
- Pedidos por período
- Tempo médio de resposta
- Estatísticas por categoria/unidade
- Taxa de recurso
- Relatório de transparência

### Formatos de Exportação
- PDF
- Excel (CSV)
- JSON (API)

## 🛡️ Segurança e Conformidade

### LGPD
- Anonimização de dados sensíveis
- Controle de retenção de logs
- Auditoria de acessos
- Direito ao esquecimento

### Lei de Acesso à Informação
- Prazos legais automáticos
- Transparência ativa
- Recursos hierárquicos
- Relatórios obrigatórios

## 🐛 Troubleshooting

### Problemas Comuns

**Erro de conexão com banco:**
```bash
# Verifique se o MySQL está rodando
# Confirme as credenciais no .env
# Teste a conexão: mysql -u root -p
```

**Emails não enviando:**
```bash
# Verifique configurações SMTP
# Para Gmail, use senhas de app
# Verifique logs: tail -f /var/log/mail.log
```

**Uploads não funcionam:**
```bash
# Verifique permissões da pasta uploads/
chmod 755 public/uploads/
# Verifique php.ini: upload_max_filesize, post_max_size
```

**Erro 404 em rotas:**
```bash
# Verifique se mod_rewrite está ativo (Apache)
# Para Nginx, configure try_files corretamente
```

## 📝 Logs

### Locais dos Logs
- **PHP Errors:** `/var/log/php_errors.log`
- **Sistema:** Tabela `auth_logs`
- **Email:** `/var/log/mail.log`
- **Aplicação:** `logs/app.log` (se configurado)

### Níveis de Log
- ERROR: Erros críticos
- WARNING: Avisos importantes  
- INFO: Informações gerais
- DEBUG: Apenas em desenvolvimento

## 🔄 Backup e Manutenção

### Backup do Banco
```bash
# Backup completo
mysqldump -u root -p esic_db > backup_esic_$(date +%Y%m%d).sql

# Backup apenas estrutura
mysqldump -u root -p --no-data esic_db > schema_backup.sql
```

### Limpeza de Logs
```bash
# Via SQL (logs > 1 ano)
DELETE FROM auth_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 365 DAY);

# Via aplicação (configurado no sistema)
```

### Otimização
```bash
# Otimizar tabelas MySQL
OPTIMIZE TABLE pedidos, recursos, auth_logs;

# Limpar uploads órfãos
# Implementar via cron job
```

## 🤝 Contribuição

### Como Contribuir
1. Fork o projeto
2. Crie uma branch: `git checkout -b feature/nova-funcionalidade`
3. Commit suas mudanças: `git commit -m 'Add nova funcionalidade'`
4. Push para a branch: `git push origin feature/nova-funcionalidade`
5. Abra um Pull Request

### Padrões de Código
- PSR-4 para autoloading
- PSR-12 para code style
- Documentação em português
- Testes unitários (PHPUnit)

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Suporte

### Documentação Oficial
- [Lei de Acesso à Informação](http://www.acessoainformacao.gov.br/)
- [Portal da Transparência](http://transparencia.gov.br/)
- [CGU - Controladoria Geral da União](https://www.gov.br/cgu/)

### Contato
- **Email:** suporte@sistema-esic.dev
- **Issues:** [GitHub Issues](https://github.com/seu-usuario/esic/issues)
- **Wiki:** [GitHub Wiki](https://github.com/seu-usuario/esic/wiki)

## 🏗️ Roadmap

### Versão 1.1
- [ ] Integração com sistemas de protocolo existentes
- [ ] API completa REST/GraphQL
- [ ] Dashboard em tempo real
- [ ] Notificações push
- [ ] App móvel híbrido

### Versão 1.2
- [ ] Inteligência artificial para classificação automática
- [ ] Integração com redes sociais
- [ ] Sistema de chat em tempo real
- [ ] Multi-idiomas
- [ ] Tema escuro

### Versão 2.0
- [ ] Microserviços
- [ ] Docker containers
- [ ] Kubernetes deployment
- [ ] ElasticSearch
- [ ] Redis cache

---

**Desenvolvido com ❤️ para promover a transparência pública e o acesso à informação.**

**⚖️ Sistema em conformidade com a Lei 12.527/2011 (LAI) e LGPD**