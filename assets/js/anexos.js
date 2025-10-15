/**
 * E-SIC - Gerenciador de Anexos
 * Componente para upload, listagem e download de anexos
 */

class ESICAnexos {
    constructor(tipoEntidade, entidadeId, containerSelector) {
        this.tipoEntidade = tipoEntidade; // 'pedido' ou 'recurso'
        this.entidadeId = entidadeId;
        this.container = document.querySelector(containerSelector);
        this.apiUrl = 'api/anexos.php';
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        this.allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'odt', 'xls', 'xlsx'];
        
        this.init();
    }
    
    /**
     * Inicializar componente
     */
    init() {
        this.renderInterface();
        this.setupEventListeners();
        this.carregarAnexos();
    }
    
    /**
     * Renderizar interface
     */
    renderInterface() {
        this.container.innerHTML = `
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-paperclip"></i> Anexos
                        </h6>
                        <button class="btn btn-sm btn-primary" onclick="esicAnexos.mostrarFormUpload()">
                            <i class="bi bi-upload"></i> Adicionar Anexo
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Form de Upload (inicialmente oculto) -->
                    <div id="formUpload" style="display: none;" class="mb-3">
                        <form id="uploadForm">
                            <div class="mb-3">
                                <label for="arquivo" class="form-label">Selecionar Arquivo</label>
                                <input type="file" class="form-control" id="arquivo" name="arquivo" required>
                                <div class="form-text">
                                    Tamanho máximo: 10MB. Formatos permitidos: ${this.allowedExtensions.join(', ').toUpperCase()}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição (opcional)</label>
                                <input type="text" class="form-control" id="descricao" name="descricao" 
                                       placeholder="Ex: Documento de identidade">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Enviar
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="esicAnexos.ocultarFormUpload()">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Lista de Anexos -->
                    <div id="listaAnexos">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        const form = document.getElementById('uploadForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleUpload(e));
        }
        
        const fileInput = document.getElementById('arquivo');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => this.validarArquivo(e));
        }
    }
    
    /**
     * Validar arquivo antes do upload
     */
    validarArquivo(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const errors = [];
        
        // Validar tamanho
        if (file.size > this.maxFileSize) {
            errors.push('Arquivo muito grande (máximo 10MB)');
        }
        
        // Validar extensão
        const extension = file.name.split('.').pop().toLowerCase();
        if (!this.allowedExtensions.includes(extension)) {
            errors.push(`Extensão .${extension} não permitida`);
        }
        
        if (errors.length > 0) {
            ESICApp.showToast(errors.join('<br>'), 'danger');
            event.target.value = '';
            return false;
        }
        
        // Mostrar preview
        this.mostrarPreview(file);
        return true;
    }
    
    /**
     * Mostrar preview do arquivo
     */
    mostrarPreview(file) {
        const previewHtml = `
            <div class="alert alert-info mt-2" id="filePreview">
                <strong>Arquivo selecionado:</strong><br>
                <i class="bi bi-file-earmark"></i> ${file.name}<br>
                <small>Tamanho: ${this.formatarTamanho(file.size)}</small>
            </div>
        `;
        
        const existingPreview = document.getElementById('filePreview');
        if (existingPreview) {
            existingPreview.remove();
        }
        
        document.getElementById('uploadForm').insertAdjacentHTML('beforeend', previewHtml);
    }
    
    /**
     * Upload de arquivo
     */
    async handleUpload(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        formData.append('action', 'upload');
        formData.append('tipo_entidade', this.tipoEntidade);
        formData.append('entidade_id', this.entidadeId);
        
        const submitBtn = event.target.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                ESICApp.showToast('Arquivo enviado com sucesso!', 'success');
                event.target.reset();
                this.ocultarFormUpload();
                this.carregarAnexos();
            } else {
                ESICApp.showToast(data.message || 'Erro ao enviar arquivo', 'danger');
            }
        } catch (error) {
            ESICApp.showToast('Erro na requisição: ' + error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-upload"></i> Enviar';
        }
    }
    
    /**
     * Carregar lista de anexos
     */
    async carregarAnexos() {
        const listaContainer = document.getElementById('listaAnexos');
        
        try {
            const response = await fetch(
                `${this.apiUrl}?action=listar&tipo_entidade=${this.tipoEntidade}&entidade_id=${this.entidadeId}`
            );
            
            const data = await response.json();
            
            if (data.success) {
                if (data.data.length === 0) {
                    listaContainer.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Nenhum anexo encontrado</p>
                        </div>
                    `;
                } else {
                    this.renderizarLista(data.data);
                }
            } else {
                listaContainer.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message || 'Erro ao carregar anexos'}
                    </div>
                `;
            }
        } catch (error) {
            listaContainer.innerHTML = `
                <div class="alert alert-danger">
                    Erro na requisição: ${error.message}
                </div>
            `;
        }
    }
    
    /**
     * Renderizar lista de anexos
     */
    renderizarLista(anexos) {
        const listaContainer = document.getElementById('listaAnexos');
        
        const html = anexos.map(anexo => `
            <div class="anexo-item border-bottom py-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        ${this.getIconeArquivo(anexo.tipo_mime)}
                    </div>
                    <div class="col">
                        <h6 class="mb-1">${anexo.nome_original}</h6>
                        <small class="text-muted">
                            ${this.formatarTamanho(anexo.tamanho)} • 
                            ${this.formatarData(anexo.data_upload)}
                            ${anexo.descricao ? `<br>${anexo.descricao}` : ''}
                        </small>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="esicAnexos.downloadAnexo(${anexo.id}, '${anexo.nome_original}')">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="esicAnexos.deletarAnexo(${anexo.id}, '${anexo.nome_original}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
        listaContainer.innerHTML = html;
    }
    
    /**
     * Download de anexo
     */
    downloadAnexo(anexoId, nomeOriginal) {
        const url = `${this.apiUrl}?action=download&anexo_id=${anexoId}`;
        
        // Criar link temporário para download
        const a = document.createElement('a');
        a.href = url;
        a.download = nomeOriginal;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        ESICApp.showToast('Download iniciado...', 'info');
    }
    
    /**
     * Deletar anexo
     */
    async deletarAnexo(anexoId, nomeOriginal) {
        if (!confirm(`Confirma a exclusão do arquivo "${nomeOriginal}"?`)) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'deletar');
        formData.append('anexo_id', anexoId);
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                ESICApp.showToast('Anexo deletado com sucesso', 'success');
                this.carregarAnexos();
            } else {
                ESICApp.showToast(data.message || 'Erro ao deletar anexo', 'danger');
            }
        } catch (error) {
            ESICApp.showToast('Erro na requisição: ' + error.message, 'danger');
        }
    }
    
    /**
     * Mostrar formulário de upload
     */
    mostrarFormUpload() {
        document.getElementById('formUpload').style.display = 'block';
    }
    
    /**
     * Ocultar formulário de upload
     */
    ocultarFormUpload() {
        document.getElementById('formUpload').style.display = 'none';
        document.getElementById('uploadForm').reset();
        const preview = document.getElementById('filePreview');
        if (preview) preview.remove();
    }
    
    /**
     * Obter ícone do arquivo baseado no MIME type
     */
    getIconeArquivo(mimeType) {
        const iconMap = {
            'application/pdf': { icon: 'file-pdf', color: 'danger' },
            'application/msword': { icon: 'file-word', color: 'primary' },
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': { icon: 'file-word', color: 'primary' },
            'application/vnd.ms-excel': { icon: 'file-excel', color: 'success' },
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': { icon: 'file-excel', color: 'success' },
            'image/jpeg': { icon: 'file-image', color: 'info' },
            'image/png': { icon: 'file-image', color: 'info' },
            'text/plain': { icon: 'file-text', color: 'secondary' }
        };
        
        const config = iconMap[mimeType] || { icon: 'file-earmark', color: 'secondary' };
        
        return `<i class="bi bi-${config.icon} text-${config.color}" style="font-size: 2rem;"></i>`;
    }
    
    /**
     * Formatar tamanho de arquivo
     */
    formatarTamanho(bytes) {
        if (bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + ' GB';
        } else if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return bytes + ' bytes';
        }
    }
    
    /**
     * Formatar data
     */
    formatarData(dataString) {
        const data = new Date(dataString);
        return data.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Instância global (será inicializada quando necessário)
let esicAnexos;