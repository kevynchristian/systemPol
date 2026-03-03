@extends('layout.app')

@section('title', 'Aplicar Treinamento')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="REGISTRO DE TREINAMENTO">REGISTRO DE TREINAMENTO</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-tactical">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-tactical">
            <ul class="mb-0 text-left">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-tactical shadow mb-4">
        <div class="card-header-tactical py-3">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-chalkboard-teacher"></i> DIÁRIO DE CLASSE (S.I.G.O.)</h6>
        </div>
        <div class="card-body-tactical">
            <form action="{{ route('treinamentos.store') }}" method="POST">
                @csrf
                <div class="form-group row mb-4">
                    <label for="aluno_id" class="col-sm-2 col-form-label text-right">ALUNO (OPERADOR):</label>
                    <div class="col-sm-10">
                        <select name="aluno_id[]" id="aluno_id" class="form-control input-tactical select2-multiple" multiple="multiple" required>
                            <option value=""></option>
                            @foreach($alunos as $aluno)
                                <option value="{{ $aluno->id }}" {{ old('aluno_id') == $aluno->id ? 'selected' : '' }}>
                                    {{ $aluno->nickname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="aula_id" class="col-sm-2 col-form-label text-right">TIPO DE FORMAÇÃO:</label>
                    <div class="col-sm-10">
                        <select name="aula_id" id="aula_id" class="form-control input-tactical" required>
                            <option value="">Selecione a Aula...</option>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula->id }}" {{ old('aula_id') == $aula->id ? 'selected' : '' }}>
                                    {{ $aula->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($aulas->isEmpty())
                            <small class="text-danger mt-1">Nenhuma aula disponível no banco de dados.</small>
                        @endif
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label class="col-sm-2 col-form-label text-right">AVALIAÇÕES:</label>
                    <div class="col-sm-10">
                        <div id="alunos-grading-container" style="display: none;">
                            <!-- Forms dinâmicos serão inseridos aqui via JS -->
                        </div>
                        <div id="alunos-grading-placeholder" class="text-muted font-italic p-3" style="border: 1px dashed rgba(0,255,204,0.3);">
                            Selecione um ou mais alunos acima para liberar o diário de classe individual de cada um.
                        </div>
                    </div>
                </div>

                <div class="form-group row mt-5">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary-tactical btn-lg w-50">
                            <i class="fas fa-file-signature"></i> CADASTRAR RELATÓRIO NO SISTEMA
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .input-tactical {
        background-color: rgba(0,0,0,0.5);
        color: var(--text-color);
        border: 1px solid rgba(0, 255, 204, 0.3);
        border-radius: 0;
    }
    .input-tactical:focus {
        background-color: rgba(0, 0, 0, 0.8);
        border-color: var(--primary-color);
        color: #fff;
        box-shadow: 0 0 5px rgba(0, 255, 204, 0.5);
    }
    .input-tactical option {
        background-color: #1a1a2e; /* Escuro para o dropdown não ficar branco */
    }

    /* Select2 Tactical Theme */
    .select2-container--default .select2-selection--multiple {
        background-color: rgba(0,0,0,0.5) !important;
        border: 1px solid rgba(0, 255, 204, 0.3) !important;
        border-radius: 0 !important;
        min-height: 38px !important;
        color: var(--text-color) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(0, 255, 204, 0.1) !important;
        border: 1px solid var(--primary-color) !important;
        color: var(--primary-color) !important;
        border-radius: 0 !important;
        margin-top: 6px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--primary-color) !important;
        margin-right: 5px !important;
        border-right: 1px solid rgba(0, 255, 204, 0.3) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff !important;
        background-color: transparent !important;
    }
    .select2-dropdown {
        background-color: #1a1a2e !important;
        border: 1px solid var(--primary-color) !important;
        border-radius: 0 !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: rgba(255, 255, 255, 0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary-color) !important;
        color: #000 !important;
    }
    .select2-search--inline .select2-search__field {
        color: #ffffff !important;
    }
    .select2-search--dropdown .select2-search__field {
        background-color: rgba(0,0,0,0.5) !important;
        border: 1px solid rgba(0, 255, 204, 0.3) !important;
        color: var(--text-color) !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary-color);
        box-shadow: 0 0 5px rgba(0, 255, 204, 0.5);
    }
</style>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const alunoSelect = $('#aluno_id');
        const aulaSelect = $('#aula_id');
        const container = $('#alunos-grading-container');
        const placeholder = $('#alunos-grading-placeholder');
        
        // Dados provindos do Backend via JSON
        const aulasDados = {!! $aulasJson !!};
        const alunosDados = {!! $alunosJson !!};
        
        console.log("Aulas Debug:", aulasDados);
        console.log("Alunos Debug:", alunosDados);
        
        // Track generated cards
        const generatedForms = new Set();
        let currentTargetPatenteId = null; 

        // Inicializa Select2
        alunoSelect.select2({
            placeholder: "Pesquise e selecione os operadores...",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() { return "Nenhum operador encontrado"; }
            }
        });

        // Função para atualizar as Aulas disponíveis com base nos alunos selecionados
        function atualizarAulasPermitidas(selectedAlunos) {
            // Se não tem aluno selecionado, reseta as opções de aula (mostra tudo que o instrutor pode dar)
            if (selectedAlunos.length === 0) {
                aulaSelect.empty().append('<option value="">Selecione a Aula...</option>');
                aulasDados.forEach(aula => {
                    aulaSelect.append(new Option(aula.name, aula.id));
                });
                return;
            }

            // Descobre a patente alvo com base no primeiro aluno (todos devem ter a mesma patente, garantido pelo onChange)
            const firstAlunoId = parseInt(selectedAlunos[0]);
            const alunoObj = alunosDados.find(a => a.id === firstAlunoId);
            const patenteAlvo = alunoObj ? alunoObj.patente_id : null;

            aulaSelect.empty().append('<option value="">Selecione a Aula...</option>');

            aulasDados.forEach(aula => {
                let patentePermitida = true;
                let preRequisitoCumprido = true;

                // 1. A Patente do aluno permite cursar esta aula?
                // Se array estiver vazio, significa que a Aula é aberta para todas as patentes
                if (aula.roles_allowed.length > 0 && patenteAlvo !== null) {
                    // Força a conversão para número para evitar bugs de tipo estrito (1 !== "1")
                    const isAllowed = aula.roles_allowed.some(roleId => parseInt(roleId) === parseInt(patenteAlvo));
                    if (!isAllowed) {
                        patentePermitida = false;
                    }
                }

                // 2. Todos os alunos selecionados têm o pré-requisito?
                if (aula.prerequisite_id !== null) {
                    selectedAlunos.forEach(alunoIdStr => {
                        const sAluno = alunosDados.find(a => parseInt(a.id) === parseInt(alunoIdStr));
                        if (sAluno) {
                            const hasPrerequisite = sAluno.aulas_aprovadas.some(aulaId => parseInt(aulaId) === parseInt(aula.prerequisite_id));
                            if (!hasPrerequisite) {
                                preRequisitoCumprido = false;
                            }
                        }
                    });
                }

                // Se passou nos 2 filtros, desenha a aula no Select
                if (patentePermitida && preRequisitoCumprido) {
                    aulaSelect.append(new Option(aula.name, aula.id));
                }
            });
        }

        // Lidar com a seleção de alunos
        alunoSelect.on('change', function(e) {
            const selectedOptions = $(this).find('option:selected');
            const selectedIds = [];
            let invalidSelection = false;
            
            selectedOptions.each(function() {
                const id = parseInt($(this).val());
                if(isNaN(id)) return;

                const alunoObj = alunosDados.find(a => a.id === id);
                
                // --- LÓGICA DE BLOQUEIO DE PATENTE MISTA ---
                if (currentTargetPatenteId === null) {
                    // Primeiro aluno selecionado, define a patente alvo
                    currentTargetPatenteId = alunoObj.patente_id;
                    selectedIds.push(id);
                } else {
                    // Já existe aluno selecionado, valida se este novo bate com a patente
                    if (alunoObj.patente_id !== currentTargetPatenteId) {
                        invalidSelection = true;
                        // Deseleciona este aluno inválido visualmente
                        $(this).prop('selected', false); 
                        iziToast.error({
                            title: 'OPERAÇÃO BLOQUEADA',
                            message: `Não é possível misturar patentes diferentes na mesma turma. ${alunoObj.nickname} é de outra patente!`,
                            position: 'topRight'
                        });
                    } else {
                        selectedIds.push(id);
                    }
                }
            });

            // Se removeu todo mundo, zera a patente alvo
            if (selectedIds.length === 0) {
                currentTargetPatenteId = null;
            }

            // Se algo foi rejeitado, engatilha o refresh do select2 sem loopar
            if (invalidSelection) {
                alunoSelect.trigger('change.select2');
            }

            // --- RENDERIZAR FORMS E ATUALIZAR AULAS ---
            const currentSelectedSet = new Set(selectedIds);

            selectedIds.forEach(id => {
                const alunoObj = alunosDados.find(a => a.id === id);
                const name = alunoObj.nickname;
                
                if (!generatedForms.has(id)) {
                    generatedForms.add(id);
                    const html = `
                        <div class="card-tactical mb-3 grading-card" id="grading-card-${id}" style="background-color: rgba(0,0,0,0.4); border: 1px solid rgba(0,255,204,0.3);">
                            <div class="card-header-tactical py-2 d-flex align-items-center" style="border-bottom: 1px dashed rgba(0,255,204,0.3);">
                                <img src="https://www.habbo.com.br/habbo-imaging/avatarimage?user=${name}&direction=2&head_direction=3&gesture=sml&headonly=1" width="25" class="rounded-circle mr-2 bg-dark" onerror="this.src='https://i.imgur.com/k9Q6E1p.png';">
                                <strong>AVALIAÇÃO DE: ${name} <span class="text-muted" style="font-size: 0.7rem;">(${alunoObj.patente_name})</span></strong>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="text-white mb-1"><small>Parecer Final</small></label>
                                            <select name="status[${id}]" class="form-control input-tactical py-1" required>
                                                <option value="">Selecione...</option>
                                                <option value="aprovado">APROVADO</option>
                                                <option value="reprovado">REPROVADO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group mb-0">
                                            <label class="text-white mb-1"><small>Relatório / Observações</small></label>
                                            <textarea name="observacao[${id}]" class="form-control input-tactical" rows="2" placeholder="Feedback sobre ${name}..." required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(html);
                }
            });

            // Remover forms de alunos desmarcados
            generatedForms.forEach(id => {
                if (!currentSelectedSet.has(id)) {
                    $(`#grading-card-${id}`).remove();
                    generatedForms.delete(id);
                }
            });
            
            // Mostrar ou esconder o container de Diário
            if(selectedIds.length > 0) {
                container.show();
                placeholder.hide();
            } else {
                container.hide();
                placeholder.show();
            }

            // Atualiza o select de Formação baseado nas regras de negócio (roles + requisitos)
            atualizarAulasPermitidas(selectedIds);
        });

        // Executar uma primeira vez para limpar se necessário (on load)
        atualizarAulasPermitidas([]);
    });
</script>
@endpush
@endsection
