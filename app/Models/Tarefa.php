<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model
{
    protected $table = 'tarefas';

    public $incrementing = true;

    protected $fillable = [
        'nome',
        'concluida',
    ];

    protected $casts = [
        'concluida' => 'boolean',
    ];
}