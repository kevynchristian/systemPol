<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.O. // @yield('title', 'Painel Operacional')</title>

    <link rel="shortcut icon" href="https://i.imgur.com/k9Q6E1p.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto+Mono:wght@300;400&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <link rel="stylesheet" href="{{ asset('css/dashboard_tactical.css') }}">
</head>

<body>
    <div class="scanline"></div>
    <div id="particles-js"></div>

    <div class="d-flex tactical-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="glitch" data-text="S.I.G.O.">S.I.G.O.</h2>
                <span>v1.0 - online</span>
            </div>
            <nav class="sidebar-nav">
                <ul>
                @php
                    $isRecrutaOnly = auth()->user() && auth()->user()->roles->count() === 1 && auth()->user()->hasRole('Recruta');

                    // Variáveis movidas para cá para ficarem visíveis no escopo global da view (para o topbar/bottom nav)
                    $hasGuiasRole = auth()->user() && auth()->user()->roles->contains(function($role) {
                        return str_contains(strtolower($role->name), 'guias');
                    });

                    $canAssignRoles = auth()->user() && auth()->user()->getAllPermissions()->contains(function($perm) {
                        return str_starts_with($perm->name, 'atribuir_');
                    });

                    $canPromote = \App\Http\Controllers\Admin\PromocaoController::getPromotionCeiling() !== null;
                @endphp

                    {{-- Item do Dashboard (verificando a rota) --}}
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt fa-fw"></i>
                            <span>Dashboard</span></a>
                    </li>

                    @if(!$isRecrutaOnly)

                    {{-- Bloco de Treinamentos/Academia --}}
                    @if($hasGuiasRole || (auth()->user() && auth()->user()->hasRole('superadmin')))
                    <li class="{{ request()->routeIs('treinamentos.*') ? 'active' : '' }}">
                        <a href="{{ route('treinamentos.create') }}"><i class="fas fa-graduation-cap fa-fw"></i> <span>Formações</span></a>
                    </li>
                    @endif

                    <li class="{{ request()->routeIs('scripts.*') ? 'active' : '' }}">
                        <a href="{{ route('scripts.index') }}"><i class="fas fa-file-signature fa-fw"></i> <span>Documentos</span></a>
                    </li>

                    @if($canAssignRoles || (auth()->user() && auth()->user()->hasRole('superadmin')))
                    <li class="{{ request()->routeIs('admin.funcoes.assign.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.funcoes.assign.index') }}"><i class="fas fa-sitemap fa-fw"></i> <span>Comissionar</span></a>
                    </li>
                    @endif

                    {{-- Bloco de Promoções --}}
                    @if($canPromote)
                    <li class="{{ request()->routeIs('admin.promocoes.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.promocoes.index') }}"><i class="fas fa-angle-double-up fa-fw"></i> <span>Promoções</span></a>
                    </li>
                    @endif

                    @if (auth()->user() && auth()->user()->is_admin)
                        <li class="{{ request()->routeIs('admin.operadores.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.operadores.index') }}"><i class="fas fa-users fa-fw"></i> <span>Operadores</span></a>
                        </li>
                        <li>
                            <a href="#"><i class="fas fa-chart-line fa-fw"></i> <span>Relatórios</span></a>
                        </li>
                        <li class="has-submenu {{ request()->routeIs('admin.*') ? 'open active' : '' }}">
                            <a href="#" class="submenu-toggle">
                                <i class="fas fa-user-shield fa-fw"></i>
                                <span>Administração</span>
                                <i class="fas fa-chevron-down submenu-arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li class="{{ request()->routeIs('admin.patentes.*') ? 'active' : '' }}"><a
                                        href="{{ route('admin.patentes.index') }}">Gerenciar Patentes</a></li>
                                <li class="{{ request()->routeIs('admin.funcoes.*') ? 'active' : '' }}"><a
                                        href="{{ route('admin.funcoes.index') }}">Gerenciar Funções</a></li>
                                <li class="{{ request()->routeIs('admin.permissoes.*') ? 'active' : '' }}"><a
                                        href="{{ route('admin.permissoes.index') }}">Gerenciar Permissões</a></li>
                                <li class="{{ request()->routeIs('admin.aulas.*') ? 'active' : '' }}"><a
                                        href="{{ route('admin.aulas.index') }}">Gerenciar Formações</a></li>
                                @can('vender_cargos')
                                    <li class="{{ request()->routeIs('admin.vendas.*') ? 'active' : '' }}"><a
                                            href="{{ route('admin.vendas.index') }}">Venda de Cargos</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endif

                    @endif

                    <li>
                        <a href="#"><i class="fas fa-cogs fa-fw"></i> <span>Configurações</span></a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt fa-fw"></i>
                            <span>Desconectar</span></a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer p-3">
                <small class="d-block mb-2 text-muted" style="font-size: 0.65rem; color: white !important;">SISTEMA DE GERENCIAMENTO</small>
                
                <!-- Usuários Ativos Widget -->
                <div class="active-users-widget mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="font-size: 0.75rem; color: var(--primary-color);">
                            <i class="fas fa-circle text-success" style="font-size: 0.5rem; vertical-align: middle; margin-right: 3px; animation: pulse 2s infinite;"></i> 
                            OPERADORES ONLINE ({{ isset($onlineUsers) ? $onlineUsers->count() : 0 }})
                        </span>
                    </div>
                    <div class="avatar-group d-flex align-items-center cursor-pointer" data-bs-toggle="modal" data-bs-target="#onlineUsersModal" title="Ver todos os operadores online">
                        @if(isset($onlineUsers) && $onlineUsers->count() > 0)
                            @foreach($onlineUsers->take(5) as $user)
                                <img src="https://www.habbo.com.br/habbo-imaging/avatarimage?user={{ $user->nickname }}&direction=2&head_direction=3&gesture=sml&headonly=1" 
                                     alt="{{ $user->nickname }}" class="rounded-circle avatar-sm" style="width: 25px; height: 25px; border: 1px solid var(--primary-color); margin-right: -8px; background-color: #1a1a2e; z-index: {{ 5 - $loop->index }}; position: relative;"
                                     onerror="this.src='https://i.imgur.com/k9Q6E1p.png';">
                            @endforeach
                            @if($onlineUsers->count() > 5)
                                <span class="badge rounded-circle d-flex justify-content-center align-items-center" style="width: 25px; height: 25px; background-color: rgba(0,255,204,0.2); color: var(--primary-color); border: 1px solid var(--primary-color); font-size: 0.6rem; z-index: 0; position: relative;">
                                    +{{ $onlineUsers->count() - 5 }}
                                </span>
                            @endif
                        @else
                            <span class="text-muted" style="font-size: 0.7rem;">Nenhum operador online.</span>
                        @endif
                    </div>
                </div>
            </div>
        </aside>

        <div class="main-content">
            <header class="topbar">
                <div class="system-status">
                    <i class="fas fa-shield-alt"></i>
                    <span>SISTEMA SEGURO</span>
                </div>

                <div class="user-info">
                    <span class="d-flex align-items-center">
                        {{-- Pega a primeira patente do usuário (onde hierarquia > 0) --}}
                        @php $uRole = Auth::user()->roles->where('hierarquia', '>', 0)->first(); @endphp
                        @if($uRole)
                            <span class="badge px-2 py-1 mr-2" style="transform: translateY(1px) ;margin-right: 5px; color: {{ $uRole->color ?? 'var(--primary-color)' }};  font-size: 0.9rem; font-family: 'Orbitron', sans-serif; letter-spacing: 3px;">
                                {{ strtoupper($uRole->name) }}
                            </span>
                        @endif
                        <strong>{{ Auth::user()->nickname }}</strong>
                    </span>

                    {{-- Carrega o avatar dinâmico do Habbo --}}
                    <img src="https://www.habbo.com.br/habbo-imaging/avatarimage?user={{ Auth::user()->nickname }}&direction=2&head_direction=3&gesture=sml&headonly=1"
                        {{-- <<-- PARÂMETRO ADICIONADO AQUI --}} alt="Avatar" class="user-avatar"
                        onerror="this.onerror=null;this.src='https://i.imgur.com/k9Q6E1p.png';">
                </div>
            </header>

            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt fa-fw"></i>
            <span>Dashboard</span>
        </a>

        @if($hasGuiasRole || (auth()->user() && auth()->user()->hasRole('superadmin')))
        <a href="{{ route('treinamentos.create') }}" class="nav-link {{ request()->routeIs('treinamentos.*') ? 'active' : '' }}">
            <i class="fas fa-graduation-cap fa-fw"></i>
            <span>Formações</span>
        </a>
        @endif

        <a href="{{ route('scripts.index') }}" class="nav-link {{ request()->routeIs('scripts.*') ? 'active' : '' }}">
            <i class="fas fa-file-signature fa-fw"></i>
            <span>Documentos</span>
        </a>

        @if($canAssignRoles || (auth()->user() && auth()->user()->hasRole('superadmin')))
        <a href="{{ route('admin.funcoes.assign.index') }}" class="nav-link {{ request()->routeIs('admin.funcoes.assign.*') ? 'active' : '' }}">
            <i class="fas fa-sitemap fa-fw"></i>
            <span>Comissionar</span>
        </a>
        @endif
        
        @if($canPromote ?? false)
        <a href="{{ route('admin.promocoes.index') }}" class="nav-link {{ request()->routeIs('admin.promocoes.*') ? 'active' : '' }}">
            <i class="fas fa-angle-double-up fa-fw"></i>
            <span>Promoções</span>
        </a>
        @endif

        @if (auth()->user() && auth()->user()->is_admin)
            <a href="{{ route('admin.operadores.index') }}" class="nav-link {{ request()->routeIs('admin.operadores.*') ? 'active' : '' }}">
                <i class="fas fa-users fa-fw"></i>
                <span>Operadores</span>
            </a>
            <a href="{{ route('admin.patentes.index') }}"
                class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield fa-fw"></i>
                <span>Admin</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-line fa-fw"></i>
                <span>Relatórios</span>
            </a>
        @endif
    </nav>
    <div class="floating-action-menu">
        <ul class="fab-options">
            <li>
                <a href="{{ route('logout') }}">
                    <span>Desconectar</span>
                    <div class="fab-icon"><i class="fas fa-sign-out-alt"></i></div>
                </a>
            </li>
            <li>
                <a href="#">
                    <span>Configurações</span>
                    <div class="fab-icon"><i class="fas fa-cogs"></i></div>
                </a>
            </li>
        </ul>
        <button class="fab-main">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <div class="floating-radio-player" id="floating-radio-player">
        {{-- O elemento de áudio real --}}
        <audio id="radio-stream" preload="none">
            <source src="https://stream.zeno.fm/umhxwwtke0hvv" type="audio/mpeg">
        </audio>

        {{-- Botão Principal (Abre/Fecha e Play/Pause) --}}
        <button id="radio-toggle-btn" class="fab-main-radio">
            <i class="fas fa-play"></i> {{-- Começa com play --}}
        </button>

        {{-- Controles que aparecem ao abrir --}}
        <div class="radio-controls">
            <div class="volume-control">
                <i class="fas fa-volume-down"></i>
                <input type="range" id="volume-slider" min="0" max="1" step="0.01"
                    value="0.5">
                <i class="fas fa-volume-up"></i>
            </div>
            {{-- Pode adicionar um span para mostrar o nome da rádio ou música aqui depois --}}
        </div>
    </div>

    <!-- Modal Usuários Online -->
    <div class="modal fade" id="onlineUsersModal" tabindex="-1" aria-labelledby="onlineUsersModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background-color: #0d0d1a; border: 1px solid var(--primary-color); border-radius: 0;">
          <div class="modal-header" style="border-bottom: 1px dashed rgba(0,255,204,0.3);">
            <h5 class="modal-title text-white" id="onlineUsersModalLabel" style="font-family: var(--font-primary); font-size: 1rem;">
                <i class="fas fa-circle text-success" style="font-size: 0.6rem; vertical-align: middle;"></i> OPERADORES ONLINE
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0" style="max-height: 300px; overflow-y: auto;">
             @if(isset($onlineUsers) && $onlineUsers->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($onlineUsers as $user)
                        <li class="list-group-item d-flex align-items-center" style="background-color: transparent; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <img src="https://www.habbo.com.br/habbo-imaging/avatarimage?user={{ $user->nickname }}&direction=2&head_direction=3&gesture=sml&headonly=1" 
                                 width="30" class="rounded-circle mr-3" style="border: 1px solid rgba(0,255,204,0.5); margin-right: 10px; background-color: #1a1a2e;" onerror="this.src='https://i.imgur.com/k9Q6E1p.png';">
                            <div>
                                <strong class="text-white d-block" style="font-size: 0.85rem;">{{ $user->nickname }}</strong>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ $user->roles->where('hierarquia', '>', 0)->first()?->name ?? 'Sem Patente' }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
             @else
                <div class="p-3 text-center text-muted">Ninguém online no momento.</div>
             @endif
          </div>
        </div>
      </div>
    </div>


    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/login_tactical.js') }}"></script>

    {{-- SCRIPT ÚNICO PARA LÓGICAS DA PÁGINA E NOTIFICAÇÕES --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- LÓGICA DO FAB MENU (MENU FLUTUANTE) ---
            const fabContainer = document.querySelector('.floating-action-menu');
            const fabMainButton = document.querySelector('.fab-main');

            // --- LÓGICA DO PLAYER FLUTUANTE (ADICIONE AQUI) ---
            const radioPlayer = document.getElementById('floating-radio-player');
            const radioStream = document.getElementById('radio-stream');
            const toggleBtn = document.getElementById('radio-toggle-btn');
            const volumeSlider = document.getElementById('volume-slider');
            const toggleIcon = toggleBtn.querySelector('i');

            let isPlaying = false;
            let isExpanded = false;

            toggleBtn.addEventListener('click', () => {
                if (!isExpanded) {
                    // Primeira vez clicado: Abre e Toca
                    radioPlayer.classList.add('active');
                    isExpanded = true;
                    if (!isPlaying) {
                        radioStream.play().catch(e => console.error("Erro ao tocar:", e));
                        toggleIcon.className = 'fas fa-pause';
                        isPlaying = true;
                    }
                } else {
                    // Já estava aberto: Toca ou Pausa
                    if (isPlaying) {
                        radioStream.pause();
                        toggleIcon.className = 'fas fa-play';
                        isPlaying = false;
                    } else {
                        radioStream.play().catch(e => console.error("Erro ao tocar:", e));
                        toggleIcon.className = 'fas fa-pause';
                        isPlaying = true;
                    }
                }
            });


            document.addEventListener('click', (event) => {
                if (!radioPlayer.contains(event.target) && isExpanded) {
                    radioPlayer.classList.remove('active');
                    isExpanded = false;
                }
            });

            volumeSlider.addEventListener('input', function() {
                radioStream.volume = this.value;
            });

            radioStream.addEventListener('error', function() {
                iziToast.error({
                    title: 'ERRO',
                    message: 'Não foi possível carregar o stream da rádio.',
                    position: 'topRight',
                    icon: 'fas fa-times-circle',
                });
                toggleIcon.className = 'fas fa-play';
                isPlaying = false;
                radioPlayer.classList.remove('active'); // Fecha em caso de erro
                isExpanded = false;
            });

            if (fabMainButton) {
                fabMainButton.addEventListener('click', () => {
                    fabContainer.classList.toggle('active');
                });
            }

            // --- LÓGICA DO SUBMENU (CORRIGIDA) ---
            document.querySelectorAll('.submenu-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    this.parentElement.classList.toggle('open');
                });
            });

            // --- LÓGICA DAS NOTIFICAÇÕES GLOBAIS (IZITOAST) ---
            @if (session('success'))
                iziToast.success({
                    title: 'SUCESSO',
                    message: '{{ session('success') }}',
                    position: 'topRight',
                    icon: 'fas fa-check-circle',
                });
            @elseif (session('error'))
                iziToast.error({
                    title: 'ERRO',
                    message: '{{ session('error') }}',
                    position: 'topRight',
                    icon: 'fas fa-times-circle',
                });
            @endif
            
            // --- PROTEÇÃO GLOBAL CONTRA DUPLO CLIQUE EM FORMULÁRIOS ---
            document.addEventListener('submit', function(e) {
                if (e.target && e.target.tagName === 'FORM') {
                    // Evita submissão múltipla travando o form via dataset
                    if (e.target.dataset.submitted === "true") {
                        e.preventDefault();
                        return;
                    }
                    
                    const submitBtn = e.target.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        e.target.dataset.submitted = "true";
                        
                        // Guarda o conteúdo original e coloca spinner tático
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> PROCESSANDO...';
                        submitBtn.style.pointerEvents = 'none';
                        submitBtn.style.opacity = '0.8';
                        
                        // Desabilita o botão logo em seguida para garantir que o formulário seja enviado com o botão
                        setTimeout(() => {
                            submitBtn.disabled = true;
                        }, 50);
                    }
                }
            });

        });
    </script>
    @stack('scripts') {{-- Mantém o @stack para scripts específicos da página --}}
</body>

</html>
