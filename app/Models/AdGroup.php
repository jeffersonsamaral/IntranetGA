<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdGroup extends Model
{
    protected $table = 'ad_groups';
    
    protected $fillable = [
        'name',
        'dn',
        'sid',
        'description',
    ];

    /**
     * As roles associadas a este grupo AD
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}