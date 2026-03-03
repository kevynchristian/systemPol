@extends('layout.app')

@section('title', 'Venda de Cargos Executivos')

@section('content')
<div class="page-header">
    <h1 class="glitch" data-text="VENDA DE CARGOS EXECUTIVOS">VENDA DE CARGOS EXECUTIVOS</h1>
    <p>Conceda cargos executivos a operadores (isso substituirá a patente militar atual).</p>
</div>

<x-admin-nav-tabs />

<div class="row justify-content-center">
    <div class="col-lg-6 mb-4">
        <div class="card-tactical">
            <div class="card-header-tactical">
                <i class="fas fa-shopping-cart mr-1"></i>
                <span id="form-title">VENDER CARGO EXECUTIVO</span>
            </div>
            <div class="card-body-tactical">
                <form id="vendas-form" action="{{ route('admin.vendas.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label for="user_id">Operador (Comprador)</label>
                        <select name="user_id" id="user_id" class="form-control select2" required style="width: 100%;">
                            <option value="">Selecione um operador...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->nickname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="role_id">Cargo Executivo</label>
                        <select name="role_id" id="role_id" class="form-control select2" required style="width: 100%;">
                            <option value="">Selecione o cargo...</option>
                            @foreach($cargosExecutivos as $cargo)
                                <option value="{{ $cargo->id }}">[{{ $cargo->hierarquia }}] {{ $cargo->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="observation">Observação / Justificativa</label>
                        <textarea name="observation" id="observation" class="form-control" rows="3" required placeholder="Ex: Efetuou o pagamento do plano Sócio Nível 4 na data X."></textarea>
                    </div>

                    <button type="submit" class="btn btn-warning-tactical w-100">
                        <i class="fas fa-check-circle"></i> PROCESSAR VENDA E ALTERAR CARGO
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializa o Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
        });
        
        // Proteção contra duplos cliques no envio (se não for tratada globalmente de forma suficiente)
        $('#vendas-form').submit(function() {
            if($(this).data('submitting')) return false;
            $(this).data('submitting', true);
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> PROCESSANDO...');
            return true;
        });
    });
</script>
<style>
    /* Ajustes pontuais para o Select2 focar melhor com o tema Tactical */
    .select2-container--bootstrap-5 .select2-selection {
        background-color: rgba(0,0,0,0.5);
        border: 1px solid var(--primary-color);
        color: #fff;
    }
    .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #fff !important;
    }
    .select2-search__field {
        background-color: #111;
        color: #fff;
    }
    .select2-results__option {
        background-color: #222;
        color: #fff;
    }
    .select2-results__option--highlighted {
        background-color: var(--primary-color) !important;
        color: #000 !important;
    }
</style>
@endpush
