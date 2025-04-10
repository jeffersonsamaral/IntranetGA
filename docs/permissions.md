# Manual do Sistema de Permissões (RBAC + ABAC)

## Visão Geral

O sistema de permissões combina dois modelos de controle de acesso:
- **RBAC (Role-Based Access Control)**: Controle de acesso baseado em papéis/funções atribuídos aos usuários.
- **ABAC (Attribute-Based Access Control)**: Controle de acesso baseado em atributos e políticas dinâmicas.

Esta integração permite utilizar os grupos do Active Directory para atribuir papéis (roles) aos usuários de forma automática, enquanto também suporta regras mais complexas com o ABAC.

## Componentes do Sistema

### 1. Permissões

Permissões são capacidades individuais para realizar ações específicas no sistema.
- Cada permissão tem um nome, um slug único (identificador) e uma descrição.
- Exemplo: 'users.create', 'users.edit', 'reports.view', etc.

### 2. Roles (Papéis/Funções)

Roles são agrupamentos de permissões que podem ser atribuídos a usuários.
- Cada role tem um nome, um slug único, uma descrição e um status (ativo/inativo).
- Exemplos: 'admin', 'manager', 'user', etc.
- As roles podem ser atribuídas diretamente a usuários ou mapeadas para grupos do AD.

### 3. Grupos do Active Directory

O sistema sincroniza grupos do Active Directory e permite mapear esses grupos para roles no sistema.
- Quando um usuário faz login, os grupos do AD aos quais ele pertence são verificados.
- O sistema atribui automaticamente as roles mapeadas para esses grupos.

### 4. Atributos (ABAC)

Atributos são pares chave-valor que podem ser associados a qualquer entidade no sistema (usuários, roles, etc.).
- Podem ser usados para decisões de controle de acesso mais granulares.
- Exemplos: 'department', 'location', 'clearance_level', etc.

### 5. Políticas de Acesso (ABAC)

Políticas definem regras para controlar o acesso baseado em atributos.
- Cada política contém:
  - Recurso que está sendo protegido
  - Ação que está sendo controlada
  - Condições (expressões lógicas) que avaliam atributos
- As condições podem incluir operadores complexos (AND, OR, maior que, igual a, etc.).

## Funcionamento

### 1. Sincronização de Grupos do AD

- O comando `php artisan ad:sync-groups` sincroniza todos os grupos do AD com o banco de dados local.
- A cada login, o sistema atualiza os grupos do usuário e sincroniza suas roles.

### 2. Mapeamento de Grupos para Roles

- No painel administrativo, é possível mapear grupos do AD para roles do sistema.
- Um grupo pode ser mapeado para múltiplas roles e vice-versa.

### 3. Verificação de Permissões (RBAC)

Para verificar se um usuário tem permissão para realizar uma ação:

```php
// Verificar se o usuário tem uma permissão específica
if ($user->hasPermission('users.create')) {
    // O usuário pode criar usuários
}

// Verificar se o usuário tem uma role específica
if ($user->hasRole('admin')) {
    // O usuário é um administrador
}
```

### 4. Middleware para Proteção de Rotas

O sistema inclui middleware para proteger rotas:

```php
// Proteger rota com verificação de permissão
Route::get('/users/create', [UserController::class, 'create'])
    ->middleware('permission:users.create');

// Proteger rota com verificação de role
Route::get('/admin/dashboard', [AdminController::class, 'index'])
    ->middleware('role:admin');
```

### 5. Políticas de Acesso (ABAC)

Para verificações mais complexas utilizando ABAC:

```php
// Usando o Gate para verificar políticas ABAC
if (Gate::allows('access', ['reports', 'view'])) {
    // O usuário pode visualizar relatórios com base nas políticas ABAC
}
```

### 6. Atributos para Usuários e Roles

Para trabalhar com atributos:

```php
// Definir um atributo para um usuário
$user->setAttributeValue('department', 'IT');

// Obter um atributo do usuário
$department = $user->getAttributeValue('department');

// Verificar se um usuário tem um atributo
if ($user->hasAttribute('clearance_level')) {
    // O usuário tem um nível de autorização definido
}
```

## Gestão via Interface Web

### Administração de Roles

- **Listar Roles**: `/admin/roles`
- **Criar Role**: `/admin/roles/create`
- **Editar Role**: `/admin/roles/{role}/edit`
- **Visualizar Role**: `/admin/roles/{role}`

### Mapeamento de Grupos AD

- **Listar Grupos AD**: `/admin/ad-groups`
- **Sincronizar Grupos AD**: `/admin/ad-groups/sync`
- **Mapear Grupo AD para Roles**: `/admin/ad-groups/{adGroup}/edit`

### Políticas de Acesso

- **Listar Políticas**: `/admin/policies`
- **Criar Política**: `/admin/policies/create`
- **Editar Política**: `/admin/policies/{policy}/edit`
- **Visualizar Política**: `/admin/policies/{policy}`

## Comandos Artisan

```bash
# Sincronizar grupos do AD
php artisan ad:sync-groups

# Atribuir role a um usuário
php artisan user:assign-role {username} {role}
```

## Exemplos de Políticas ABAC

### Exemplo 1: Acesso a relatórios financeiros apenas para usuários do departamento financeiro

```json
{
  "resource": "financial-reports",
  "action": "view",
  "conditions": {
    "operator": "eq",
    "attribute": "user.department",
    "value": "Finance"
  }
}
```

### Exemplo 2: Acesso a documentos confidenciais apenas para usuários com nível de autorização superior a 3 OU administradores

```json
{
  "resource": "confidential-documents",
  "action": "view",
  "conditions": {
    "operator": "or",
    "conditions": [
      {
        "operator": "gt",
        "attribute": "user.clearance_level",
        "value": 3
      },
      {
        "operator": "in",
        "attribute": "user.roles",
        "value": ["admin"]
      }
    ]
  }
}
```

### Exemplo 3: Acesso a documentos por localização

```json
{
  "resource": "regional-documents",
  "action": "view",
  "conditions": {
    "operator": "eq",
    "attribute": "user.location",
    "value": "document.region"
  }
}
```

## Considerações de Segurança

1. **Verificação em Camadas**: Sempre aplique verificações de permissão em múltiplas camadas (rotas, controladores, serviços).
2. **Atualize Regularmente**: Mantenha a sincronização de grupos AD atualizada.
3. **Auditoria**: Implemente logs de auditoria para ações importantes.
4. **Princípio do Menor Privilégio**: Atribua apenas as permissões mínimas necessárias para cada função.
5. **Teste de Permissões**: Teste regularmente o sistema de permissões para garantir que está funcionando conforme esperado.

## Solução de Problemas

1. **Permissões não estão sendo aplicadas corretamente**:
   - Verifique se as roles do usuário estão sincronizadas com os grupos AD.
   - Confirme se a permissão está atribuída à role do usuário.
   - Verifique se a role está ativa.

2. **Grupos AD não estão sincronizando**:
   - Verifique as configurações de conexão LDAP.
   - Execute o comando de sincronização manualmente e verifique logs de erros.

3. **Políticas ABAC não estão funcionando**:
   - Verifique se as condições JSON estão corretamente formatadas.
   - Confirme se os atributos referenciados existem.
   - Verifique os logs para mensagens de erro durante a avaliação de políticas.