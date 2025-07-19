<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class UserShow extends Component
{
    use AuthorizesRequests;

    public User $user;

    public function mount(User $user)
    {
        // Cargar las relaciones necesarias
        $this->user = $user->load([
            'roles', 
            'supervisor', 
            'conductores.roles', 
            'conductores.unidadOrganizacional',
            'unidadOrganizacional',
            'supervisados' // Relación para usuarios que supervisa
        ]);
        
        // Autorizar que el usuario actual puede ver este usuario
        $this->authorize('ver usuarios');
        
        // Verificar permisos específicos según el rol
        $currentUser = Auth::user();
        
        if ($currentUser->hasRole('Supervisor')) {
            // Un supervisor solo puede ver usuarios que supervisa o su propio perfil
            if ($this->user->supervisor_id !== $currentUser->id && $this->user->id !== $currentUser->id) {
                abort(403, 'No tienes permisos para ver este usuario.');
            }
        } elseif ($currentUser->hasRole('Admin')) {
            // Un admin puede ver usuarios de su unidad organizacional
            if ($this->user->unidad_organizacional_id !== $currentUser->unidad_organizacional_id && 
                $this->user->supervisor_id !== $currentUser->id) {
                abort(403, 'No tienes permisos para ver este usuario.');
            }
        }
        // Admin General puede ver todos los usuarios
    }

    /**
     * Obtiene estadísticas del usuario según su rol
     */
    public function getUserStats()
    {
        $stats = [];
        
        if ($this->user->hasRole('Admin General')) {
            $stats = [
                'admins_total' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Admin');
                })->count(),
                'supervisores_total' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Supervisor');
                })->count(),
                'conductores_total' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Conductor/Operador');
                })->count(),
                'usuarios_pendientes' => User::where('estado', 'Pendiente')->count(),
            ];
        } elseif ($this->user->hasRole('Admin')) {
            $stats = [
                'supervisores_asignados' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Supervisor');
                })->where('supervisor_id', $this->user->id)->count(),
                'conductores_en_unidad' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Conductor/Operador');
                })->where('unidad_organizacional_id', $this->user->unidad_organizacional_id)->count(),
                'usuarios_activos' => User::where('supervisor_id', $this->user->id)
                    ->orWhere('unidad_organizacional_id', $this->user->unidad_organizacional_id)
                    ->where('estado', 'Activo')->count(),
            ];
        } elseif ($this->user->hasRole('Supervisor')) {
            $stats = [
                'conductores_activos' => $this->user->conductores->where('estado', 'Activo')->count(),
                'conductores_inactivos' => $this->user->conductores->where('estado', 'Inactivo')->count(),
                'conductores_pendientes' => $this->user->conductores->where('estado', 'Pendiente')->count(),
            ];
        }
        
        return $stats;
    }

    /**
     * Obtiene la cadena de supervisión del usuario
     */
    public function getSupervisionChain()
    {
        $chain = [];
        $currentUser = $this->user;
        
        // Construir la cadena hacia arriba
        while ($currentUser->supervisor) {
            $chain[] = $currentUser->supervisor;
            $currentUser = $currentUser->supervisor;
        }
        
        return $chain;
    }

    /**
     * Obtiene los usuarios que supervisa directamente
     */
    public function getDirectSupervisees()
    {
        return User::where('supervisor_id', $this->user->id)
            ->with(['roles', 'unidadOrganizacional'])
            ->get();
    }

    public function render()
    {
        return view('livewire.user.user-show', [
            'userStats' => $this->getUserStats(),
            'supervisionChain' => $this->getSupervisionChain(),
            'directSupervisees' => $this->getDirectSupervisees(),
        ]);
    }
}
