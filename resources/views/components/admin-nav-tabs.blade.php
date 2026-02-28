{{-- Este é o seu painel de navegação reutilizável --}}
<div class="admin-nav-tabs">
    <a href="{{ route('admin.patentes.index') }}" class="{{ request()->routeIs('admin.patentes.*') ? 'active' : '' }}">
        <i class="fas fa-sitemap fa-fw"></i>
        <span>Patentes</span>
    </a>
    <a href="{{ route('admin.funcoes.index') }}" class="{{ request()->routeIs('admin.funcoes.*') ? 'active' : '' }}">
        <i class="fas fa-briefcase fa-fw"></i>
        <span>Funções</span>
    </a>
    <a href="{{ route('admin.permissoes.index') }}" class="{{ request()->routeIs('admin.permissoes.*') ? 'active' : '' }}">
        <i class="fas fa-user-shield fa-fw"></i>
        <span>Permissões</span>
    </a>
</div>
