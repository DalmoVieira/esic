# 🏛️ E-SIC - Sistema Eletrônico do Serviço de Informação ao Cidadão

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Node.js](https://img.shields.io/badge/Node.js-20+-green.svg)](https://nodejs.org/)
[![Express](https://img.shields.io/badge/Express-5.x-lightgrey.svg)](https://expressjs.com/)
[![Prisma](https://img.shields.io/badge/Prisma-6.x-2D3748.svg)](https://www.prisma.io/)

Sistema completo para implementação da **Lei 12.527/2011 (Lei de Acesso à Informação)** no Brasil. Permite que cidadãos solicitem informações públicas e órgãos governamentais gerenciem essas solicitações de forma transparente e eficiente.

## 🚀 Características

### Para Cidadãos 👥
- ✅ Criar solicitações de acesso à informação
- ✅ Acompanhar status em tempo real
- ✅ Consultar por protocolo único (ESIC-ANO-XXXXXX)
- ✅ Receber respostas com documentos
- ✅ Criar recursos em caso de negativa
- ✅ Opção de solicitação anônima

### Para Órgãos Públicos 🏢
- ✅ Gerenciar solicitações recebidas
- ✅ Sistema de prazos (20 dias - LAI)
- ✅ Responder com anexos
- ✅ Gestão de recursos
- ✅ Controle por unidades
- ✅ Timeline completa de ações

### Recursos Técnicos 🛠️
- ✅ API RESTful completa
- ✅ Autenticação JWT
- ✅ 4 níveis de permissão (Cidadão, Agente, Gestor, Admin)
- ✅ Banco de dados PostgreSQL
- ✅ Upload de arquivos
- ✅ Validação de CPF brasileiro
- ✅ Segurança com bcrypt

## 📦 Instalação Rápida

```bash
# Clone o repositório
git clone https://github.com/DalmoVieira/esic.git
cd esic

# Instale as dependências
npm install

# Configure o ambiente
cp .env.example .env
# Edite o .env com suas configurações

# Configure o banco de dados
npm run prisma:generate
npm run prisma:migrate

# Inicie o servidor
npm run dev
```

Acesse: `http://localhost:3001`

## 🔧 Configuração

### Variáveis de Ambiente

Crie um arquivo `.env` baseado no `.env.example`:

```env
DATABASE_URL="postgresql://user:password@localhost:5432/esic_db"
JWT_SECRET="your-secret-key"
PORT=3001
NODE_ENV=development
```

### Banco de Dados PostgreSQL

```bash
# Usando Docker
docker run --name esic-postgres \
  -e POSTGRES_PASSWORD=password \
  -e POSTGRES_DB=esic_db \
  -p 5432:5432 \
  -d postgres:14

# Ou instale localmente
# https://www.postgresql.org/download/
```

## 📖 Documentação

- **[Documentação Completa](DOCUMENTATION.md)** - Guia detalhado com exemplos de API
- **[API Reference](#api-endpoints)** - Referência rápida de endpoints
- **[Lei 12.527/2011](http://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm)** - Lei de Acesso à Informação

## 📡 API Endpoints

### Autenticação
```
POST   /api/auth/register      - Registrar usuário
POST   /api/auth/login         - Login
GET    /api/auth/profile       - Obter perfil
PUT    /api/auth/profile       - Atualizar perfil
POST   /api/auth/change-password - Alterar senha
```

### Solicitações
```
POST   /api/requests                        - Criar solicitação
GET    /api/requests/my-requests            - Listar minhas solicitações
GET    /api/requests/protocol/:protocol     - Consultar por protocolo
GET    /api/requests/unit                   - Listar da unidade (staff)
POST   /api/requests/protocol/:protocol/response  - Adicionar resposta (staff)
PUT    /api/requests/protocol/:protocol/status    - Atualizar status (staff)
```

### Unidades
```
GET    /api/units              - Listar unidades
GET    /api/units/:id          - Obter unidade
POST   /api/units              - Criar unidade (admin)
PUT    /api/units/:id          - Atualizar unidade (admin)
DELETE /api/units/:id          - Excluir unidade (admin)
```

### Recursos
```
POST   /api/appeals/request/:protocol      - Criar recurso
GET    /api/appeals/request/:protocol      - Listar recursos da solicitação
GET    /api/appeals/:id                    - Obter recurso
PUT    /api/appeals/:id/status             - Atualizar status (staff)
```

### Usuários
```
GET    /api/users              - Listar usuários (admin/manager)
GET    /api/users/:id          - Obter usuário (admin/manager)
POST   /api/users              - Criar usuário (admin/manager)
PUT    /api/users/:id          - Atualizar usuário (admin/manager)
DELETE /api/users/:id          - Excluir usuário (admin)
```

## 🎯 Exemplo de Uso

### 1. Registrar e Login

```bash
# Registrar cidadão
curl -X POST http://localhost:3001/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "cidadao@email.com",
    "password": "Senha123",
    "name": "João Silva"
  }'

# Login
curl -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "cidadao@email.com",
    "password": "Senha123"
  }'
```

### 2. Criar Solicitação

```bash
curl -X POST http://localhost:3001/api/requests \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "subject": "Informações sobre licitação",
    "description": "Solicito informações sobre a licitação XYZ",
    "unitId": "unit-uuid"
  }'
```

### 3. Consultar por Protocolo

```bash
curl -X GET http://localhost:3001/api/requests/protocol/ESIC-2025-123456 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🗄️ Estrutura do Projeto

```
esic/
├── prisma/
│   └── schema.prisma          # Schema do banco de dados
├── src/
│   ├── config/                # Configurações (DB, JWT)
│   ├── controllers/           # Lógica de negócio
│   ├── middleware/            # Middlewares (auth, upload, error)
│   ├── routes/                # Rotas da API
│   ├── utils/                 # Utilitários (validações, protocolo)
│   └── server.js              # Servidor Express
├── public/
│   └── index.html             # Interface web
├── uploads/                   # Arquivos enviados
├── .env.example               # Exemplo de variáveis
├── .gitignore
├── package.json
├── DOCUMENTATION.md           # Documentação detalhada
└── README.md
```

## 🔐 Níveis de Acesso

| Role | Descrição | Permissões |
|------|-----------|------------|
| **CITIZEN** | Cidadão comum | Criar e acompanhar solicitações |
| **AGENT** | Atendente do órgão | Responder solicitações da unidade |
| **MANAGER** | Gestor da unidade | Gerenciar equipe e solicitações |
| **ADMIN** | Administrador | Acesso total ao sistema |

## 🧪 Scripts Disponíveis

```bash
npm start              # Iniciar servidor (produção)
npm run dev            # Iniciar com nodemon (desenvolvimento)
npm run prisma:generate # Gerar Prisma Client
npm run prisma:migrate  # Executar migrations
npm run prisma:studio   # Abrir Prisma Studio (GUI)
```

## 🤝 Contribuindo

Contribuições são bem-vindas! Siga os passos:

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/NovaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona NovaFeature'`)
4. Push para a branch (`git push origin feature/NovaFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🌟 Sobre a Lei 12.527/2011

A **Lei de Acesso à Informação (LAI)** garante o direito constitucional de acesso às informações públicas. As solicitações devem ser respondidas em até **20 dias**, prorrogáveis por mais 10 dias mediante justificativa.

Principais pontos:
- Qualquer pessoa pode fazer solicitações
- Não é necessário justificar o motivo
- O serviço é gratuito
- Possibilidade de recurso em caso de negativa
- Transparência ativa e passiva

## 📞 Suporte

- 🐛 [Reportar Bug](https://github.com/DalmoVieira/esic/issues)
- 💡 [Sugerir Feature](https://github.com/DalmoVieira/esic/issues)
- 📧 Email: contato@exemplo.com

## 🙏 Agradecimentos

Projeto desenvolvido para promover a transparência e o acesso à informação pública no Brasil, em conformidade com a Lei 12.527/2011.

---

**⭐ Se este projeto foi útil, considere dar uma estrela no GitHub!**
