@extends('layouts.app')

@section('title', 'Gerenciamento de Regras')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Gerenciamento de Regras</h1>
        <div class="dashboard-actions">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> &nbsp; Nova Regra
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
    @endif

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                       <!-- <th>Slug</th> -->
                        <th>Descrição</th>
                        <th>Status</th>
                        <!-- <th>Permissões</th> -->
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <!-- <td><code>{{ $role->slug }}</code></td>  -->
                        <td>{{ $role->description ?? 'Sem descrição' }}</td>
                        <td>
                            @if($role->is_active)
                            <span class="badge bg-success">Ativo</span>
                            @else
                            <span class="badge bg-danger">Inativo</span>
                            @endif
                        </td>
                        <!-- <td>{{ $role->permissions->count() }}</td> -->
                        <td> &nbsp;&nbsp;&nbsp;&nbsp;
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;
                        <td>
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                        <td>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        <td>
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
                        <td colspan="6" class="text-center">Nenhuma Regra encontrada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a tabela responsiva */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.dashboard-actions {
    margin-bottom: var(--spacing-4);
}

@media (min-width: 768px) {
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .dashboard-actions {
        margin-bottom: 0;
    }
}

/* Estilos para os botões de ação */
td .btn {
    margin-right: 3px;
}

/* Ajuste para telas pequenas */
@media (max-width: 576px) {
    .table th, .table td {
        padding: 0.5rem;
    }
    
    .badge {
        font-size: 0.7rem;
    }
    
    code {
        font-size: 0.75rem;
    }
}
</style>
@endsection