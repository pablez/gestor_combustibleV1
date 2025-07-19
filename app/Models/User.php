<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'estado',
        'supervisor_id',
        'unidad_organizacional_id',
        'foto_perfil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con el supervisor (quien lo creó/supervisa)
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relación con los usuarios que este usuario supervisa
     */
    public function subordinados()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Relación con los conductores supervisados
     */
    public function conductores()
    {
        return $this->hasMany(User::class, 'supervisor_id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Conductor/Operador');
            });
    }

    /**
     * Relación específica para supervisores supervisados (para admins)
     */
    public function supervisoresAsignados()
    {
        return $this->hasMany(User::class, 'supervisor_id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Supervisor');
            });
    }

    /**
     * Relación específica para admins supervisados (para admin general)
     */
    public function adminsAsignados()
    {
        return $this->hasMany(User::class, 'supervisor_id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Admin');
            });
    }

    /**
     * Relación con la unidad organizacional a la que pertenece
     */
    public function unidadOrganizacional()
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_organizacional_id', 'id_unidad_organizacional');
    }

    /**
     * Obtener la URL de la foto de perfil
     */
    public function getFotoPerfilUrlAttribute()
    {
        // Si el usuario tiene una foto de perfil personalizada
        if ($this->foto_perfil && Storage::disk('public')->exists($this->foto_perfil)) {
            return asset('storage/' . $this->foto_perfil);
        }
        
        // Generar avatar por defecto basado en las iniciales
        $iniciales = strtoupper(substr($this->nombre, 0, 1) . substr($this->apellido ?? '', 0, 1));
        return "https://ui-avatars.com/api/?name={$iniciales}&color=7F9CF5&background=EBF4FF&size=128";
    }

    /**
     * Verificar si el usuario tiene foto de perfil personalizada
     */
    public function hasCustomProfilePhoto()
    {
        return $this->foto_perfil && Storage::disk('public')->exists($this->foto_perfil);
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    /**
     * Verificar si el usuario pertenece a una unidad organizacional específica
     */
    public function perteneceAUnidad($unidadId)
    {
        return $this->unidad_organizacional_id == $unidadId;
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'Activo');
    }

    /**
     * Scope para usuarios inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('estado', 'Inactivo');
    }

    /**
     * Scope para usuarios pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'Pendiente');
    }

    /**
     * Scope para usuarios con rol específico
     */
    public function scopeConRol($query, $rol)
    {
        return $query->whereHas('roles', function ($q) use ($rol) {
            $q->where('name', $rol);
        });
    }

    /**
     * Scope para usuarios de una unidad organizacional específica
     */
    public function scopeDeUnidad($query, $unidadId)
    {
        return $query->where('unidad_organizacional_id', $unidadId);
    }

    /**
     * Obtener todos los usuarios en la cadena de supervisión hacia arriba
     */
    public function getCadenaSupervisionAttribute()
    {
        $cadena = collect();
        $usuario = $this;

        while ($usuario->supervisor) {
            $cadena->push($usuario->supervisor);
            $usuario = $usuario->supervisor;
        }

        return $cadena;
    }

    /**
     * Obtener todos los usuarios supervisados de forma recursiva
     */
    public function getTodosSupervisionadosAttribute()
    {
        $supervisados = collect();
        
        $this->supervisados->each(function ($user) use ($supervisados) {
            $supervisados->push($user);
            $supervisados = $supervisados->merge($user->todos_supervisionados);
        });

        return $supervisados;
    }

    /**
     * Verificar si el usuario puede supervisar a otro usuario
     */
    public function puedeSupervizar(User $usuario)
    {
        return $this->id === $usuario->supervisor_id || 
               $this->todos_supervisionados->contains('id', $usuario->id);
    }

    /**
     * Verificar si el usuario está en la misma unidad organizacional
     */
    public function mismaunidad(User $usuario)
    {
        return $this->unidad_organizacional_id === $usuario->unidad_organizacional_id;
    }

    /**
     * Obtener estadísticas del usuario según su rol
     */
    public function getEstadisticasAttribute()
    {
        $estadisticas = [];

        if ($this->hasRole('Admin General')) {
            $estadisticas = [
                'total_usuarios' => User::count(),
                'usuarios_activos' => User::activos()->count(),
                'usuarios_pendientes' => User::pendientes()->count(),
                'total_admins' => User::conRol('Admin')->count(),
                'total_supervisores' => User::conRol('Supervisor')->count(),
                'total_conductores' => User::conRol('Conductor/Operador')->count(),
            ];
        } elseif ($this->hasRole('Admin')) {
            $estadisticas = [
                'supervisores_asignados' => $this->supervisoresAsignados()->count(),
                'conductores_unidad' => User::conRol('Conductor/Operador')
                    ->deUnidad($this->unidad_organizacional_id)->count(),
                'usuarios_activos_unidad' => User::activos()
                    ->deUnidad($this->unidad_organizacional_id)->count(),
            ];
        } elseif ($this->hasRole('Supervisor')) {
            $estadisticas = [
                'conductores_supervisados' => $this->conductores()->count(),
                'conductores_activos' => $this->conductores()->activos()->count(),
                'conductores_pendientes' => $this->conductores()->pendientes()->count(),
            ];
        }

        return $estadisticas;
    }

    /**
     * Verificar si el usuario necesita aprobación
     */
    public function necesitaAprobacion()
    {
        return $this->estado === 'Pendiente';
    }

    /**
     * Aprobar usuario
     */
    public function aprobar()
    {
        $this->update(['estado' => 'Activo']);
    }

    /**
     * Rechazar usuario
     */
    public function rechazar()
    {
        $this->update(['estado' => 'Inactivo']);
    }

    /**
     * Obtener el rol principal del usuario
     */
    public function getRolPrincipalAttribute()
    {
        return $this->roles->first()->name ?? 'Sin rol';
    }

    /**
     * Verificar si el usuario puede ser editado por el usuario actual
     */
    public function puedeSerEditadoPor(User $usuario)
    {
        // Admin General puede editar a todos
        if ($usuario->hasRole('Admin General')) {
            return true;
        }

        // Admin puede editar usuarios de su unidad organizacional
        if ($usuario->hasRole('Admin')) {
            return $this->unidad_organizacional_id === $usuario->unidad_organizacional_id ||
                   $this->supervisor_id === $usuario->id;
        }

        // Supervisor puede editar solo conductores que supervisa
        if ($usuario->hasRole('Supervisor')) {
            return $this->supervisor_id === $usuario->id && 
                   $this->hasRole('Conductor/Operador');
        }

        return false;
    }

    /**
     * Verificar si el usuario puede ser visto por el usuario actual
     */
    public function puedeSerVistoPor(User $usuario)
    {
        // Admin General puede ver a todos
        if ($usuario->hasRole('Admin General')) {
            return true;
        }

        // Admin puede ver usuarios de su unidad organizacional
        if ($usuario->hasRole('Admin')) {
            return $this->unidad_organizacional_id === $usuario->unidad_organizacional_id ||
                   $this->supervisor_id === $usuario->id ||
                   $this->id === $usuario->id;
        }

        // Supervisor puede ver usuarios que supervisa y su propio perfil
        if ($usuario->hasRole('Supervisor')) {
            return $this->supervisor_id === $usuario->id || 
                   $this->id === $usuario->id;
        }

        // Conductor solo puede ver su propio perfil
        return $this->id === $usuario->id;
    }
}
