# Sistema de Login Oficial - E-SIC

## 📋 Estrutura do Sistema

O E-SIC v3.0.0 utiliza uma arquitetura MVC (Model-View-Controller) moderna com sistema de roteamento.

### Estrutura de Diretórios

```
esic/
├── public/                    # Ponto de entrada público
│   ├── index.php             # Front Controller (roteamento)
│   ├── .htaccess             # Configuração Apache
│   ├── css/                  # Arquivos CSS
│   └── js/                   # Arquivos JavaScript
│
├── app/                      # Código da aplicação
│   ├── controllers/          # Controllers
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── AdminController.php
│   │   └── ...
│   │
│   ├── models/               # Models
│   │   ├── Usuario.php
│   │   ├── Pedido.php
│   │   └── ...
│   │
│   ├── views/                # Views
│   │   ├── auth/             # Páginas de autenticação
│   │   │   └── login.php     # 🔐 PÁGINA DE LOGIN OFICIAL
│   │   ├── public/           # Páginas públicas
│   │   ├── admin/            # Páginas administrativas
│   │   └── layouts/          # Layouts (templates)
│   │
│   └── config/               # Configurações
│       ├── Database.php
│       ├── Auth.php
│       └── ...
│
├── index.php                 # Redireciona para public/
└── .htaccess                 # Redireciona para public/
```

## 🔐 Página de Login Oficial

### Localização
**Arquivo:** `app/views/auth/login.php`
**Rota:** `http://localhost/esic/auth/login`
**Controller:** `AuthController@login`

### Características da Página Oficial

#### 1. Design Profissional
- Layout dividido em duas colunas (Desktop)
- Formulário de login à esquerda
- Painel informativo à direita com:
  - Logo e informações do sistema
  - Estatísticas em tempo real
  - Citação da Lei de Acesso à Informação

#### 2. Funcionalidades

##### Autenticação Padrão
- Login com email e senha
- Validação em tempo real
- Checkbox "Lembrar de mim"
- Link para recuperação de senha

##### OAuth (Login Social)
- **Google**: Login com conta Google
- **Gov.br**: Login com conta Gov.br (gov.br)

##### Acesso Público
- Botão para fazer pedido sem login
- Botão para consultar pedido existente

#### 3. Segurança
- Proteção CSRF Token
- Rate limiting (limite de tentativas)
- Validação de campos
- Logs de autenticação
- Passwords hasheadas (password_hash)

#### 4. Responsividade
- Design adaptável para mobile
- Gradiente de fundo em telas pequenas
- Card flutuante em dispositivos móveis

## 🚀 Como Funciona

### 1. Fluxo de Acesso

```
1. Usuário acessa: http://localhost/esic/
   ↓
2. .htaccess redireciona para: public/
   ↓
3. public/index.php (Router) processa a URL
   ↓
4. Rota "/" redireciona para: /auth/login
   ↓
5. AuthController@login é executado
   ↓
6. View auth/login.php é renderizada
```

### 2. Fluxo de Autenticação

```
POST /auth/login
   ↓
AuthController@processLogin()
   ↓
Auth::attempt($email, $password)
   ↓
Usuario::findByEmail()
   ↓
password_verify()
   ↓
Session + Cookie (se "lembrar")
   ↓
Redirect para dashboard ou home
```

## 🎨 Personalizando a Página de Login

### Cores e Estilos

A página usa as classes Bootstrap 5.3.2 e CSS customizado:

```css
/* Gradiente do painel direito */
.col-lg-6.bg-primary {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%);
}

/* Cards de estatísticas */
.border-white:hover {
    background-color: rgba(255,255,255,0.1);
}
```

### Modificar Logo

Edite em `app/views/auth/login.php`:

```php
<div class="mb-4">
    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
</div>
```

Substitua por:

```php
<div class="mb-4">
    <img src="<?= asset('images/logo.png') ?>" alt="Logo" style="max-width: 150px;">
</div>
```

### Modificar Estatísticas

As estatísticas são passadas pelo controller:

```php
// AuthController.php - método login()
$stats = [
    'total_pedidos' => 1234,
    'tempo_medio_resposta' => 15
];

$this->render('auth/login', [
    'title' => 'Login - Sistema E-SIC',
    'stats' => $stats
]);
```

## 🔧 Configuração

### Habilitar OAuth

#### Google Login

