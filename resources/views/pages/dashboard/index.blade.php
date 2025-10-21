@extends('layout.app')

@section('title', 'Dashboard')

@section('content')

    @if (auth()->user() && auth()->user()->is_admin)
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-tactical">
                    <div class="card-body-tactical">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Operadores Ativos</div>
                                <div class="h5 mb-0 font-weight-bold">47</div>
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
                                <div class="h5 mb-0 font-weight-bold">189</div>
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
                                <div class="h5 mb-0 font-weight-bold text-warning">3</div>
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
                                <div class="h5 mb-0 font-weight-bold">99.8%</div>
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

                    {{-- Indicadores (Pontos - mantidos para referência) --}}
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>

                    {{-- Slides --}}
                    <div class="carousel-inner">
                        {{-- Slide 1 --}}
                        <div class="carousel-item active">
                            <div class="image-placeholder"> {{-- Novo container para a imagem --}}
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWcsKq-YH8z86s4TDlRUW8Us1KNcv94CHi-A&s" class="d-block w-100 announcement-banner"
                                    alt="Treinamento"
                                    onerror="this.parentElement.classList.add('image-failed'); this.style.display='none';">
                                {{-- Fallback visual --}}
                            </div>
                            <div class="carousel-caption d-none d-md-block">
                                <p class="announcement-description">Pagamento Geral.</p>
                                <a href="#" class="btn btn-sm btn-primary-tactical">VER DETALHES</a>
                            </div>
                        </div>
                        {{-- Slide 2 (Exemplo) --}}
                        <div class="carousel-item">
                            <div class="image-placeholder">
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR5Sf7WUfkPiJvENz3vjaitVVT9rPrJ0LE7GxRsyYPc-DZb3i1CHnHLPmJib12NB6c66SA&usqp=CAU" {{-- URL inválida para testar --}}
                                    class="d-block w-100 announcement-banner" alt="Diretriz"
                                    onerror="this.parentElement.classList.add('image-failed'); this.style.display='none';">
                            </div>
                            <div class="carousel-caption d-none d-md-block">
                                <p class="announcement-description">Nova diretriz de comunicação implementada. Leia agora.
                                </p>
                                <a href="#" class="btn btn-sm btn-primary-tactical">LER DIRETRIZ</a>
                            </div>
                        </div>
                        {{-- Slide 3 (Exemplo) --}}
                        <div class="carousel-item">
                            <div class="image-placeholder">
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIQll7X9JzYbKF5_81-Dez0DEfX7Fyx4TRRgZskWIgwK_SEZdNnK6uQs4TV1ev0vTBDVg&usqp=CAU"
                                    class="d-block w-100 announcement-banner" alt="Alerta"
                                    onerror="this.parentElement.classList.add('image-failed'); this.style.display='none';">
                            </div>
                            <div class="carousel-caption d-none d-md-block">
                                <p class="announcement-description">Manutenção programada do servidor secundário hoje às
                                    23:00.</p>
                                <a href="#" class="btn btn-sm btn-primary-tactical">MAIS INFO</a>
                            </div>
                        </div>
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
                        <li><a href="#"><i class="fas fa-user-plus fa-fw"></i> Registrar Operador</a></li>
                        <li><a href="#"><i class="fas fa-gavel fa-fw"></i> Aplicar Punição</a></li>
                        <li><a href="#"><i class="fas fa-file-alt fa-fw"></i> Gerar Relatório</a></li>
                    </ul>
                </div>
            </div>

            {{-- 3. ÚLTIMOS LOGS (REPOSICIONADO) --}}
            <div class="card-tactical mb-4">
                <div class="card-header-tactical">
                    <i class="fas fa-terminal mr-1"></i> Últimos Logs
                </div>
                <div class="card-body-tactical log-feed" style="max-height: 200px; overflow-y: auto;">
                    <p><span class="log-time">[17:55]</span><span class="log-info">[INFO]</span> Login: Kevyn.</p>
                    <p><span class="log-time">[17:54]</span><span class="log-warn">[WARN]</span> Acesso falhou:
                        192.168.1.5.
                    </p>
                    <p><span class="log-time">[17:52]</span><span class="log-info">[INFO]</span> Login: Mikael.</p>
                    <p><span class="log-time">[17:50]</span><span class="log-error">[ERROR]</span> DB Secundário offline.
                    </p>
                </div>
            </div>

        </div>
    </div>

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
                                                <span>${data.patente}</span>
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
