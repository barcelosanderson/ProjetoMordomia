<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarefa extends Model
{
    protected $table = 'tarefas';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'nome',
        'concluida',
    ];

    protected $casts = [
        'concluida' => 'boolean',
    ];

    /**
     * Relacionamento: uma tarefa pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
