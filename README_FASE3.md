# 📋 E-SIC - Sistema Eletrônico de Informação ao Cidadão
## Prefeitura Municipal de Rio Claro - SP

Sistema completo de gestão de pedidos de acesso à informação em conformidade com a **Lei 12.527/2011 (Lei de Acesso à Informação - LAI)**.

---

## 🚀 **FASE 3 - COMPLETA!**

### ✅ Recursos Implementados

#### 📎 **Sistema de Anexos Completo**
- **Upload de arquivos** (PDF, DOC, DOCX, JPG, PNG, etc.)
- **Validação de segurança** (tipo MIME, extensão, tamanho)
- **Limite de 10MB por arquivo**
- **Download seguro** com registro de logs
- **Gestão visual** com ícones por tipo de arquivo
- **Exclusão** de anexos por administradores

**Arquivos criados:**
- `api/anexos.php` - API completa para gerenciamento de anexos
- `assets/js/anexos.js` - Classe JavaScript para interface de anexos
- `uploads/` - Diretório para armazenamento (criar manualmente)

#### 📧 **Sistema de Notificações por Email**
- **SMTP configurável** via painel administrativo
- **Templates HTML responsivos** com identidade visual
- **Notificações automáticas** para:
  - ✅ Novo pedido criado (confirmação)
  - ✅ Mudança de status
  - ✅ Pedido respondido
  - ⏰ Prazo próximo do vencimento (5 dias)
  - ⚠️ Prazo vencido
  - 🔄 Novo recurso registrado

**Arquivos criados:**
- `app/classes/EmailNotificacao.php` - Classe principal de emails
- `cron/notificacoes.php` - Script para execução automática
- `admin-configuracoes.php` - Painel de configuração SMTP

---

## 📦 **Estrutura do Projeto**

```
esic/
├── api/
│   ├── pedidos.php              # API de pedidos (criar, listar, buscar)
│   ├── pedidos-admin.php        # API administrativa (responder, alterar status)
│   ├── recursos.php             # API de recursos (3 instâncias)
│   └── anexos.php               # API de anexos (upload, download, deletar)
│
├── app/
│   ├── classes/
│   │   └── EmailNotificacao.php # Classe de emails
│   └── config/
│       └── Database.php         # Singleton de conexão
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── main.js
│   │   ├── app.js
│   │   └── anexos.js            # Gerenciador de anexos
│   └── images/
│
├── cron/
│   └── notificacoes.php         # Script de notificações automáticas
│
├── database/
│   └── schema_novo.sql          # Schema completo atualizado
│
├── uploads/                      # Diretório de anexos (criar)
│
├── novo-pedido-v2.php           # Formulário de pedido
├── acompanhar-v2.php            # Acompanhamento de protocolos
├── recurso.php                  # Formulário de recurso
├── admin-pedidos.php            # Painel administrativo
├── admin-configuracoes.php      # Configurações SMTP
└── dashboard.php                # Dashboard principal
```

---

## ⚙️ **Instalação e Configuração**

### 1. **Banco de Dados**

Execute o schema atualizado:

```bash
cd C:\xampp\htdocs\esic
C:\xampp\mysql\bin\mysql.exe -u root < database\schema_novo.sql
```

### 2. **Criar Diretório de Uploads**

```bash
mkdir uploads
chmod 755 uploads  # Linux
# No Windows, garantir que o diretório tem permissão de escrita
```

### 3. **Configurar SMTP**

Acesse o painel administrativo:
- URL: `http://localhost/esic/admin-configuracoes.php?tipo=administrador`
- Configure:
  - Servidor SMTP (ex: `smtp.gmail.com`)
  - Porta (587 para TLS)
  - Usuário e senha
  - Email remetente

### 4. **Testar Envio de Email**

No painel de configurações:
1. Vá para a aba "Testar Email"
2. Informe um email de destino
3. Escolha o tipo de notificação
4. Clique em "Enviar Email de Teste"

