@extends('layout.app')

@section('title', 'Gerenciamento de Patentes')

@section('content')

    <div class="page-header">
        <h1 class="glitch" data-text="GERENCIAMENTO DE PATENTES">GERENCIAMENTO DE PATENTES</h1>
        <p>Crie, edite e defina as permissões para cada patente do sistema.</p>
    </div>

    <x-admin-nav-tabs />

    <div class="row">
        {{-- Coluna do Formulário --}}
        <div class="col-lg-4 mb-4">
            <div class="card-tactical">
                <div class="card-header-tactical">
                    <i class="fas fa-plus-circle mr-1"></i>
                    <span id="form-title">CRIAR NOVA PATENTE</span>
                </div>
                <div class="card-body-tactical">
                    <form id="role-form" action="{{ route('admin.patentes.store') }}" method="POST">
                        @csrf
                        <div id="form-method"></div> {{-- Para o método PUT na edição --}}

                        <div class="form-group mb-3">
                            <label for="name">Nome da Patente</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tipo">Tipo da Patente</label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value="militar">Militar</option>
                                <option value="executivo">Executivo</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="color">Cor da Patente</label>
                            <input type="color" name="color" id="color" class="form-control" value="#00ffcc" title="Escolha a cor que representará esta patente" style="height: 40px; padding: 5px;">
                        </div>

                        <div class="form-group mb-3">
                            <label for="hierarquia">Nível Hierárquico</label>
                            <input type="number" name="hierarquia" id="hierarquia" class="form-control" required
                                min="0" placeholder="Ex: 1 para Soldado, 10 para Comando">
                            <small class="form-text">Quanto maior o número, maior o poder.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label>Permissões</label>
                            <input type="text" id="permission-search" class="form-control mb-2"
                                placeholder="Buscar permissão...">

                            <div class="permission-selector">
                                {{-- Coluna de Disponíveis --}}
                                <div class="permission-list-container">
                                    <div class="list-header">Disponíveis</div>
                                    <ul id="available-permissions" class="permission-list-box">
                                        @foreach ($permissions as $permission)
                                            <li data-value="{{ $permission->name }}">{{ $permission->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Controles (agora responsivos) --}}
                                <div class="permission-controls">
                                    <button type="button" class="btn btn-secondary-tactical" id="add-permission"
                                        title="Adicionar selecionada(s)">
                                        <i class="fas fa-angle-right"></i><i class="fas fa-angle-double-right"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary-tactical" id="remove-permission"
                                        title="Remover selecionada(s)">
                                        <i class="fas fa-angle-left"></i><i class="fas fa-angle-double-left"></i>
                                    </button>
                                </div>

                                {{-- Coluna de Atribuídas --}}
                                <div class="permission-list-container">
                                    <div class="list-header">Atribuídas</div>
                                    <ul id="assigned-permissions" class="permission-list-box">
                                        {{-- Será preenchido via JS --}}
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="sync_with_role_id">Sincronizar Permissões Com</label>
                            <select name="sync_with_role_id" id="sync_with_role_id" class="form-control">
                                <option value="">Nenhuma Sincronização</option>
                                @foreach ($patentes as $patente)
                                    <option value="{{ $patente->id }}">{{ $patente->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text">As permissões desta patente serão um espelho da patente
                                selecionada.</small>
                        </div>

                        <button type="submit" class="btn btn-primary-tactical w-100">SALVAR</button>
                        <button type="button" class="btn btn-secondary-tactical w-100 mt-2 d-none"
                            id="cancel-edit-btn">CANCELAR EDIÇÃO</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Coluna da Tabela --}}
        {{-- Coluna da Lista de Patentes (NOVA VERSÃO COM CARDS) --}}
        <div class="col-lg-8 mb-4">
            <div class="card-tactical">
                <div class="card-header-tactical">
                    <i class="fas fa-sitemap mr-1"></i>
                    HIERARQUIA DE PATENTES
                </div>
                <div class="card-body-tactical">
                    <div class="row g-3">
                        @forelse($patentes as $patente)
                            <div class="col-12 col-lg-6">
                                <div class="patent-card" style="border-left: 4px solid {{ $patente->color ?? 'var(--primary-color)' }}; background-color: {{ $patente->color ? $patente->color.'1a' : 'rgba(0,0,0,0.5)' }};">
                                    <div class="patent-card-header" style="border-bottom: 1px solid {{ $patente->color ?? 'var(--primary-color)' }}40;">
                                        <h5 class="patent-name" style="color: {{ $patente->color ?? 'var(--primary-color)' }}; text-shadow: 0 0 5px {{ $patente->color ?? 'var(--primary-color)' }}40;">{{ $patente->name }}</h5>
                                        <span class="patent-level" style="color: {{ $patente->color ?? 'black' }};">
                                            NÍVEL {{ $patente->hierarquia }} 
                                            <span class="badge {{ $patente->tipo === 'executivo' ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ strtoupper($patente->tipo) }}</span>
                                        </span>
                                    </div>
                                    <div class="patent-card-body">
                                        <h6><i class="fas fa-user-shield fa-fw me-1 opacity-50"></i>Permissões:</h6>
                                        <div class="permission-tags">
                                            @forelse($patente->permissions as $permission)
                                                <span class="badge bg-info-tactical">{{ $permission->name }}</span>
                                            @empty
                                                <span class="badge bg-secondary">Nenhuma permissão atribuída</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="patent-card-footer" style="border-top: 1px solid {{ $patente->color ?? 'var(--primary-color)' }}40;">
                                        <button class="btn btn-sm btn-warning-tactical edit-btn"
                                            data-id="{{ $patente->id }}" data-name="{{ $patente->name }}"
                                            data-hierarquia="{{ $patente->hierarquia }}"
                                            data-color="{{ $patente->color ?? '#00ffcc' }}"
                                            data-tipo="{{ $patente->tipo }}"
                                            data-sync-id="{{ $patente->sync_with_role_id }}"
                                            data-permissions="{{ json_encode($patente->permissions->pluck('name')) }}">
                                            <i class="fas fa-pencil-alt"></i> EDITAR
                                        </button>
                                        <form id="delete-form-{{ $patente->id }}"
                                            action="{{ route('admin.patentes.destroy', $patente->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger-tactical"
                                                onclick="confirmDelete({{ $patente->id }}, '{{ $patente->name }}')">
                                                <i class="fas fa-trash"></i> EXCLUIR
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center opacity-50 mt-4">Nenhuma patente cadastrada.</p>
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
        // Injeta os dados de todas as patentes e suas permissões em uma variável JS
        const allRolesData = @json($rolesWithPermissions);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Referências do Formulário ---
            const form = document.getElementById('role-form');
            const formTitle = document.getElementById('form-title');
            const nameInput = document.getElementById('name');
            const colorInput = document.getElementById('color');
            const tipoInput = document.getElementById('tipo');
            const hierarquiaInput = document.getElementById('hierarquia');
            const syncSelect = document.getElementById('sync_with_role_id');
            const formMethodDiv = document.getElementById('form-method');
            const cancelBtn = document.getElementById('cancel-edit-btn');
            const originalAction = form.action;

            // --- Referências do Seletor de Permissões ---
            const permissionSelector = document.querySelector('.permission-selector');
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
                    item.style.display = item.textContent.toLowerCase().includes(filter) ? '' :
                        'none';
                });
            });

            // --- ANTES DE ENVIAR: Cria inputs hidden para as permissões atribuídas ---
            form.addEventListener('submit', function() {
                // Limpa inputs antigos para não duplicar
                form.querySelectorAll('input[name="permissions[]"]').forEach(input => input.remove());

                assignedList.querySelectorAll('li').forEach(item => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'permissions[]';
                    hiddenInput.value = item.dataset.value;
                    form.appendChild(hiddenInput);
                });
            });

            syncSelect.addEventListener('change', function() {
                const syncId = this.value;
                // Limpa a lista de atribuídas antes de sincronizar
                assignedList.querySelectorAll('li').forEach(item => availableList.appendChild(item));

                if (syncId) {
                    const permissionsToSync = allRolesData[syncId];
                    if (permissionsToSync) {
                        permissionsToSync.forEach(permissionName => {
                            const item = availableList.querySelector(
                                `li[data-value="${permissionName}"]`);
                            if (item) {
                                assignedList.appendChild(item);
                            }
                        });
                    }
                    // Desativa o seletor
                    permissionSelector.style.opacity = '0.5';
                    permissionSelector.style.pointerEvents = 'none';
                } else {
                    // Ativa o seletor
                    permissionSelector.style.opacity = '1';
                    permissionSelector.style.pointerEvents = 'auto';
                }
            });

            // ### FUNÇÃO DE EDITAR ATUALIZADA ###
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    resetForm();

                    const roleId = this.dataset.id;
                    const roleData = {
                        name: this.dataset.name,
                        hierarquia: this.dataset.hierarquia,
                        color: this.dataset.color,
                        tipo: this.dataset.tipo,
                        permissions: JSON.parse(this.dataset.permissions),
                        syncId: this.dataset.syncId || ""
                    };

                    // 1. Preenche os campos do formulário
                    formTitle.textContent = `EDITAR: ${roleData.name.toUpperCase()}`;
                    form.action = `/admin/patentes/${roleId}`;
                    formMethodDiv.innerHTML = '@method("PUT")';
                    nameInput.value = roleData.name;
                    colorInput.value = roleData.color || '#00ffcc';
                    tipoInput.value = roleData.tipo || 'militar';
                    hierarquiaInput.value = roleData.hierarquia;
                    syncSelect.value = roleData.syncId;

                    // 2. Move as permissões que a patente já tem
                    roleData.permissions.forEach(permissionName => {
                        const item = availableList.querySelector(
                            `li[data-value="${permissionName}"]`);
                        if (item) assignedList.appendChild(item);
                    });

                    // 3. ATUALIZA A UI DEPOIS DE TUDO ESTAR NO LUGAR CERTO
                    // Se estiver sincronizando, desativa o seletor. Senão, ativa.
                    if (roleData.syncId) {
                        permissionSelector.style.opacity = '0.5';
                        permissionSelector.style.pointerEvents = 'none';
                    } else {
                        permissionSelector.style.opacity = '1';
                        permissionSelector.style.pointerEvents = 'auto';
                    }

                    // A linha que causava o bug foi REMOVIDA daqui.

                    cancelBtn.classList.remove('d-none');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });

            function resetForm() {
                formTitle.textContent = 'CRIAR NOVA PATENTE';
                form.action = originalAction;
                formMethodDiv.innerHTML = '';
                form.reset();
                colorInput.value = '#00ffcc';
                tipoInput.value = 'militar';

                assignedList.querySelectorAll('li').forEach(item => {
                    availableList.appendChild(item);
                });

                cancelBtn.classList.add('d-none');
            }
            cancelBtn.addEventListener('click', resetForm);
        });

        // SUA FUNÇÃO DE CONFIRMAÇÃO DE EXCLUSÃO (continua igual)
        function confirmDelete(roleId, roleName) {
            iziToast.question({
                theme: 'dark',
                timeout: 20000,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'question',
                zindex: 999,
                title: 'CONFIRMAÇÃO NECESSÁRIA',
                message: `Deseja realmente deletar a patente: <strong>${roleName}</strong>?`,
                position: 'center',
                buttons: [
                    ['<button><b>SIM</b>, EXCLUIR</button>', function(instance, toast) {
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                        // IMPORTANTE: O ID do formulário precisa ser único
                        document.getElementById('delete-form-' + roleId).submit();
                    }, true],
                    ['<button>NÃO, CANCELAR</button>', function(instance, toast) {
                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                    }],
                ]
            });
        }
    </script>
@endpush
