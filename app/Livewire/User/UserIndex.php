<?php

namespace App\Livewire\User;

use App\Models\User; 
use Livewire\Component;
use Livewire\WithPagination; 
use Spatie\Permission\Models\Role; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 
use Illuminate\Support\Facades\Auth;

class UserIndex extends Component
{
    use WithPagination, AuthorizesRequests; 

    public $search = ''; 
    public $perPage = 10; 
    public $roleFilter = ''; // Filtro para roles
    public $statusFilter = ''; // Filtro para estados

    protected $queryString = [
        'search' => ['except' => ''], 
        'perPage', 
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => '']
    ]; 

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function deleteUser(User $user)
    {
        $this->authorize('eliminar usuarios');

        $currentUser = User::with('roles')->find(Auth::id());

        // Solo los Administradores pueden eliminar usuarios
        if (!$currentUser->hasRole('Administrador')) {
            session()->flash('error', 'No tienes permisos para eliminar usuarios.');
            return;
        }

        // Opcional: Evitar que el usuario actual se elimine a sí mismo
        if (Auth::user()->id === $user->id) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        $user->delete();

        session()->flash('message', 'Usuario eliminado correctamente.');
    }

    // Propiedades computadas para las estadísticas
    public function getActiveUsersCountProperty()
    {
        $currentUser = User::with('roles')->find(Auth::id());

        if ($currentUser->hasRole('Administrador')) {
            // Administradores ven todos los usuarios activos
            return User::where('estado', 'Activo')->count();
        } elseif ($currentUser->hasRole('Supervisor')) {
            // Supervisores solo ven usuarios bajo su supervisión
            return User::where('estado', 'Activo')
                      ->where('supervisor_id', $currentUser->id)
                      ->count();
        }

        return 0;
    }

    public function getPendingUsersCountProperty()
    {
        $currentUser = User::with('roles')->find(Auth::id());

        if ($currentUser->hasRole('Administrador')) {
            // Administradores ven todos los usuarios pendientes
            return User::where('estado', 'Pendiente')->count();
        } elseif ($currentUser->hasRole('Supervisor')) {
            // Supervisores solo ven usuarios pendientes bajo su supervisión
            return User::where('estado', 'Pendiente')
                      ->where('supervisor_id', $currentUser->id)
                      ->count();
        }

        return 0;
    }

    public function getInactiveUsersCountProperty()
    {
        $currentUser = User::with('roles')->find(Auth::id());

        if ($currentUser->hasRole('Administrador')) {
            // Administradores ven todos los usuarios inactivos
            return User::where('estado', 'Inactivo')->count();
        } elseif ($currentUser->hasRole('Supervisor')) {
            // Supervisores solo ven usuarios inactivos bajo su supervisión
            return User::where('estado', 'Inactivo')
                      ->where('supervisor_id', $currentUser->id)
                      ->count();
        }

        return 0;
    }

    public function getTotalUsersCountProperty()
    {
        $currentUser = User::with('roles')->find(Auth::id());

        if ($currentUser->hasRole('Administrador')) {
            // Administradores ven todos los usuarios
            return User::count();
        } elseif ($currentUser->hasRole('Supervisor')) {
            // Supervisores solo ven usuarios bajo su supervisión
            return User::where('supervisor_id', $currentUser->id)->count();
        }

        return 0;
    }

    public function render()
    {
        $this->authorize('ver usuarios');

        // Obtener el usuario actual con sus roles
        $currentUser = User::with('roles')->find(Auth::id());

        // Construir la consulta base
        $query = User::with(['roles', 'supervisor']);

        // Aplicar filtro de búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('apellido', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtros según el rol del usuario autenticado
        if ($currentUser->hasRole('Administrador')) {
            // Los administradores pueden ver todos los usuarios
            
            // Aplicar filtro de rol si está seleccionado
            if ($this->roleFilter) {
                if ($this->roleFilter === 'sin-rol') {
                    $query->whereDoesntHave('roles');
                } else {
                    $query->whereHas('roles', function ($subQuery) {
                        $subQuery->where('name', $this->roleFilter);
                    });
                }
            }

            // Aplicar filtro de estado si está seleccionado
            if ($this->statusFilter) {
                $query->where('estado', $this->statusFilter);
            }

        } elseif ($currentUser->hasRole('Supervisor')) {
            // Los supervisores solo pueden ver usuarios bajo su supervisión
            $query->where('supervisor_id', $currentUser->id);

            // Aplicar filtro de estado si está seleccionado
            if ($this->statusFilter) {
                $query->where('estado', $this->statusFilter);
            }
        }

        // Obtener usuarios paginados
        $users = $query->latest()->paginate($this->perPage);

        // Obtener roles para la asignación si se muestra en la tabla
        $roles = Role::all();

        return view('livewire.user.user-index', [
            'users' => $users,
            'roles' => $roles,
            'activeUsersCount' => $this->activeUsersCount,
            'pendingUsersCount' => $this->pendingUsersCount,
            'inactiveUsersCount' => $this->inactiveUsersCount,
            'totalUsersCount' => $this->totalUsersCount,
        ]);
    }
}