### 5. **Configurar Cron para Notificações Automáticas**

**Linux/Mac:**
```bash
crontab -e
# Adicionar linha:
0 8 * * * php /caminho/completo/esic/cron/notificacoes.php
```

**Windows (Agendador de Tarefas):**
```powershell
# Criar tarefa que executa diariamente às 8h:
schtasks /create /tn "E-SIC Notificações" /tr "C:\xampp\php\php.exe C:\xampp\htdocs\esic\cron\notificacoes.php" /sc daily /st 08:00
```

**Executar manualmente para testar:**
```bash
php cron/notificacoes.php
```

---

## 📧 **Usando o Sistema de Anexos**

### No Frontend (Cidadão)

```javascript
// Inicializar componente de anexos
esicAnexos = new ESICAnexos('pedido', pedidoId, '#containerAnexos');

// O componente já gerencia automaticamente:
// - Upload com preview
// - Validação de arquivos
// - Lista de anexos
// - Download
// - Exclusão (se permitido)
```

### Exemplo de uso na página:

```html
<div id="containerAnexos"></div>

<script src="assets/js/anexos.js"></script>
<script>
    // Inicializar para pedido com ID 123
    const anexos = new ESICAnexos('pedido', 123, '#containerAnexos');
</script>
```

---

## 📨 **Sistema de Notificações**

### Tipos de Notificações

#### 1. **Novo Pedido**
```php
$emailService = new EmailNotificacao();
$emailService->notificarNovoPedido($pedido, $requerente);
```

#### 2. **Mudança de Status**
```php
$emailService->notificarMudancaStatus($pedido, $requerente, 'em_analise');
```

#### 3. **Resposta ao Pedido**
```php
$emailService->notificarResposta($pedido, $requerente);
```

#### 4. **Prazo Próximo (5 dias)**
```php
$emailService->notificarPrazoProximo($pedido, $requerente, 5);
```

#### 5. **Prazo Vencido**
```php
$emailService->notificarPrazoVencido($pedido, $requerente);
```

#### 6. **Novo Recurso**
```php
$emailService->notificarNovoRecurso($recurso, $pedido, $requerente);
```

### Integração Automática

As notificações são enviadas automaticamente quando:
- ✅ Um novo pedido é criado (`api/pedidos.php`)
- ✅ O status de um pedido muda (`api/pedidos-admin.php`)
- ✅ Um pedido recebe resposta (`api/pedidos-admin.php`)
- ⏰ O cron verifica prazos (`cron/notificacoes.php`)

---

## 🔐 **Segurança**

### Anexos
- ✅ Validação de tipo MIME real (não só extensão)
- ✅ Nomes de arquivo com hash único
- ✅ Limite de tamanho (10MB)
- ✅ Extensões permitidas configuráveis
- ✅ Armazenamento fora do diretório web (recomendado)

### Emails
- ✅ Templates HTML sanitizados
- ✅ Credenciais SMTP no banco de dados
- ✅ Rate limiting (configurável)
- ✅ Logs de envio completos

---

## 📊 **Monitoramento**

### Logs do Sistema

Todos os eventos são registrados na tabela `logs_sistema`:

```sql
SELECT * FROM logs_sistema 
WHERE acao IN ('upload_anexo', 'download_anexo', 'cron_notificacoes')
ORDER BY data_log DESC 
LIMIT 50;
```

### Ver Anexos de um Pedido

```sql
SELECT a.*, p.protocolo 
FROM anexos a
JOIN pedidos p ON a.entidade_id = p.id AND a.tipo_entidade = 'pedido'
WHERE p.protocolo = 'P2025000001';
```

### Estatísticas de Email

```sql
-- Total de notificações por tipo (via logs)
SELECT 
    JSON_EXTRACT(detalhes, '$.tipo') as tipo_notificacao,
    COUNT(*) as total
FROM logs_sistema
WHERE acao LIKE '%email%'
GROUP BY tipo_notificacao;
```

