# 🤝 Guia de Contribuição

Obrigado por considerar contribuir com o projeto E-SIC! Este documento fornece diretrizes para contribuir com o projeto de forma eficiente e organizada.

---

## 📋 Índice

1. [Código de Conduta](#código-de-conduta)
2. [Como Posso Contribuir?](#como-posso-contribuir)
3. [Configurando o Ambiente](#configurando-o-ambiente)
4. [Padrões de Código](#padrões-de-código)
5. [Processo de Pull Request](#processo-de-pull-request)
6. [Reportando Bugs](#reportando-bugs)
7. [Sugerindo Melhorias](#sugerindo-melhorias)

---

## 📜 Código de Conduta

Este projeto segue um Código de Conduta. Ao participar, você concorda em manter um ambiente respeitoso e colaborativo.

### Nossos Padrões

✅ **Comportamentos Esperados:**
- Usar linguagem acolhedora e inclusiva
- Respeitar diferentes pontos de vista
- Aceitar críticas construtivas
- Focar no que é melhor para a comunidade
- Mostrar empatia com outros membros

❌ **Comportamentos Inaceitáveis:**
- Uso de linguagem sexualizada ou imagens inadequadas
- Trolling, comentários insultuosos ou ataques pessoais
- Assédio público ou privado
- Publicar informações privadas de terceiros
- Conduta não profissional

---

## 🎯 Como Posso Contribuir?

Há várias formas de contribuir com o projeto:

### 1. 🐛 Reportar Bugs
Encontrou um bug? [Abra um issue](https://github.com/DalmoVieira/esic/issues/new?template=bug_report.md) descrevendo:
- O que você estava fazendo
- O que esperava que acontecesse
- O que realmente aconteceu
- Passos para reproduzir
- Prints ou logs (se possível)

### 2. 💡 Sugerir Melhorias
Tem uma ideia legal? [Crie um feature request](https://github.com/DalmoVieira/esic/issues/new?template=feature_request.md) com:
- Descrição clara da funcionalidade
- Casos de uso
- Benefícios esperados
- Mockups ou exemplos (opcional)

### 3. 📝 Melhorar Documentação
- Corrigir erros de digitação
- Adicionar exemplos
- Traduzir documentação
- Escrever tutoriais

### 4. 💻 Contribuir com Código
- Corrigir bugs existentes
- Implementar novas funcionalidades
- Otimizar performance
- Adicionar testes

### 5. 🎨 Melhorar Design
- Aprimorar UI/UX
- Criar novos ícones
- Melhorar acessibilidade
- Tornar interface mais intuitiva

---

## 🛠️ Configurando o Ambiente

### Pré-requisitos
- **PHP 8.0+**
- **MySQL 8.0+**
- **Composer** (opcional)
- **Git**
- **XAMPP** (para desenvolvimento local)

### Passo a Passo

#### 1. Fork do Repositório
Clique em "Fork" no GitHub para criar uma cópia do projeto na sua conta.

#### 2. Clone seu Fork
```bash
git clone https://github.com/SEU-USUARIO/esic.git
cd esic
```

#### 3. Configure o Upstream
```bash
git remote add upstream https://github.com/DalmoVieira/esic.git
git fetch upstream
```

#### 4. Configure o Banco de Dados
```bash
# Acesse o MySQL
mysql -u root -p

# Crie o banco
CREATE DATABASE esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Importe o schema
USE esic_db;
SOURCE database/schema_novo.sql;
```

#### 5. Configure a Aplicação
```bash
# Copie para o diretório do XAMPP
cp -r . c:\xampp\htdocs\esic\

# Edite as configurações
# Arquivo: app/config/Database.php
```

#### 6. Instale Dependências (se usar Composer)
```bash
composer install
```

#### 7. Teste a Instalação
Acesse: http://localhost/esic/

---

## 📏 Padrões de Código

### PHP

#### PSR-12 (PHP Standards Recommendations)
```php
<?php

namespace App\Classes;

use PDO;
use Exception;

/**
 * Classe de exemplo
 */
class ExemploClasse
{
    private PDO $db;
    private string $tabela = 'usuarios';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Método de exemplo
     *
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->tabela} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado ?: null;
    }
}
```

#### Convenções PHP
- **Indentação:** 4 espaços (não tabs)
- **Encoding:** UTF-8 sem BOM
- **Linhas:** Máximo 120 caracteres
- **Classes:** PascalCase (`MinhaClasse`)
- **Métodos:** camelCase (`meuMetodo`)
- **Constantes:** UPPER_SNAKE_CASE (`MINHA_CONSTANTE`)
- **Variáveis:** snake_case (`$minha_variavel`)

#### Documentação (PHPDoc)
```php
/**
 * Descrição breve do método
 *
 * Descrição detalhada (opcional)
 *
 * @param string $parametro1 Descrição do parâmetro
 * @param int $parametro2 Descrição do parâmetro
 * @return bool Descrição do retorno
 * @throws Exception Quando ocorre erro
 */
public function meuMetodo(string $parametro1, int $parametro2): bool
{
    // código
}
```

### JavaScript

#### Convenções JavaScript
```javascript
/**
 * Classe de exemplo
 */
class ESICExemplo {
    /**
     * Construtor
     * @param {string} containerId - ID do container
     */
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.dados = [];
    }

    /**
     * Inicializa o componente
     */
    init() {
        this.carregarDados();
        this.renderizar();
    }

    /**
     * Carrega dados via AJAX
     * @returns {Promise<void>}
     */
    async carregarDados() {
        try {
            const response = await fetch('/api/dados.php');
            this.dados = await response.json();
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
        }
    }

    /**
     * Renderiza o componente
     */
    renderizar() {
        // código
    }
}

// Uso
const exemplo = new ESICExemplo('meu-container');
exemplo.init();
```

#### Convenções JS
- **Indentação:** 4 espaços
- **Classes:** PascalCase (`MinhaClasse`)
- **Funções:** camelCase (`minhaFuncao`)
- **Constantes:** UPPER_SNAKE_CASE (`MINHA_CONSTANTE`)
- **Variáveis:** camelCase (`minhaVariavel`)
- **Use `const` e `let`**, evite `var`
- **Use arrow functions** quando apropriado
- **Use template literals** para strings

### SQL

#### Convenções SQL
```sql
-- Tabelas: snake_case plural
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('cidadao', 'atendente', 'admin') DEFAULT 'cidadao',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Queries: uppercase para palavras-chave
SELECT 
    u.id,
    u.nome,
    COUNT(p.id) AS total_pedidos
FROM usuarios u
LEFT JOIN pedidos p ON u.id = p.usuario_id
WHERE u.tipo_usuario = 'cidadao'
GROUP BY u.id
ORDER BY total_pedidos DESC
LIMIT 10;
```

### HTML/CSS

#### HTML
```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-SIC - Título da Página</title>
</head>
<body>
    <!-- Usar classes semânticas -->
    <div class="container">
        <header class="header">
            <h1 class="header__title">Título</h1>
        </header>
        
        <main class="main-content">
            <!-- Conteúdo -->
        </main>
        
        <footer class="footer">
            <!-- Rodapé -->
        </footer>
    </div>
</body>
</html>
```

#### CSS
```css
/* Usar metodologia BEM */
.card {
    padding: 1rem;
    border: 1px solid #ddd;
}

.card__header {
    font-weight: bold;
}

.card__body {
    margin-top: 1rem;
}

.card--highlighted {
    border-color: #0d47a1;
}
```

---

## 🔄 Processo de Pull Request

### 1. Crie uma Branch
```bash
# Atualize seu fork
git checkout main
git pull upstream main

# Crie uma branch descritiva
git checkout -b feature/nova-funcionalidade
# ou
git checkout -b fix/correcao-bug
```

### 2. Faça suas Mudanças
```bash
# Edite os arquivos necessários
# Siga os padrões de código

# Adicione os arquivos
git add .

# Faça o commit (veja padrões abaixo)
git commit -m "✨ feat: adiciona sistema de busca avançada"
```

### 3. Padrões de Commit

Use [Conventional Commits](https://www.conventionalcommits.org/pt-br/):

```
<tipo>: <descrição>

[corpo opcional]

[rodapé opcional]
```

**Tipos:**
- ✨ `feat:` - Nova funcionalidade
- 🐛 `fix:` - Correção de bug
- 📚 `docs:` - Documentação
- 🎨 `style:` - Formatação (não afeta lógica)
- ♻️ `refactor:` - Refatoração
- ⚡ `perf:` - Melhoria de performance
- ✅ `test:` - Adição de testes
- 🔧 `chore:` - Tarefas de build/config

**Exemplos:**
```bash
git commit -m "✨ feat: adiciona filtro por data no painel admin"
git commit -m "🐛 fix: corrige erro no upload de anexos"
git commit -m "📚 docs: atualiza guia de instalação"
git commit -m "🎨 style: padroniza indentação em pedidos.php"
git commit -m "♻️ refactor: simplifica lógica de validação"
git commit -m "⚡ perf: otimiza query de busca de pedidos"
```

### 4. Envie para seu Fork
```bash
git push origin feature/nova-funcionalidade
```

### 5. Abra o Pull Request

1. Vá até seu fork no GitHub
2. Clique em "Compare & pull request"
3. Preencha o template:

```markdown
## Descrição
Breve descrição das mudanças.

## Tipo de Mudança
- [ ] 🐛 Bug fix
- [ ] ✨ Nova funcionalidade
- [ ] 📚 Documentação
- [ ] 🎨 Melhoria de UI/UX
- [ ] ♻️ Refatoração

## Checklist
- [ ] Código segue os padrões do projeto
- [ ] Comentários foram adicionados em código complexo
- [ ] Documentação foi atualizada
- [ ] Não gera novos warnings
- [ ] Testes foram adicionados/atualizados (se aplicável)
- [ ] Mudanças foram testadas localmente

## Screenshots (se aplicável)
Cole aqui prints das mudanças visuais.

## Issues Relacionadas
Closes #123
```

### 6. Aguarde Revisão

- Mantenha-se disponível para discussões
- Responda feedbacks rapidamente
- Faça ajustes se solicitado

---

## 🐛 Reportando Bugs

### Antes de Reportar
1. Verifique se já existe um [issue aberto](https://github.com/DalmoVieira/esic/issues)
2. Teste na versão mais recente
3. Tente reproduzir o bug

### Template de Bug Report

```markdown
## Descrição do Bug
Descrição clara e concisa do problema.

## Passos para Reproduzir
1. Vá para '...'
2. Clique em '...'
3. Role até '...'
4. Veja o erro

## Comportamento Esperado
O que deveria acontecer.

## Comportamento Atual
O que realmente acontece.

## Screenshots
Se aplicável, adicione screenshots.

## Ambiente
- **OS:** [ex: Windows 10]
- **Navegador:** [ex: Chrome 120]
- **Versão do PHP:** [ex: 8.2]
- **Versão do MySQL:** [ex: 8.0.35]

## Informações Adicionais
Qualquer contexto adicional sobre o problema.

## Logs
```
Cole aqui logs de erro relevantes
```
```

---

## 💡 Sugerindo Melhorias

### Template de Feature Request

```markdown
## Resumo da Funcionalidade
Descrição breve e clara da funcionalidade.

## Problema que Resolve
Explique qual problema esta funcionalidade resolve.

## Solução Proposta
Como você imagina que esta funcionalidade funcionaria?

## Alternativas Consideradas
Quais outras soluções você pensou?

## Benefícios
- Benefício 1
- Benefício 2
- Benefício 3

## Possíveis Desvantagens
- Desvantagem 1
- Desvantagem 2

## Mockups/Exemplos (opcional)
Adicione imagens, links ou exemplos de código.

## Prioridade
- [ ] 🔴 Crítica
- [ ] 🟠 Alta
- [ ] 🟡 Média
- [ ] 🟢 Baixa
```

---

## ✅ Checklist de Contribuição

Antes de enviar seu PR, certifique-se de:

### Código
- [ ] Segue os padrões de código do projeto
- [ ] Está bem comentado (especialmente lógica complexa)
- [ ] Foi testado localmente
- [ ] Não quebra funcionalidades existentes

### Documentação
- [ ] README atualizado (se necessário)
- [ ] Comentários PHPDoc/JSDoc adicionados
- [ ] CHANGELOG atualizado (se aplicável)

### Segurança
- [ ] Não expõe informações sensíveis
- [ ] Valida inputs do usuário
- [ ] Usa prepared statements (SQL)
- [ ] Sanitiza outputs

### Performance
- [ ] Não causa degradação de performance
- [ ] Queries otimizadas
- [ ] Sem loops desnecessários

---

## 📞 Precisa de Ajuda?

- **GitHub Discussions:** [Faça perguntas](https://github.com/DalmoVieira/esic/discussions)
- **Email:** dalmo@rioclaro.sp.gov.br
- **Issues:** [Crie um issue](https://github.com/DalmoVieira/esic/issues)

---

## 🙏 Agradecimentos

Obrigado por contribuir com o E-SIC! Sua ajuda torna este projeto melhor para todos. 🎉

### Principais Contribuidores

- [Dalmo Vieira](https://github.com/DalmoVieira) - Criador e mantenedor
- _Seu nome aqui!_ - Seja o próximo contribuidor

---

## 📄 Licença

Ao contribuir, você concorda que suas contribuições serão licenciadas sob a [MIT License](LICENSE).

---

**Desenvolvido com ❤️ para a transparência pública**
