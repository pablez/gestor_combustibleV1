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

    protected $queryString = ['search' => ['except' => ''], 'perPage']; 

    public function updatingSearch()
    {
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

    public function render()
    {
        $this->authorize('ver usuarios'); // Autorizar la acción de ver usuarios

        // Obtener el usuario actual con sus roles
        $currentUser = User::with('roles')->find(Auth::id());

        // Obtener usuarios con búsqueda, paginación y filtrado por rol
        $users = User::with('roles') // Cargar roles de todos los usuarios
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('apellido', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            // Filtrar usuarios por rol según el usuario autenticado
            ->when($currentUser->hasRole('Supervisor'), function ($query) {
                // Si es Supervisor, solo mostrar usuarios con rol "Conductor/Operador"
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'Conductor/Operador');
                });
            })
            
            // Si es Administrador, se muestran todos los usuarios (sin filtro adicional)
            ->orderBy('id', 'asc') // Ordenar por el ID más reciente
            ->paginate($this->perPage);

        // Obtener roles para la asignación si se muestra en la tabla
        $roles = Role::all();

        return view('livewire.user.user-index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}