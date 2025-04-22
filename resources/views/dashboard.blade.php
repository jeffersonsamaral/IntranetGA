@extends('layouts.app')

@section('title', 'Dashboard - Intranet')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Bem-vindo, {{ Auth::user()->name }}!</h1>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            @can('board.view')
                @include('components.recent-board-messages')
            @endcan
  <!--          
            <div class="row">
                <div class="col-12">
                    @include('components.dashboard-board')
                </div>
            </div>
    -->
            
        </div>
    </div>
</div>
@endsection