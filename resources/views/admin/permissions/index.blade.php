@extends('layouts.app')

@section('title', 'Gerenciamento de Permissões')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciamento de Permissões</h1>
        @can('permissions.assign')
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Permissão
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Descrição</th>
                            <th>Roles</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                        <tr>
                            <td>{{ $permission->name }}</td>
                            <td><code>{{ $permission->slug }}</code></td>
                            <td>{{ $permission->description ?? 'Sem descrição' }}</td>
                            <td>{{ $permission->roles->count() }}</td>
                            <td>
                                <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-sm btn-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('permissions.assign')
                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma permissão encontrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $permissions->links() }}
        </div>
    </div>
</div>
@endsection