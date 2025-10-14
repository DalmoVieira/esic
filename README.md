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
- 🔍 **Acompanhamento** - Consulta de status via protocolo
- 👁️ **Portal da Transparência** - Acesso direto a informações públicas
- 📊 **Estatísticas** - Dados sobre solicitações e prazos

### 🏛️ Para Administradores
- 📋 **Gestão de Pedidos** - Controle completo do fluxo de solicitações
- ⏰ **Controle de Prazos** - Monitoramento automático de deadlines
- 👤 **Gerenciamento de Usuários** - Controle de acesso e permissões
- 📈 **Relatórios** - Análises e métricas detalhadas
- ⚙️ **Configurações** - Personalização do sistema

## 🚀 Demonstração

Acesse a demonstração online: **[http://localhost/esic/](http://localhost/esic/)**

### Páginas Disponíveis:
- **🏠 Página Principal:** Interface moderna com navegação completa
- **📝 Nova Solicitação:** `/novo-pedido.php` - Formulário de pedidos
- **🔍 Acompanhar:** `/acompanhar.php` - Consulta por protocolo
- **👁️ Transparência:** `/transparencia.php` - Portal de dados públicos

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
├── 📂 app/                     # Aplicação principal
│   ├── 📂 controllers/         # Controladores MVC
│   ├── 📂 models/             # Modelos de dados
│   ├── 📂 views/              # Templates e layouts
│   ├── 📂 middleware/         # Middlewares de autenticação
│   ├── 📂 core/               # Classes centrais do sistema
│   ├── 📂 utils/              # Utilitários e helpers
│   └── 📂 libraries/          # Bibliotecas personalizadas
├── 📂 config/                 # Configurações do sistema
├── 📂 database/               # Scripts e schema do banco
├── 📂 public/                 # Arquivos públicos e testes
├── 📂 uploads/                # Arquivos enviados pelos usuários
├── 📄 index.php               # Página principal (atual)
├── 📄 novo-pedido.php         # Formulário de solicitação
├── 📄 acompanhar.php          # Consulta de protocolo
├── 📄 transparencia.php       # Portal da transparência
├── 📄 bootstrap.php           # Inicializador do sistema MVC
├── 📄 .htaccess              # Configurações do Apache
└── 📄 README.md              # Este arquivo
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

### ✅ Concluído
- [x] Interface web completa
- [x] Sistema de navegação
- [x] Formulários funcionais (frontend)
- [x] Portal da transparência
- [x] Design responsivo
- [x] Estrutura MVC preparada
- [x] Schema do banco de dados
- [x] Sistema de diagnóstico

### 🚧 Em Desenvolvimento
- [ ] Backend MVC completo
- [ ] Sistema de autenticação
- [ ] Integração com banco de dados
- [ ] Envio de emails
- [ ] Upload de arquivos
- [ ] Relatórios avançados
- [ ] API REST
- [ ] Testes automatizados

### 🔮 Planejado
- [ ] PWA (Progressive Web App)
- [ ] Notificações push
- [ ] Chat de suporte
- [ ] Integração com redes sociais
- [ ] Dashboard analítico
- [ ] App mobile
- [ ] Integração com e-gov

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

## 📞 Suporte e Contato

### 🐛 Reportar Bugs
- **GitHub Issues:** [Criar novo issue](https://github.com/DalmoVieira/esic/issues)
- **Email:** suporte@esic.gov.br (exemplo)

### 💡 Sugestões
- **Discussions:** [GitHub Discussions](https://github.com/DalmoVieira/esic/discussions)
- **Feature Request:** [Solicitar funcionalidade](https://github.com/DalmoVieira/esic/issues/new?template=feature_request.md)

### 📚 Documentação
- **Wiki:** [GitHub Wiki](https://github.com/DalmoVieira/esic/wiki)
- **API Docs:** Em desenvolvimento
- **Guia do Usuário:** Em desenvolvimento

## 📄 Licença

Este projeto está licenciado sob a **MIT License** - veja o arquivo [LICENSE](LICENSE) para detalhes.

```
MIT License

Copyright (c) 2024 E-SIC - Sistema LAI

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

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

**🇧🇷 Desenvolvido com ❤️ para a transparência pública brasileira**

[![GitHub Stars](https://img.shields.io/github/stars/DalmoVieira/esic?style=social)](https://github.com/DalmoVieira/esic/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/DalmoVieira/esic?style=social)](https://github.com/DalmoVieira/esic/network)
[![GitHub Issues](https://img.shields.io/github/issues/DalmoVieira/esic)](https://github.com/DalmoVieira/esic/issues)

</div>