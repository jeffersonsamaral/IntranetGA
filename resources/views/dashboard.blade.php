<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard - IntranetGA')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Bem-vindo, {{ Auth::user()->name }}!</h1>
        <p class="dashboard-subtitle">Esta é a tela principal da intranet.</p>
    </div>
    
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
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Informações do Usuário</h2>
            <p class="card-text">Confira abaixo suas informações de cadastro.</p>
            
            <div class="dashboard-table">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection