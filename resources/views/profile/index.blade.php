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
                            <span>Usuário: {{ Auth::user()->username }}</span>
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