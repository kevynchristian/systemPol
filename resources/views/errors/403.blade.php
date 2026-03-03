@extends('layout.app')

@section('title', 'Acesso Negado - 403')

@section('content')
<div class="container h-full d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center p-5 card-tactical animate__animated animate__zoomIn" style="max-width: 600px; border: 1px solid var(--danger-color); box-shadow: 0 0 30px rgba(255, 0, 0, 0.2);">
        <div class="error-code mb-4" style="font-family: 'Orbitron', sans-serif; font-size: 6rem; color: var(--danger-color); text-shadow: 0 0 20px rgba(255, 0, 0, 0.5); line-height: 1;">
            403
        </div>
        
        <div class="mb-4">
            {{-- GIF de Dedinho / Acesso Negado --}}
            <img src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNGJreGxqZ3ZqeHpqeGxqZ3ZqeHhqeGxqZ3ZqeHhqeGxqZ3ZqeHhqZSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/kL1K71nMNlcK5SPhOq/giphy.gif" 
                 alt="Acesso Negado" 
                 style="width: 200px; border-radius: 10px; border: 2px solid var(--danger-color); filter: grayscale(0.5) contrast(1.2);">
        </div>

        <h2 class="glitch mb-3" data-text="ACESSO INTERROMPIDO" style="font-family: 'Orbitron', sans-serif; color: #fff;">
            ACESSO INTERROMPIDO
        </h2>

        <p class="mb-4 text-white opacity-75" style="font-size: 1.1rem; border-left: 3px solid var(--danger-color); padding-left: 15px; text-align: left;">
            {{ $exception->getMessage() ?: 'Você não possui as credenciais necessárias para acessar este setor do S.I.G.O.' }}
        </p>

        <div class="alert mt-4 mb-4" style="background: rgba(255, 0, 0, 0.1); border: 1px dashed var(--danger-color); color: #ffcccc; font-family: monospace; font-size: 0.9rem;">
            [ IP: {{ request()->ip() }} RASTREADO E ARQUIVADO ]<br>
            TENTATIVA DE INVASÃO REGISTRADA NO BANCO DE DADOS.
        </div>

        <a href="{{ url('/') }}" class="btn btn-primary-tactical px-5">
            <i class="fas fa-home me-2"></i> RETORNAR À BASE
        </a>
    </div>
</div>

<style>
    .card-tactical {
        background: rgba(10, 10, 15, 0.95) !important;
        backdrop-filter: blur(10px);
    }
    
    .btn-primary-tactical {
        background-color: var(--danger-color) !important;
        border-color: var(--danger-color) !important;
        color: #000 !important;
        font-weight: bold;
    }
    
    .btn-primary-tactical:hover {
        background-color: transparent !important;
        color: var(--danger-color) !important;
        box-shadow: 0 0 15px var(--danger-color);
    }
</style>
@endsection
