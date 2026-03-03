@extends('layout.app')

@section('title', 'Perfil de ' . $user->nickname)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white glitch" data-text="DOSSIÊ MILITAR">DOSSIÊ MILITAR</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary-tactical">
            <i class="fas fa-arrow-left"></i> VOLTAR
        </a>
    </div>

    <div class="row">
        {{-- COLUNA ESQUERDA: INFORMAÇÕES DO OPERADOR --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card-tactical shadow mb-4">
                <div class="card-header-tactical py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-id-card"></i> IDENTIFICAÇÃO</h6>
                </div>
                <div class="card-body-tactical text-center pt-5">
                    <div class="position-relative d-inline-block mb-3">
                        <img class="img-profile rounded-circle outline-tactical" src="https://www.habbo.com.br/habbo-imaging/avatarimage?user={{ $user->nickname }}&direction=2&head_direction=3&gesture=sml&size=l" style="width: 120px; height: 120px; object-fit: cover; background: rgba(0,0,0,0.5);" alt="Avatar de {{ $user->nickname }}" onerror="this.onerror=null;this.src='https://i.imgur.com/k9Q6E1p.png';">
                        
                        {{-- Status de Atividade --}}
                        <div class="position-absolute" style="bottom: 5px; right: 5px;">
                            @if($user->is_active ?? true)
                                <i class="fas fa-circle text-success" title="Ativo" style="font-size: 1.2rem; text-shadow: 0 0 5px #28a745;"></i>
                            @else
                                <i class="fas fa-circle text-danger" title="Inativo" style="font-size: 1.2rem; text-shadow: 0 0 5px #dc3545;"></i>
                            @endif
                        </div>
                    </div>

                    <h4 class="font-weight-bold text-white mb-1" style="font-family: var(--font-primary); letter-spacing: 2px;">{{ strtoupper($user->nickname) }}</h4>
                    <p class="text-muted mb-4">{{ $user->email }}</p>

                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4" style="gap: 10px;">
                        @foreach($user->roles as $role)
                            @if($role->hierarquia > 0)
                                <span class="badge p-2" style="background-color: {{ $role->color ? $role->color.'1a' : 'rgba(0, 255, 204, 0.1)' }}; border: 1px solid {{ $role->color ?? 'var(--primary-color)' }}; color: {{ $role->color ?? 'var(--primary-color)' }}; text-shadow: 0 0 5px {{ $role->color ? $role->color.'40' : 'rgba(0,255,204,0.3)' }};">
                                    <i class="fas fa-star"></i> {{ strtoupper($role->name) }}
                                </span>
                            @else
                                <span class="badge badge-secondary p-2" style="background-color: transparent; border: 1px solid #6c757d; color: #ced4da;"><i class="fas fa-id-badge"></i> {{ strtoupper($role->name) }}</span>
                            @endif
                        @endforeach
                    </div>

                    <div class="text-left mt-4 border-top pt-3" style="border-color: rgba(0, 255, 204, 0.2) !important;">
                        <p class="mb-2"><strong style="color: var(--primary-color);">REGISTRO CIVIL:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                        <p class="mb-2"><strong style="color: var(--primary-color);">IDH:</strong> #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p class="mb-2"><strong style="color: var(--primary-color);">SITUAÇÃO:</strong> {{ ($user->is_active ?? true) ? 'APTO AO SERVIÇO' : 'RESERVA/AFASTADO' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA: TIMELINE DE TREINAMENTOS --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card-tactical shadow mb-4">
                <div class="card-header-tactical py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-history"></i> HISTÓRICO ACADÊMICO E DISCIPLINAR</h6>
                </div>
                <div class="card-body-tactical">
                    @if($timeline->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x mb-3" style="color: rgba(0,255,204,0.2)"></i>
                            <p class="text-muted" style="font-family: var(--font-primary);">NENHUM REGISTRO ENCONTRADO NO DOSSIÊ.</p>
                        </div>
                    @else
                        <div class="timeline-tactical mt-3">
                            @foreach($timeline as $item)
                                @if($item['type'] == 'aula')
                                    @php $registro = $item['data']; @endphp
                                    <div class="timeline-item">
                                        <div class="timeline-marker {{ $registro->status == 'aprovado' ? 'marker-success' : 'marker-danger' }}">
                                            <i class="fas fa-book-reader" style="font-size: 0.6em; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #000;"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0" style="color: var(--text-color); font-family: var(--font-primary);">
                                                    @if($registro->aula)
                                                        {{ strtoupper($registro->aula->name) }}
                                                        @if($registro->aula->trashed())
                                                            <small class="text-danger" style="font-size: 0.6em;">[INATIVA]</small>
                                                        @endif
                                                    @else
                                                        AULA EXCLUÍDA
                                                    @endif
                                                </h5>
                                                <small class="text-muted" style="font-family: var(--font-mono);">
                                                    {{ $item['date']->format('d/m/Y \à\s H:i') }}
                                                </small>
                                            </div>
                                            
                                            <div class="mb-2">
                                                @if($registro->status == 'aprovado')
                                                    <span class="badge badge-success" style="background: transparent; border: 1px solid #28a745; color: #28a745;">
                                                        <i class="fas fa-check"></i> APROVADO
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger" style="background: transparent; border: 1px solid #dc3545; color: #dc3545;">
                                                        <i class="fas fa-times"></i> REPROVADO
                                                    </span>
                                                @endif
                                                <span class="ml-2" style="font-size: 0.85rem; color: #adb5bd;">
                                                    <i class="fas fa-user-tie"></i> Inst: <strong>{{ $registro->instrutor->nickname ?? 'Desconhecido' }}</strong>
                                                </span>
                                            </div>
                                            
                                            <div class="p-3 mt-3 rounded" style="background: rgba(0,0,0,0.5); border-left: 3px solid {{ $registro->status == 'aprovado' ? '#28a745' : '#dc3545' }};">
                                                <p class="mb-0 font-italic" style="color: #ced4da; font-size: 0.9rem;">
                                                    "{{ $registro->observacao }}"
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item['type'] == 'promocao')
                                    @php 
                                        $promocao = $item['data']; 
                                        $isExoneracao = str_starts_with($promocao->description, '[EXONERAÇÃO]');
                                        $isRebaixamento = str_starts_with($promocao->description, '[REBAIXAMENTO]');
                                        
                                        $icon = $isExoneracao ? 'fa-user-times' : ($isRebaixamento ? 'fa-angle-double-down' : 'fa-angle-double-up');
                                        $color = $isExoneracao ? '#dc3545' : ($isRebaixamento ? '#ffc107' : 'var(--primary-color)');
                                        $title = $isExoneracao ? 'EXONERAÇÃO DE PATENTE' : ($isRebaixamento ? 'REBAIXAMENTO DE PATENTE' : 'PROMOÇÃO DE PATENTE');
                                        
                                        $cleanDesc = str_replace(['[EXONERAÇÃO] ', '[REBAIXAMENTO] '], '', $promocao->description);
                                    @endphp
                                    <div class="timeline-item">
                                        <div class="timeline-marker marker-promotion" style="background-color: {{ $color }};">
                                            <i class="fas {{ $icon }}" style="font-size: 0.6em; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #000;"></i>
                                        </div>
                                        <div class="timeline-content" style="border-left-color: {{ $color }};">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0 text-white" style="font-family: var(--font-primary);">
                                                    <i class="fas {{ $icon }}" style="color: {{ $color }};"></i> {{ $title }}
                                                </h5>
                                                <small class="text-muted" style="font-family: var(--font-mono);">
                                                    {{ $item['date']->format('d/m/Y \à\s H:i') }}
                                                </small>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <span class="badge" style="background: transparent; border: 1px solid #6c757d; color: #ced4da;">
                                                    {{ $promocao->oldRole ? strtoupper($promocao->oldRole->name) : 'CIVIL' }}
                                                </span>
                                                <i class="fas fa-arrow-right mx-2" style="color: {{ $color }};"></i>
                                                <span class="badge" style="background: {{ $color }}1a; border: 1px solid {{ $color }}; color: {{ $color }};">
                                                    {{ $promocao->newRole ? strtoupper($promocao->newRole->name) : 'CIVIL / EXONERADO' }}
                                                </span>
                                                
                                                <span class="ml-3" style="font-size: 0.85rem; color: #adb5bd;">
                                                    <i class="fas fa-user-shield"></i> Oficial: <strong>{{ $promocao->promoter->nickname ?? 'Desconhecido' }}</strong>
                                                </span>
                                            </div>
                                            
                                            <div class="p-3 mt-3 rounded" style="background: rgba(0,0,0,0.5); border-left: 3px solid {{ $color }};">
                                                <p class="mb-0 font-italic" style="color: #ced4da; font-size: 0.9rem;">
                                                    "{{ $cleanDesc }}"
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .outline-tactical {
        border: 3px solid var(--primary-color);
        padding: 5px;
        box-shadow: 0 0 15px rgba(0, 255, 204, 0.3);
    }
    .badge-primary-tactical {
        background-color: rgba(0, 255, 204, 0.1);
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    /* CSS da Timeline Tática */
    .timeline-tactical {
        position: relative;
        padding-left: 30px;
    }
    .timeline-tactical::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 15px;
        width: 2px;
        background: rgba(0, 255, 204, 0.2);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--bg-color);
        border: 2px solid var(--primary-color);
        box-shadow: 0 0 5px var(--primary-color);
        z-index: 1;
    }
    .marker-success {
        border-color: #28a745;
        box-shadow: 0 0 8px #28a745;
        background: #28a745;
    }
    .marker-danger {
        border-color: #dc3545;
        box-shadow: 0 0 8px #dc3545;
        background: #dc3545;
    }
    .timeline-content {
        padding-bottom: 15px;
        border-bottom: 1px dashed rgba(255,255,255,0.1);
    }
    .timeline-item:last-child .timeline-content {
        border-bottom: none;
    }
</style>
@endsection
