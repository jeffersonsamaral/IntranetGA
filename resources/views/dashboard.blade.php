<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard - IntranetGA')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Bem-vindo, {{ Auth::user()->name }}!</h1>
        <p class="dashboard-subtitle">Esta é a tela principal da intranet.</p>
    </div>
    
    <div class="row">
        <!-- Coluna principal -->
        <div class="col-lg-8 mb-4">
            <!-- Cards informativos -->
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <i class="fas fa-user fa-lg"></i>
                    </div>
                    <h3 class="dashboard-card-title">Perfil</h3>
                    <p class="dashboard-card-text">Visualize e gerencie suas informações de perfil.</p>
                    <a href="#" class="btn btn-primary btn-sm">Ver perfil</a>
                </div>
                
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                    <h3 class="dashboard-card-title">Documentos</h3>
                    <p class="dashboard-card-text">Acesse documentos e formulários importantes.</p>
                    <a href="#" class="btn btn-primary btn-sm">Ver documentos</a>
                </div>
                
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <i class="fas fa-calendar fa-lg"></i>
                    </div>
                    <h3 class="dashboard-card-title">Agenda</h3>
                    <p class="dashboard-card-text">Consulte sua agenda de compromissos.</p>
                    <a href="#" class="btn btn-primary btn-sm">Ver agenda</a>
                </div>
            </div>
            
            <!-- Informações do usuário -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informações do Usuário</h5>
                </div>
                <div class="card-body">
                    <div class="dashboard-table-body">
                        <div class="dashboard-table-row">
                            <div class="dashboard-table-col"><strong>Nome:</strong></div>
                            <div class="dashboard-table-col">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="dashboard-table-row">
                            <div class="dashboard-table-col"><strong>Email:</strong></div>
                            <div class="dashboard-table-col">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="dashboard-table-row">
                            <div class="dashboard-table-col"><strong>Usuário:</strong></div>
                            <div class="dashboard-table-col">{{ Auth::user()->username }}</div>
                        </div>
                        <div class="dashboard-table-row">
                            <div class="dashboard-table-col"><strong>Último login:</strong></div>
                            <div class="dashboard-table-col">{{ Auth::user()->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Coluna lateral -->
        <div class="col-lg-4">
            <!-- Componente de permissões do usuário -->
            @include('components.user-permissions')
            
            <!-- Atividades recentes -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="card-title">Atividades Recentes</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Login realizado</strong>
                                <div class="text-muted small">Acesso ao sistema</div>
                            </div>
                            <span class="text-muted small">{{ now()->format('d/m H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Sincronização do AD</strong>
                                <div class="text-muted small">Grupos atualizados</div>
                            </div>
                            <span class="text-muted small">{{ now()->subHours(2)->format('d/m H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection