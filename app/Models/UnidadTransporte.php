<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadTransporte extends Model
{
    use HasFactory; // Es una buena práctica incluir el trait para las factorías.

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'unidad_transportes';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipo_unidad',
        'placa_identificador',
        'marca',
        'modelo',
        'anio',
        'tipo_combustible',
        'capacidad_tanque_litros',
        'estado_operativo',
        'kilometraje_actual',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'anio' => 'integer',
        'capacidad_tanque_litros' => 'decimal:2',
        'kilometraje_actual' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- RELACIONES ELOQUENT ---

    /**
     * Define la relación "uno a muchos" con Mantenimiento.
     * Una unidad de transporte puede tener muchos registros de mantenimiento.
     */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'unidad_transporte_id');
    }

    /**
     * Define la relación "uno a muchos" con ConsumoCombustible.
     * Una unidad de transporte puede tener muchos registros de consumo.
     */
    public function consumosCombustible(): HasMany
    {
        return $this->hasMany(ConsumoCombustible::class, 'unidad_transporte_id');
    }

    /**
     * Define la relación "uno a muchos" con SolicitudCombustible.
     * Una unidad de transporte puede estar en muchas solicitudes de combustible.
     */
    public function solicitudesCombustible(): HasMany
    {
        return $this->hasMany(SolicitudCombustible::class, 'unidad_transporte_id');
    }

    /**
     * Define la relación "uno a muchos" con GastoExtraTransporte.
     * Una unidad de transporte puede tener muchos gastos extra asociados.
     */
    public function gastosExtraTransporte(): HasMany
    {
        return $this->hasMany(GastoExtraTransporte::class, 'unidad_transporte_id');
    }
}
