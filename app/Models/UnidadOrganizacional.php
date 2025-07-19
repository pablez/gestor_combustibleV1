<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadOrganizacional extends Model
{
    use HasFactory;

    protected $table = 'unidad_organizacionals';
    protected $primaryKey = 'id_unidad_organizacional';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre_unidad',
        'siglas',
        'tipo_unidad',
        'descripcion',
        'fecha_creacion',
        'activa',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'activa' => 'boolean',
        ];
    }

    /**
     * Relación con usuarios que pertenecen a esta unidad organizacional
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'unidad_organizacional_id', 'id_unidad_organizacional');
    }

    /**
     * Relación con unidades de transporte asignadas a esta unidad organizacional
     */
    public function unidadesTransporte()
    {
        return $this->hasMany(UnidadTransporte::class, 'unidad_organizacional_id', 'id_unidad_organizacional');
    }

    /**
     * Relación con presupuestos asignados a esta unidad organizacional
     */
    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class, 'unidad_organizacional_id', 'id_unidad_organizacional');
    }

    /**
     * Scope para obtener solo unidades activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para filtrar por tipo de unidad
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_unidad', $tipo);
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre_unidad . ' (' . $this->siglas . ')';
    }
}
