@props(['user' => null])

@php
    // Se não foi passado um usuário, usa o usuário autenticado
    $user = $user ?? auth()->user();
    
    // Obtém todas as roles do usuário
    $roles = $user->roles;
    
    // Obtém todas as permissões associadas às roles do usuário
    $permissions = collect();
    foreach ($roles as $role) {
        $rolePermissions = $role->permissions;
        $permissions = $permissions->merge($rolePermissions);
    }
    
    // Remove duplicatas de permissões
    $permissions = $permissions->unique('id');
    
    // Agrupa permissões por categoria (baseado no prefixo do slug)
    $groupedPermissions = $permissions->groupBy(function ($permission) {
        $parts = explode('.', $permission->slug);
        return $parts[0] ?? 'other';
    });
    
    // Define nomes amigáveis para as categorias
    $categoryNames = [
        'users' => 'Usuários',
        'roles' => 'Funções',
        'permissions' => 'Permissões',
        'ad-groups' => 'Grupos AD',
        'policies' => 'Políticas',
        'other' => 'Outras'
    ];
    
    // Define ícones para categorias
    $categoryIcons = [
        'users' => 'fa-users',
        'roles' => 'fa-user-tag',
        'permissions' => 'fa-key',
        'ad-groups' => 'fa-users-cog',
        'policies' => 'fa-shield-alt',
        'other' => 'fa-asterisk'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'user-permissions-component']) }}>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Minhas Funções e Permissões</h5>
            @if($roles->isNotEmpty())
                <div>
                    @foreach($roles as $role)
                        <span class="badge bg-primary me-1">{{ $role->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        
        <div class="card-body">
            @if($permissions->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Você ainda não possui permissões atribuídas no sistema.
                </div>
            @else
                <div class="row">
                    @foreach($groupedPermissions as $category => $categoryPermissions)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">
                                        <i class="fas {{ $categoryIcons[$category] ?? 'fa-check' }} text-primary me-2"></i>
                                        {{ $categoryNames[$category] ?? ucfirst($category) }}
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="permission-tags">
                                        @foreach($categoryPermissions as $permission)
                                            <span class="permission-tag">{{ $permission->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-2 text-muted small text-center">
                    <i class="fas fa-info-circle"></i> 
                    Para mais detalhes sobre suas permissões, consulte o administrador do sistema.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.permission-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.permission-tag {
    background-color: #f0f4f8;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 0.85em;
    white-space: nowrap;
    color: #333;
}
</style>