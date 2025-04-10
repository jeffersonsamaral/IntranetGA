@extends('layouts.app')

@section('title', 'Detalhes da Permissão')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalhes da Permissão</h1>
        <div>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @can('permissions.assign')
            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
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
                    <h5 class="card-title mb-0">Informações da Permissão</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Nome:</dt>
                        <dd class="col-sm-9">{{ $permission->name }}</dd>

                        <dt class="col-sm-3">Slug:</dt>
                        <dd class="col-sm-9"><code>{{ $permission->slug }}</code></dd>

                        <dt class="col-sm-3">Descrição:</dt>
                        <dd class="col-sm-9">{{ $permission->description ?? 'Sem descrição' }}</dd>

                        <dt class="col-sm-3">Criada em:</dt>
                        <dd class="col-sm-9">{{ $permission->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-3">Atualizada em:</dt>
                        <dd class="col-sm-9">{{ $permission->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Roles com esta Permissão</h5>
                </div>
                <div class="card-body">
                    @if($permission->roles->count() > 0)
                    <ul class="list-group">
                        @foreach($permission->roles as $role)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $role->name }}
                            <span class="badge bg-primary rounded-pill">{{ $role->slug }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted">Esta permissão não está associada a nenhuma role.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection