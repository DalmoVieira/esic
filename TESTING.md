# Guia de Testes da API E-SIC

Este guia mostra como testar a API do E-SIC usando diferentes ferramentas.

## 📋 Pré-requisitos

1. Sistema E-SIC rodando localmente (`npm run dev`)
2. Banco de dados configurado e com seed (`npm run prisma:seed`)

## 🧪 Testando com cURL

### 1. Health Check

```bash
curl http://localhost:3001/api/health
```

### 2. Registrar Novo Usuário

```bash
curl -X POST http://localhost:3001/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "teste@email.com",
    "password": "Teste123",
    "name": "Usuário Teste",
    "cpf": "12345678901",
    "phone": "11999999999"
  }'
```

### 3. Login

```bash
curl -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao.cidadao@email.com",
    "password": "Citizen123"
  }'
```

**Salve o token retornado para os próximos comandos!**

### 4. Listar Unidades

```bash
curl http://localhost:3001/api/units
```

### 5. Criar Solicitação

```bash
# Substitua YOUR_TOKEN pelo token obtido no login
# Substitua UNIT_UUID pelo ID de uma unidade

curl -X POST http://localhost:3001/api/requests \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "subject": "Informações sobre merenda escolar",
    "description": "Solicito informações sobre o cardápio e custos da merenda escolar",
    "unitId": "UNIT_UUID",
    "anonymous": false
  }'
```

### 6. Listar Minhas Solicitações

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:3001/api/requests/my-requests
```

### 7. Consultar por Protocolo

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:3001/api/requests/protocol/ESIC-2025-000001
```

## 🎯 Testando com Postman

1. Importe o arquivo `E-SIC.postman_collection.json`
2. Configure a variável `baseUrl` para `http://localhost:3001/api`
3. Faça login e copie o token
4. Configure a variável `token` com o valor retornado
5. Execute as requisições da coleção

## 🧩 Fluxo Completo de Teste

### Cenário: Cidadão fazendo uma solicitação

```bash
# 1. Registrar como cidadão
curl -X POST http://localhost:3001/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "cidadao.teste@email.com",
    "password": "Teste123",
    "name": "Cidadão de Teste"
  }'

# 2. Fazer login
TOKEN=$(curl -s -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "cidadao.teste@email.com",
    "password": "Teste123"
  }' | jq -r '.token')

echo "Token: $TOKEN"

# 3. Listar unidades disponíveis
UNIT_ID=$(curl -s http://localhost:3001/api/units | jq -r '.units[0].id')

echo "Unit ID: $UNIT_ID"

# 4. Criar solicitação
PROTOCOL=$(curl -s -X POST http://localhost:3001/api/requests \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"subject\": \"Teste de solicitação\",
    \"description\": \"Esta é uma solicitação de teste\",
    \"unitId\": \"$UNIT_ID\",
    \"anonymous\": false
  }" | jq -r '.request.protocol')

echo "Protocol: $PROTOCOL"

# 5. Consultar solicitação
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:3001/api/requests/protocol/$PROTOCOL | jq
```

### Cenário: Agente respondendo uma solicitação

```bash
# 1. Login como agente
AGENT_TOKEN=$(curl -s -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "agente.educacao@cidade.gov.br",
    "password": "Agent123"
  }' | jq -r '.token')

# 2. Listar solicitações da unidade
curl -H "Authorization: Bearer $AGENT_TOKEN" \
  http://localhost:3001/api/requests/unit | jq

# 3. Responder solicitação
curl -X POST http://localhost:3001/api/requests/protocol/ESIC-2025-000001/response \
  -H "Authorization: Bearer $AGENT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Seguem as informações solicitadas...",
    "partial": false
  }' | jq

# 4. Atualizar status
curl -X PUT http://localhost:3001/api/requests/protocol/ESIC-2025-000001/status \
  -H "Authorization: Bearer $AGENT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "ANSWERED"
  }' | jq
```

### Cenário: Cidadão criando recurso

```bash
# Login como cidadão (use o token do cidadão anterior)

# Criar recurso
curl -X POST http://localhost:3001/api/appeals/request/ESIC-2025-000001 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "reason": "A resposta não está completa. Solicito informações adicionais."
  }' | jq
```

