<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarefa extends Model
{
    protected $table = 'tarefas';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'nome',
        'concluida',
        'dias_semana',
    ];

    protected $casts = [
        'concluida' => 'boolean',
        'dias_semana' => 'array',
    ];

    /**
     * Relacionamento: uma tarefa pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: uma tarefa pode ter várias conclusões,
     * uma para cada data em que foi marcada como feita.
     */
    public function conclusoes(): HasMany
    {
        return $this->hasMany(TarefaConclusao::class);
    }
}
