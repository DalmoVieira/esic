<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-question-circle me-2"></i>
            Ajuda e Documentação
        </h1>
        <p class="text-muted mb-0">Guias e informações sobre o sistema E-SIC</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="imprimirPagina()">
            <i class="bi bi-printer me-1"></i>
            Imprimir
        </button>
        <button class="btn btn-outline-info" onclick="exportarPDF()">
            <i class="bi bi-file-pdf me-1"></i>
            Exportar PDF
        </button>
    </div>
</div>

<!-- Search Box -->
<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control" id="searchHelp" 
                   placeholder="Buscar na documentação...">
        </div>
    </div>
</div>

<div class="row">
    <!-- Navigation Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-list me-2"></i>
                    Índice
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="#introducao" class="list-group-item list-group-item-action">
                        <i class="bi bi-info-circle me-2"></i>
                        Introdução
                    </a>
                    <a href="#lai" class="list-group-item list-group-item-action">
                        <i class="bi bi-book me-2"></i>
                        Lei de Acesso
                    </a>
                    <a href="#pedidos" class="list-group-item list-group-item-action">
                        <i class="bi bi-file-text me-2"></i>
                        Pedidos de Informação
                    </a>
                    <a href="#recursos" class="list-group-item list-group-item-action">
                        <i class="bi bi-arrow-up-right-circle me-2"></i>
                        Recursos
                    </a>
                    <a href="#usuarios" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i>
                        Usuários
                    </a>
                    <a href="#administracao" class="list-group-item list-group-item-action">
                        <i class="bi bi-gear me-2"></i>
                        Administração
                    </a>
                    <a href="#relatorios" class="list-group-item list-group-item-action">
                        <i class="bi bi-graph-up me-2"></i>
                        Relatórios
                    </a>
                    <a href="#configuracoes" class="list-group-item list-group-item-action">
                        <i class="bi bi-sliders me-2"></i>
                        Configurações
                    </a>
                    <a href="#faq" class="list-group-item list-group-item-action">
                        <i class="bi bi-question-octagon me-2"></i>
                        Perguntas Frequentes
                    </a>
                    <a href="#suporte" class="list-group-item list-group-item-action">
                        <i class="bi bi-headset me-2"></i>
                        Suporte Técnico
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="col-lg-9">
        <!-- Introdução -->
        <section id="introducao" class="mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Introdução ao Sistema E-SIC
                    </h2>
                    
                    <p class="lead">
                        O Sistema Eletrônico do Serviço de Informações ao Cidadão (E-SIC) é uma plataforma 
                        desenvolvida para facilitar o acesso dos cidadãos às informações públicas, em conformidade 
                        com a Lei nº 12.527/2011 (Lei de Acesso à Informação).
                    </p>
                    
                    <h4>Principais Funcionalidades</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Submissão de pedidos de informação
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Acompanhamento de solicitações
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Interposição de recursos
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Notificações automáticas
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Painel administrativo completo
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Relatórios e estatísticas
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Controle de prazos legais
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Integração com Gov.br
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Lei de Acesso -->
        <section id="lai" class="mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary mb-3">
                        <i class="bi bi-book me-2"></i>
                        Lei de Acesso à Informação
                    </h2>
                    
                    <div class="alert alert-info">
                        <h5 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            Lei nº 12.527/2011
                        </h5>
                        <p class="mb-0">
                            A Lei de Acesso à Informação regulamenta o direito constitucional de acesso 
                            às informações públicas e criou mecanismos que possibilitam a qualquer pessoa, 
                            física ou jurídica, sem necessidade de apresentar motivo, o recebimento de 
                            informações públicas dos órgãos e entidades.
                        </p>
                    </div>
                    
                    <h4>Principais Direitos do Cidadão</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-primary">Acesso à Informação</h6>
                                    <p class="small mb-0">
                                        Obter informações públicas de órgãos e entidades 
                                        sem necessidade de justificativa.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-primary">Prazos Definidos</h6>
                                    <p class="small mb-0">
                                        Resposta em até 20 dias, prorrogável por mais 
                                        10 dias mediante justificativa.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-primary">Gratuidade</h6>
                                    <p class="small mb-0">
                                        Fornecimento gratuito de informações, exceto 
                                        custos de reprodução.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-primary">Direito a Recurso</h6>
                                    <p class="small mb-0">
                                        Possibilidade de recurso em caso de negativa 
                                        ou não atendimento do pedido.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Hipóteses de Sigilo</h4>
                    <p>
                        Algumas informações podem ter acesso restrito por questões de:
                    </p>
                    <ul>
                        <li>Segurança da sociedade ou do Estado</li>
                        <li>Intimidade, vida privada, honra e imagem de pessoas</li>
                        <li>Informações comerciais confidenciais</li>
                        <li>Informações protegidas por sigilo constitucional ou legal</li>
                    </ul>
                </div>
            </div>
        </section>
        
        <!-- Pedidos de Informação -->
        <section id="pedidos" class="mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary mb-3">
                        <i class="bi bi-file-text me-2"></i>
                        Pedidos de Informação
                    </h2>
                    
                    <h4>Como Fazer um Pedido</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex mb-3">
                                <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">1</div>
                                <div>
                                    <h6>Acesse o Sistema</h6>
                                    <p class="text-muted mb-0">
                                        Entre no E-SIC através do site oficial ou faça login com Gov.br
                                    </p>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-3">
                                <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">2</div>
                                <div>
                                    <h6>Preencha o Formulário</h6>
                                    <p class="text-muted mb-0">
                                        Complete todos os campos obrigatórios com suas informações e descreva claramente o que deseja
                                    </p>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-3">
                                <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">3</div>
                                <div>
                                    <h6>Envie o Pedido</h6>
                                    <p class="text-muted mb-0">
                                        Após revisar as informações, envie seu pedido e anote o número de protocolo
                                    </p>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-3">
                                <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">4</div>
                                <div>
                                    <h6>Acompanhe o Status</h6>
                                    <p class="text-muted mb-0">
                                        Use o número de protocolo para acompanhar o andamento de sua solicitação
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4>Status dos Pedidos</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Status</th>
                                    <th>Descrição</th>
                                    <th>Ação do Cidadão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-secondary">Recebido</span></td>
                                    <td>Pedido foi recebido pelo sistema</td>
                                    <td>Aguardar análise</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Em Análise</span></td>
                                    <td>Pedido está sendo analisado pelo órgão</td>
                                    <td>Acompanhar prazos</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Pendente</span></td>
                                    <td>Necessárias informações complementares</td>
                                    <td>Fornecer esclarecimentos</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">Atendido</span></td>
                                    <td>Informação foi disponibilizada</td>
                                    <td>Acessar resposta</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Negado</span></td>
                                    <td>Pedido foi negado com justificativa</td>
                                    <td>Pode interpor recurso</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Recursos -->
        <section id="recursos" class="mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary mb-3">
                        <i class="bi bi-arrow-up-right-circle me-2"></i>
                        Sistema de Recursos
                    </h2>
                    
                    <p>
                        O recurso é um direito garantido pela LAI quando o cidadão não concorda com a resposta 
                        recebida ou quando o prazo não é cumprido pelo órgão.
                    </p>
                    
                    <h4>Quando Interpor Recurso</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item border-0 ps-0">
                                    <i class="bi bi-x-circle text-danger me-2"></i>
                                    Pedido foi negado
                                </li>
                                <li class="list-group-item border-0 ps-0">
                                    <i class="bi bi-clock text-warning me-2"></i>
                                    Prazo foi descumprido
                                </li>
                                <li class="list-group-item border-0 ps-0">
                                    <i class="bi bi-info-circle text-info me-2"></i>
                                    Resposta incompleta
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item border-0 ps-0">
                                    <i class="bi bi-question-circle text-secondary me-2"></i>
                                    Resposta inadequada
                                </li>
                                <li class="list-group-item border-0 ps-0">
                                    <i class="bi bi-currency-dollar text-success me-2"></i>
                                    Cobrança indevida
                                </li>
                                <li class="list-group-item border-0 ps-0">
                                    <i class="bi bi-shield text-primary me-2"></i>
                                    Classificação indevida
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <h6 class="alert-heading">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Prazo para Recurso
                        </h6>
                        <p class="mb-0">
                            O recurso deve ser interposto em até <strong>10 dias corridos</strong> 
                            a partir da ciência da decisão.
                        </p>
                    </div>
                    
                    <h4>Instâncias de Recurso</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="badge bg-primary rounded-circle mb-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        1ª
                                    </div>
                                    <h6>Primeira Instância</h6>
                                    <p class="small text-muted mb-0">
                                        Autoridade hierarquicamente superior
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="badge bg-info rounded-circle mb-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        2ª
                                    </div>
                                    <h6>Segunda Instância</h6>
                                    <p class="small text-muted mb-0">
                                        Autoridade máxima do órgão
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="badge bg-success rounded-circle mb-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        3ª
                                    </div>
                                    <h6>Terceira Instância</h6>
                                    <p class="small text-muted mb-0">
                                        CGU (Controladoria-Geral da União)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Perguntas Frequentes -->
        <section id="faq" class="mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary mb-3">
                        <i class="bi bi-question-octagon me-2"></i>
                        Perguntas Frequentes
                    </h2>
                    
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Preciso me identificar para fazer um pedido?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim, é necessária a identificação do requerente. Você pode se identificar com CPF 
                                    ou fazer login através do Gov.br. A identificação é obrigatória por lei e garante 
                                    que a resposta chegue até você.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Qual o prazo para resposta?
                                </button>
                            </h3>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    O prazo é de 20 dias úteis, prorrogável por mais 10 dias mediante justificativa. 
                                    O órgão deve comunicar sobre a prorrogação antes do término do prazo inicial.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Posso fazer quantos pedidos quiser?
                                </button>
                            </h3>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim, não há limite para o número de pedidos. Porém, evite repetir pedidos 
                                    idênticos ou fazer pedidos genéricos que dificultem o atendimento.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Há custo para obter informações?
                                </button>
                            </h3>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    O acesso à informação é gratuito. Podem ser cobrados apenas os custos dos 
                                    serviços de reprodução (cópias, impressões, mídias digitais).
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    O que fazer se meu pedido for negado?
                                </button>
                            </h3>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Você pode interpor recurso em até 10 dias corridos. O recurso será analisado 
                                    por autoridade hierarquicamente superior e você terá uma nova oportunidade 
                                    de obter a informação.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Suporte Técnico -->
        <section id="suporte" class="mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary mb-3">
                        <i class="bi bi-headset me-2"></i>
                        Suporte Técnico
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h4>Canais de Atendimento</h4>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope text-primary me-3"></i>
                                        <div>
                                            <h6 class="mb-0">E-mail</h6>
                                            <small class="text-muted">suporte@orgao.gov.br</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-telephone text-primary me-3"></i>
                                        <div>
                                            <h6 class="mb-0">Telefone</h6>
                                            <small class="text-muted">(xx) xxxx-xxxx</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock text-primary me-3"></i>
                                        <div>
                                            <h6 class="mb-0">Horário</h6>
                                            <small class="text-muted">Segunda a Sexta, 8h às 17h</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <h4>Problemas Técnicos Comuns</h4>
                            <div class="accordion" id="supportAccordion">
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#support1">
                                            Não consigo fazer login
                                        </button>
                                    </h3>
                                    <div id="support1" class="accordion-collapse collapse" data-bs-parent="#supportAccordion">
                                        <div class="accordion-body">
                                            Verifique se está usando o CPF correto, limpe o cache do navegador 
                                            ou tente usar outro navegador. Se persistir, entre em contato conosco.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#support2">
                                            Erro ao enviar pedido
                                        </button>
                                    </h3>
                                    <div id="support2" class="accordion-collapse collapse" data-bs-parent="#supportAccordion">
                                        <div class="accordion-body">
                                            Verifique sua conexão com a internet, certifique-se de que todos os 
                                            campos obrigatórios estão preenchidos e tente novamente.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            Informações do Sistema
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Versão:</strong> E-SIC v2.0</p>
                                <p class="mb-0"><strong>Última Atualização:</strong> <?= date('d/m/Y') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Navegadores Suportados:</strong></p>
                                <p class="mb-0">Chrome, Firefox, Safari, Edge (versões atuais)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchHelp').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const sections = document.querySelectorAll('section');
    
    sections.forEach(section => {
        const text = section.textContent.toLowerCase();
        if (text.includes(searchTerm) || searchTerm === '') {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
});

// Smooth scrolling for navigation links
document.querySelectorAll('.list-group-item-action').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            // Update active state
            document.querySelectorAll('.list-group-item-action').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        }
    });
});

// Print functionality
function imprimirPagina() {
    window.print();
}

// Export PDF functionality
function exportarPDF() {
    // This would typically integrate with a PDF library
    showNotification('Funcionalidade de export PDF será implementada em breve.', 'info');
}

// Notification system
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// Highlight current section in navigation
window.addEventListener('scroll', function() {
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.list-group-item-action');
    
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.getBoundingClientRect().top;
        if (sectionTop <= 100) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
        }
    });
});
</script>

<style>
.sticky-top {
    top: 20px !important;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #0d6efd;
}

.list-group-item-action.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.list-group-item-action:hover {
    background-color: #e9ecef;
}

.list-group-item-action.active:hover {
    background-color: #0b5ed7;
}

@media print {
    .col-lg-3 {
        display: none !important;
    }
    
    .col-lg-9 {
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        page-break-inside: avoid;
    }
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative !important;
        top: 0 !important;
    }
}
</style>