@extends('layout.app')

@section('title', 'Editar Formação')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="EDITAR FORMAÇÃO">EDITAR FORMAÇÃO</h1>
        <a href="{{ route('admin.aulas.index') }}" class="btn btn-secondary-tactical">
            <i class="fas fa-arrow-left"></i> VOLTAR
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-tactical">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-tactical shadow mb-4">
        <div class="card-header-tactical py-3">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-edit"></i> DETALHES DA FORMAÇÃO</h6>
        </div>
        <div class="card-body-tactical">
            <form action="{{ route('admin.aulas.update', $aula->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group row mb-4">
                    <label for="name" class="col-sm-2 col-form-label text-right">NOME (EX: CFSD):</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control input-tactical" id="name" name="name" value="{{ old('name', $aula->name) }}" required>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="required_permission" class="col-sm-2 col-form-label text-right">FUNÇÃO REQUERIDA:</label>
                    <div class="col-sm-10">
                        <select class="form-control input-tactical select2-funcoes" id="required_permission" name="required_permission" style="width: 100%;">
                            <option value="">Nenhuma (Qualquer Instrutor)</option>
                            @foreach($funcoes as $funcao)
                                <option value="{{ $funcao->name }}" {{ old('required_permission', $aula->required_permission) == $funcao->name ? 'selected' : '' }}>
                                    {{ $funcao->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Apenas membros que possuam esta Função (ex: Guias) poderão aplicar esta aula futuramente.</small>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="roles" class="col-sm-2 col-form-label text-right">DESTINADO ÀS PATENTES:</label>
                    <div class="col-sm-10">
                        <select class="form-control input-tactical select2-patentes" id="roles" name="roles[]" multiple="multiple" style="width: 100%;">
                            @php
                                $selectedRoles = $aula->roles->pluck('id')->toArray();
                            @endphp
                            @foreach($patentes as $patente)
                                <option value="{{ $patente->id }}" {{ in_array($patente->id, $selectedRoles) ? 'selected' : '' }}>{{ $patente->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Selecione quais patentes podem realizar esta etapa (ex: Soldado, Cabo).</small>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="prerequisite_id" class="col-sm-2 col-form-label text-right">PRÉ-REQUISITO (OPCIONAL):</label>
                    <div class="col-sm-10">
                        <select class="form-control input-tactical select2-prerequisite" id="prerequisite_id" name="prerequisite_id" style="width: 100%;">
                            <option value="">Nenhum (Livre)</option>
                            @foreach($aulas as $aulaItem)
                                <option value="{{ $aulaItem->id }}" {{ $aula->prerequisite_id == $aulaItem->id ? 'selected' : '' }}>{{ $aulaItem->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Se selecionado, o militar DEVE estar Aprovado na aula acima para cursar esta.</small>
                    </div>
                </div>

                <div class="form-group row mt-5">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary-tactical btn-lg w-50">
                            <i class="fas fa-save"></i> ATUALIZAR REGISTRO
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-funcoes').select2({
            placeholder: "Selecione a Função (Opcional)",
            allowClear: true
        });
        $('.select2-patentes').select2({
            placeholder: "Selecione as Patentes",
            allowClear: true
        });
        $('.select2-prerequisite').select2({
            placeholder: "Selecione o Pré-requisito"
        });
    });
</script>
@endpush
