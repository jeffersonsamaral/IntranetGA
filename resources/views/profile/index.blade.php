@extends('layouts.app')

@section('title', 'Meu Perfil - IntranetGA')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-user-circle text-primary me-2"></i>
            Meu Perfil
        </h1>
    </div>

    <div class="row">
        <!-- Informações pessoais -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        Informações do Usuário
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-initials">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <h4>{{ Auth::user()->name }}</h4>
                    </div>
                    
                    <div class="user-info-grid">
                        <div class="info-item">
                            <i class="fas fa-user text-primary"></i>
                            <span>{{ Auth::user()->username }}</span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-envelope text-primary"></i>
                            <span>{{ Auth::user()->email ?: 'Não informado' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span>{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-clock text-primary"></i>
                            <span>{{ Auth::user()->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Permissões -->
        <div class="col-lg-8 mb-4">
            @include('components.user-permissions', ['user' => Auth::user()])
            
            <!-- Atividades Recentes -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        Atividades Recentes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="activities-grid">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Login realizado</div>
                                <div class="activity-subtitle">Acesso ao sistema</div>
                            </div>
                            <div class="activity-time">{{ now()->format('d/m H:i') }}</div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="fas fa-sync"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Sincronização do AD</div>
                                <div class="activity-subtitle">Grupos atualizados</div>
                            </div>
                            <div class="activity-time">{{ now()->subHours(2)->format('d/m H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilo para o avatar circular */
.avatar-circle {
    width: 80px;
    height: 80px;
    background-color: var(--color-primary);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.avatar-initials {
    color: white;
    font-size: 40px;
    font-weight: bold;
    text-transform: uppercase;
}

/* Estilo para grid de informações do usuário */
.user-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.info-item i {
    font-size: 14px;
    width: 20px;
    text-align: center;
}

.info-item span {
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Estilo para grid de atividades */
.activities-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
}

.activity-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 15px;
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

.activity-title {
    font-weight: 500;
    font-size: 14px;
}

.activity-subtitle {
    font-size: 12px;
    color: #6c757d;
}

.activity-time {
    font-size: 12px;
    color: #6c757d;
    white-space: nowrap;
}
</style>
@endsection