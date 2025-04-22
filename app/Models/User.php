<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use App\Traits\HasAttributes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements LdapAuthenticatable
{
    use Notifiable, AuthenticatesWithLdap, HasAttributes, HasRoles {
        HasRoles::hasDirectPermission as spatieHasDirectPermission;
        HasRoles::getDirectPermissions as spatieGetDirectPermissions;
    }
    
    /**
     * Override Spatie's direct permission check to use our role-based approach
     */
    public function hasDirectPermission($permission): bool
    {
        return $this->hasPermission($permission);
    }
    
    /**
     * Override Spatie's getDirectPermissions to avoid table queries
     */
    public function getDirectPermissions()
    {
        return collect();
    }
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * As roles atribuídas ao usuário
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Obtém um atributo personalizado do usuário (ABAC)
     */
    public function getCustomAttribute(string $key, $default = null)
    {
        return $this->getCustomAttributeValue($key, $default);
    }

    /**
     * Define um atributo personalizado para o usuário (ABAC)
     */
    public function setCustomAttribute(string $key, $value): void
    {
        $this->setCustomAttributeValue($key, $value);
    }

    /**
     * Verifica se o usuário possui uma determinada role
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        
        return $this->roles->contains('id', $role->id);
    }

    /**
     * Verifica se o usuário possui uma determinada permissão
     */
    public function hasPermission($permission): bool
    {
        // Admin tem todas as permissões
        if ($this->hasRole('admin')) {
            return true;
        }
    
        // Verifica em todas as roles do usuário
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Sincroniza as roles do usuário com base nos grupos do AD
     */
    public function syncRolesFromLdapGroups(array $ldapGroups): void
    {
        // Busca os grupos do AD que estão no banco de dados
        $adGroups = AdGroup::whereIn('dn', $ldapGroups)->get();
        
        // Coleta todas as roles associadas a esses grupos
        $roleIds = [];
        foreach ($adGroups as $group) {
            $groupRoleIds = $group->roles()->pluck('roles.id')->toArray();
            $roleIds = array_merge($roleIds, $groupRoleIds);
        }
        
        // Elimina duplicatas
        $roleIds = array_unique($roleIds);
        
        // Sincroniza as roles do usuário
        $this->roles()->sync($roleIds);
    }
}