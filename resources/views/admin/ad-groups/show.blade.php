{{-- resources/views/admin/ad-groups/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Grupo AD')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalhes do Grupo AD: {{ $adGroup->name }}</h1>
        <div>
            <a href="{{ route('admin.ad-groups.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @can('ad-groups.map')
            <a href="{{ route('admin.ad-groups.edit', $adGroup) }}" class="btn btn-primary">
                <i class="fas fa-map-marker-alt"></i> Mapear para Roles
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Grupo</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nome:</dt>
                        <dd class="col-sm-8">{{ $adGroup->name }}</dd>

                        <dt class="col-sm-4">Distinguished Name:</dt>
                        <dd class="col-sm-8"><code>{{ $adGroup->dn }}</code></dd>

                        <dt class="col-sm-4">SID:</dt>
                        <dd class="col-sm-8"><code>{{ $adGroup->sid }}</code></dd>

                        <dt class="col-sm-4">Descrição:</dt>
                        <dd class="col-sm-8">{{ $adGroup->description ?? 'Sem descrição' }}</dd>

                        <dt class="col-sm-4">Sincronizado em:</dt>
                        <dd class="col-sm-8">{{ $adGroup->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Roles Mapeadas</h5>
                </div>
                <div class="card-body">
                    @if($adGroup->roles->isEmpty())
                    <p class="text-muted">Este grupo não está mapeado para nenhuma role.</p>
                    <p>Use o botão "Mapear para Roles" para configurar o mapeamento.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adGroup->roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td><code>{{ $role->slug }}</code></td>
                                    <td>
                                        @if($role->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                        @else
                                        <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection