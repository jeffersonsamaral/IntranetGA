<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'key',
        'value',
    ];

    /**
     * Retorna a entidade associada a este atributo
     */
    public function entity()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }
}