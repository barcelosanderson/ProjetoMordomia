<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarefaConclusao extends Model
{
    protected $table = 'tarefa_conclusoes';

    protected $fillable = [
        'user_id',
        'tarefa_id',
        'data',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    /**
     * Uma conclusão pertence a uma tarefa.
     */
    public function tarefa(): BelongsTo
    {
        return $this->belongsTo(Tarefa::class);
    }

    /**
     * Uma conclusão pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
