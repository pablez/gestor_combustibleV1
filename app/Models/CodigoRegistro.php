<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoRegistro extends Model
{
    protected $fillable = [
        'codigo',
        'vigente_hasta',
        'usado',
        'rol_solicitado',
        'unidad_organizacional_id',
        'supervisor_id',
        'creado_por',
    ];

    protected $casts = [
        'vigente_hasta' => 'datetime',
        'usado' => 'boolean',
    ];

    // Relaciones
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function unidadOrganizacional()
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_organizacional_id', 'id_unidad_organizacional');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
