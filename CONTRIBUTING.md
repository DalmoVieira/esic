# Guia de Contribuição - E-SIC

Obrigado pelo seu interesse em contribuir com o E-SIC! Este documento fornece diretrizes para contribuir com o projeto.

## 📋 Código de Conduta

Este projeto adere a um código de conduta. Ao participar, espera-se que você mantenha este código.

## 🚀 Como Contribuir

### Reportando Bugs

1. Verifique se o bug já foi reportado nas [issues](https://github.com/DalmoVieira/esic/issues)
2. Se não foi, crie uma nova issue incluindo:
   - Descrição clara do problema
   - Passos para reproduzir
   - Comportamento esperado vs atual
   - Screenshots (se aplicável)
   - Ambiente (SO, Node.js version, etc)

### Sugerindo Melhorias

1. Abra uma issue com a tag `enhancement`
2. Descreva claramente a melhoria proposta
3. Explique por que essa melhoria seria útil

### Pull Requests

1. **Fork o repositório**
   ```bash
   git clone https://github.com/SEU-USUARIO/esic.git
   cd esic
   ```

2. **Crie uma branch para sua feature**
   ```bash
   git checkout -b feature/minha-nova-feature
   ```

3. **Configure o ambiente de desenvolvimento**
   ```bash
   npm install
   cp .env.example .env
   # Configure o .env com suas credenciais
   npm run prisma:generate
   npm run prisma:migrate
   ```

4. **Faça suas alterações**
   - Mantenha o código limpo e bem documentado
   - Siga os padrões de código do projeto
   - Adicione testes se necessário
   - Mantenha os commits pequenos e descritivos

5. **Teste suas alterações**
   ```bash
   npm run dev
   # Teste manualmente as alterações
   ```

6. **Commit suas alterações**
   ```bash
   git add .
   git commit -m "feat: adiciona nova funcionalidade X"
   ```

   Use mensagens de commit semânticas:
   - `feat`: Nova funcionalidade
   - `fix`: Correção de bug
   - `docs`: Alterações na documentação
   - `style`: Formatação, ponto e vírgula, etc
   - `refactor`: Refatoração de código
   - `test`: Adição de testes
   - `chore`: Tarefas de manutenção

7. **Push para o GitHub**
   ```bash
   git push origin feature/minha-nova-feature
   ```

8. **Abra um Pull Request**
   - Acesse seu fork no GitHub
   - Clique em "New Pull Request"
   - Descreva suas alterações detalhadamente
   - Referencie issues relacionadas (ex: "Closes #123")

## 📝 Padrões de Código

### JavaScript/Node.js

- Use ES6+ quando possível
- Use `const` e `let` ao invés de `var`
- Use arrow functions para callbacks
- Use async/await ao invés de callbacks
- Adicione JSDoc para funções complexas
- Mantenha funções pequenas e focadas

Exemplo:
```javascript
/**
 * Generates a unique protocol for information requests
 * @returns {string} Protocol in format ESIC-YYYY-NNNNNN
 */
const generateProtocol = () => {
  const year = new Date().getFullYear();
  const random = Math.floor(Math.random() * 1000000)
    .toString()
    .padStart(6, '0');
  return `ESIC-${year}-${random}`;
};
```

### Prisma Schema

- Use nomes descritivos em inglês
- Documente relacionamentos complexos
- Use enums para valores fixos
- Adicione índices onde necessário

### API Routes

- Use verbos HTTP apropriados (GET, POST, PUT, DELETE)
- Use nomes de recursos no plural (ex: `/api/requests`)
- Use kebab-case para URLs
- Retorne status codes apropriados
- Sempre retorne JSON

### Error Handling

- Use try-catch em funções async
- Passe erros para o middleware de erro
- Retorne mensagens de erro claras
- Não exponha detalhes internos em produção

```javascript
try {
  // código
} catch (error) {
  next(error); // Passa para error middleware
}
```

## 🧪 Testes

Atualmente não temos cobertura de testes completa. Se você quiser contribuir com testes:

1. Use Jest como framework de testes
2. Crie testes unitários para funções utilitárias
3. Crie testes de integração para APIs
4. Mantenha cobertura acima de 80%

Estrutura sugerida:
```
tests/
├── unit/
│   ├── utils/
│   └── middleware/
└── integration/
    └── api/
```

## 📚 Documentação

- Mantenha o README.md atualizado
- Atualize DOCUMENTATION.md com novas features
- Adicione exemplos de código
- Documente breaking changes
- Traduza para português quando possível

## 🔍 Code Review

Seu PR será revisado considerando:

- **Funcionalidade**: O código faz o que deveria?
- **Qualidade**: O código está limpo e bem organizado?
- **Segurança**: Há vulnerabilidades de segurança?
- **Performance**: O código é eficiente?
- **Testes**: As alterações estão testadas?
- **Documentação**: As alterações estão documentadas?

## 🎯 Prioridades de Desenvolvimento

Áreas que precisam de contribuições:

### Alta Prioridade
- [ ] Sistema de notificações por email
- [ ] Testes automatizados
- [ ] Dashboard administrativo
- [ ] Relatórios e estatísticas

### Média Prioridade
- [ ] Busca avançada
- [ ] Exportação de dados (PDF, Excel)
- [ ] API de webhooks
- [ ] Documentação Swagger/OpenAPI

### Baixa Prioridade
- [ ] Internacionalização (i18n)
- [ ] Mobile app
- [ ] Integração com sistemas externos
- [ ] Logs estruturados

## 📞 Comunicação

- Use issues para discussões técnicas
- Seja respeitoso e construtivo
- Mantenha discussões focadas no tópico
- Evite duplicar issues

## 🏆 Reconhecimento

Contribuidores serão listados no README.md e terão crédito nos commits.

## 📄 Licença

Ao contribuir, você concorda que suas contribuições serão licenciadas sob a mesma licença MIT do projeto.

## 💡 Dúvidas?

Se você tiver dúvidas, pode:
- Abrir uma issue com a tag `question`
- Entrar em contato através do email do projeto
- Consultar a documentação existente

Obrigado por contribuir para tornar o E-SIC melhor! 🎉
