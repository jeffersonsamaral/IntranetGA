{{-- resources/views/admin/roles/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes da Role')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalhes da Role: {{ $role->name }}</h1>
        <div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @can('roles.edit')
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
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
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações da Role</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nome:</dt>
                        <dd class="col-sm-8">{{ $role->name }}</dd>

                        <dt class="col-sm-4">Slug:</dt>
                        <dd class="col-sm-8"><code>{{ $role->slug }}</code></dd>

                        <dt class="col-sm-4">Descrição:</dt>
                        <dd class="col-sm-8">{{ $role->description ?? 'Sem descrição' }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($role->is_active)
                            <span class="badge bg-success">Ativo</span>
                            @else
                            <span class="badge bg-danger">Inativo</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Criada em:</dt>
                        <dd class="col-sm-8">{{ $role->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Atualizada em:</dt>
                        <dd class="col-sm-8">{{ $role->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Permissões</h5>
                    <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                </div>
                <div class="card-body">
                    @if($role->permissions->isEmpty())
                    <p class="text-muted">Esta role não possui permissões.</p>
                    @else
                    <div class="row">
                        @foreach($role->permissions->groupBy(function($item) {
                            return explode('.', $item->slug)[0] ?? 'outros';
                        }) as $category => $items)
                        <div class="col-12 mb-3">
                            <h6 class="text-capitalize">{{ $category }}</h6>
                            <div class="row">
                                @foreach($items as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <div>
                                            <div>{{ $permission->name }}</div>
                                            <small class="text-muted"><code>{{ $permission->slug }}</code></small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Seção de Grupos AD -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Grupos AD associados</h5>
                    <span class="badge bg-primary">{{ $role->adGroups->count() }}</span>
                </div>
                <div class="card-body">
                    @if($role->adGroups->isEmpty())
                    <p class="text-muted">Nenhum grupo AD está associado a esta role.</p>
                    @can('ad-groups.map')
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Associar Grupos AD
                    </a>
                    @endcan
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->adGroups as $adGroup)
                                <tr>
                                    <td>{{ $adGroup->name }}</td>
                                    <td>{{ Str::limit($adGroup->description, 50) }}</td>
                                    <td>
                                        <a href="{{ route('admin.ad-groups.show', $adGroup) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('ad-groups.map')
                                        <form action="{{ route('admin.roles.remove-ad-group', ['role' => $role->id, 'adGroup' => $adGroup->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este grupo AD da role?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Seção de Usuários -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Usuários com esta Role</h5>
                    <span class="badge bg-primary">{{ $role->users->count() }}</span>
                </div>
                <div class="card-body">
                    @if($role->users->isEmpty())
                    <p class="text-muted">Nenhum usuário possui esta role.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Usuário</th>
                                    <th>Email</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @can('users.view')
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('roles.edit')
                                        <form action="{{ route('admin.users.remove-role', ['user' => $user->id, 'role' => $role->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover esta role do usuário?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @endcan
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
    
    @can('roles.edit')
    <!-- Seção para adicionar usuários -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Adicionar Usuários à Role</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.add-users', $role) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-10">
                                <select name="user_id" class="form-control" required>
                                    <option value="">Selecione um usuário...</option>
                                    @foreach(\App\Models\User::whereDoesntHave('roles', function($query) use ($role) {
                                        $query->where('roles.id', $role->id);
                                    })->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Seção para adicionar grupos AD -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Adicionar Grupos AD à Role</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.add-ad-groups', $role) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-10">
                                <select name="ad_group_id" class="form-control" required>
                                    <option value="">Selecione um grupo AD...</option>
                                    @foreach(\App\Models\AdGroup::whereDoesntHave('roles', function($query) use ($role) {
                                        $query->where('roles.id', $role->id);
                                    })->orderBy('name')->get() as $adGroup)
                                    <option value="{{ $adGroup->id }}">{{ $adGroup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan

</div>
@endsection