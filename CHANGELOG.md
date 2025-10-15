# 📝 Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## [3.0.0] - 2025-01-XX (ATUAL) ✨

### 🎉 Versão de Produção - Sistema Completo

### ✨ Adicionado
- **Sistema de Anexos Completo**
  - Upload seguro de arquivos (PDF, DOC, XLS, imagens)
  - Download com controle de acesso
  - Validação de tipo MIME e extensão
  - Limite de 10MB por arquivo
  - API REST `/api/anexos.php`
  - Interface JavaScript reutilizável

- **Sistema de Notificações por Email**
  - 6 tipos de notificações automáticas
  - Templates HTML responsivos
  - SMTP configurável via painel
  - Cron job para verificação automática
  - Alertas de prazo próximo (5 dias)
  - Notificação de prazo vencido

- **Painel de Configurações**
  - Aba de configuração SMTP
  - Aba de gerenciamento de notificações
  - Aba de instruções de cron
  - Teste de envio de email integrado

- **Documentação Completa de Deploy**
  - Guia detalhado em 12 etapas (DEPLOY_PRODUCAO.md)
  - Script automatizado de deploy (deploy.sh)
  - Checklist rápido (CHECKLIST_DEPLOY.md)
  - Menu de comandos úteis (comandos-rapidos.sh)
  - Notas de versão (RELEASE_NOTES.md)

- **Automação**
  - Script cron de notificações diárias
  - Backup automático configurado
  - Script de deploy completo
  - Menu interativo de comandos

- **Banco de Dados**
  - Campos `notificado_prazo_proximo` e `notificado_prazo_vencido`
  - Configurações de SMTP na tabela `configuracoes`
  - Índices otimizados

### 🔧 Modificado
- Interface administrativa padronizada (`.container` em todas as páginas)
- README.md atualizado com status de produção
- Schema do banco atualizado (`schema_novo.sql`)

### 🐛 Corrigido
- Inconsistência de largura entre páginas cidadão e admin
- Validação de upload de arquivos
- Tratamento de erros em APIs

### 🔒 Segurança
- Validação rigorosa de tipos de arquivo
- Proteção contra directory traversal
- .htaccess em diretório de uploads
- Prepared statements em todas as queries
- Sanitização de inputs

---

## [2.0.0] - 2024-12-XX

### 🏛️ Fase 2 - Painel Administrativo

### ✨ Adicionado
- **Painel Administrativo Completo**
  - Dashboard com 6 cards de estatísticas
  - Tabela de pedidos com filtros
  - Sistema de busca avançada
  - Modais de visualização e resposta

- **Sistema de Recursos**
  - 3 instâncias recursais (1ª, 2ª, 3ª)
  - Prazos de 10, 5 e 5 dias
  - Histórico completo de recursos
  - Painel de análise de recursos

- **Gestão de Usuários**
  - CRUD completo de usuários
  - 4 tipos de perfil (cidadão, atendente, admin, coordenador)
  - Controle de acesso por tipo

- **Gestão de Órgãos e Setores**
  - Cadastro de estrutura organizacional
  - Atribuição de responsáveis
  - Hierarquia de órgãos

- **Sistema de Tramitação**
  - Histórico completo de movimentações
  - Registro de observações
  - Logs de auditoria

- **APIs Administrativas**
  - `/api/pedidos-admin.php` - Gestão de pedidos
  - `/api/recursos.php` - Sistema de recursos
  - `/api/tramitacoes.php` - Histórico

### 🔧 Modificado
- Sistema de autenticação aprimorado
- Controle de sessões refinado
- Validações de permissões

### 📚 Documentação
- README atualizado com Fase 2

---

## [1.0.0] - 2024-11-XX

### 🎯 Fase 1 - Sistema Base

### ✨ Adicionado
- **Estrutura Inicial**
  - Banco de dados MySQL com 8 tabelas
  - Sistema de conexão PDO (Singleton)
  - Estrutura de pastas organizada

- **Sistema de Pedidos**
  - Formulário de nova solicitação
  - Validação de campos obrigatórios
  - Geração automática de protocolo
  - Cálculo de prazos (20+10 dias)

- **Sistema de Acompanhamento**
  - Busca por protocolo
  - Visualização de status
  - Histórico de movimentações
  - Timeline de eventos

- **Portal da Transparência**
  - Estatísticas públicas
  - Relatórios de pedidos
  - Dados de prazos
  - Gráficos informativos

- **Interface Web**
  - Design responsivo (Bootstrap 5.3.2)
  - Página principal moderna
  - Navegação intuitiva
  - Tema institucional azul

- **APIs REST**
  - `/api/pedidos.php` - CRUD de pedidos
  - Formato JSON
  - Validações de entrada
  - Tratamento de erros

### 📚 Documentação
- README.md inicial
- Schema SQL documentado
- Comentários no código

### 🔒 Segurança
- Password hashing (bcrypt)
- Prepared statements
- Validação de inputs
- Sanitização de outputs

---

## [0.1.0] - 2024-10-XX

### 🌱 Versão Inicial - Protótipo

### ✨ Adicionado
- Estrutura básica de diretórios
- Configuração do XAMPP
- Primeiras páginas HTML
- Estilos CSS básicos
- Testes iniciais

---

## 📋 Tipos de Mudanças

- ✨ **Adicionado** - Novas funcionalidades
- 🔧 **Modificado** - Mudanças em funcionalidades existentes
- ❌ **Removido** - Funcionalidades removidas
- 🐛 **Corrigido** - Correções de bugs
- 🔒 **Segurança** - Correções de segurança
- 📚 **Documentação** - Mudanças na documentação
- ⚡ **Performance** - Melhorias de performance
- ♻️ **Refatoração** - Refatoração de código

---

## 📊 Comparação de Versões

| Versão | Data | Status | Principais Recursos |
|--------|------|--------|-------------------|
| 3.0.0 | Jan/2025 | ✅ **ATUAL** | Anexos, Emails, Deploy |
| 2.0.0 | Dez/2024 | ✅ Concluída | Admin, Recursos, Tramitação |
| 1.0.0 | Nov/2024 | ✅ Concluída | Pedidos, Acompanhamento, Transparência |
| 0.1.0 | Out/2024 | ✅ Concluída | Protótipo inicial |

---

## 🔗 Links Úteis

- [Código Fonte](https://github.com/DalmoVieira/esic)
- [Issues](https://github.com/DalmoVieira/esic/issues)
- [Pull Requests](https://github.com/DalmoVieira/esic/pulls)
- [Releases](https://github.com/DalmoVieira/esic/releases)

---

## 📞 Contato

**Desenvolvedor:** Dalmo Vieira  
**Email:** dalmo@rioclaro.sp.gov.br  
**GitHub:** [@DalmoVieira](https://github.com/DalmoVieira)

**Órgão:** Prefeitura Municipal de Rio Claro - SP  
**Website:** https://www.rioclaro.sp.gov.br  
**Email:** esic@rioclaro.sp.gov.br  

---

**Desenvolvido com ❤️ para a transparência pública**

[3.0.0]: https://github.com/DalmoVieira/esic/releases/tag/v3.0.0
[2.0.0]: https://github.com/DalmoVieira/esic/releases/tag/v2.0.0
[1.0.0]: https://github.com/DalmoVieira/esic/releases/tag/v1.0.0
[0.1.0]: https://github.com/DalmoVieira/esic/releases/tag/v0.1.0
