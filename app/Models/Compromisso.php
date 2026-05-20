<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compromisso extends Model
{
    protected $table = 'compromissos';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'titulo',
        'data',
        'hora',
    ];

    /**
     * Relacionamento: um compromisso pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
