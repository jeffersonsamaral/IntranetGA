<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasAttributes;

class Role extends Model
{
    use HasAttributes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * As permissões associadas a esta role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Os usuários que possuem esta role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Os grupos do AD associados a esta role
     */
    public function adGroups(): BelongsToMany
    {
        return $this->belongsToMany(AdGroup::class, 'ad_group_role');
    }

    /**
     * Verifica se a role tem uma permissão específica
     */
    public function hasPermission($permission): bool
    {
        // Verifica por slug ou por objeto
        if (is_string($permission)) {
            // Verifica diretamente pelo slug da permissão
            if ($this->permissions()->where('slug', $permission)->exists()) {
                return true;
            }
            
            // Verifica por permissão de grupo (formato 'grupo.*')
            $parts = explode('.', $permission);
            if (count($parts) > 1) {
                $group = $parts[0];
                $wildcardPermission = $group . '.*';
                
                if ($this->permissions()->where('slug', $wildcardPermission)->exists()) {
                    return true;
                }
            }
            
            return false;
        }
        
        // Verifica pelo objeto de permissão, especificando a tabela
        return $this->permissions()->where('permissions.id', $permission->id)->exists();
    }
}