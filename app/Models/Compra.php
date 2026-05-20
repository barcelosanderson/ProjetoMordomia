<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compra extends Model
{
    protected $table = 'compras';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'nome',
        'comprado',
    ];

    protected $casts = [
        'comprado' => 'boolean',
    ];

    /**
     * Relacionamento: um item de compra pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
