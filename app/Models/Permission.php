<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class Permission extends Model implements PermissionContract
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Find a permission by its name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function findByName(string $name, ?string $guardName = null): PermissionContract
    {
        $permission = static::where('slug', $name)->first();
        
        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }
        
        return $permission;
    }

    /**
     * Find a permission by its id.
     *
     * @param string|int $id
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function findById(string|int $id, ?string $guardName = null): PermissionContract
    {
        $permission = static::find($id);
        
        if (! $permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }
        
        return $permission;
    }

    /**
     * Find or create permission by its name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function findOrCreate(string $name, ?string $guardName = null): PermissionContract
    {
        $permission = static::where('slug', $name)->first();
        
        if (! $permission) {
            return static::create(['name' => $name, 'slug' => $name]);
        }
        
        return $permission;
    }
    /**
     * As roles que possuem esta permissão
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}