<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoRegistro extends Model
{
    protected $fillable = [
        'codigo',
        'vigente_hasta',
    ];

    protected $casts = [
        'vigente_hasta' => 'datetime',
    ];
}
