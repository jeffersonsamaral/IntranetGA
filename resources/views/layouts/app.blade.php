<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'IntranetGA')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @if(auth()->check())
        <!-- Sidebar / Menu Lateral -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <!-- Espaço para o logotipo -->
                <img src="{{ asset('images/logo.png') }}" alt="Logo IntranetGA" onerror="this.src='{{ asset('images/default-logo.png') }}'; this.onerror='';">
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-menu-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> <span>Perfil</span>
                </a>
                <a href="{{ route('admin.board.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.board.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard"></i> <span>Mural de Recados</span>
                </a>
                <a href="{{ route('admin.board.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.board.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> <span>Mural de Recados</span>
                </a>
                <a href="#" class="sidebar-menu-item has-submenu" id="config-menu">
                    <i class="fas fa-cog"></i> <span>Configurações</span>
                    <i class="fas fa-chevron-down submenu-icon"></i>
                </a>
                <!-- Submenu de Configurações -->
                <div class="sidebar-submenu" id="config-submenu">
                    @can('permissions.view')
                    <a href="{{ route('admin.permissions.index') }}" class="sidebar-menu-item submenu-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="fas fa-key"></i> <span>Permissões</span>
                    </a>
                    @endcan
                    <a href="{{ route('admin.roles.index') }}" class="sidebar-menu-item submenu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tag"></i> <span>Roles</span>
                    </a>
                    <a href="{{ route('admin.ad-groups.index') }}" class="sidebar-menu-item submenu-item {{ request()->routeIs('admin.ad-groups.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> <span>Grupos AD</span>
                    </a>
                </div>
            </nav>  
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Navbar Mobile - Só aparece em telas pequenas -->
        <nav class="navbar d-md-none">
            <div class="navbar-container">
                <button type="button" id="sidebar-toggle" class="btn">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('dashboard') }}" class="navbar-brand">IntranetGA</a>
            </div>
        </nav>
    @endif
    
    <!-- Conteúdo principal -->
    <main class="@if(auth()->check()) dashboard-container @endif">
        @yield('content')
    </main>
    
    <!-- Scripts adicionais -->
    @stack('scripts')
    
    @if(auth()->check())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar em dispositivos móveis
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-visible');
                });
            }
            
            // Fechar sidebar ao clicar fora em dispositivos móveis
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth <= 768) {
                    sidebar.classList.remove('mobile-visible');
                }
            });
            
            // Submenu toggle
            const configMenu = document.getElementById('config-menu');
            const configSubmenu = document.getElementById('config-submenu');
            
            if (configMenu && configSubmenu) {
                // Verificar se o submenu deve estar aberto (baseado na rota atual)
                const isPermissionsRoute = window.location.pathname.includes('/permissions');
                if (isPermissionsRoute) {
                    configMenu.classList.add('open');
                    configSubmenu.classList.add('open');
                }
                
                configMenu.addEventListener('click', function(e) {
                    e.preventDefault();
                    configMenu.classList.toggle('open');
                    configSubmenu.classList.toggle('open');
                });
            }
        });


                
                // JavaScript para gerenciar o comportamento do submenu
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar em dispositivos móveis
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-visible');
                });
            }
            
            // Fechar sidebar ao clicar fora em dispositivos móveis
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth <= 768) {
                    sidebar.classList.remove('mobile-visible');
                }
            });
            
            // Gerenciamento do submenu de configurações
            const configMenu = document.getElementById('config-menu');
            const configSubmenu = document.getElementById('config-submenu');
            
            if (configMenu && configSubmenu) {
                // Verificar se o submenu deve estar aberto (baseado na rota atual)
                const isConfigRoute = window.location.pathname.includes('/admin/permissions') ||
                                    window.location.pathname.includes('/admin/roles') ||
                                    window.location.pathname.includes('/admin/ad-groups');
                
                if (isConfigRoute) {
                    configMenu.classList.add('open');
                    configSubmenu.style.display = 'block';
                }
                
                configMenu.addEventListener('click', function(e) {
                    e.preventDefault();
                    configMenu.classList.toggle('open');
                    
                    if (configSubmenu.style.display === 'block') {
                        configSubmenu.style.display = 'none';
                    } else {
                        configSubmenu.style.display = 'block';
                    }
                });
            }
        });
    </script>
    @endif
</body>
</html>