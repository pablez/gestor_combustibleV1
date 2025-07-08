<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\SolicitudCombustible;
use App\Models\SolicitudViatico;
use App\Models\GastoExtraTransporte;
use App\Models\ConsumoCombustible;
use App\Models\RegistroAuditoria;   

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.   
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'estado',
        'supervisor_id', // Añadido para permitir asignación masiva
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación para obtener el supervisor de un usuario
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Relación para obtener los subordinados de un supervisor
    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    // Relaciones
    public function solicitudesCombustible(): HasMany
    {
        return $this->hasMany(SolicitudCombustible::class, 'usuario_id');
    }

    public function solicitudesViatico(): HasMany
    {
        return $this->hasMany(SolicitudViatico::class, 'usuario_id');
    }

    public function gastosExtraTransporte(): HasMany
    {
        return $this->hasMany(GastoExtraTransporte::class, 'usuario_id');
    }

    public function consumosCombustible(): HasMany
    {
        return $this->hasMany(ConsumoCombustible::class, 'conductor_id');
    }

    public function registrosAuditoria(): HasMany
    {
        return $this->hasMany(RegistroAuditoria::class, 'usuario_id');
    }

    // Relación para supervisores de solicitudes de combustible
    public function supervisedFuelRequests(): HasMany
    {
        return $this->hasMany(SolicitudCombustible::class, 'supervisor_id');
    }

    // Relación para supervisores de solicitudes de viático
    public function supervisedTravelRequests(): HasMany
    {
        return $this->hasMany(SolicitudViatico::class, 'supervisor_id');
    }

    // Relación para supervisores de gastos extra
    public function supervisedExtraExpenses(): HasMany
    {
        return $this->hasMany(GastoExtraTransporte::class, 'supervisor_id');
    }
}
