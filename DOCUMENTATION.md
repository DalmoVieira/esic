# 🏛️ E-SIC - Sistema Eletrônico do Serviço de Informação ao Cidadão

## 📋 Visão Geral

O E-SIC é um sistema completo para implementação da **Lei 12.527/2011 (Lei de Acesso à Informação - LAI)** no Brasil. O sistema permite que cidadãos solicitem informações públicas e que órgãos governamentais gerenciem essas solicitações de forma transparente e eficiente.

## 🎯 Funcionalidades Principais

### Para Cidadãos
- ✅ Criar solicitações de informação pública
- ✅ Acompanhar status das solicitações em tempo real
- ✅ Consultar por protocolo único
- ✅ Receber respostas com documentos anexados
- ✅ Criar recursos (appeals) em caso de negativa ou resposta insatisfatória
- ✅ Opção de solicitação anônima

### Para Órgãos Públicos
- ✅ Gerenciar solicitações recebidas
- ✅ Atribuir responsáveis (agentes)
- ✅ Responder solicitações dentro do prazo legal (20 dias)
- ✅ Anexar documentos às respostas
- ✅ Gerenciar recursos dos cidadãos
- ✅ Controle de prazos e alertas

### Para Administradores
- ✅ Gerenciar usuários e permissões
- ✅ Criar e gerenciar unidades/órgãos
- ✅ Visão completa de todas as solicitações
- ✅ Relatórios e estatísticas

## 🛠️ Tecnologias Utilizadas

- **Backend**: Node.js + Express
- **Banco de Dados**: PostgreSQL
- **ORM**: Prisma
- **Autenticação**: JWT (JSON Web Tokens)
- **Segurança**: bcryptjs para hash de senhas
- **Upload de Arquivos**: Multer
- **Validação**: express-validator

## 📦 Instalação

### Pré-requisitos
- Node.js 18+ 
- PostgreSQL 14+
- npm ou yarn

### Passo a Passo

1. **Clone o repositório**
```bash
git clone https://github.com/DalmoVieira/esic.git
cd esic
```

2. **Instale as dependências**
```bash
npm install
```

3. **Configure as variáveis de ambiente**
```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:
```env
DATABASE_URL="postgresql://user:password@localhost:5432/esic_db?schema=public"
JWT_SECRET="sua-chave-secreta-aqui"
JWT_EXPIRES_IN="7d"
PORT=3001
NODE_ENV=development
```

4. **Configure o banco de dados**
```bash
# Gerar o Prisma Client
npm run prisma:generate

# Criar as tabelas no banco
npm run prisma:migrate
```

5. **Inicie o servidor**
```bash
# Modo desenvolvimento
npm run dev

# Modo produção
npm start
```

O servidor estará disponível em `http://localhost:3001`

## 📡 API Documentation

### Autenticação

#### Registrar Usuário
```http
POST /api/auth/register
Content-Type: application/json

{
  "email": "cidadao@email.com",
  "password": "Senha123",
  "name": "João Silva",
  "cpf": "12345678900",
  "phone": "11999999999"
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "cidadao@email.com",
  "password": "Senha123"
}
```

**Resposta:**
```json
{
  "message": "Login realizado com sucesso",
  "user": {
    "id": "uuid",
    "email": "cidadao@email.com",
    "name": "João Silva",
    "role": "CITIZEN"
  },
  "token": "jwt-token-here"
}
```

#### Obter Perfil
```http
GET /api/auth/profile
Authorization: Bearer {token}
```

### Solicitações

#### Criar Solicitação
```http
POST /api/requests
Authorization: Bearer {token}
Content-Type: application/json

{
  "subject": "Informações sobre licitação",
  "description": "Gostaria de obter informações detalhadas sobre a licitação XYZ",
  "unitId": "uuid-da-unidade",
  "anonymous": false
}
```

**Resposta:**
```json
{
  "message": "Solicitação criada com sucesso",
  "request": {
    "id": "uuid",
    "protocol": "ESIC-2025-123456",
    "subject": "Informações sobre licitação",
    "status": "PENDING",
    "deadlineAt": "2025-01-30T00:00:00.000Z",
    "createdAt": "2025-01-10T00:00:00.000Z"
  }
}
```

#### Consultar por Protocolo
```http
GET /api/requests/protocol/ESIC-2025-123456
Authorization: Bearer {token}
```

#### Listar Minhas Solicitações
```http
GET /api/requests/my-requests?page=1&limit=10&status=PENDING
Authorization: Bearer {token}
```

#### Adicionar Resposta (Staff)
```http
POST /api/requests/protocol/ESIC-2025-123456/response
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Informamos que os documentos solicitados estão em anexo...",
  "partial": false
}
```

### Unidades

#### Listar Unidades
```http
GET /api/units?active=true
```

