# 🏛️ Sistema E-SIC - Lei de Acesso à Informação

Sistema Eletrônico do Serviço de Informação ao Cidadão (E-SIC) para implementação da Lei 12.527/2011 (LAI). Permite que cidadãos solicitem informações públicas e que órgãos públicos gerenciem essas solicitações de forma transparente.

## ✅ Status do Projeto - 100% Completo

### Backend Implementado
- ✅ MVC Architecture completa com PHP 8.0+
- ✅ Sistema de roteamento avançado
- ✅ Controllers (Admin, Auth, API, Home, Pedido, Recurso)
- ✅ Models com Active Record pattern
- ✅ Middleware de autenticação e segurança
- ✅ Sistema de JWT + Sessions
- ✅ OAuth2 (Google/Gov.br)
- ✅ Database schema com 9 tabelas
- ✅ Triggers e procedures MySQL

### Frontend Responsivo
- ✅ Templates Bootstrap 5
- ✅ Layout principal e administrativo
- ✅ Homepage com estatísticas
- ✅ Formulário multi-step para pedidos
- ✅ Sistema de consulta de protocolos
- ✅ Dashboard administrativo com charts
- ✅ Páginas de autenticação
- ✅ Tratamento de erros (404, 403, 500, etc.)

### Funcionalidades Principais
- ✅ Criação de pedidos com protocolo automático
- ✅ Sistema de recursos administrativos
- ✅ Gerenciamento de usuários e permissões
- ✅ Notificações por email
- ✅ API RESTful completa
- ✅ Relatórios e estatísticas
- ✅ Conformidade com LAI (Lei 12.527/2011)

## 🛠️ Tecnologias

- **Backend:** PHP 8.0+ (MVC Vanilla)
- **Frontend:** Bootstrap 5, HTML5, CSS3, JavaScript
- **Banco:** MySQL 5.7+
- **Segurança:** JWT, CSRF Protection, XSS Prevention
- **Autenticação:** Sessions + OAuth2

## 🚀 Instalação Rápida

1. **Clone o projeto:**
```bash
git clone https://github.com/DalmoVieira/esic.git
cd esic
```

2. **Configure o ambiente:**
```bash
cp .env.example .env
# Edite o .env com suas configurações
```

3. **Configure o banco:**
```sql
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Importe database/schema.sql
```

4. **Execute o sistema:**
```bash
cd public
php -S localhost:8000
```

5. **Acesse:** http://localhost:8000

## 📁 Estrutura

```
esic/
├── app/
│   ├── config/          # Database, Auth
│   ├── controllers/     # AdminController, AuthController, etc.
│   ├── models/         # Usuario, Pedido, Recurso, etc.
│   ├── views/          # Templates e layouts
│   ├── middleware/     # AuthMiddleware
│   └── libraries/      # Bibliotecas auxiliares
├── public/
│   ├── index.php       # Front Controller
│   ├── css/           # Estilos
│   ├── js/            # Scripts
│   └── uploads/       # Arquivos
├── database/
│   └── schema.sql     # Esquema do banco
└── README.md
```

## 🔧 Configuração

### Banco de Dados (.env)
```env
DB_HOST=localhost
DB_NAME=esic_db
DB_USER=root
DB_PASS=sua_senha
```

### Aplicação (.env)
```env
APP_URL=http://localhost:8000
APP_ENV=development
APP_DEBUG=true
JWT_SECRET=sua-chave-256-bits
```

### Email SMTP (.env)
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu@email.com
MAIL_PASSWORD=sua-senha-app
```

## 👤 Usuário Padrão

- **Email:** admin@esic.gov.br
- **Senha:** password
- **Nível:** Administrador

## 📱 Funcionalidades

### Para Cidadãos
- Solicitar informações online
- Protocolo automático (ESIC-YYYYMMDD-NNNN)
- Acompanhar pedidos
- Interpor recursos
- Interface responsiva

### Para Administradores
- Dashboard completo
- Gerenciar pedidos/recursos
- Prazos automáticos (20+10 dias)
- Relatórios e estatísticas
- Controle de usuários
- Logs de auditoria

## 🛡️ Segurança

- Autenticação JWT + Sessions
- Proteção CSRF
- Prevenção SQL Injection/XSS
- Rate limiting
- Logs de segurança
- Conformidade LGPD

## 📊 API REST

### Públicas
```
GET /api/pedidos/stats     # Estatísticas
GET /api/pedido/{protocolo} # Buscar pedido
POST /api/pedido           # Criar pedido
```

### Administrativas (Auth)
```
GET /api/admin/pedidos     # Listar
PUT /api/admin/pedido/{id} # Atualizar
GET /api/admin/stats       # Dashboard
```

## ⚖️ Conformidade Legal

- ✅ Lei 12.527/2011 (LAI)
- ✅ Prazos legais automáticos
- ✅ Transparência ativa
- ✅ LGPD compliance
- ✅ Recursos hierárquicos

## 📈 Métricas

- Dashboard em tempo real
- Estatísticas públicas
- Relatórios personalizados
- Tempo médio de resposta
- Taxa de recursos

## 🏗️ Roadmap

### v1.1
- [ ] Docker containers
- [ ] Cache Redis
- [ ] Queue system
- [ ] Mobile app

### v1.2
- [ ] IA classification
- [ ] Multi-idiomas
- [ ] Chat em tempo real
- [ ] ElasticSearch

## 🤝 Contribuição

1. Fork o projeto
2. Crie branch: `git checkout -b feature/nova-feature`
3. Commit: `git commit -m 'Add nova feature'`
4. Push: `git push origin feature/nova-feature`
5. Pull Request

## 📄 Licença

MIT License - veja [LICENSE](LICENSE)

## 📞 Suporte

- **Issues:** [GitHub Issues](https://github.com/DalmoVieira/esic/issues)
- **Email:** suporte@sistema-esic.dev
- **Documentação:** [LAI Official](http://www.acessoainformacao.gov.br/)

---

**Sistema 100% funcional e pronto para produção** 🚀

**Desenvolvido para promover transparência pública** ❤️