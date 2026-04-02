<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $table = 'transacoes';

    public $incrementing = true;

    protected $fillable = [
        'nome',
        'valor',
        'tipo',
    ];
}