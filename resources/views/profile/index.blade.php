@extends('layouts.app')

@section('title', 'Meu Perfil - IntranetGA')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Meu Perfil</h1>
    </div>

    <div class="row">
        <!-- Informações pessoais -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="dashboard-card-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3 class="dashboard-card-title">Informações do Usuário</h3>
                
                <div class="mt-4">
                    <div class="user-info-grid">
                        <div class="info-item">
                            <i class="fas fa-user text-primary"></i>
                            <span>Usuário: {{ Auth::user()->username }}</span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-id-card text-primary"></i>
                            <span>Nome: {{ Auth::user()->name }}</span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-envelope text-primary"></i>
                            <span>E-mail: {{ Auth::user()->email ?: 'Não informado' }}</span>
                        </div>
                                               
                        <div class="info-item">
                            <i class="fas fa-clock text-primary"></i>
                            <span>Última Atualização: {{ Auth::user()->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Permissões e funções -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="dashboard-card-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="dashboard-card-title">Funções e Permissões</h3>
                
                <div class="mt-4">
                    <h5 class="mb-3">Suas Permissões:</h5>
                    <div class="role-badges mb-4">
                        @forelse(Auth::user()->roles as $role)
                            <span class="role-badge">
                                <i class="fas fa-user-tag me-1"></i>
                                {{ $role->name }}
                            </span>
                        @empty
                            <p class="text-muted">Nenhuma função atribuída.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a página de perfil */
.user-info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-bottom: 24px;
}

@media (min-width: 576px) {
    .user-info-grid {
        grid-template-columns: 1fr 1fr;
    }
}

.info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background-color: rgba(var(--color-primary-rgb), 0.03);
    border-radius: var(--border-radius-md);
    transition: all 0.2s ease;
}

.info-item:hover {
    background-color: rgba(var(--color-primary-rgb), 0.06);
}

.info-item i {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.role-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    background-color: rgba(var(--color-primary-rgb), 0.1);
    color: var(--color-primary);
    padding: 6px 12px;
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-medium);
}

.permission-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

@media (min-width: 576px) {
    .permission-list {
        grid-template-columns: 1fr 1fr;
    }
}

.permission-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: var(--font-size-sm);
    padding: 6px 0;
}
</style>
@endsection