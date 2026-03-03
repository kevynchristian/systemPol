@extends('layout.app')

@section('title', 'Promoções e Rebaixamentos')

@push('styles')
@endpush

@section('content')
<div class="page-header">
    <h1 class="glitch" data-text="PROMOÇÕES DE EFETIVO">PROMOÇÕES DE EFETIVO</h1>
    <p>Promova militares subordinados. Seu teto de promoção atual é: <strong>{{ $ceilingPatente->name ?? 'Nenhum' }}</strong>.</p>
</div>

<div class="row">
    {{-- Coluna dos Formulários de Ação (Tabs) --}}
    <div class="col-lg-4 mb-4">
        <div class="card-tactical">
            <div class="card-header-tactical p-0 border-bottom-0">
                <ul class="nav nav-tabs nav-fill tactical-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="promocao-tab" data-bs-toggle="tab" data-bs-target="#promocao" type="button" role="tab" aria-controls="promocao" aria-selected="true">
                            <i class="fas fa-angle-double-up text-success"></i> PROMOVER
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rebaixamento-tab" data-bs-toggle="tab" data-bs-target="#rebaixamento" type="button" role="tab" aria-controls="rebaixamento" aria-selected="false">
                            <i class="fas fa-angle-double-down text-warning"></i> REBAIXAR
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="exoneracao-tab" data-bs-toggle="tab" data-bs-target="#exoneracao" type="button" role="tab" aria-controls="exoneracao" aria-selected="false">
                            <i class="fas fa-times-circle text-danger"></i> EXONERAR
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body-tactical pt-4">
                <div class="tab-content" id="myTabContent">
                    
                    {{-- TAB 1: PROMOÇÃO --}}
                    <div class="tab-pane fade show active" id="promocao" role="tabpanel" aria-labelledby="promocao-tab">
                        @if(!$ceilingPatente)
                            <div class="alert alert-danger mb-0"><i class="fas fa-lock"></i> Você não possui permissão para promover.</div>
                        @else
                            <form id="promocao-form" action="{{ route('admin.promocoes.store') }}" method="POST">
                                @csrf
                                <div class="form-group row mb-4 align-items-center">
                                    <label for="user_id" class="col-sm-12 col-form-label mb-2 text-success"><i class="fas fa-crosshairs"></i> MILITAR SUBORDINADO (ALVO):</label>
                                    <div class="col-sm-12">
                                        <select name="user_id" id="user_id" class="form-control input-tactical select2-funcoes" style="width: 100%;" required>
                                            <option value="">Selecione o Militar...</option>
                                            @foreach($subordinados as $sub)
                                                <option value="{{ $sub->id }}" data-hierarquia="{{ $sub->highest_patente ? $sub->highest_patente->hierarquia : 0 }}">
                                                    {{ $sub->nickname }} (Atual: {{ $sub->highest_patente_nome }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4 align-items-center">
                                    <label for="new_role_id" class="col-sm-12 col-form-label mb-2">NOVA PATENTE OFICIAL:</label>
                                    <div class="col-sm-12">
                                        <select name="new_role_id" id="new_role_id" class="form-control input-tactical select2-funcoes" style="width: 100%;" required disabled>
                                            <option value="">Aguardando seleção do alvo...</option>
                                            @foreach($patentesDisponiveis as $patente)
                                                <option value="{{ $patente->id }}">
                                                    {{ $patente->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="new_role_id_hidden" id="new_role_id_hidden">
                                    </div>
                                </div>

                                <div class="form-group row mb-4 align-items-center">
                                    <label for="description" class="col-sm-12 col-form-label mb-2">DESCRIÇÃO DA PROMOÇÃO:</label>
                                    <div class="col-sm-12">
                                        <textarea name="description" id="description" class="form-control input-tactical" rows="3" placeholder="Informe o motivo da promoção..." required></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary-tactical w-100" id="btn-promover" disabled style="background-color: rgba(40, 167, 69, 0.2); border-color: #28a745; color: #28a745;">
                                    <i class="fas fa-check-circle"></i> APLICAR PROMOÇÃO
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- TAB 2: REBAIXAMENTO --}}
                    <div class="tab-pane fade" id="rebaixamento" role="tabpanel" aria-labelledby="rebaixamento-tab">
                        @if(!isset($demotionCeiling))
                            <div class="alert alert-danger mb-0"><i class="fas fa-lock"></i> Você não possui permissão para rebaixar.</div>
                        @else
                            <form id="rebaixamento-form" action="{{ route('admin.promocoes.demote') }}" method="POST">
                                @csrf
                                <div class="form-group row mb-4 align-items-center">
                                    <label for="user_id_demote" class="col-sm-12 col-form-label mb-2 text-warning"><i class="fas fa-crosshairs"></i> MILITAR SUBORDINADO (ALVO):</label>
                                    <div class="col-sm-12">
                                        <select name="user_id" id="user_id_demote" class="form-control input-tactical select2-funcoes" style="width: 100%;" required>
                                            <option value="">Selecione o Militar...</option>
                                            @foreach($subordinadosDemote as $sub)
                                                <option value="{{ $sub->id }}" data-hierarquia="{{ $sub->highest_patente ? $sub->highest_patente->hierarquia : 0 }}">
                                                    {{ $sub->nickname }} (Atual: {{ $sub->highest_patente_nome }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4 align-items-center">
                                    <label for="prev_role_id" class="col-sm-12 col-form-label mb-2">REDUZIR PARA A PATENTE:</label>
                                    <div class="col-sm-12">
                                        <select name="prev_role_id" id="prev_role_id" class="form-control input-tactical select2-funcoes" style="width: 100%;" required disabled>
                                            <option value="">Aguardando seleção do alvo...</option>
                                            @foreach($patentesDisponiveis as $patente)
                                                <option value="{{ $patente->id }}">
                                                    {{ $patente->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4 align-items-center">
                                    <label for="description_demote" class="col-sm-12 col-form-label mb-2">DESCRIÇÃO DO REBAIXAMENTO:</label>
                                    <div class="col-sm-12">
                                        <textarea name="description" id="description_demote" class="form-control input-tactical" rows="3" placeholder="Informe o motivo disciplinar..." required></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary-tactical w-100" id="btn-rebaixar" disabled style="background-color: rgba(255, 193, 7, 0.2); border-color: #ffc107; color: #ffc107;">
                                    <i class="fas fa-exclamation-triangle"></i> APLICAR REBAIXAMENTO
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- TAB 3: EXONERAÇÃO --}}
                    <div class="tab-pane fade" id="exoneracao" role="tabpanel" aria-labelledby="exoneracao-tab">
                        @if(!isset($exonerationCeiling))
                            <div class="alert alert-danger mb-0"><i class="fas fa-lock"></i> Você não possui permissão para exonerar militares.</div>
                        @else
                            <form id="exoneracao-form" action="{{ route('admin.promocoes.exonerate') }}" method="POST">
                                @csrf
                                <div class="alert alert-danger text-center mb-4" style="background: rgba(220, 53, 69, 0.1); border: 1px solid #dc3545; color: #ff6b6b; font-size: 0.85rem;">
                                    <i class="fas fa-radiation fa-2x mb-2 d-block"></i>
                                    <strong>Atenção:</strong> A exoneração removerá todas as patentes oficiais do alvo, transformando-o em Civil/Inativo. Esta ação é severa.
                                </div>

                                <div class="form-group row mb-4 align-items-center">
                                    <label for="user_id_exo" class="col-sm-12 col-form-label mb-2 text-danger"><i class="fas fa-crosshairs"></i> MILITAR ALVO (EXONERAÇÃO):</label>
                                    <div class="col-sm-12">
                                        <select name="user_id" id="user_id_exo" class="form-control input-tactical select2-funcoes" style="width: 100%;" required>
                                            <option value="">Selecione o Militar...</option>
                                            @foreach($subordinadosExonerate as $sub)
                                                <option value="{{ $sub->id }}" data-hierarquia="{{ $sub->highest_patente ? $sub->highest_patente->hierarquia : 0 }}">
                                                    {{ $sub->nickname }} (Atual: {{ $sub->highest_patente_nome }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4 align-items-center">
                                    <label for="description_exo" class="col-sm-12 col-form-label mb-2">DESCRIÇÃO DA EXONERAÇÃO:</label>
                                    <div class="col-sm-12">
                                        <textarea name="description" id="description_exo" class="form-control input-tactical" rows="3" placeholder="Informe o motivo da destituição..." required></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary-tactical w-100" id="btn-exonerar" disabled style="background-color: rgba(220, 53, 69, 0.2); border-color: #dc3545; color: #ff6b6b;">
                                    <i class="fas fa-user-times"></i> APLICAR EXONERAÇÃO
                                </button>
                            </form>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    {{-- Coluna da Tabela de Subordinados --}}
    <div class="col-lg-8 mb-4">
        <div class="card-tactical">
            <div class="card-header-tactical">
                <i class="fas fa-users mr-1"></i>
                MILITARES ELEGÍVEIS (SEU COMANDO)
            </div>
            <div class="card-body-tactical p-0">
            <div id="promotion-ceiling" data-hierarquia="{{ $ceilingPatente->hierarquia ?? 0 }}" style="display:none;"></div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover table-striped mb-0 text-center align-middle m-0">
                        <thead class="text-uppercase" style="background: rgba(0,0,0,0.5); border-bottom: 2px solid var(--primary-color);">
                            <tr>
                                <th width="15%">AVATAR</th>
                                <th width="35%" class="text-start">NICKNAME</th>
                                <th width="25%">PATENTE ATUAL</th>
                                <th width="25%">AÇÃO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subordinados as $aluno)
                            <tr>
                                <td>
                                    <div class="avatar-container outline mx-auto" style="width: 45px; height: 45px;">
                                        <img src="https://www.habbo.com.br/habbo-imaging/avatarimage?user={{ $aluno->nickname  }}&direction=2&head_direction=3&gesture=sml&headonly=1" alt="{{ $aluno->nickname }}" style="margin-top: -15px;">

                                    </div>
                                </td>
                                <td class="text-start fw-bold">
                                    {{ $aluno->nickname }}
                                </td>
                                <td>
                                    <span class="badge fw-bold" style="background-color: {{ $aluno->highest_patente && $aluno->highest_patente->color ? $aluno->highest_patente->color.'1a' : 'rgba(108, 117, 125, 0.2)' }}; border: 1px solid {{ $aluno->highest_patente && $aluno->highest_patente->color ? $aluno->highest_patente->color : '#6c757d' }}; color: {{ $aluno->highest_patente && $aluno->highest_patente->color ? $aluno->highest_patente->color : '#ced4da' }}; text-shadow: 0 0 5px {{ $aluno->highest_patente && $aluno->highest_patente->color ? $aluno->highest_patente->color.'40' : 'rgba(0,0,0,0)' }};">
                                        {{ $aluno->highest_patente_nome }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" style="color: white;" class="btn btn-sm btn-outline-primary-tactical select-btn" data-id="{{ $aluno->id }}" title="Selecionar este militar">
                                        <i class="fas fa-hand-pointer"></i> Selecionar
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-muted py-4">
                                    <i class="fas fa-ghost fs-2 mb-2 d-block opacity-50"></i>
                                    Nenhum subordinado elegível encontrado abaixo do seu teto de promoção.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.select2-funcoes').select2({
        theme: 'default',
        width: '100%',
        dropdownCssClass: "tactical-dropdown"
    });

    const userSelect = $('#user_id');
    const roleSelect = $('#new_role_id');
    const hiddenRoleInput = $('#new_role_id_hidden');
    const btnPromover = $('#btn-promover');
    const promotionCeilingHierarquia = parseInt($('#promotion-ceiling').data('hierarquia')) || 0;
    
    // Configs Rebaixamento
    const userSelectDemote = $('#user_id_demote');
    const prevRoleSelect = $('#prev_role_id');
    const btnRebaixar = $('#btn-rebaixar');
    const demotionCeilingHierarquia = parseInt($('#demotion-ceiling').data('hierarquia')) || 0;

    // Configs Exoneracao
    const userSelectExon = $('#user_id_exo');
    const btnExonerar = $('#btn-exonerar');

    const patentes = @json($patentesDisponiveis);
    const patentesArray = Object.values(patentes).sort((a, b) => parseInt(a.hierarquia) - parseInt(b.hierarquia));

    // ============================================
    // LÓGICA DE PROMOÇÃO (Achar a próxima > atual)
    // ============================================
    userSelect.on('change', function() {
        const selectedUserId = $(this).val();
        if (!selectedUserId) {
            btnPromover.prop('disabled', true);
            roleSelect.val('').trigger('change');
            hiddenRoleInput.val('');
            return;
        }

        const selectedOption = $(this).find('option:selected');
        const targetCurrentHierarquia = parseInt(selectedOption.data('hierarquia')) || 0;

        let autoSelectId = null;
        for (let i = 0; i < patentesArray.length; i++) {
            const p = patentesArray[i];
            const pHierarquia = parseInt(p.hierarquia);

            if (pHierarquia > targetCurrentHierarquia && pHierarquia <= promotionCeilingHierarquia) {
                autoSelectId = p.id;
                break; 
            }
        }

        if (autoSelectId) {
            roleSelect.val(autoSelectId).trigger('change');
            hiddenRoleInput.val(autoSelectId);
            btnPromover.prop('disabled', false);
        } else {
            roleSelect.val('').trigger('change');
            hiddenRoleInput.val('');
            btnPromover.prop('disabled', true);
        }
    });

    $('#promocao-form').on('submit', function(e) {
        roleSelect.prop('disabled', false);
        if (!userSelect.val() || !roleSelect.val()) {
            e.preventDefault();
            roleSelect.prop('disabled', true);
            iziToast.error({title: 'Erro de Validação', message: 'Selecione o Militar e a Patente.', position: 'topRight'});
        }
    });

    // ============================================
    // LÓGICA DE REBAIXAMENTO (Achar a anterior < atual)
    // ============================================
    userSelectDemote.on('change', function() {
        const selectedUserId = $(this).val();
        if (!selectedUserId) {
            btnRebaixar.prop('disabled', true);
            prevRoleSelect.html('<option value="">Aguardando seleção do alvo...</option>');
            return;
        }

        const selectedOption = $(this).find('option:selected');
        const targetCurrentHierarquia = parseInt(selectedOption.data('hierarquia')) || 0;

        // Procura do fim pro inicio (da maior pra menor), pra achar logo a imediatamente inferior
        let autoSelectP = null;
        for (let i = patentesArray.length - 1; i >= 0; i--) {
            const p = patentesArray[i];
            const pHierarquia = parseInt(p.hierarquia);

            if (pHierarquia < targetCurrentHierarquia) {
                autoSelectP = p;
                break;
            }
        }

        if (autoSelectP) {
            prevRoleSelect.html(`<option value="${autoSelectP.id}" selected>${autoSelectP.name}</option>`);
            btnRebaixar.prop('disabled', false);
        } else {
            prevRoleSelect.html('<option value="">NENHUMA (Use a aba de Exoneração)</option>');
            btnRebaixar.prop('disabled', true);
        }
    });

    // ============================================
    // LÓGICA DE EXONERACAO
    // ============================================
    userSelectExon.on('change', function() {
        if ($(this).val()) {
            btnExonerar.prop('disabled', false);
        } else {
            btnExonerar.prop('disabled', true);
        }
    });

    // Ao clicar no btn de listagem geral (selecionar manual na tabela) joga no select que tiver ativo
    $('.select-btn').on('click', function() {
        const id = $(this).data('id');
        
        // Verifica qual tab está ativa
        const activeTab = $('.nav-tabs .nav-link.active').attr('id');
        
        if(activeTab === 'promocao-tab' && $('#user_id').length) {
            $('#user_id').val(id).trigger('change');
        } else if(activeTab === 'rebaixamento-tab' && $('#user_id_demote').length) {
            $('#user_id_demote').val(id).trigger('change');
        } else if(activeTab === 'exoneracao-tab' && $('#user_id_exo').length) {
            $('#user_id_exo').val(id).trigger('change');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>
@endpush
