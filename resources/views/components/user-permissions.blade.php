@props(['user' => null])

@php
    // Se não foi passado um usuário, usa o usuário autenticado
    $user = $user ?? auth()->user();
    
    // Obtém todas as roles do usuário
    $roles = $user->roles;
    
    // Obtém todas as permissões associadas às roles do usuário
    $permissions = collect();
    foreach ($roles as $role) {
        // Certifique-se de que o relacionamento permissions() está configurado corretamente
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
@endphp

<div {{ $attributes->merge(['class' => 'user-permissions-component']) }}>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Minhas Permissões</h5>
            <span class="badge bg-primary rounded-pill">{{ $permissions->count() }}</span>
        </div>
        
        <div class="card-body">
            <h6 class="mb-3">Roles Atribuídas</h6>
            <div class="mb-4">
                @forelse($roles as $role)
                    <span class="badge bg-secondary me-2 mb-2">{{ $role->name }}</span>
                @empty
                    <p class="text-muted">Nenhuma role atribuída.</p>
                @endforelse
            </div>
            
            <h6 class="mb-3">Permissões por Categoria</h6>
            
            @if($permissions->isEmpty())
                <p class="text-muted">Nenhuma permissão encontrada.</p>
            @else
                <div class="accordion" id="permissionsAccordion">
                    @foreach($groupedPermissions as $category => $categoryPermissions)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $category }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $category }}" aria-expanded="false" aria-controls="collapse{{ $category }}">
                                    <span class="text-capitalize">{{ $category }}</span>
                                    <span class="badge bg-primary ms-2">{{ $categoryPermissions->count() }}</span>
                                </button>
                            </h2>
                            <div id="collapse{{ $category }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $category }}" data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        @foreach($categoryPermissions as $permission)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $permission->name }}</strong>
                                                    <div class="text-muted small">{{ $permission->description }}</div>
                                                </div>
                                                <code class="text-primary small">{{ $permission->slug }}</code>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Funções de inicialização para o acordeão de permissões
    document.addEventListener('DOMContentLoaded', function() {
        // Se existirem mais de 7 categorias, inicializa o acordeão fechado
        // caso contrário, abre a primeira categoria
        const categories = document.querySelectorAll('.accordion-item');
        if (categories.length > 0 && categories.length <= 7) {
            const firstCategory = categories[0];
            const buttonEl = firstCategory.querySelector('.accordion-button');
            const collapseEl = firstCategory.querySelector('.accordion-collapse');
            
            if (buttonEl && collapseEl) {
                buttonEl.classList.remove('collapsed');
                buttonEl.setAttribute('aria-expanded', 'true');
                collapseEl.classList.add('show');
            }
        }
    });
</script>
@endpush