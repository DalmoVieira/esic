# 📋 E-SIC - Sistema Eletrônico de Informações ao Cidadão

![E-SIC Logo](https://img.shields.io/badge/E--SIC-Sistema%20LAI-blue?style=for-the-badge&logo=government)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.2-7952B3?style=flat&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat)](LICENSE)

Sistema web completo para gerenciamento de solicitações de acesso à informação pública, desenvolvido em conformidade com a **Lei de Acesso à Informação (LAI - Lei nº 12.527/2011)**.

## 🎯 Funcionalidades Principais

### 👥 Para Cidadãos
- 📝 **Nova Solicitação** - Formulário completo para pedidos de informação
- � **Upload de Anexos** - Envio seguro de documentos (PDF, DOC, XLS, imagens)
- �🔍 **Acompanhamento** - Consulta de status via protocolo
- � **Notificações por Email** - Atualizações automáticas sobre o pedido
- 🔄 **Sistema de Recursos** - 3 instâncias recursais (conforme LAI)
- �👁️ **Portal da Transparência** - Acesso direto a informações públicas
- 📊 **Estatísticas** - Dados sobre solicitações e prazos

### 🏛️ Para Administradores
- 📋 **Gestão de Pedidos** - Controle completo do fluxo de solicitações
- ✅ **Responder Solicitações** - Interface completa com editor de texto
- ⏰ **Controle de Prazos** - Monitoramento automático (20+10 dias)
- 📧 **Sistema de Emails** - SMTP configurável + 6 tipos de notificações
- 🔔 **Alertas Automáticos** - Prazo próximo do vencimento (5 dias)
- 📎 **Gestão de Anexos** - Download/upload de documentos
- 👤 **Gerenciamento de Usuários** - Controle de acesso e permissões
- 🏢 **Cadastro de Órgãos** - Estrutura organizacional completa
- 📈 **Relatórios** - Análises e métricas detalhadas
- 🔍 **Logs de Auditoria** - Rastreamento completo de ações
- ⚙️ **Configurações SMTP** - Painel para email, cron e notificações

## 🚀 Demonstração

### 🖥️ **Ambiente Local (Desenvolvimento)**
Acesse: **[http://localhost/esic/](http://localhost/esic/)**

### 🌐 **Deploy em Produção**

#### **Opção 1: Deploy Automático** (Recomendado)
```bash
# Conectar no servidor Linux
ssh usuario@servidor.com.br

# Baixar e executar script
wget https://raw.githubusercontent.com/DalmoVieira/esic/main/deploy.sh
chmod +x deploy.sh
sudo ./deploy.sh
```

#### **Opção 2: Deploy Manual**
Siga o guia completo de 12 etapas: **[DEPLOY_PRODUCAO.md](DEPLOY_PRODUCAO.md)**

#### **Opção 3: Checklist Rápido**
Guia resumido passo a passo: **[CHECKLIST_DEPLOY.md](CHECKLIST_DEPLOY.md)**

### Páginas Disponíveis:

#### 🌐 **Páginas Públicas (Cidadãos)**
- **🏠 Página Principal:** `/index.php` - Interface moderna com navegação
- **📝 Nova Solicitação:** `/novo-pedido.php` - Formulário de pedidos + anexos
- **🔍 Acompanhar:** `/acompanhar.php` - Consulta por protocolo
- **👁️ Transparência:** `/transparencia.php` - Portal de dados públicos

#### 🏛️ **Páginas Administrativas** (Requer login)
- **📋 Gestão de Pedidos:** `/admin-pedidos.php` - Dashboard + tabela de pedidos
- **🔄 Gestão de Recursos:** `/admin-recursos.php` - Análise de recursos (1ª, 2ª, 3ª instância)
- **⚙️ Configurações:** `/admin-configuracoes.php` - SMTP, Notificações, Cron

#### 🔌 **APIs REST**
- **POST** `/api/pedidos.php` - CRUD de pedidos (criar, listar, buscar)
- **POST** `/api/recursos.php` - Sistema de recursos
- **POST** `/api/anexos.php` - Upload/download de arquivos
- **POST** `/api/pedidos-admin.php` - Ações administrativas (responder, tramitar)
- **POST** `/api/tramitacoes.php` - Histórico de movimentações

## 🛠️ Tecnologias Utilizadas

### Frontend
- **Bootstrap 5.3.2** - Framework CSS responsivo
- **Bootstrap Icons** - Biblioteca de ícones
- **JavaScript** - Interatividade e animações
- **CSS3** - Estilização personalizada

### Backend
- **PHP 8.2+** - Linguagem de programação
- **MySQL 8.0+** - Banco de dados
- **Apache 2.4+** - Servidor web
- **MVC Pattern** - Arquitetura organizada

### Ferramentas
- **XAMPP** - Ambiente de desenvolvimento
- **Git** - Controle de versão
- **Composer** - Gerenciador de dependências (futuro)

## � Pré-requisitos

### Software Necessário
- **PHP 8.2 ou superior**
- **MySQL 8.0 ou superior**
- **Apache 2.4 ou superior**
- **XAMPP** (recomendado para desenvolvimento)

### Extensões PHP
- `mysqli` - Conexão com MySQL
- `pdo` - Abstração de banco de dados
- `mbstring` - Manipulação de strings
- `json` - Processamento JSON
- `session` - Gerenciamento de sessões

## 🚀 Instalação

### 1. Clone o Repositório
```bash
git clone https://github.com/DalmoVieira/esic.git
cd esic
```

### 2. Configure o Ambiente
1. **Instale o XAMPP** - [Download aqui](https://www.apachefriends.org)
2. **Copie o projeto** para `c:\xampp\htdocs\esic\`
3. **Inicie os serviços** Apache e MySQL no XAMPP

### 3. Configure o Banco de Dados
1. Acesse o **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Crie um banco chamado `esic_db`
3. Execute o script de criação:
   ```sql
   # Execute o arquivo: database/esic_schema.sql
   ```

### 4. Configure a Aplicação
```php
// Edite: config/database.php
return [
    'host' => 'localhost',
    'dbname' => 'esic_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

### 5. Teste a Instalação
- **Acesse:** [http://localhost/esic/](http://localhost/esic/)
- **Diagnóstico:** [http://localhost/esic/diagnostico.php](http://localhost/esic/diagnostico.php)

## 📁 Estrutura do Projeto

```
esic/
├── 📂 api/                     # APIs REST
│   ├── 📄 pedidos.php          # CRUD de pedidos (cidadãos)
│   ├── � pedidos-admin.php    # Gestão admin de pedidos
│   ├── 📄 recursos.php         # Sistema de recursos
│   ├── � anexos.php           # Upload/download de anexos
│   ├── � tramitacoes.php      # Histórico de movimentações
│   └── 📄 configuracoes.php    # Configurações do sistema
├── 📂 app/                     
│   ├── 📂 classes/             # Classes PHP
│   │   └── 📄 EmailNotificacao.php  # Sistema de emails
│   └── 📂 config/              # Configurações
│       └── 📄 Database.php     # Conexão com MySQL
├── 📂 assets/                  
│   ├── 📂 css/                 # Estilos personalizados
│   ├── 📂 js/                  # JavaScript
│   │   ├── 📄 main.js          # Scripts principais
│   │   ├── 📄 app.js           # App core
│   │   └── 📄 anexos.js        # Gestão de anexos
│   └── 📂 images/              # Imagens
├── 📂 cron/                    # Scripts automáticos
│   └── 📄 notificacoes.php     # Envio de emails automático
├── 📂 database/                # Schema do banco
│   └── � schema_novo.sql      # Estrutura completa (8 tabelas)
├── 📂 uploads/                 # Arquivos enviados
│   └── 📄 .htaccess            # Proteção de acesso direto
├── 📄 index.php                # Página principal
├── 📄 novo-pedido.php          # Formulário de solicitação
├── 📄 acompanhar.php           # Consulta de protocolo
├── 📄 transparencia.php        # Portal da transparência
├── 📄 admin-pedidos.php        # Painel administrativo
├── 📄 admin-recursos.php       # Gestão de recursos
├── 📄 admin-configuracoes.php  # Configurações SMTP
├── 📄 deploy.sh                # ⭐ Deploy automatizado
├── 📄 comandos-rapidos.sh      # ⭐ Menu de comandos
├── 📄 DEPLOY_PRODUCAO.md       # ⭐ Guia completo (12 etapas)
├── 📄 CHECKLIST_DEPLOY.md      # ⭐ Checklist de implantação
├── 📄 README_FASE3.md          # Documentação Fase 3
├── 📄 .htaccess                # Configurações Apache
└── 📄 README.md                # Este arquivo
```

## ⚖️ Conformidade Legal

### Lei de Acesso à Informação (LAI)
✅ **Prazos Legais** - Controle de 20 dias + 10 prorrogáveis  
✅ **Transparência Ativa** - Portal com dados obrigatórios  
✅ **Transparência Passiva** - Sistema de solicitações  
✅ **Recursos** - Possibilidade de contestação  
✅ **Acompanhamento** - Protocolo para consultas  
✅ **Gratuidade** - Acesso livre e gratuito  

### Dados Protegidos
- 🔒 **LGPD Compliance** - Proteção de dados pessoais
- 🛡️ **Segurança** - Validação e sanitização de inputs
- 🔐 **Autenticação** - Sistema de login seguro
- 📝 **Logs** - Auditoria de ações do sistema

## 🎨 Interface e Design

### Características
- **📱 Responsivo** - Adapta-se a todos os dispositivos
- **♿ Acessível** - Seguindo diretrizes WCAG
- **🎨 Moderno** - Interface limpa e profissional
- **🚀 Rápido** - Carregamento otimizado
- **🇧🇷 Português** - Totalmente em português brasileiro

### Paleta de Cores
- **Primary:** `#0d47a1` (Azul institucional)
- **Secondary:** `#1565c0` (Azul complementar)
- **Success:** `#198754` (Verde)
- **Warning:** `#ffc107` (Amarelo)
- **Info:** `#0dcaf0` (Ciano)

## � Status do Desenvolvimento

### ✅ **FASE 1 - CONCLUÍDA**
- [x] Estrutura do banco de dados completa
- [x] Sistema de pedidos (frontend + backend)
- [x] Sistema de acompanhamento
- [x] Portal da transparência
- [x] Design responsivo
- [x] API REST de pedidos

### ✅ **FASE 2 - CONCLUÍDA**
- [x] Painel administrativo completo
- [x] Sistema de autenticação e sessões
- [x] Gestão de usuários e órgãos
- [x] Sistema de recursos (3 instâncias)
- [x] Controle de prazos (20+10 dias)
- [x] Sistema de tramitação
- [x] Logs de auditoria

### ✅ **FASE 3 - CONCLUÍDA**
- [x] Sistema completo de anexos
- [x] Notificações por email (SMTP configurável)
- [x] Cron jobs automáticos
- [x] Painel de configurações SMTP
- [x] Templates HTML profissionais
- [x] Alertas de prazo próximo/vencido

### 🚀 **PRODUÇÃO - PRONTO**
- [x] Documentação completa ([DEPLOY_PRODUCAO.md](DEPLOY_PRODUCAO.md))
- [x] Script de deploy automatizado ([deploy.sh](deploy.sh))
- [x] Checklist de implantação ([CHECKLIST_DEPLOY.md](CHECKLIST_DEPLOY.md))
- [x] Menu de comandos úteis ([comandos-rapidos.sh](comandos-rapidos.sh))
- [x] Sistema de backup automático
- [x] Configuração SSL/HTTPS
- [x] Segurança completa (Fail2Ban, Firewall)

### 🔮 Funcionalidades Futuras (Opcionais)
- [ ] Dashboard com gráficos interativos
- [ ] Relatórios em PDF
- [ ] Exportação de dados (Excel/CSV)
- [ ] FAQ automatizado
- [ ] Integração WhatsApp
- [ ] Assinatura digital (ICP-Brasil)
- [ ] PWA (Progressive Web App)
- [ ] App mobile nativo

## 🤝 Como Contribuir

### Desenvolvimento
1. **Fork** o projeto
2. **Crie** uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. **Commit** suas mudanças (`git commit -m '✨ Adiciona nova funcionalidade'`)
4. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
5. **Abra** um Pull Request

### Padrões de Commit
- `✨ feat:` Nova funcionalidade
- `🐛 fix:` Correção de bug
- `📚 docs:` Atualização de documentação
- `🎨 style:` Melhorias de estilo/UI
- `♻️ refactor:` Refatoração de código
- `⚡ perf:` Melhoria de performance
- `✅ test:` Adição de testes

---

## 📚 **Documentação Adicional**

| Documento | Descrição |
|-----------|-----------|
| [📘 DEPLOY_PRODUCAO.md](DEPLOY_PRODUCAO.md) | Guia completo de deploy em 12 etapas detalhadas |
| [📝 CHECKLIST_DEPLOY.md](CHECKLIST_DEPLOY.md) | Checklist rápido de implantação passo a passo |
| [📖 README_FASE3.md](README_FASE3.md) | Documentação da Fase 3 (Anexos e Notificações) |
| [🔧 deploy.sh](deploy.sh) | Script bash automatizado de deploy |
| [⚡ comandos-rapidos.sh](comandos-rapidos.sh) | Menu interativo com 11 comandos úteis |

---

## 🆘 **Suporte Pós-Deploy**

### Comandos Úteis no Servidor:

```bash
# Menu interativo (recomendado)
sudo bash /var/www/esic/comandos-rapidos.sh

# Ver logs em tempo real
sudo tail -f /var/log/apache2/esic-error.log

# Status dos serviços
sudo systemctl status apache2 mysql

# Reiniciar serviços
sudo systemctl restart apache2

# Testar cron de notificações
sudo php /var/www/esic/cron/notificacoes.php

# Backup manual
sudo /usr/local/bin/backup-esic.sh

# Verificar espaço em disco
df -h

# Ver últimos pedidos
mysql -u esic_user -p esic_db -e "SELECT id, protocolo, status FROM pedidos ORDER BY created_at DESC LIMIT 10;"
```

### Troubleshooting:

| Problema | Solução |
|----------|---------|
| ❌ Erro 500 | `sudo tail -50 /var/log/apache2/esic-error.log` |
| ❌ Banco não conecta | Verificar credenciais em `app/config/Database.php` |
| ❌ Upload não funciona | `sudo chmod 775 /var/www/esic/uploads` |
| ❌ Email não envia | Testar SMTP em `/admin-configuracoes.php` |
| ❌ SSL expirado | `sudo certbot renew` |

## 📞 Suporte e Contato

### 🐛 Reportar Bugs
- **GitHub Issues:** [Criar novo issue](https://github.com/DalmoVieira/esic/issues)
- **Email:** suporte@rioclaro.sp.gov.br

### 💡 Sugestões
- **Discussions:** [GitHub Discussions](https://github.com/DalmoVieira/esic/discussions)
- **Feature Request:** [Solicitar funcionalidade](https://github.com/DalmoVieira/esic/issues/new?template=feature_request.md)

### 📚 Documentação
- **Guia de Deploy:** [DEPLOY_PRODUCAO.md](DEPLOY_PRODUCAO.md)
- **Checklist:** [CHECKLIST_DEPLOY.md](CHECKLIST_DEPLOY.md)
- **Fase 3:** [README_FASE3.md](README_FASE3.md)

### 🏛️ Órgão
- **Prefeitura Municipal de Rio Claro - SP**
- Website: https://www.rioclaro.sp.gov.br
- Email: esic@rioclaro.sp.gov.br
- Telefone: (19) 3522-7600

## 📄 Licença

Este projeto está licenciado sob a **MIT License** - veja o arquivo [LICENSE](LICENSE) para detalhes.

Sistema proprietário desenvolvido para a **Prefeitura Municipal de Rio Claro - SP**.

```
MIT License

Copyright (c) 2025 Prefeitura Municipal de Rio Claro - SP

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

---

## 🎉 **STATUS: PRODUÇÃO READY!**

✅ **Sistema 100% funcional e testado**  
✅ **3 fases de desenvolvimento completas**  
✅ **Documentação completa de deploy**  
✅ **Scripts automatizados prontos**  
✅ **Segurança implementada (SSL, Firewall, Fail2Ban)**  
✅ **Conforme Lei 12.527/2011 (LAI)**  
✅ **Backup automático configurado**  
✅ **Monitoramento e logs ativos**  

**🚀 Pronto para deploy imediato em produção!**

---

## 📈 Versão

**Versão:** 3.0.0 (Produção)  
**Data:** Janeiro 2025  
**Status:** ✅ Production Ready  

### Histórico de Versões:
- **v1.0.0** - Fase 1: Core system (pedidos, acompanhamento)
- **v2.0.0** - Fase 2: Admin panel (recursos, gestão)
- **v3.0.0** - Fase 3: Anexos, emails, deploy (ATUAL)

---

## 🏆 Reconhecimentos

### Tecnologias
- **Bootstrap Team** - Framework CSS incrível
- **PHP Community** - Linguagem robusta e versátil
- **MySQL** - Banco de dados confiável
- **Apache Foundation** - Servidor web poderoso

### Inspiração
- **Portal da Transparência** - Governo Federal
- **e-SIC** - Sistema oficial brasileiro
- **Governo Digital** - Iniciativas de transparência

---

<div align="center">

**🇧🇷 Desenvolvido para a Prefeitura Municipal de Rio Claro - SP**

**Sistema completo de transparência pública em conformidade com a LAI (Lei 12.527/2011)**

[![GitHub Stars](https://img.shields.io/github/stars/DalmoVieira/esic?style=social)](https://github.com/DalmoVieira/esic/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/DalmoVieira/esic?style=social)](https://github.com/DalmoVieira/esic/network)
[![GitHub Issues](https://img.shields.io/github/issues/DalmoVieira/esic)](https://github.com/DalmoVieira/esic/issues)

**Desenvolvido com ❤️ por [Dalmo Vieira](https://github.com/DalmoVieira)**

</div>