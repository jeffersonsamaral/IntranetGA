<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Intranet')</title>

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
            <div class="sidebar-top">
                <button type="button" id="sidebar-toggle" class="btn-sidebar-toggle d-none d-md-block">
                    <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>
                
                <a href="{{ route('profile') }}" class="sidebar-menu-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> <span>Perfil</span>
                </a>
                
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasPermission('board.view'))
                <a href="{{ route('admin.board.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.board.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> <span>Mural de Recados</span>
                </a>
                @endif
                
                @if(auth()->user()->hasRole('admin') || 
                    auth()->user()->hasPermission('roles.view') || 
                    auth()->user()->hasPermission('ad-groups.view'))
                <a href="#" class="sidebar-menu-item has-submenu" id="config-menu">
                    <i class="fas fa-cog"></i> <span>Configurações</span>
                    <i class="fas fa-chevron-down submenu-icon"></i>
                </a>
                <!-- Submenu de Configurações -->
                <div class="sidebar-submenu" id="config-submenu">
                     
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasPermission('roles.view'))
                    <a href="{{ route('admin.roles.index') }}" class="sidebar-menu-item submenu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tag"></i> <span>Regras de Acessos</span>
                    </a>
                    @endif
                    
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasPermission('ad-groups.view'))
                    <a href="{{ route('admin.ad-groups.index') }}" class="sidebar-menu-item submenu-item {{ request()->routeIs('admin.ad-groups.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> <span>Grupos de Acesso (AD)</span>
                    </a>
                    @endif
                </div>
                @endif
            </nav>  
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Navbar Superior - Presente em todas as telas -->
        <nav class="top-navbar">
            <div class="navbar-container">
                <div class="navbar-left">
                <button type="button" id="mobile-sidebar-toggle" class="mobile-menu-button">
                    <i class="fas fa-bars"></i>
                </button>
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Intranet" class="navbar-logo" onerror="this.src='{{ asset('images/default-logo.png') }}'; this.onerror='';" >
                </div>
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
            const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const dashboardContainer = document.querySelector('.dashboard-container');
            
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation(); // Impede propagação do evento
                    sidebar.classList.toggle('mobile-visible');
                });
    }
            
            // Toggle de colapso do sidebar para desktop
            const sidebarToggle = document.getElementById('sidebar-toggle');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('sidebar-collapsed');
                    if (dashboardContainer) {
                        dashboardContainer.classList.toggle('sidebar-collapsed-content');
                    }
                });
            }
            
            // Fechar sidebar ao clicar fora em dispositivos móveis
            document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = mobileSidebarToggle && mobileSidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('mobile-visible')) {
                    sidebar.classList.remove('mobile-visible');
                }
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
                } else {
                    // Define o menu como recolhido por padrão
                    configSubmenu.style.display = 'none';
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