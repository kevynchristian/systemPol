@extends('layout.app')

@section('title', 'Gerenciamento de Permissões')

@section('content')

    <div class="page-header">
        <h1 class="glitch" data-text="GERENCIAMENTO DE PERMISSÕES">GERENCIAMENTO DE PERMISSÕES</h1>
        <p>Crie ou remova as permissões granulares que podem ser atribuídas aos cargos.</p>
    </div>

    <x-admin-nav-tabs />

    <div class="row">
        {{-- Coluna do Formulário --}}
        <div class="col-lg-4 mb-4">
            <div class="card-tactical">
                <div class="card-header-tactical">
                    <i class="fas fa-plus-circle mr-1"></i>
                    CRIAR NOVA PERMISSÃO
                </div>
                <div class="card-body-tactical">
                    <form action="{{ route('admin.permissoes.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Nome da Permissão</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Ex: promover soldado" required>
                            <small class="form-text">Use nomes curtos, em minúsculas e sem acentos (ex: `aplicar aula
                                basica`).</small>
                        </div>

                        <button type="submit" class="btn btn-primary-tactical w-100">CRIAR PERMISSÃO</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Coluna da Tabela --}}
        <div class="col-lg-8 mb-4">
            <div class="card-tactical">
                <div class="card-header-tactical">
                    <i class="fas fa-shield-alt mr-1"></i>
                    PERMISSÕES EXISTENTES
                </div>
                <div class="card-body-tactical">
                    {{-- A Mágica começa aqui: Trocamos a tabela por uma div com 'row' --}}
                    <div class="row g-3">
                        @forelse($permissions as $permission)
                            {{-- Cada permissão vira uma coluna no grid --}}
                            <div class="col-12 col-md-6">
                                <div class="permission-card">
                                    <span class="permission-name">
                                        <i class="fas fa-user-shield fa-fw me-2 opacity-50"></i>{{ $permission->name }}
                                    </span>
                                    {{-- NOVO FORMULÁRIO --}}
                                    <form id="delete-form-{{ $permission->id }}"
                                        action="{{ route('admin.permissoes.destroy', $permission->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        {{-- O botão agora é do tipo "button" para não enviar o form, e chama nossa função JS --}}
                                        <button type="button" class="btn btn-sm btn-danger-tactical"
                                            onclick="confirmDelete({{ $permission->id }}, '{{ $permission->name }}')"
                                            data-bs-toggle="tooltip" title="Excluir Permissão">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center opacity-50 mt-4">Nenhuma permissão cadastrada.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function confirmDelete(permissionId, permissionName) {
    iziToast.question({
        theme: 'dark', // Usa o tema escuro como base
        timeout: 20000,
        close: false,
        overlay: true,
        displayMode: 'once',
        id: 'question',
        zindex: 999,
        title: 'CONFIRMAÇÃO NECESSÁRIA',
        message: `Deseja realmente deletar a permissão: <strong>${permissionName}</strong>?`,
        position: 'center',
        buttons: [
            ['<button><b>SIM</b>, EXCLUIR</button>', function (instance, toast) {
                // Se o usuário clicar em SIM, fecha o toast e envia o formulário correspondente
                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                document.getElementById('delete-form-' + permissionId).submit();
            }, true], // O 'true' foca neste botão
            ['<button>NÃO, CANCELAR</button>', function (instance, toast) {
                // Se o usuário clicar em NÃO, apenas fecha o toast
                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
            }],
        ]
    });
}
</script>
@endpush
