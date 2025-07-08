<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'unidad_transporte_id',
        'fecha_mantenimiento',
        'tipo_mantenimiento',
        'descripcion_trabajo',
        'costo_bs',
        'kilometraje_mantenimiento',
        'proveedor_id',
        'fecha_proximo_mantenimiento',
    ];

    protected $casts = [
        'fecha_mantenimiento' => 'date',
        'fecha_proximo_mantenimiento' => 'date',
        'costo_bs' => 'decimal:2',
        'kilometraje_mantenimiento' => 'decimal:2',
    ];

    public function unidadTransporte()
    {
        return $this->belongsTo(UnidadTransporte::class, 'unidad_transporte_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}
