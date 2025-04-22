<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasAttributes;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class Role extends Model implements RoleContract
{
    use HasAttributes, HasPermissions;
    
    /**
     * Find a role by its name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     *
     * @return \Spatie\Permission\Contracts\Role
     */
    public static function findByName(string $name, ?string $guardName = null): RoleContract
    {
        $role = static::where('slug', $name)->first();
        
        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }
        
        return $role;
    }

    /**
     * Find a role by its id.
     *
     * @param string|int $id
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     *
     * @return \Spatie\Permission\Contracts\Role
     */
    public static function findById(string|int $id, ?string $guardName = null): RoleContract
    {
        $role = static::find($id);
        
        if (! $role) {
            throw RoleDoesNotExist::withId($id);
        }
        
        return $role;
    }

    /**
     * Find or create role by its name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Spatie\Permission\Contracts\Role
     */
    public static function findOrCreate(string $name, ?string $guardName = null): RoleContract
    {
        $role = static::where('slug', $name)->first();
        
        if (! $role) {
            return static::create(['name' => $name, 'slug' => $name]);
        }
        
        return $role;
    }

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
            return $this->permissions()->where('slug', $permission)->exists();
        }
        
        return $this->permissions()->where('id', $permission->id)->exists();
    }
}