1. Criar projeto no [Google Cloud Console](https://console.cloud.google.com/)
2. Configurar OAuth 2.0
3. Adicionar em `app/config/OAuthHandler.php`:

```php
private $googleClientId = 'seu-client-id';
private $googleClientSecret = 'seu-client-secret';
private $googleRedirectUri = 'http://localhost/esic/auth/google/callback';
```

#### Gov.br Login

1. Cadastrar aplicação no [Portal de Desenvolvedores Gov.br](https://sso.acesso.gov.br/)
2. Configurar em `app/config/OAuthHandler.php`:

```php
private $govbrClientId = 'seu-client-id';
private $govbrClientSecret = 'seu-client-secret';
private $govbrRedirectUri = 'http://localhost/esic/auth/govbr/callback';
```

### Configurar Email (Recuperação de Senha)

Em `app/config/Mail.php` ou `.env`:

```php
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=esic@rioclaro.sp.gov.br
MAIL_FROM_NAME=Sistema E-SIC
```

## 📱 Responsividade

### Desktop (> 992px)
- Layout de 2 colunas
- Painel informativo visível
- Formulário centralizado

### Tablet (768px - 991px)
- Layout de 2 colunas reduzido
- Painel informativo simplificado

### Mobile (< 768px)
- Layout de 1 coluna
- Painel informativo oculto
- Card de login com gradiente de fundo
- Formulário em card flutuante

## 🔒 Segurança

### CSRF Protection

Todos os formulários incluem token CSRF:

```php
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
```

Verificação no backend:

```php
AuthMiddleware::verifyCSRF();
```

### Rate Limiting

Proteção contra ataques de força bruta:

```php
private function checkLoginRateLimit($email) {
    $authLogModel = new AuthLog();
    return $authLogModel->checkRateLimit($email);
}
```

Limites:
- 5 tentativas por 15 minutos
- Bloqueio temporário após exceder

### Password Hashing

Senhas são hasheadas com `password_hash()`:

```php
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
```

Verificação:

```php
password_verify($inputPassword, $hashedPassword);
```

## 🐛 Troubleshooting

### Página em Branco

1. Verificar logs de erro do PHP:
   ```powershell
   Get-Content C:\xampp\apache\logs\error.log -Tail 50
   ```

2. Verificar se módulos Apache estão ativos:
   - mod_rewrite
   - mod_headers
   - mod_dir

3. Limpar cache de sessão:
   ```php
   session_start();
   session_destroy();
   ```

### Erro 404

Verificar `.htaccess` em `public/`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
Options -Indexes
```

### Erro de Conexão com Banco

Verificar `app/config/Database.php`:

```php
private $host = 'localhost';
private $user = 'root';
private $pass = '';
private $dbname = 'esic';
```

### OAuth Não Funciona

1. Verificar se URLs de callback estão corretas
2. Verificar se credenciais estão configuradas
3. Verificar se extensões PHP estão habilitadas:
   - cURL
   - OpenSSL

## 📚 Rotas Disponíveis

### Públicas
- `GET /` - Redireciona para login
- `GET /auth/login` - Página de login
- `POST /auth/login` - Processar login
- `GET /auth/logout` - Fazer logout
- `GET /auth/forgot-password` - Recuperar senha
- `GET /novo-pedido` - Fazer pedido sem login
- `GET /acompanhar` - Consultar pedido

### Administrativas (requer autenticação)
- `GET /admin/dashboard` - Dashboard
- `GET /admin/pedidos` - Gerenciar pedidos
- `GET /admin/usuarios` - Gerenciar usuários
- `GET /admin/relatorios` - Relatórios

### OAuth
- `GET /auth/google` - Iniciar login Google
- `GET /auth/google/callback` - Callback Google
- `GET /auth/govbr` - Iniciar login Gov.br
- `GET /auth/govbr/callback` - Callback Gov.br

## 🎯 Próximos Passos

1. ✅ Página de login oficial funcionando
2. ⏳ Criar usuário administrador
3. ⏳ Configurar banco de dados
4. ⏳ Testar autenticação
5. ⏳ Configurar OAuth (opcional)
6. ⏳ Deploy em produção

## 📞 Suporte

Para dúvidas ou problemas:
1. Verificar logs em `C:\xampp\apache\logs\error.log`
2. Verificar configurações em `app/config/`
3. Revisar documentação em `docs/`

---

**Versão:** 3.0.0  
**Data:** 16/10/2025  
**Autor:** Sistema E-SIC - Prefeitura de Rio Claro/SP
