@extends('layout.app')

@section('title', 'Documentos Oficiais')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white mb-0"><i class="fas fa-file-signature mr-2"></i> DOCUMENTOS OFICIAIS</h2>
        @if($canManage)
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fas fa-folder-plus"></i> Nova Categoria
            </button>
        @endif
    </div>

    <div class="row">
        <!-- Coluna da Esquerda: Categorias e Scripts -->
        <div class="col-md-4 mb-4">
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><i class="fas fa-list"></i> ÍNDICE</h5>
                    @if($canManage)
                         <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#createScriptModal" title="Adicionar Documento">
                            <i class="fas fa-plus"></i>
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="scriptsAccordion">
                        @forelse($categories as $category)
                            <div class="accordion-item bg-transparent border-secondary">
                                <h2 class="accordion-header" id="heading{{ $category->id }}">
                                    <button class="accordion-button collapsed bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $category->id }}" aria-expanded="false" aria-controls="collapse{{ $category->id }}">
                                        <i class="fas fa-folder text-warning me-2"></i> {{ $category->name }}
                                        @if($category->role)
                                            <span class="badge bg-secondary ms-2" style="font-size: 0.65rem;">Divisão: {{ $category->role->name }}</span>
                                        @endif
                                    </button>
                                </h2>
                                <div id="collapse{{ $category->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $category->id }}" data-bs-parent="#scriptsAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="list-group list-group-flush">
                                            @forelse($category->scripts as $script)
                                                <button type="button" class="list-group-item list-group-item-action bg-dark text-light border-secondary d-flex justify-content-between align-items-center script-item" 
                                                        data-id="{{ $script->id }}" 
                                                        data-title="{{ $script->title }}" 
                                                        data-date="{{ \Carbon\Carbon::parse($script->updated_at)->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') }}"
                                                        data-ip="{{ $script->ip_address ?? 'Não registrado' }}"
                                                        data-creator="{{ $script->creator ? $script->creator->nickname : 'Sistema' }}"
                                                        data-editor="{{ $script->editor ? $script->editor->nickname : '' }}">
                                                    <span><i class="fas fa-file-alt text-info me-2"></i> {{ $script->title }}</span>
                                                    <!-- Conteúdo invisível para recuperar depois sem quebrar HTML attributes -->
                                                    <div class="d-none script-content-raw">{!! $script->content !!}</div>
                                                </button>
                                            @empty
                                                <div class="p-3 text-muted small">Nenhum documento nesta categoria.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">Ainda não há categorias ou documentos disponíveis.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna da Direita: Conteúdo do Script Selecionado -->
        <div class="col-md-8">
            <div class="card border-secondary text-white" style="background-color: #12141c; min-height: 400px; padding: 20px;">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center bg-transparent">
                    <h5 class="mb-0 text-white" id="scriptDisplayTitle"><i class="fas fa-file-contract"></i> VISUALIZADOR</h5>
                    <!-- <div>
                        <button id="copyScriptBtn" class="btn btn-sm btn-outline-primary" style="display: none;">
                            <i class="fas fa-copy"></i> Copiar Texto
                        </button>
                        <button id="printScriptBtn" class="btn btn-sm btn-outline-light ms-2" style="display: none;" onclick="printDocument()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div> -->
                </div>
                <div class="card-body p-0 position-relative mt-4">
                    <div id="scriptPlaceholder" class="text-center text-muted py-5 w-100">
                        <i class="fas fa-file-signature fs-1 mb-3 opacity-50"></i>
                        <p>Selecione um documento no menu lateral para visualizá-lo.</p>
                    </div>
                    
                    <div id="scriptContentArea" style="display: none; padding-bottom: 20px;">
                        
                        <!-- Folha A4 Oficial -->
                        <div class="document-a4-container" id="printableArea">
                            <div class="document-header text-center mb-4">
                                <!-- <img src="https://i.imgur.com/k9Q6E1p.png" alt="Brasão do Sistema" style="width: 80px; margin-bottom: 10px;"> -->
                                <h4 class="mb-0 text-dark" style="font-weight: bold; text-transform: uppercase;">DEPARTAMENTO DE POLÍCIA</h4>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">Documento Oficial de Registro</p>
                                <hr style="border-top: 2px solid #000; opacity: 1; margin-top: 15px; margin-bottom: 30px;">
                            </div>
                            
                            <!-- Título do Documento -->
                            <h3 class="text-center text-dark mb-5 document-title-display" style="font-weight: bold; font-family: 'Times New Roman', Times, serif;"></h3>

                            <!-- Conteúdo Renderizado -->
                            <div id="scriptDisplayContent" class="document-body text-dark">
                            </div>
                            
                            <div class="document-footer mt-5 pt-5">
                                <div class="text-end mb-4">
                                    <p class="mb-0 text-dark">Brasilia, <span id="documentDateDisplay"></span></p>
                                    <p class="mb-0 text-dark" style="font-size: 0.9rem;">IP: <span id="documentIpDisplay"></span></p>
                                </div>
                                <br><br>
                                <div class="text-center mt-5" style="border-top: 1px solid #000; width: 60%; margin: 0 auto; padding-top: 10px;">
                                    <p class="mb-0 text-dark" style="font-weight: bold; font-family: 'Courier New', Courier, monospace;">ASSINADO DIGITALMENTE</p>
                                    <p class="text-muted mb-0" id="documentSignatureDisplay" style="font-size: 0.9rem; font-style: italic;"></p>
                                    <p class="text-muted mt-1" style="font-size: 0.70rem; font-family: monospace;">S.I.G.O V1.0</p>
                                </div>
                            </div>
                        </div>

                        <!-- Textarea oculta para copiar para área de transferencia se necessário -->
                        <textarea id="hiddenCopyArea" style="position: absolute; left: -9999px;"></textarea>
                        
                        @if($canManage)
                            <div class="mt-4 text-end" id="scriptActionButtons" style="display: none;">
                                <button class="btn btn-warning me-2 edit-script-btn" data-bs-toggle="modal" data-bs-target="#editScriptModal">
                                    <i class="fas fa-edit"></i> Editar Documento
                                </button>
                                <button class="btn btn-outline-danger delete-script-btn" data-bs-toggle="modal" data-bs-target="#deleteScriptModal">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($canManage)
