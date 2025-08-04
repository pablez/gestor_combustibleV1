<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\UnidadOrganizacional;
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
    public $roleFilter = '';
    public $statusFilter = '';
    public $unidadFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage',
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'unidadFilter' => ['except' => '']
    ];

    /**
     * Determina si el usuario autenticado puede gestionar al usuario objetivo.
     * Lógica actualizada según la jerarquía específica.
     */
    public function canManage(User $targetUser): bool
    {
        $currentUser = Auth::user();

        // Nadie puede gestionarse a sí mismo
        if ($currentUser->id === $targetUser->id) {
            return false;
        }

        // Admin General puede gestionar a todos los usuarios
        if ($currentUser->hasRole('Admin General')) {
            return true;
        }

        // Admin puede gestionar solo a Supervisores y Conductores de su misma unidad organizacional
        if ($currentUser->hasRole('Admin')) {
            return $targetUser->hasRole(['Supervisor', 'Conductor/Operador']) &&
                   $targetUser->unidad_organizacional_id === $currentUser->unidad_organizacional_id;
        }

        // Supervisor puede gestionar solo a Conductores/Operadores de su misma unidad y bajo su supervisión
        if ($currentUser->hasRole('Supervisor')) {
            return $targetUser->hasRole('Conductor/Operador') &&
                   $targetUser->unidad_organizacional_id === $currentUser->unidad_organizacional_id &&
                   $targetUser->supervisor_id === $currentUser->id;
        }

        return false;
    }

    public function deleteUser(User $user)
    {
        $this->authorize('eliminar usuarios');

        if (!$this->canManage($user)) {
            session()->flash('error', 'No tienes permisos para eliminar a este usuario.');
            return;
        }

        $user->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
    }

    /**
     * Obtiene la consulta base de usuarios que el usuario actual puede gestionar
     * según la jerarquía específica definida.
     */
    private function getManagableUsersQuery()
    {
        $currentUser = Auth::user();
        
        // Admin General puede ver todos los usuarios
        if ($currentUser->hasRole('Admin General')) {
            return User::query();
        }

        // Admin puede ver solo Supervisores y Conductores de su misma unidad organizacional
        if ($currentUser->hasRole('Admin')) {
            return User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Supervisor', 'Conductor/Operador']);
            })->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id);
        }

        // Supervisor puede ver solo Conductores/Operadores de su misma unidad y bajo su supervisión
        if ($currentUser->hasRole('Supervisor')) {
            return User::where('supervisor_id', $currentUser->id)
                      ->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)
                      ->whereHas('roles', function ($q) {
                          $q->where('name', 'Conductor/Operador');
                      });
        }

        // Si no tiene ninguno de los roles anteriores, no puede ver usuarios
        return User::where('id', -1);
    }

    public function getTotalUsersCountProperty()
    {
        return $this->getManagableUsersQuery()->count();
    }

    public function getActiveUsersCountProperty()
    {
        return $this->getManagableUsersQuery()->where('estado', 'Activo')->count();
    }

    public function getPendingUsersCountProperty()
    {
        return $this->getManagableUsersQuery()->where('estado', 'Pendiente')->count();
    }

    public function getInactiveUsersCountProperty()
    {
        return $this->getManagableUsersQuery()->where('estado', 'Inactivo')->count();
    }

    // Resetear la página al cambiar cualquier filtro
    public function updatedRoleFilter() { $this->resetPage(); }
    public function updatedUnidadFilter() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedSearch() { $this->resetPage(); }

    /**
     * Obtiene las unidades organizacionales que el usuario puede filtrar
     */
    private function getFilterableUnidades()
    {
        $currentUser = Auth::user();
        
        // Admin General puede filtrar por todas las unidades
        if ($currentUser->hasRole('Admin General')) {
            return UnidadOrganizacional::activas()->orderBy('nombre_unidad')->get();
        }

        // Admin y Supervisor solo pueden filtrar por su propia unidad
        if ($currentUser->hasRole(['Admin', 'Supervisor']) && $currentUser->unidadOrganizacional) {
            return collect([$currentUser->unidadOrganizacional]);
        }

        return collect();
    }

    /**
     * Obtiene los roles que el usuario puede filtrar
     */
    private function getFilterableRoles()
    {
        $currentUser = Auth::user();
        
        if ($currentUser->hasRole('Admin General')) {
            // Solo mostrar filtros para Admin, Supervisor y Conductor/Operador
            return ['Admin', 'Supervisor', 'Conductor/Operador'];
        }

        if ($currentUser->hasRole('Admin')) {
            return ['Supervisor', 'Conductor/Operador'];
        }

        if ($currentUser->hasRole('Supervisor')) {
            return ['Conductor/Operador'];
        }

        return [];
    }

    public function render()
    {
        $this->authorize('ver usuarios');
        $currentUser = Auth::user();

        // Iniciar la consulta con los usuarios que el usuario actual puede gestionar
        $query = $this->getManagableUsersQuery()->with(['roles', 'supervisor', 'unidadOrganizacional']);

        // Aplicar filtro de búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('apellido', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de rol
        if ($this->roleFilter) {
            if ($this->roleFilter === 'sin-rol') {
                $query->whereDoesntHave('roles');
            } else {
                if ($this->roleFilter === 'Admin') {
                    $query->whereHas('roles', function($q) {
                        $q->whereIn('name', ['Admin', 'Admin General']);
                    });
                } else {
                    $query->whereHas('roles', function($q) {
                        $q->where('name', $this->roleFilter);
                    });
                }
            }
        }

        // Aplicar filtro de estado
        if ($this->statusFilter) {
            $query->where('estado', $this->statusFilter);
        }

        // Aplicar filtro de unidad organizacional
        if ($this->unidadFilter) {
            $unidadId = (int) $this->unidadFilter;
            $query->where('unidad_organizacional_id', $unidadId);
        }

        // Excluir al usuario actual de la lista
        $users = $query->where('id', '!=', $currentUser->id)
                       ->latest()
                       ->paginate($this->perPage);

        // Obtener roles y unidades filtrables según el usuario actual
        $managableRoles = $this->getFilterableRoles();
        $unidadesOrganizacionales = $this->getFilterableUnidades();

        return view('livewire.user.user-index', [
            'users' => $users,
            'managableRoles' => $managableRoles,
            'unidadesOrganizacionales' => $unidadesOrganizacionales,
        ]);
    }
}