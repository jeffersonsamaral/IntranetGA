<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',  // Corrigir de author_id para user_id
        'is_active',
        'is_pinned',
        'attachment'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean'
    ];

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Relacionamento com o quadro (board)
     */
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Escopo para mensagens ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Escopo para mensagens fixadas
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}