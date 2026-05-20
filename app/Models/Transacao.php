<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transacao extends Model
{
    protected $table = 'transacoes';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'nome',
        'valor',
        'tipo',
    ];

    /**
     * Relacionamento: uma transação pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
