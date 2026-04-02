<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';

    public $incrementing = true;

    protected $fillable = [
        'nome',
        'comprado',
    ];

    protected $casts = [
        'comprado' => 'boolean',
    ];
}