## 🔐 Testando Diferentes Roles

### Admin
```bash
# Login
ADMIN_TOKEN=$(curl -s -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@esic.gov.br",
    "password": "Admin123"
  }' | jq -r '.token')

# Criar nova unidade
curl -X POST http://localhost:3001/api/units \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Secretaria de Tecnologia",
    "email": "tech@cidade.gov.br",
    "phone": "1133338888"
  }' | jq

# Listar todos os usuários
curl -H "Authorization: Bearer $ADMIN_TOKEN" \
  http://localhost:3001/api/users | jq
```

### Manager
```bash
# Login
MANAGER_TOKEN=$(curl -s -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "gestor.educacao@cidade.gov.br",
    "password": "Manager123"
  }' | jq -r '.token')

# Criar novo agente
curl -X POST http://localhost:3001/api/users \
  -H "Authorization: Bearer $MANAGER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "novo.agente@cidade.gov.br",
    "password": "Agente123",
    "name": "Novo Agente",
    "role": "AGENT",
    "unitId": "UNIT_ID_HERE"
  }' | jq
```

## 📊 Status Codes Esperados

| Status | Significado | Quando |
|--------|-------------|--------|
| 200 | OK | Requisição bem sucedida |
| 201 | Created | Recurso criado com sucesso |
| 400 | Bad Request | Dados inválidos |
| 401 | Unauthorized | Não autenticado ou token inválido |
| 403 | Forbidden | Sem permissão para acessar |
| 404 | Not Found | Recurso não encontrado |
| 500 | Server Error | Erro interno do servidor |

## 🐛 Depuração

### Verificar logs do servidor
```bash
# O servidor em dev mode mostra logs detalhados
npm run dev
```

### Testar conexão com banco
```bash
npx prisma studio
# Abre interface gráfica para ver os dados
```

### Ver queries SQL (desenvolvimento)
```bash
# Configure no .env
DATABASE_URL="postgresql://user:pass@localhost:5432/db?schema=public&connection_limit=5&connect_timeout=10"

# As queries serão mostradas no console quando NODE_ENV=development
```

## 📝 Exemplos de Respostas

### Login Bem Sucedido
```json
{
  "message": "Login realizado com sucesso",
  "user": {
    "id": "uuid",
    "email": "usuario@email.com",
    "name": "Nome do Usuário",
    "role": "CITIZEN"
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Solicitação Criada
```json
{
  "message": "Solicitação criada com sucesso",
  "request": {
    "id": "uuid",
    "protocol": "ESIC-2025-123456",
    "subject": "Assunto da solicitação",
    "status": "PENDING",
    "deadlineAt": "2025-01-30T00:00:00.000Z",
    "createdAt": "2025-01-10T00:00:00.000Z"
  }
}
```

### Erro de Validação
```json
{
  "error": "Email e senha são obrigatórios"
}
```

### Erro de Autenticação
```json
{
  "error": "Token inválido ou expirado"
}
```

## 🎯 Checklist de Testes

- [ ] Registro de usuário com dados válidos
- [ ] Registro com email duplicado (deve falhar)
- [ ] Login com credenciais corretas
- [ ] Login com credenciais incorretas (deve falhar)
- [ ] Acesso a rota protegida sem token (deve falhar)
- [ ] Acesso a rota protegida com token válido
- [ ] Criar solicitação
- [ ] Listar solicitações
- [ ] Consultar solicitação por protocolo
- [ ] Responder solicitação (como agente)
- [ ] Criar recurso
- [ ] Tentar acessar recurso de outro usuário (deve falhar)
- [ ] Admin criar unidade
- [ ] Admin criar usuário
- [ ] Não-admin tentar criar unidade (deve falhar)

## 💡 Dicas

1. **Use jq** para formatar JSON: `curl ... | jq`
2. **Salve tokens** em variáveis para reutilizar
3. **Use Postman** para testes interativos
4. **Verifique logs** do servidor em tempo real
5. **Use Prisma Studio** para ver os dados no banco

## 🔗 Recursos Adicionais

- [Documentação completa da API](DOCUMENTATION.md)
- [Coleção Postman](E-SIC.postman_collection.json)
- [Lei 12.527/2011 (LAI)](http://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm)
