@extends('layout.app')

@section('title', 'Gerenciamento de Funções')

@section('content')

<div class="page-header">
    <h1 class="glitch" data-text="GERENCIAMENTO DE FUNÇÕES">GERENCIAMENTO DE FUNÇÕES</h1>
    <p>Crie cargos funcionais (Ex: Instrutor, Vendedor) e atribua permissões a eles.</p>
</div>

<x-admin-nav-tabs />

<div class="row">
    {{-- Coluna do Formulário --}}
    <div class="col-lg-4 mb-4">
        <div class="card-tactical">
            <div class="card-header-tactical">
                <i class="fas fa-plus-circle mr-1"></i>
                <span id="form-title">CRIAR NOVA FUNÇÃO</span>
            </div>
            <div class="card-body-tactical">
                <form id="role-form" action="{{ route('admin.funcoes.store') }}" method="POST">
                    @csrf
                    <div id="form-method"></div>

                    <div class="form-group mb-3">
                        <label for="name">Nome da Função</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Ex: Instrutor" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="color">Cor da Função</label>
                        <input type="color" name="color" id="color" class="form-control" value="#6c757d" title="Escolha a cor que representará esta função" style="height: 40px; padding: 5px;">
                    </div>

                    <div class="form-group mb-3">
                        <label>Permissões</label>
                        <input type="text" id="permission-search" class="form-control mb-2" placeholder="Buscar permissão...">
                        <div class="permission-selector">
                            <div class="permission-list-container">
                                <div class="list-header">Disponíveis</div>
                                <ul id="available-permissions" class="permission-list-box">
                                    @foreach ($permissions as $permission)
                                        <li data-value="{{ $permission->name }}">{{ $permission->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="permission-controls">
                                <button type="button" class="btn btn-secondary-tactical" id="add-permission" title="Adicionar"><i class="fas fa-angle-right"></i><i class="fas fa-angle-double-right"></i></button>
                                <button type="button" class="btn btn-secondary-tactical" id="remove-permission" title="Remover"><i class="fas fa-angle-left"></i><i class="fas fa-angle-double-left"></i></button>
                            </div>
                            <div class="permission-list-container">
                                <div class="list-header">Atribuídas</div>
                                <ul id="assigned-permissions" class="permission-list-box"></ul>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-tactical w-100">SALVAR</button>
                    <button type="button" class="btn btn-secondary-tactical w-100 mt-2 d-none" id="cancel-edit-btn">CANCELAR EDIÇÃO</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Coluna da Lista de Funções --}}
    <div class="col-lg-8 mb-4">
        <div class="card-tactical">
            <div class="card-header-tactical">
                <i class="fas fa-briefcase mr-1"></i>
                FUNÇÕES EXISTENTES
            </div>
            <div class="card-body-tactical">
                <div class="row g-3">
                    @forelse($funcoes as $funcao)
                        <div class="col-12 col-lg-6">
                            <div class="patent-card" style="border-left: 4px solid {{ $funcao->color ?? '#6c757d' }}; background-color: {{ $funcao->color ? $funcao->color.'1a' : 'rgba(0,0,0,0.5)' }};">
                                <div class="patent-card-header" style="border-bottom: 1px solid {{ $funcao->color ?? '#6c757d' }}40;">
                                    <h5 class="patent-name" style="color: {{ $funcao->color ?? '#ced4da' }}; text-shadow: 0 0 5px {{ $funcao->color ?? '#6c757d' }}40;">{{ $funcao->name }}</h5>
                                </div>
                                <div class="patent-card-body">
                                    <h6><i class="fas fa-user-shield fa-fw me-1 opacity-50"></i>Permissões:</h6>
                                    <div class="permission-tags">
                                        @forelse($funcao->permissions as $permission)
                                            <span class="badge bg-info-tactical">{{ $permission->name }}</span>
                                        @empty
                                            <span class="badge bg-secondary">Nenhuma</span>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="patent-card-footer">
                                    <button class="btn btn-sm btn-warning-tactical edit-btn"
                                        data-id="{{ $funcao->id }}"
                                        data-name="{{ $funcao->name }}"
                                        data-color="{{ $funcao->color ?? '#6c757d' }}"
                                        data-permissions="{{ json_encode($funcao->permissions->pluck('name')) }}">
                                        <i class="fas fa-pencil-alt"></i> EDITAR
                                    </button>
                                    <form id="delete-form-{{ $funcao->id }}" action="{{ route('admin.funcoes.destroy', $funcao->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger-tactical" onclick="confirmDelete({{ $funcao->id }}, '{{ $funcao->name }}')">
                                            <i class="fas fa-trash"></i> EXCLUIR
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center opacity-50 mt-4">Nenhuma função cadastrada.</p>
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
document.addEventListener('DOMContentLoaded', function() {
    // --- Referências dos Elementos ---
    const form = document.getElementById('role-form');
    const formTitle = document.getElementById('form-title');
    const nameInput = document.getElementById('name');
    const formMethodDiv = document.getElementById('form-method');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    const originalAction = form.action;

    const availableList = document.getElementById('available-permissions');
    const assignedList = document.getElementById('assigned-permissions');
    const addBtn = document.getElementById('add-permission');
    const removeBtn = document.getElementById('remove-permission');
    const searchInput = document.getElementById('permission-search');

    // --- Lógica do Seletor de Permissões ---
    function moveItems(source, destination) {
        source.querySelectorAll('li.selected').forEach(item => {
            item.classList.remove('selected');
            destination.appendChild(item);
        });
    }

    [availableList, assignedList].forEach(list => {
        list.addEventListener('click', e => {
            if (e.target && e.target.nodeName === "LI") {
                e.target.classList.toggle('selected');
            }
        });
    });

    addBtn.addEventListener('click', () => moveItems(availableList, assignedList));
    removeBtn.addEventListener('click', () => moveItems(assignedList, availableList));

    // --- Lógica da Busca ---
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        availableList.querySelectorAll('li').forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

    // --- ANTES DE ENVIAR: Cria inputs hidden para as permissões atribuídas ---
    form.addEventListener('submit', function() {
        form.querySelectorAll('input[name="permissions[]"]').forEach(input => input.remove());
        assignedList.querySelectorAll('li').forEach(item => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'permissions[]';
            hiddenInput.value = item.dataset.value;
            form.appendChild(hiddenInput);
        });
    });

    // --- Função de Editar ---
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const roleId = this.dataset.id;
            const roleName = this.dataset.name;
            const roleColor = this.dataset.color || '#6c757d';
            const rolePermissions = JSON.parse(this.dataset.permissions);

            formTitle.textContent = `EDITAR: ${roleName.toUpperCase()}`;
            form.action = `/admin/funcoes/${roleId}`; // Rota correta para funções
            formMethodDiv.innerHTML = '@method("PUT")';
            nameInput.value = roleName;
            document.getElementById('color').value = roleColor;

            rolePermissions.forEach(permissionName => {
                const item = availableList.querySelector(`li[data-value="${permissionName}"]`);
                if (item) assignedList.appendChild(item);
});

            cancelBtn.classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    function resetForm() {
        formTitle.textContent = 'CRIAR NOVA FUNÇÃO';
        form.action = originalAction;
        formMethodDiv.innerHTML = '';
        form.reset();
        assignedList.querySelectorAll('li').forEach(item => availableList.appendChild(item));
        cancelBtn.classList.add('d-none');
    }
    cancelBtn.addEventListener('click', resetForm);
});

function confirmDelete(roleId, roleName) {
    iziToast.question({
        theme: 'dark',
        timeout: 20000,
        close: false,
        overlay: true,
        displayMode: 'once',
        id: 'question',
        zindex: 999,
        title: 'ATENÇÃO',
        message: `Tem certeza que deseja excluir permanentemente a função <b>${roleName}</b> e suas permissões associadas?`,
        position: 'center',
        buttons: [
            ['<button><b>SIM, EXCLUIR</b></button>', function (instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                document.getElementById('delete-form-' + roleId).submit();
            }, true],
            ['<button>CANCELAR</button>', function (instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
            }],
        ]
    });
}
</script>
@endpush