<!-- Modais de Gerenciamento -->

<!-- Modal: Criar Categoria -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('scripts.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="fas fa-folder-plus"></i> Nova Categoria</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome da Categoria</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Restrita a uma Divisão?</label>
                        <select name="role_id" class="form-select bg-dark text-white border-secondary">
                            <option value="">Global (Disponível para todos os membros)</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Selecione uma divisão se quiser criar documentos exclusivos para ela.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição (Opcional)</label>
                        <input type="text" name="description" class="form-control bg-dark text-white border-secondary">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Adicionar Script -->
<div class="modal fade" id="createScriptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-white border-secondary" style="background-color: #1a1e29;">
            <form action="{{ route('scripts.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Novo Documento Oficial</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light text-dark p-4">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Título do Documento</label>
                            <input type="text" name="title" class="form-control" placeholder="Ex: Código Penal Mínimo, Guia de CFSD..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoria</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} {{ $category->role ? '('.$category->role->name.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bold">Corpo do Documento (Normas ABNT via Editor)</label>
                        <textarea name="content" class="summernote" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary" style="background-color: #1a1e29;">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Salvar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ocultar Forms de Update e Delete -->
<form id="deleteScriptForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<div class="modal fade" id="editScriptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content text-white border-secondary" style="background-color: #1a1e29;">
            <form id="editScriptForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Documento Oficial</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light text-dark p-4">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Título do Documento</label>
                            <input type="text" name="title" id="editScriptTitle" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoria</label>
                            <select name="category_id" id="editScriptCategory" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} {{ $category->role ? '('.$category->role->name.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bold">Corpo do Documento</label>
                        <textarea name="content" id="editScriptContent" class="summernote" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary" style="background-color: #1a1e29;">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Atualizar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    function printDocument() {
        var printContents = document.getElementById('printableArea').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        document.body.style.backgroundColor = "white";
        
        window.print();

        document.body.innerHTML = originalContents;
        window.location.reload(); // Recarrega a pagina para voltar ao normal apos impressao
    }

    $(document).ready(function() {
        // Inicializa o Summernote
        $('.summernote').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview']]
            ],
            fontNames: ['Arial', 'Times New Roman', 'Arial Black', 'Comic Sans MS', 'Courier New'],
            fontNamesIgnoreCheck: ['Times New Roman'],
            placeholder: 'Digite o conteúdo do documento aqui seguindo as normas da ABNT...'
        });

        // Lógica de seleção
        const scriptItems = document.querySelectorAll('.script-item');
        const scriptDisplayContent = document.getElementById('scriptDisplayContent');
        const scriptPlaceholder = document.getElementById('scriptPlaceholder');
        const scriptContentArea = document.getElementById('scriptContentArea');
        const copyBtn = document.getElementById('copyScriptBtn');
        const printBtn = document.getElementById('printScriptBtn');
        const actionButtons = document.getElementById('scriptActionButtons');
        
        const docDateDisplay = document.getElementById('documentDateDisplay');
        const docIpDisplay = document.getElementById('documentIpDisplay');
        const docSignatureDisplay = document.getElementById('documentSignatureDisplay');
        const documentTitleDisplay = document.querySelector('.document-title-display');
        const hiddenCopyArea = document.getElementById('hiddenCopyArea');
        
        let currentScriptId = null;
        let currentScriptCategoryId = null;
        let currentRawContent = "";

        scriptItems.forEach(item => {
            item.addEventListener('click', function() {
                scriptItems.forEach(i => i.classList.remove('active', 'bg-primary'));
                this.classList.add('active', 'bg-primary');
                
                const id = this.dataset.id;
                const title = this.dataset.title;
                const rawContent = this.querySelector('.script-content-raw').innerHTML;
                const date = this.dataset.date;
                const ip = this.dataset.ip;
                const creator = this.dataset.creator;
                const editor = this.dataset.editor;
                
                const categoryAccordion = this.closest('.accordion-item');
                const catIdRaw = categoryAccordion.querySelector('.accordion-collapse').id.replace('collapse', '');
                
                currentScriptId = id;
                currentScriptCategoryId = catIdRaw;
                currentRawContent = rawContent;

                documentTitleDisplay.innerText = title;
                scriptDisplayContent.innerHTML = rawContent; // injeta HMTL 
                
                // Set plain text to hidden text area for easiest copy to Habbo
                // Converts common tags to formatting
                let plainText = rawContent
                                 .replace(/<br\s*[\/]?>/gi, "\n")
                                 .replace(/<\/p>/gi, "\n")
                                 .replace(/<\/div>/gi, "\n")
                                 .replace(/<[^>]+>/g, '') // remove outras tags
                                 .replace(/&nbsp;/g, ' '); // decode html entities comum
                hiddenCopyArea.value = plainText.trim();

                docDateDisplay.innerText = date;
                if(docIpDisplay) docIpDisplay.innerText = ip;
                if (editor && editor.trim() !== '') {
                    docSignatureDisplay.innerText = creator.toUpperCase() + ' - EDITADO POR ' + editor.toUpperCase();
                } else {
                    docSignatureDisplay.innerText = creator.toUpperCase();
                }
                
                scriptPlaceholder.style.display = 'none';
                scriptContentArea.style.display = 'block';
                if (copyBtn) copyBtn.style.display = 'inline-block';
                if (printBtn) printBtn.style.display = 'inline-block';
                
                if(actionButtons) {
                    actionButtons.style.display = 'block';
                }
            });
        });

        if(copyBtn) {
            copyBtn.addEventListener('click', function() {
                hiddenCopyArea.select();
                document.execCommand('copy');
                iziToast.success({
                    title: 'COPIADO',
                    message: 'Conteúdo copiado (texto limpo) para a área de transferência!',
                    position: 'topRight'
                });
            });
        }
        
        let loadedEditableContent = "";
        let loadedFullContent = "";

        @if($canManage)
        const editBtns = document.querySelectorAll('.edit-script-btn');
        if(editBtns) {
            editBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if(!currentScriptId) return;
                    document.getElementById('editScriptForm').action = `/scripts/${currentScriptId}`;
                    document.getElementById('editScriptTitle').value = documentTitleDisplay.innerText.trim();
                    document.getElementById('editScriptCategory').value = currentScriptCategoryId;
                    
                    loadedFullContent = currentRawContent;
                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = loadedFullContent;
                    let redacoes = tempDiv.querySelectorAll('.nova-redacao');
                    
                    if (redacoes.length > 0) {
                        loadedEditableContent = redacoes[redacoes.length - 1].innerHTML;
                    } else {
                        loadedEditableContent = loadedFullContent;
                    }
                    
                    // Seta valor no Summernote
                    $('#editScriptContent').summernote('code', loadedEditableContent);
                });
            });
        }

        const editScriptForm = document.getElementById('editScriptForm');
        if (editScriptForm) {
            editScriptForm.addEventListener('submit', function(e) {
                let currentHtmlInEditor = $('#editScriptContent').summernote('code');
                
                if (currentHtmlInEditor.trim() !== loadedEditableContent.trim()) {
                    let fullHtml = "<div class='revogado' style='text-decoration: line-through; opacity: 0.6;'>" 
                                 + loadedFullContent 
                                 + "</div><div class='nova-redacao mt-4'>" 
                                 + currentHtmlInEditor 
                                 + "</div>";
                    $('#editScriptContent').val(fullHtml);
                } else {
                    $('#editScriptContent').val(loadedFullContent);
                }
            });
        }

        const deleteBtns = document.querySelectorAll('.delete-script-btn');
        if(deleteBtns) {
            deleteBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if(confirm("Deseja realmente excluir este documento?")) {
                        const form = document.getElementById('deleteScriptForm');
                        form.action = `/scripts/${currentScriptId}`;
                        form.submit();
                    }
                });
            });
        }
        @endif
    });
