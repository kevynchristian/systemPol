@extends('layout.app')

@section('title', 'Novo Operador')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="REGISTRAR OPERADOR">REGISTRAR OPERADOR</h1>
        <a href="{{ route('admin.operadores.index') }}" class="btn btn-secondary-tactical">
            <i class="fas fa-arrow-left"></i> VOLTAR
        </a>
    </div>

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
            <h6 class="m-0 font-weight-bold"><i class="fas fa-user-plus"></i> DADOS DO OPERADOR</h6>
        </div>
        <div class="card-body-tactical">
            <form action="{{ route('admin.operadores.store') }}" method="POST">
                @csrf
                <div class="form-group row mb-4">
                    <label for="nickname" class="col-sm-2 col-form-label text-right">NICKNAME (HABBO):</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control input-tactical" id="nickname" name="nickname" value="{{ old('nickname') }}" required>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="email" class="col-sm-2 col-form-label text-right">E-MAIL MILITAR:</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control input-tactical" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="password" class="col-sm-2 col-form-label text-right">CHAVE DE ACESSO:</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control input-tactical" id="password" name="password" required>
                    </div>
                    <label for="password_confirmation" class="col-sm-2 col-form-label text-right">CONFIRMAR CHAVE:</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control input-tactical" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <hr style="border-top: 1px solid rgba(0, 255, 204, 0.2);">
                <h6 class="mb-3 text-primary" style="font-family: var(--font-primary);">ATRIBUIÇÕES INICIAIS</h6>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label d-block text-warning" style="font-family: var(--font-primary);"><i class="fas fa-star"></i> PATENTE (HIERARQUIA)</label>
                        <select name="roles[]" class="form-control input-tactical">
                            <option value="">Selecione a patente inicial (Opcional)</option>
                            @foreach($patentes as $patente)
                                <option value="{{ $patente->name }}" {{ in_array($patente->name, old('roles', [])) ? 'selected' : '' }}>
                                    {{ $patente->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text mt-2 opacity-50">Selecione apenas a patente mais alta.</small>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label d-block text-info" style="font-family: var(--font-primary);"><i class="fas fa-briefcase"></i> FUNÇÕES ADICIONAIS</label>
                        <div class="row" style="max-height: 200px; overflow-y: auto; overflow-x: hidden; padding-left: 10px;">
                            @foreach($funcoes as $funcao)
                                <div class="col-12 mb-2">
                                    <div class="custom-control custom-checkbox custom-checkbox-tactical text-left">
                                        <input type="checkbox" class="custom-control-input" id="funcao_{{ $funcao->id }}" name="roles[]" value="{{ $funcao->name }}" {{ in_array($funcao->name, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="funcao_{{ $funcao->id }}">{{ $funcao->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group row mt-5">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary-tactical btn-lg w-50">
                            <i class="fas fa-save"></i> SALVAR REGISTRO
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Estilos extras */
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
    .custom-checkbox-tactical .custom-control-label::before {
        background-color: transparent;
        border-color: rgba(0, 255, 204, 0.5);
        border-radius: 0;
    }
    .custom-checkbox-tactical .custom-control-input:checked~.custom-control-label::before {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
</style>
@endsection
