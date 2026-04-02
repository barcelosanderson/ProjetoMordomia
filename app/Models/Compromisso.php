<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compromisso extends Model
{
    protected $table = 'compromissos';

    public $incrementing = true;

    protected $fillable = [
        'titulo',
        'data',
        'hora',
    ];
}