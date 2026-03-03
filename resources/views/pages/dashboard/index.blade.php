@extends('layout.app')

@section('title', 'Dashboard')

@section('content')

    @php
        $isRecrutaOnly = auth()->user() && auth()->user()->roles->count() === 1 && auth()->user()->hasRole('Recruta');
    @endphp

    @if($isRecrutaOnly)
        <div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
            <div class="text-center p-5" style="background: rgba(0,0,0,0.7); border: 2px solid var(--primary-color); border-radius: 10px; max-width: 600px; box-shadow: 0 0 20px rgba(0,255,204,0.2);">
                <i class="fas fa-id-badge mb-4" style="font-size: 4rem; color: var(--primary-color); text-shadow: 0 0 15px rgba(0,255,204,0.5);"></i>
                <h2 class="glitch mb-3" data-text="BEM-VINDO, RECRUTA" style="color: var(--primary-color); font-family: var(--font-primary);">BEM-VINDO, RECRUTA</h2>
                <p class="mb-4" style="color: #ccc; font-size: 1.1rem; line-height: 1.6;">
                    Seu cadastro foi aprovado com sucesso no Sistema Integrado de Gerenciamento Operacional.
                </p>
                <div class="p-3 mb-4" style="background: rgba(0,255,204,0.1); border-left: 4px solid var(--primary-color);">
                    <span class="d-block mb-2 text-white"><i class="fas fa-exclamation-triangle" style="color: var(--primary-color);"></i> <strong>AÇÃO REQUERIDA:</strong></span>
                    <span style="color: #aaa;">Para obter acesso completo ao terminal Operacional, você deve procurar a <strong>Divisão de Guias</strong> para realizar o seu Curso de Formação Inicial.</span>
                </div>
                <p class="text-muted" style="font-size: 0.85rem;">
                    Permaneça no Habbo Hotel e aguarde instruções de um Guia/Instrutor disponível.
                </p>
            </div>
        </div>
    @else
        @if (auth()->user() && auth()->user()->is_admin)
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-tactical">
                    <div class="card-body-tactical">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Operadores Ativos</div>
                                <div class="h5 mb-0 font-weight-bold">{{ $totalOperadores }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-astronaut fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-tactical">
                    <div class="card-body-tactical">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Relatórios Gerados (Mês)</div>
                                <div class="h5 mb-0 font-weight-bold">{{ $relatoriosMes }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-tactical">
                    <div class="card-body-tactical">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Alertas do Sistema</div>
                                <div class="h5 mb-0 font-weight-bold {{ $alertasAtivos > 0 ? 'text-warning' : 'text-success' }}">{{ $alertasAtivos }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-tactical">
                    <div class="card-body-tactical">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Uptime do Servidor</div>
                                <div class="h5 mb-0 font-weight-bold">{{ $uptime }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-server fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">

        {{-- COLUNA DA ESQUERDA (75% da largura) --}}
        <div class="col-lg-8">
            {{-- Terminal de Busca (ocupa 100% da coluna da esquerda) --}}
            <div class="card-tactical search-terminal mb-4">
                <div class="card-header-tactical">
                    <i class="fas fa-search me-2"></i>TERMINAL DE BUSCA RÁPIDA DE OPERADOR
                </div>
                <div class="card-body-tactical">
                    <div class="input-group">
                        <span class="input-group-text">></span>
                        <input type="text" id="operator-search-input" class="form-control"
                            placeholder="DIGITE O NICKNAME EXATO DO OPERADOR E AGUARDE..." autocomplete="off">
                    </div>
                    <div id="operator-search-results" class="mt-3">
                        {{-- O status da busca aparecerá aqui --}}
                    </div>
                </div>
            </div>

            {{-- Atividade Recente (ocupa 100% da coluna da esquerda) --}}
            <div class="card-tactical mb-4">
                <div class="card-header-tactical">
                    <i class="fas fa-chart-area mr-1"></i>
                    Atividade Recente do Sistema
                </div>
                <div class="card-body-tactical">
                    <div class="text-center p-5">
                        [ GRÁFICO DE ATIVIDADE ]
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA DA DIREITA (PAINEL DE WIDGETS) --}}
        <div class="col-lg-4">

            {{-- NOVO CAROUSEL AUTOMÁTICO DE COMUNICADOS --}}
            <div class="card-tactical mb-4">
                <div class="card-header-tactical">
                    <i class="fas fa-bullhorn mr-1"></i> COMUNICADOS
                    {{-- Controles de Navegação REMOVIDOS daqui --}}
                </div>
                <div id="announcementCarousel" class="carousel slide tactical-carousel" data-bs-ride="carousel"
                    data-bs-interval="7000"> {{-- Autoplay ativado, intervalo de 7 segundos --}}

                    {{-- Indicadores --}}
                    <div class="carousel-indicators">
                        @foreach($comunicados as $index => $comunicado)
                            <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>

                    {{-- Slides --}}
                    <div class="carousel-inner">
                        @forelse($comunicados as $index => $comunicado)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="image-placeholder">
                                    <img src="{{ $comunicado->image_url ?? 'https://via.placeholder.com/800x400/101010/00ffcc?text=S.I.G.O.+%2B+COMUNICADO' }}" class="d-block w-100 announcement-banner" alt="Banner do Comunicado" onerror="this.parentElement.classList.add('image-failed'); this.style.display='none';">
                                </div>
                                <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.6); padding: 5px; border-radius: 5px; max-width: 80%; margin: 0 auto; backdrop-filter: blur(2px);">
                                    <h5 style="color: var(--primary-color); font-family: var(--font-primary); font-size: 1rem; margin-bottom: 2px;">{{ strtoupper($comunicado->title) }}</h5>
                                    <p class="announcement-description mb-1">{{ Str::limit($comunicado->content, 80) }}</p>
                                    @if($comunicado->type == 'alerta')
                                        <span class="badge badge-danger">ALERTA</span>
                                    @elseif($comunicado->type == 'aula')
                                        <span class="badge badge-warning">AULA</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="carousel-item active">
                                <div class="image-placeholder" style="height: 150px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
                                    <div class="text-center w-100">
                                        <i class="fas fa-satellite-dish fa-3x mb-3" style="color: rgba(0,255,204,0.3)"></i>
                                        <p class="mb-0 text-muted" style="font-family: var(--font-primary);">CAIXA DE COMUNICADOS VAZIA</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 1. AÇÕES RÁPIDAS (RESTAURADO) --}}
            <div class="card-tactical mb-4">
                <div class="card-header-tactical">
                    <i class="fas fa-bolt mr-1"></i> AÇÕES RÁPIDAS
                </div>
                <div class="card-body-tactical">
                    <ul class="quick-links-list">
                        <li><a href="{{ route('admin.operadores.create') }}"><i class="fas fa-user-plus fa-fw"></i> Registrar Operador</a></li>
                        <li><a href="#"><i class="fas fa-gavel fa-fw"></i> Aplicar Punição</a></li>
                        <li><a href="#"><i class="fas fa-file-alt fa-fw"></i> Gerar Relatório</a></li>
                    </ul>
                </div>
            </div>

            {{-- 3. ÚLTIMOS LOGS (REPOSICIONADO) --}}
            <div class="card-tactical mb-4">
                <div class="card-header-tactical">
                    <i class="fas fa-terminal mr-1"></i> Atividade Recente
                </div>
                <div class="card-body-tactical log-feed" style="max-height: 200px; overflow-y: auto;">
                    @forelse($atividadesRecentes as $atividade)
                        <p>
                            <span class="log-time">[{{ $atividade->created_at->format('H:i') }}]</span>
                            @if($atividade->status == 'aprovado')
                                <span class="log-info" style="color: #00ffcc;">[APROV]</span>
                            @else
                                <span class="log-error" style="color: #ff3333;">[REPROV]</span>
                            @endif
                            <a href="{{ route('perfil.show', $atividade->aluno_id) }}" style="color: inherit; text-decoration: none;">
                                <strong>{{ $atividade->aluno->nickname ?? 'Desconhecido' }}</strong>
                            </a>
                            foi avaliado em {{ $atividade->aula->name ?? 'Aula Desconhecida' }}.
                        </p>
                    @empty
                        <p class="text-muted"><span class="log-time">[{{ now()->format('H:i') }}]</span><span class="log-info">[SYS]</span> Nenhum registro recente de aula encontrado.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
        </div>
    @endif
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchTerminal = document.querySelector('.search-terminal');
            const searchInput = document.getElementById('operator-search-input');
            const resultsContainer = document.getElementById('operator-search-results');
            let debounceTimer;
            let typewriterInterval; // Variável para controlar a animação de digitação

            const initialHtml = `<p class="search-status-text opacity-50">// AGUARDANDO COMANDO...</p>`;
            resultsContainer.innerHTML = initialHtml;

            function startTypewriter(element, text) {
                clearInterval(typewriterInterval); // Para qualquer animação anterior
                let i = 0;
                element.innerHTML = ''; // Limpa o conteúdo
                element.classList.add('is-typing'); // Adiciona classe para o cursor piscando

                typewriterInterval = setInterval(() => {
                    if (i < text.length) {
                        element.innerHTML += text.charAt(i);
                        i++;
                    } else {
                        clearInterval(typewriterInterval);
                        element.classList.remove('is-typing'); // Remove o cursor ao terminar
                    }
                }, 30); // Velocidade da digitação (em milissegundos)
            }

            // --- LÓGICA DE EXPANDIR/RECOLHER ---
            searchInput.addEventListener('focus', () => searchTerminal.classList.add('is-expanded'));
            searchInput.addEventListener('blur', () => {
                if (searchInput.value.trim() === "") {
                    searchTerminal.classList.remove('is-expanded');
                }
            });
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const nickname = this.value.trim();

                if (nickname === "") {
                    resultsContainer.innerHTML = initialHtml;
                    searchTerminal.classList.remove('is-expanded');
                    return;
                }

                searchTerminal.classList.add('is-expanded');
                // Prepara o elemento, mas chama a função para o efeito
                resultsContainer.innerHTML = `<p class="search-status-text"></p>`;
                const statusTextElement = resultsContainer.querySelector('.search-status-text');
                startTypewriter(statusTextElement,
                    `// BUSCANDO REGISTRO PARA: ${nickname.toUpperCase()}...`);

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('dashboard.searchOperator') }}?nickname=${nickname}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(err.message ||
                                        'OPERADOR NÃO ENCONTRADO')
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            let badgesHtml = '';
                            if (data && data.emblemas_selecionados && data.emblemas_selecionados
                                .length > 0) {
                                data.emblemas_selecionados.forEach(badge => {
                                    badgesHtml +=
                                        `<img src="${badge.url}" alt="${badge.name}" class="badge-item" data-bs-toggle="tooltip" title="${badge.name}: ${badge.description}">`;
                                });
                            } else {
                                badgesHtml =
                                    '<p class="opacity-50 small">Nenhum emblema destacado.</p>';
                            }

                            const profileHtml = `
                                <div class="operator-profile-card">
                                    <div class="profile-content-grid">

                                        {{-- COLUNA DA ESQUERDA: VITAIS --}}
                                        <div class="profile-vitals">
                                            <img src="${data.avatar_url}" alt="Avatar" class="avatar">
                                            <div class="vitals-item">
                                                <label>Status</label>
                                                <span>
                                                    <span class="online-indicator ${data.online_agora ? 'is-online' : ''}" title="${data.online_agora ? 'Online Agora' : 'Offline'}"></span>
                                                    ${data.online_agora ? 'Online' : 'Offline'}
                                                </span>
                                            </div>
                                            <div class="vitals-item">
                                                <label>Status S.I.G.O.</label>
                                                <span class="${data.status_class}">${data.status}</span>
                                            </div>
                                            <div class="vitals-item">
                                                <label>Patente</label>
                                                <span class="badge px-2 py-1" style="background-color: ${data.patente_color}1a; border: 1px solid ${data.patente_color}; color: ${data.patente_color}; text-shadow: 0 0 5px ${data.patente_color}40; font-size: 0.7rem; font-family: 'Orbitron', sans-serif; letter-spacing: 1px; border-radius: 0; line-height: 1;">
                                                    ${data.patente.toUpperCase()}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- COLUNA DA DIREITA: DETALHES --}}
                                        <div class="profile-details">
                                            <div class="detail-section">
                                                <label>Missão In-Game (Último Acesso: ${data.ultimo_acesso_habbo})</label>
                                                <blockquote class="operator-mission">${data.missao}</blockquote>
                                            </div>

                                            <div class="detail-section">
                                                <label>Tempo de Serviço</label>
                                                <p class="mb-0">${data.tempo_de_servico}</p>
                                            </div>

                                            <div class="detail-section">
                                                <label>Emblemas Destacados</label>
                                                <div class="badges-grid">${badgesHtml}</div>
                                            </div>

                                            <div class="mt-3 text-right border-top pt-3" style="border-color: rgba(0, 255, 204, 0.2) !important;">
                                                <a href="${data.profile_url}" class="btn btn-outline-primary btn-sm" style="color: var(--primary-color); border-color: var(--primary-color);">
                                                    <i class="fas fa-folder-open"></i> ABRIR DOSSIÊ
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            `;

                            // ### ESPIÃO 1: VERIFICAR OS DADOS RECEBIDOS ###
                            console.log("DADOS RECEBIDOS DO SERVIDOR:", data);

                            // ### ESPIÃO 2: VERIFICAR O HTML FINAL GERADO ###
                            console.log("HTML A SER INSERIDO NA PÁGINA:", profileHtml);
                            resultsContainer.innerHTML = profileHtml;

                            // Reativa os tooltips do Bootstrap para os novos emblemas
                            const tooltipTriggerList = [].slice.call(document.querySelectorAll(
                                '[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.map(function(tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        })
                        .catch(error => {
                            const errorMessage = error.message || `FALHA NA COMUNICAÇÃO`;
                            resultsContainer.innerHTML =
                                `<p class="search-status-text text-danger"></p>`;
                            const errorElement = resultsContainer.querySelector(
                                '.search-status-text');
                            startTypewriter(errorElement,
                                `// ALERTA: ${errorMessage.toUpperCase()}`);
                        });
                }, 500);
            });
        });
    </script>
@endpush
