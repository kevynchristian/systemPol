@extends('layout.app')

@section('title', 'Atribuir Sargentos e Funções')

@section('content')

    <div class="page-header">
        <h1 class="glitch" data-text="DELEGAÇÃO DE CARGOS">DELEGAÇÃO DE CARGOS</h1>
        <p>Atribua ou revogue funções específicas sob sua liderança aos militares.</p>
    </div>

    <div class="row">
        {{-- Formulário de Atribuição --}}
        <div class="col-lg-5 mb-4">
            <div class="card-tactical">
                <div class="card-header-tactical">
                    <i class="fas fa-users-cog mr-1"></i>
                    COMISSIONAR MILITARES
                </div>
                <div class="card-body-tactical">
                    <form action="{{ route('admin.funcoes.assign.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="role_id">Cargo a ser Atribuído</label>
                            <select name="role_id" id="role_id" class="form-control input-tactical" required>
                                <option value="" disabled selected>-- Selecione o Cargo --</option>
                                @foreach($rolesAllowedToAssign as $role)
                                    <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                                @endforeach
                            </select>
                            <small class="form-text mt-2"><i class="fas fa-info-circle"></i> Apenas funções marcadas na sua patente estão visíveis.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="aluno_id">Selecione os Militares (Alvos)</label>
                            <select name="aluno_id[]" id="aluno_id" class="form-control input-tactical select2-alunos" multiple="multiple" style="width: 100%;" required>
                                @foreach($alunos as $aluno)
                                    <option value="{{ $aluno->id }}">{{ $aluno->nickname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary-tactical w-100"><i class="fas fa-check-circle"></i> APLICAR CARGO</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabela de Revogação (Usuários que já possuem o cargo) --}}
        <div class="col-lg-7 mb-4">
            <div class="card-tactical h-100">
                <div class="card-header-tactical">
                    <i class="fas fa-list-alt mr-1"></i>
                    EFETIVO SOB SEU COMANDO
                </div>
                <div class="card-body-tactical p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-tactical mb-0">
                            <thead>
                                <tr>
                                    <th>NICKNAME</th>
                                    <th>CARGOS VINCULADOS</th>
                                    <th class="text-end">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usuariosComCargos as $user)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-tactical mr-3 position-relative">
                                                    <img src="https://www.habbo.com.br/habbo-imaging/avatarimage?user={{ $user->nickname }}&action=std&direction=2&head_direction=2&img_format=png&gesture=sml&headonly=1&size=m"
                                                        alt="{{ $user->nickname }}">
                                                    @if($user->isOnline())
                                                        <span class="status-indicator online" title="Online Agora"></span>
                                                    @else
                                                        <span class="status-indicator offline" title="Offline"></span>
                                                    @endif
                                                </div>
                                                <strong>{{ strtoupper($user->nickname) }}</strong>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @foreach($user->roles as $role)
                                                @if($role->hierarquia == 0 || is_null($role->hierarquia))
                                                    @if(in_array($role->name, $allowedRoleNames))
                                                        <span class="badge bg-warning text-dark mb-1" title="Você gerencia esta função">{{ strtoupper($role->name) }}</span><br>
                                                    @else
                                                        <span class="badge bg-secondary mb-1" title="Apenas leitura">{{ strtoupper($role->name) }}</span><br>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="align-middle text-end">
                                            @foreach($user->roles as $role)
                                                @if(in_array($role->name, $allowedRoleNames))
                                                    @if($controller->isSuperior($user) || $user->id == auth()->id())
                                                        <button type="button" class="btn btn-sm btn-secondary mb-1" style="cursor: not-allowed; opacity: 0.5;" title="Você não pode modificar cargos deste membro">
                                                            <i class="fas fa-lock"></i>
                                                        </button>
                                                    @else
                                                        <form id="revoke-form-{{ $user->id }}-{{ $role->id }}" 
                                                            action="{{ route('admin.funcoes.assign.destroy', $user->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="role_name" value="{{ $role->name }}">
                                                            <button type="button" class="btn btn-sm btn-danger-tactical mb-1" 
                                                                onclick="confirmRevoke({{ $user->id }}, {{ $role->id }}, '{{ $user->nickname }}', '{{ $role->name }}')"
                                                                title="Revogar {{ strtoupper($role->name) }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Nenhum militar possui os cargos que você lidera atualmente.
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2-alunos').select2({
            placeholder: "-- Escolha os Militares --",
            allowClear: true
        });
    });

    function confirmRevoke(userId, roleId, nickname, roleName) {
        iziToast.question({
            theme: 'dark',
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question-revoke',
            zindex: 999,
            title: 'REVOGAR CARGO',
            message: `Você tem certeza que deseja remover o cargo de <b>${roleName.toUpperCase()}</b> do militar <b>${nickname}</b>?`,
            position: 'center',
            buttons: [
                ['<button><b>SIM</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    document.getElementById('revoke-form-' + userId + '-' + roleId).submit();
                }, true],
                ['<button>CANCELAR</button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                }],
            ]
        });
    }
</script>

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
    
    /* Select2 Tactical Adjustments (Copiado de Aulas/Create) */
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        background-color: rgba(0,0,0,0.5) !important;
        border: 1px solid rgba(0, 255, 204, 0.3) !important;
        border-radius: 0 !important;
        color: #00ffcc !important;
        min-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #00ffcc !important;
        line-height: 38px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(0, 255, 204, 0.2) !important;
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
        background-color: var(--primary-color);
        color: #000;
    }
    .select2-search--inline .select2-search__field {
        color: #ffffff;
    }
    .select2-search--dropdown .select2-search__field {
        background-color: rgba(0,0,0,0.5);
        border: 1px solid rgba(0, 255, 204, 0.3);
        color: var(--text-color);
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 5px rgba(0, 255, 204, 0.5) !important;
    }
</style>
@endpush
