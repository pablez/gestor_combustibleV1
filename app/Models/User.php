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
     * Relación con el supervisor (auto-referencia)
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relación con los conductores supervisados
     */
    public function conductores()
    {
        return $this->hasMany(User::class, 'supervisor_id');
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
        
        // Foto por defecto
        return asset('images/foto-perfil.png');
    }

    /**
     * Verificar si el usuario tiene foto de perfil personalizada
     */
    public function hasCustomProfilePhoto()
    {
        return $this->foto_perfil && Storage::disk('public')->exists($this->foto_perfil);
    }
}
