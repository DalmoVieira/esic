/**
 * E-SIC Rio Claro - Scripts Personalizados
 * Funcionalidades JavaScript para melhorar a experiência do usuário
 */

// Aguarda o carregamento completo da página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidades
    initFormValidation();
    initTooltips();
    initAnimations();
    initSmoothScroll();
    initBackToTop();
    initLogoFallback();
    
    console.log('E-SIC Rio Claro: Sistema carregado com sucesso!');
});

/**
 * Validação aprimorada de formulários
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Encontrar o primeiro campo inválido e focar nele
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            
            form.classList.add('was-validated');
        });
        
        // Validação em tempo real
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });
    });
}

/**
 * Inicializar tooltips do Bootstrap
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Animações de entrada para elementos
 */
function initAnimations() {
    // Observador de interseção para animar elementos quando entram na tela
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, { threshold: 0.1 });
    
    // Observar cards e elementos importantes
    const animatedElements = document.querySelectorAll('.card, .alert, .btn-group');
    animatedElements.forEach(el => observer.observe(el));
}

/**
 * Scroll suave para âncoras
 */
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Botão "Voltar ao Topo"
 */
function initBackToTop() {
    // Criar botão se não existir
    if (!document.getElementById('backToTop')) {
        const backToTopBtn = document.createElement('button');
        backToTopBtn.id = 'backToTop';
        backToTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
        backToTopBtn.className = 'btn btn-primary position-fixed';
        backToTopBtn.style.cssText = `
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        document.body.appendChild(backToTopBtn);
        
        // Mostrar/esconder baseado no scroll
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'block';
                backToTopBtn.classList.add('fade-in');
            } else {
                backToTopBtn.style.display = 'none';
            }
        });
        
        // Ação do clique
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * Formatação automática de campos
 */
function formatCPF(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    input.value = value;
}

function formatCNPJ(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1/$2');
    value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    input.value = value;
}

function formatPhone(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    } else {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }
    input.value = value;
}

/**
 * Busca em tempo real para tabelas
 */
function initTableSearch(tableId, searchId) {
    const searchInput = document.getElementById(searchId);
    const table = document.getElementById(tableId);
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });
    }
}

/**
 * Loading spinner para formulários
 */
function showLoading(buttonElement, text = 'Processando...') {
    const originalText = buttonElement.innerHTML;
    buttonElement.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        ${text}
    `;
    buttonElement.disabled = true;
    
    return function() {
        buttonElement.innerHTML = originalText;
        buttonElement.disabled = false;
    };
}

/**
 * Notificações toast personalizadas
 */
function showToast(message, type = 'info', duration = 5000) {
    // Criar container de toasts se não existir
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '1055';
        document.body.appendChild(toastContainer);
    }
    
    // Criar toast
    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast" role="alert">
            <div class="toast-header">
                <i class="bi bi-${getToastIcon(type)} text-${type} me-2"></i>
                <strong class="me-auto">E-SIC Rio Claro</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: duration });
    toast.show();
    
    // Remover após esconder
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

function getToastIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Copiar texto para a área de transferência
 */
function copyToClipboard(text, successMessage = 'Copiado!') {
    navigator.clipboard.writeText(text).then(function() {
        showToast(successMessage, 'success', 3000);
    }).catch(function() {
        // Fallback para navegadores mais antigos
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast(successMessage, 'success', 3000);
    });
}

/**
 * Máscara para campos de entrada
 */
function applyMask(input, mask) {
    input.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        let maskedValue = '';
        let valueIndex = 0;
        
        for (let i = 0; i < mask.length && valueIndex < value.length; i++) {
            if (mask[i] === '9') {
                maskedValue += value[valueIndex];
                valueIndex++;
            } else {
                maskedValue += mask[i];
            }
        }
        
        this.value = maskedValue;
    });
}

/**
 * Verificar conectividade
 */
function checkConnection() {
    return navigator.onLine;
}

// Event listeners globais
window.addEventListener('online', function() {
    showToast('Conexão restaurada', 'success');
});

window.addEventListener('offline', function() {
    showToast('Conexão perdida. Verifique sua internet.', 'warning');
});

/**
 * Fallback para logos que não carregam
 */
function initLogoFallback() {
    const logos = document.querySelectorAll('img[alt*="Logo"]');
    
    logos.forEach(logo => {
        // Se o logo não carregar, criar um texto de fallback
        logo.addEventListener('error', function() {
            const fallback = document.createElement('div');
            fallback.className = 'logo-fallback d-inline-flex align-items-center';
            fallback.style.cssText = `
                background: linear-gradient(45deg, #0d47a1, #1565c0);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 8px;
                font-weight: bold;
                font-size: 0.9rem;
                margin-right: 0.5rem;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            `;
            fallback.textContent = 'RIO CLARO';
            
            this.parentNode.insertBefore(fallback, this);
            this.style.display = 'none';
        });
        
        // Verificar se o logo já falhou ao carregar
        if (logo.complete && logo.naturalHeight === 0) {
            logo.dispatchEvent(new Event('error'));
        }
    });
}

// Exportar funções para uso global
window.ESICApp = {
    formatCPF,
    formatCNPJ,
    formatPhone,
    showToast,
    copyToClipboard,
    showLoading,
    initTableSearch,
    applyMask,
    checkConnection,
    initLogoFallback
};