@extends('layout.app')

@section('title', 'Falha no Sistema - 500')

@section('content')
<div class="container h-full d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center p-5 card-tactical animate__animated animate__flash" style="max-width: 600px; border: 1px solid #ffaa00; box-shadow: 0 0 30px rgba(255, 170, 0, 0.2);">
        <div class="error-code mb-4" style="font-family: 'Orbitron', sans-serif; font-size: 6rem; color: #ffaa00; text-shadow: 0 0 20px rgba(255, 170, 0, 0.5); line-height: 1;">
            500
        </div>
        
        <div class="mb-4">
            {{-- GIF de Explosão / Erro Crítico --}}
            <img src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNnhqZ3ZqeHhqeGxqZ3ZqeHhqeGxqZ3ZqeHhqeGxqZ3ZqeHhqZSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/26n6Wywqy72cQyCBy/giphy.gif" 
                 alt="Erro Crítico" 
                 style="width: 200px; border-radius: 10px; border: 2px solid #ffaa00; filter: contrast(1.5);">
        </div>

        <h2 class="glitch mb-3" data-text="COLAPSO NO CORE" style="font-family: 'Orbitron', sans-serif; color: #fff;">
            COLAPSO NO CORE
        </h2>

        <p class="mb-4 text-white opacity-75" style="font-size: 1.1rem; border-left: 3px solid #ffaa00; padding-left: 15px; text-align: left;">
            O núcleo do S.I.G.O. sofreu uma sobrecarga inesperada. Nossos técnicos de TI já foram acionados para conter o vazamento de dados.
        </p>

        <div class="alert mt-4 mb-4" style="background: rgba(255, 170, 0, 0.1); border: 1px dashed #ffaa00; color: #fff4cc; font-family: monospace; font-size: 0.9rem;">
            [ ALERTA DE SISTEMA ]<br>
            CORE_DUMP_DETECTOR: FATAL_EXCEPTION_0x00E4.
        </div>

        <a href="{{ url('/') }}" class="btn btn-primary-tactical px-5" style="background-color: #ffaa00 !important; border-color: #ffaa00 !important;">
            <i class="fas fa-sync me-2"></i> REINICIAR TERMINAL
        </a>
    </div>
</div>

<style>
    .card-tactical {
        background: rgba(10, 10, 15, 0.95) !important;
        backdrop-filter: blur(10px);
    }
</style>
@endsection
