@extends('layout.app')

@section('title', 'Gerenciar Formações')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="REGISTRO DE FORMAÇÕES">REGISTRO DE FORMAÇÕES</h1>
        <a href="{{ route('admin.aulas.create') }}" class="btn btn-primary-tactical">
            <i class="fas fa-plus"></i> NOVA FORMAÇÃO
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-tactical">
            {{ session('success') }}
        </div>
    @endif

    <div class="card-tactical shadow mb-4">
        <div class="card-header-tactical py-3">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-graduation-cap"></i> LISTAGEM DE FORMAÇÕES</h6>
        </div>
        <div class="card-body-tactical">
            <div class="table-responsive">
                <table class="table table-dark table-tactical table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="40%">NOME DA FORMAÇÃO</th>
                            <th width="30%">FUNÇÃO REQUERIDA</th>
                            <th width="20%" class="text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aulas as $aula)
                            <tr>
                                <td>{{ $aula->id }}</td>
                                <td>{{ strtoupper($aula->name) }}</td>
                                <td>
                                    @if($aula->required_permission)
                                        <span class="badge badge-tactical">{{ strtoupper($aula->required_permission) }}</span>
                                    @else
                                        <span class="badge badge-secondary p-1" style="background: transparent; border: 1px solid #6c757d; color: #ced4da;">N/D</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.aulas.edit', $aula->id) }}" class="btn btn-warning btn-sm btn-action" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.aulas.destroy', $aula->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-action" title="Excluir" onclick="return confirm('ATENÇÃO: Deseja realmente excluir esta formação? O histórico dos alunos será mantido.');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Nenhuma formação encontrada nos registros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex justify-content-center">
                {{ $aulas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
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
