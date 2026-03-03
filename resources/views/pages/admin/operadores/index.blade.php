@extends('layout.app')

@section('title', 'Gerenciar Operadores')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="REGISTRO DE OPERADORES">REGISTRO DE OPERADORES</h1>
        <a href="{{ route('admin.operadores.create') }}" class="btn btn-primary-tactical">
            <i class="fas fa-plus"></i> NOVO OPERADOR
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-tactical">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-tactical">
            {{ session('error') }}
        </div>
    @endif

    <div class="card-tactical shadow mb-4">
        <div class="card-header-tactical py-3">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-users"></i> LISTAGEM DE OPERADORES</h6>
        </div>
        <div class="card-body-tactical">
            <div class="table-responsive">
                <table class="table table-dark table-tactical table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NICKNAME</th>
                            <th>EMAIL</th>
                            <th>PATENTES / FUNÇÕES</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operadores as $operador)
                            <tr>
                                <td>{{ $operador->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://www.habbo.com.br/habbo-imaging/avatarimage?user={{ $operador->nickname }}&direction=2&head_direction=3&gesture=sml&headonly=1" alt="Avatar" width="30" class="mr-2 rounded-circle" onerror="this.src='https://i.imgur.com/k9Q6E1p.png';">
                                        <a href="{{ route('perfil.show', $operador->id) }}" class="text-white text-decoration-none" style="font-family: var(--font-primary);">
                                            {{ $operador->nickname }}
                                        </a>
                                    </div>
                                </td>
                                <td>{{ $operador->email }}</td>
                                <td>
                                    @foreach($operador->roles as $role)
                                        <span class="badge badge-tactical {{ $role->hierarquia > 0 ? 'badge-patente' : 'badge-funcao' }}"
                                              style="{{ $role->hierarquia > 0 ? 'background-color: '.($role->color ? $role->color.'1a' : 'rgba(0, 255, 204, 0.1)').'; border-color: '.($role->color ?? 'var(--primary-color)').'; color: '.($role->color ?? 'var(--primary-color)').';' : '' }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.operadores.edit', $operador->id) }}" class="btn btn-warning btn-sm btn-action" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($operador->id !== auth()->id())
                                    <form action="{{ route('admin.operadores.destroy', $operador->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-action" title="Excluir" onclick="return confirm('ATENÇÃO: Deseja realmente excluir este operador do banco de dados militar? Está ação é irreversível.');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhum operador encontrado nos registros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex justify-content-center">
                {{ $operadores->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos extras específicos para esta página baseados no tactical.css */
    .table-tactical {
        background-color: transparent;
        color: var(--text-color);
    }
    .table-tactical th {
        border-bottom: 2px solid var(--primary-color);
        color: var(--primary-color);
        font-family: var(--font-primary);
        letter-spacing: 1px;
    }
    .table-tactical td, .table-tactical th {
        vertical-align: middle;
        border-top: 1px solid rgba(0, 255, 204, 0.1);
    }
    .badge-tactical {
        background-color: rgba(0, 255, 204, 0.1);
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        font-weight: normal;
        margin-right: 5px;
    }
    .badge-patente {
        border-color: #ffd700;
        color: #ffd700;
        background-color: rgba(255, 215, 0, 0.1);
    }
    .btn-action {
        background-color: transparent;
        border: 1px solid;
    }
    .btn-warning.btn-action {
        border-color: #ffc107;
        color: #ffc107;
    }
    .btn-warning.btn-action:hover {
        background-color: #ffc107;
        color: #000;
    }
    .btn-danger.btn-action {
        border-color: #dc3545;
        color: #dc3545;
    }
    .btn-danger.btn-action:hover {
        background-color: #dc3545;
        color: #fff;
    }
</style>
@endsection
