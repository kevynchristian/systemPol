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
                    {{-- Item do Dashboard (verificando a rota) --}}
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt fa-fw"></i>
                            <span>Dashboard</span></a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-users fa-fw"></i> <span>Operadores</span></a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-chart-line fa-fw"></i> <span>Relatórios</span></a>
                    </li>

                    {{-- Bloco de Administração (só para Super Admin) --}}
                    @if (auth()->user() && auth()->user()->is_admin)
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
                            </ul>
                        </li>
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
            <div class="sidebar-footer">
                <small>SISTEMA INTEGRADO DE GERENCIAMENTO OPERACIONAL.</small>
            </div>
        </aside>

        <div class="main-content">
            <header class="topbar">
                <div class="system-status">
                    <i class="fas fa-shield-alt"></i>
                    <span>SISTEMA SEGURO</span>
                </div>
                <div class="user-info">
                    <span>Bem-vindo, Operador <strong>{{ Auth::user()->nickname }}</strong></span>
                    <img src="https://i.imgur.com/k9Q6E1p.png" alt="Avatar" class="user-avatar">
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
        <a href="#" class="nav-link">
            <i class="fas fa-users fa-fw"></i>
            <span>Operadores</span>
        </a>

        {{-- NOVO BOTÃO DE ADMIN PARA O CELULAR --}}
        @if (auth()->user() && auth()->user()->is_admin)
            <a href="{{ route('admin.patentes.index') }}"
                class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield fa-fw"></i>
                <span>Admin</span>
            </a>
        @endif

        <a href="#" class="nav-link">
            <i class="fas fa-chart-line fa-fw"></i>
            <span>Relatórios</span>
        </a>
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


    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
<script src="{{ asset('js/login_tactical.js') }}"></script>

{{-- SCRIPT ÚNICO PARA LÓGICAS DA PÁGINA E NOTIFICAÇÕES --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- LÓGICA DO FAB MENU (MENU FLUTUANTE) ---
        const fabContainer = document.querySelector('.floating-action-menu');
        const fabMainButton = document.querySelector('.fab-main');

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
        // Adicione 'warning' e 'info' aqui se precisar
    });
</script>
    @stack('scripts') {{-- Mantém o @stack para scripts específicos da página --}}
</body>

</html>