</script>
<style>
    .accordion-button:not(.collapsed) {
        background-color: #1a1e29 !important;
        color: #fff;
    }
    .script-item {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .script-item:hover {
        background-color: #2c3244 !important;
    }
    .script-item.active {
        border-right: 4px solid var(--primary-color) !important;
    }
    
    /* ABNT Official Document Styling */
    .document-a4-container {
        background-color: #ffffff;
        color: #000000;
        padding: 60px 80px;
        margin: 0 auto;
        border-radius: 2px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        min-height: 842px; /* Proporcao A4 a 72dpi */
        max-width: 794px;
        font-family: 'Times New Roman', Times, serif;
    }
    .document-body {
        font-size: 12pt;
        line-height: 1.5;
        text-align: justify;
        min-height: 300px;
    }
    .document-body p {
        margin-bottom: 1.5em;
        text-indent: 1.25cm; /* Estilo padrao ABNT para paragrafos */
    }
    .document-body ul, .document-body ol {
        margin-bottom: 1.5em;
    }
    
    /* Summernote customization for Bootstrap 5 & Dark mode overlay */
    .note-editor.note-frame {
        background-color: #fff !important;
        color: #000 !important;
    }
    .note-editable {
        background-color: #fff !important;
        color: #000 !important;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.5;
    }
    .note-btn {
        color: #333 !important;
    }
    .note-toolbar {
        background-color: #f8f9fa !important;
    }
</style>
@endpush
