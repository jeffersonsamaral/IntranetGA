<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'board_id',
        'is_pinned',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_pinned' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com o autor (usuário)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
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