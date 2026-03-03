@extends('layout.app')

@section('title', 'Novo Comunicado')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="NOVO COMUNICADO">NOVO COMUNICADO</h1>
        <a href="{{ route('admin.comunicados.index') }}" class="btn btn-secondary-tactical">
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
            <h6 class="m-0 font-weight-bold"><i class="fas fa-pen"></i> REDIGIR INFORMAÇÃO</h6>
        </div>
        <div class="card-body-tactical">
            <form action="{{ route('admin.comunicados.store') }}" method="POST">
                @csrf
                <div class="form-group row mb-4">
                    <label for="title" class="col-sm-2 col-form-label text-right">TÍTULO:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control input-tactical" id="title" name="title" value="{{ old('title') }}" required maxlength="255">
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="type" class="col-sm-2 col-form-label text-right">CATEGORIA:</label>
                    <div class="col-sm-4">
                        <select name="type" id="type" class="form-control input-tactical" required>
                            <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Informação Geral</option>
                            <option value="alerta" {{ old('type') == 'alerta' ? 'selected' : '' }}>Alerta / Prioridade</option>
                            <option value="aula" {{ old('type') == 'aula' ? 'selected' : '' }}>Aviso de Treinamento</option>
                        </select>
                    </div>

                    <label for="image_url" class="col-sm-2 col-form-label text-right">URL DA IMAGEM:</label>
                    <div class="col-sm-4">
                        <input type="url" class="form-control input-tactical" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://imgur.com/... (Opcional)">
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label for="content" class="col-sm-2 col-form-label text-right">CONTEÚDO:</label>
                    <div class="col-sm-10">
                        <textarea class="form-control input-tactical" id="content" name="content" rows="6" required placeholder="Escreva a mensagem aqui...">{{ old('content') }}</textarea>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <div class="col-sm-2 text-right">
                        <label>VISIBILIDADE:</label>
                    </div>
                    <div class="col-sm-10 text-left">
                        <div class="custom-control custom-checkbox custom-checkbox-tactical">
                            <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="active">Ativo (Visível no Dashboard)</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row mt-5">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary-tactical btn-lg w-50">
                            <i class="fas fa-broadcast-tower"></i> PUBLICAR COMUNICADO
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
