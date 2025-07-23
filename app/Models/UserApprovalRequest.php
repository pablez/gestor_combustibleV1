<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserApprovalRequest extends Model
{
    protected $fillable = [
        'usuario_id',
        'creado_por',
        'supervisor_asignado_id',
        'tipo_solicitud',
        'estado',
        'rol_solicitado',
        'razon',
        'aprobado_por',
        'fecha_aprobacion',
        'razon_rechazo',
        'comentarios_aprobacion',
        'rol_creador',
        'rol_aprobador',
        'unidad_organizacional_id',
        'datos_usuario',
        'metadatos_aprobacion'
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
        'datos_usuario' => 'array',
        'metadatos_aprobacion' => 'array',
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function supervisorAsignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_asignado_id');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function unidadOrganizacional(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_organizacional_id', 'id_unidad_organizacional');
    }

    // Scopes para consultas
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobado($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeRechazado($query)
    {
        return $query->where('estado', 'rechazado');
    }

    public function scopeEnRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    // Scope para Admin General - Ve TODO
    public function scopeParaAdminGeneral($query)
    {
        return $query->with(['usuario.roles', 'usuario.unidadOrganizacional', 'creador', 'aprobador']);
    }

    // Scope para Admin - Ve solicitudes de Supervisores de su unidad + usuarios que él aprobó de su unidad
    public function scopeParaAdmin($query, $admin)
    {
        return $query->with(['usuario.roles', 'usuario.unidadOrganizacional', 'creador', 'aprobador'])
            ->where(function($q) use ($admin) {
                $q->where(function($subQuery) use ($admin) {
                    // Solicitudes pendientes de Supervisores de su unidad
                    $subQuery->where('estado', 'pendiente')
                             ->where('unidad_organizacional_id', $admin->unidad_organizacional_id)
                             ->where('rol_creador', 'Supervisor')
                             ->where('rol_solicitado', 'Conductor/Operador');
                })->orWhere(function($subQuery) use ($admin) {
                    // Solicitudes que él aprobó de su unidad (para reportes)
                    $subQuery->where('aprobado_por', $admin->id)
                             ->where('unidad_organizacional_id', $admin->unidad_organizacional_id);
                });
            });
    }

    // Métodos para manejo de aprobaciones
    public function aprobar(User $aprobador, ?string $comentarios = null): bool
    {
        $this->update([
            'estado' => 'aprobado',
            'aprobado_por' => $aprobador->id,
            'fecha_aprobacion' => now(),
            'comentarios_aprobacion' => $comentarios,
            'rol_aprobador' => $aprobador->roles->first()->name ?? 'Sin rol',
        ]);

        // Activar usuario
        $this->usuario->update(['estado' => 'Activo']);

        return true;
    }

    public function rechazar(User $rechazador, string $razon): bool
    {
        $this->update([
            'estado' => 'rechazado',
            'razon_rechazo' => $razon,
            'aprobado_por' => $rechazador->id,
            'fecha_aprobacion' => now(),
            'rol_aprobador' => $rechazador->roles->first()->name ?? 'Sin rol',
        ]);

        // Eliminar usuario rechazado
        $this->usuario->delete();

        return true;
    }

    // Métodos para reportes
    public function getTiempoProcesamiento()
    {
        if (!$this->fecha_aprobacion) {
            return $this->created_at->diffInDays(now());
        }
        
        return $this->created_at->diffInDays($this->fecha_aprobacion);
    }

    public function getClaseBadgeEstado()
    {
        return match($this->estado) {
            'pendiente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'aprobado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'rechazado' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        };
    }
}
