<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras'; // $table - qual tabela

    public $incrementing = true; 

    protected $fillable = [ // $fillable - lista os campos que podem ser preenchidos
        'nome',
        'comprado',
    ];

    protected $casts = [ // $casts converte o campo true/false 
        'comprado' => 'boolean',
    ];
}