---

## 🎯 **Conformidade LAI**

### ✅ Requisitos Atendidos

- ✅ **Art. 10** - Prazo de 20 dias para resposta
- ✅ **Art. 11, §1º** - Prorrogação por mais 10 dias
- ✅ **Art. 11, §2º** - Notificação de prorrogação
- ✅ **Art. 21** - Sistema de recursos (3 instâncias)
- ✅ **Art. 21, §1º** - Prazos de recursos (10/5/5 dias)
- ✅ **Art. 8º** - Transparência ativa via portal
- ✅ **Art. 9º** - Acesso facilitado ao cidadão
- ✅ **Art. 31** - Identificação do requerente

### Prazos Implementados

| Tipo | Prazo | Base Legal |
|------|-------|------------|
| Resposta ao pedido | 20 dias | Art. 11, caput |
| Prorrogação | +10 dias | Art. 11, §1º |
| Recurso 1ª instância | 10 dias | Art. 21, §1º |
| Recurso 2ª instância | 5 dias | Art. 21, §2º |
| Recurso 3ª instância (CGU) | 5 dias | Art. 21, §3º |

---

## 🧪 **Testes**

### Testar Upload de Anexo

1. Acesse `novo-pedido-v2.php`
2. Preencha o formulário
3. Adicione um arquivo PDF
4. Submeta o pedido
5. Verifique em `acompanhar-v2.php` se o anexo aparece

### Testar Notificação por Email

```bash
# Executar cron manualmente
php cron/notificacoes.php

# Ou via painel admin
# Acessar admin-configuracoes.php > Testar Email
```

### Verificar Logs

```bash
# Ver últimos logs
tail -f logs/app.log  # Se implementado

# Ou consultar banco
SELECT * FROM logs_sistema ORDER BY data_log DESC LIMIT 20;
```

---

## 📝 **Próximos Passos (Opcional)**

### Fase 4 - Melhorias
- [ ] Dashboard com gráficos (Chart.js)
- [ ] Relatórios em PDF (TCPDF/DomPDF)
- [ ] Exportação de dados (CSV/Excel)
- [ ] Sistema de FAQ automático
- [ ] Integração com WhatsApp Business
- [ ] Assinatura digital de respostas
- [ ] Versionamento de documentos
- [ ] Busca avançada com Elasticsearch

---

## 🆘 **Suporte**

### Problemas Comuns

**1. Anexos não são enviados**
- Verificar permissões do diretório `uploads/`
- Verificar limite de upload no `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 12M
  ```

**2. Emails não são enviados**
- Verificar configurações SMTP
- Testar conexão: `telnet smtp.servidor.com 587`
- Verificar logs de erro do PHP
- Para Gmail, ativar "Aplicativos menos seguros"

**3. Cron não executa**
- Verificar permissões: `chmod +x cron/notificacoes.php`
- Testar execução manual
- Verificar logs do cron: `/var/log/cron` (Linux)

---

## 📄 **Licença**

Este sistema foi desenvolvido para uso da Prefeitura Municipal de Rio Claro - SP em conformidade com a Lei 12.527/2011.

---

## 👨‍💻 **Desenvolvimento**

**Sistema:** E-SIC v3.0  
**Framework:** PHP 8+ | MySQL 8+ | Bootstrap 5  
**Conformidade:** Lei 12.527/2011 (LAI)  
**Data:** Outubro 2025

---

## ✅ **Status do Projeto**

**FASE 3 CONCLUÍDA COM SUCESSO! 🎉**

- ✅ Sistema de Anexos Completo
- ✅ Notificações por Email
- ✅ Cron de Verificação Automática
- ✅ Painel de Configurações SMTP
- ✅ Templates HTML Responsivos
- ✅ Segurança e Validações
- ✅ Logs Completos

**Sistema pronto para produção!** 🚀