#### Criar Unidade (Admin)
```http
POST /api/units
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Secretaria de Educação",
  "description": "Responsável pela gestão educacional",
  "email": "educacao@cidade.gov.br",
  "phone": "1133334444",
  "address": "Rua Principal, 100"
}
```

### Recursos (Appeals)

#### Criar Recurso
```http
POST /api/appeals/request/ESIC-2025-123456
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "A resposta fornecida não está completa..."
}
```

#### Atualizar Status do Recurso (Staff)
```http
PUT /api/appeals/{appeal-id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "ACCEPTED",
  "decision": "Recurso aceito. Nova resposta será fornecida."
}
```

### Usuários (Admin/Manager)

#### Listar Usuários
```http
GET /api/users?role=AGENT&page=1&limit=10
Authorization: Bearer {token}
```

#### Criar Usuário
```http
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "email": "agente@cidade.gov.br",
  "password": "Senha123",
  "name": "Maria Santos",
  "role": "AGENT",
  "unitId": "uuid-da-unidade"
}
```

## 🔐 Roles e Permissões

### CITIZEN (Cidadão)
- Criar solicitações
- Ver suas próprias solicitações
- Criar recursos
- Atualizar perfil

### AGENT (Agente)
- Todas as permissões de CITIZEN
- Ver solicitações da sua unidade
- Responder solicitações
- Atualizar status de solicitações
- Gerenciar recursos

### MANAGER (Gestor)
- Todas as permissões de AGENT
- Criar usuários (AGENT)
- Gerenciar usuários da unidade

### ADMIN (Administrador)
- Todas as permissões do sistema
- Criar e gerenciar unidades
- Criar usuários com qualquer role
- Ver todas as solicitações

## 📊 Status das Solicitações

- **PENDING**: Aguardando atendimento
- **IN_PROGRESS**: Em andamento (resposta parcial)
- **ANSWERED**: Respondida
- **DENIED**: Negada
- **APPEALED**: Com recurso
- **CLOSED**: Finalizada

## 🕒 Prazos Legais (Lei 12.527/2011)

- **Resposta inicial**: 20 dias (prorrogáveis por mais 10)
- **Recurso**: Até 10 dias após negativa
- **Análise de recurso**: 5 dias

## 🗄️ Modelo de Dados

### User (Usuário)
- Email, senha (hash), nome, CPF, telefone
- Role (CITIZEN, AGENT, MANAGER, ADMIN)
- Unidade vinculada (para staff)

### Unit (Unidade/Órgão)
- Nome, descrição, email, telefone, endereço
- Status (ativo/inativo)

### Request (Solicitação)
- Protocolo único
- Assunto, descrição
- Status, prazo
- Cidadão, unidade
- Opção de anonimato

### Response (Resposta)
- Conteúdo da resposta
- Autor (agente/gestor)
- Flag de resposta parcial
- Documentos anexados

### Appeal (Recurso)
- Motivo do recurso
- Status, decisão
- Documentos anexados

### Document (Documento)
- Arquivo anexado
- Metadados (nome, tipo, tamanho)

### Timeline (Linha do Tempo)
- Histórico de ações
- Rastreamento completo

## 🚀 Deploy

### Usando Docker (recomendado)

```dockerfile
# Dockerfile exemplo
FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production

COPY . .

RUN npx prisma generate

EXPOSE 3001

CMD ["npm", "start"]
```

### Variáveis de Ambiente para Produção

```env
NODE_ENV=production
DATABASE_URL=postgresql://user:pass@host:5432/db
JWT_SECRET=strong-secret-key
PORT=3001
```

## 🧪 Testes

```bash
# Executar testes (quando implementados)
npm test

# Verificar cobertura
npm run test:coverage
```

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📞 Suporte

Para questões e suporte:
- Abra uma [issue no GitHub](https://github.com/DalmoVieira/esic/issues)
- Email: [seu-email@exemplo.com]

## 📚 Referências

- [Lei 12.527/2011 (LAI)](http://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm)
- [Decreto 7.724/2012](http://www.planalto.gov.br/ccivil_03/_ato2011-2014/2012/decreto/d7724.htm)
- [Portal da Transparência](http://www.portaltransparencia.gov.br/)

## 🎯 Roadmap

- [ ] Implementar notificações por email
- [ ] Dashboard com estatísticas
- [ ] Relatórios exportáveis (PDF, Excel)
- [ ] API de webhooks
- [ ] Integração com e-mail institucional
- [ ] Sistema de busca avançada
- [ ] Mobile app (React Native)
- [ ] Testes automatizados completos
- [ ] CI/CD pipeline
- [ ] Documentação interativa (Swagger)

---

**Desenvolvido com ❤️ para a transparência pública no Brasil**
