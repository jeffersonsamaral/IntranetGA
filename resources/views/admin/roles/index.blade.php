@extends('layouts.app')

@section('title', 'Gerenciamento de Roles')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciamento de Roles</h1>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Role
        </a>
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
                            <th>Status</th>
                            <th>Permissões</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td>{{ $role->description ?? 'Sem descrição' }}</td>
                            <td>
                                @if($role->is_active)
                                <span class="badge bg-success">Ativo</span>
                                @else
                                <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                            <td>{{ $role->permissions->count() }}</td>
                            <td>
                                <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta role?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma role encontrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection