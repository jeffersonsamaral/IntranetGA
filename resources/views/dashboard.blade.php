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
        <div class="col-lg-12">

        @can('board.view')
            @include('components.recent-board-messages')
        @endcan
        <div class="row">
            <div class="col-12">
                @include('components.dashboard-board')
            </div>
        </div>


            <!-- Cards informativos -->
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <i class="fas fa-user fa-lg"></i>
                    </div>
                    <h3 class="dashboard-card-title">Perfil</h3>
                    <p class="dashboard-card-text">Visualize e gerencie suas informações de perfil.</p>
                    <a href="{{ route('profile') }}" class="btn btn-primary btn-sm">Ver perfil</a>
                </div>
                
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <i class="fas fa-cog fa-lg"></i>
                    </div>
                    <h3 class="dashboard-card-title">Configurações</h3>
                    <p class="dashboard-card-text">Acesse as configurações do sistema.</p>
                    <a href="#" onclick="document.getElementById('config-menu').click(); return false;" class="btn btn-primary btn-sm">Ver configurações</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection