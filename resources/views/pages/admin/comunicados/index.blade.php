@extends('layout.app')

@section('title', 'Gerenciar Comunicados')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="MURAL DE COMUNICADOS">MURAL DE COMUNICADOS</h1>
        <a href="{{ route('admin.comunicados.create') }}" class="btn btn-primary-tactical">
            <i class="fas fa-plus"></i> NOVO COMUNICADO
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
            <h6 class="m-0 font-weight-bold"><i class="fas fa-bullhorn"></i> COMUNICADOS PUBLICADOS</h6>
        </div>
        <div class="card-body-tactical">
            <div class="table-responsive">
                <table class="table table-dark table-tactical table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>TÍTULO</th>
                            <th>TIPO</th>
                            <th>STATUS</th>
                            <th>AUTOR</th>
                            <th>CRIADO EM</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($comunicados as $comunicado)
                            <tr>
                                <td>{{ Str::limit($comunicado->title, 40) }}</td>
                                <td>
                                    @if($comunicado->type == 'alerta')
                                        <span class="badge badge-danger badge-tactical" style="border-color:#dc3545; color:#dc3545;"><i class="fas fa-exclamation-triangle"></i> ALERTA</span>
                                    @elseif($comunicado->type == 'info')
                                        <span class="badge badge-info badge-tactical"><i class="fas fa-info-circle"></i> INFO</span>
                                    @elseif($comunicado->type == 'aula')
                                        <span class="badge badge-warning badge-tactical" style="border-color:#ffc107; color:#ffc107;"><i class="fas fa-chalkboard-teacher"></i> AULA</span>
                                    @endif
                                </td>
                                <td>
                                    @if($comunicado->active)
                                        <span class="text-success" style="font-family: var(--font-primary); font-size: 0.8rem;"><i class="fas fa-circle"></i> ATIVO</span>
                                    @else
                                        <span class="text-secondary" style="font-family: var(--font-primary); font-size: 0.8rem;"><i class="far fa-circle"></i> INATIVO</span>
                                    @endif
                                </td>
                                <td>{{ $comunicado->author->nickname ?? 'Desconhecido' }}</td>
                                <td>{{ $comunicado->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.comunicados.edit', $comunicado->id) }}" class="btn btn-warning btn-sm btn-action" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.comunicados.destroy', $comunicado->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-action" title="Excluir" onclick="return confirm('Deseja realmente apagar este comunicado do mural?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Ainda não há comunicados registrados no sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex justify-content-center">
                {{ $comunicados->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos herdados do index de operadores */
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
        background-color: transparent !important;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        font-weight: normal;
        padding: 5px 8px;
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
