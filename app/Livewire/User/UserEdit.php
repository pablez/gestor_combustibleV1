<?php

namespace App\Livewire\User;

use App\Models\User; // Importa el modelo User
use Livewire\Component;
use Illuminate\Support\Facades\Auth; // Para acceder al usuario autenticado
use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie Permission
use Illuminate\Validation\Rule; // Para reglas de validación
use Illuminate\Validation\ValidationException; // Para manejar excepciones de validación
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Trait para autorizar acciones

class UserEdit extends Component
{
    use AuthorizesRequests; // Usamos el trait AuthorizesRequests

    public User $user; // Propiedad para el modelo User que se está editando
    public $nombre = '';
    public $apellido = '';
    public $email = '';
    public $password = ''; // Opcional, solo si se quiere cambiar
    public $password_confirmation = '';
    public $selectedRole = null; // Usamos selectedRole (singular) para un solo rol
    public $estado = '';

    // Nuevas propiedades para la asignación de supervisor
    public $supervisor_id = null;
    public $supervisors;
    public $conductorRoleId;

    // Reglas de validación para el formulario
    protected function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            // El email debe ser único, excepto para el usuario actual
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // 'nullable' permite no cambiar la contraseña
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            // La validación ahora es para un solo rol (entero)
            'selectedRole' => ['required', 'integer', 'exists:roles,id'],
            // Nueva regla para el supervisor_id
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    // Método que se ejecuta al montar el componente, recibe el modelo User inyectado por la ruta
    public function mount(User $user)
    {
        // Autorizar la acción de editar usuarios al cargar el componente
        $this->authorize('editar usuarios');

        $this->user = $user; // Asigna el modelo User recibido a la propiedad

        // Rellenar las propiedades públicas con los datos del usuario
        $this->nombre = $this->user->nombre;
        $this->apellido = $this->user->apellido;
        $this->email = $this->user->email;
        $this->estado = $this->user->estado;
        // Asignamos el primer (y único) rol del usuario a selectedRole
        $this->selectedRole = $this->user->roles->first()->id ?? null;
        // Asignamos el supervisor actual
        $this->supervisor_id = $this->user->supervisor_id;

        // Cargamos los datos necesarios para el selector de supervisor
        $this->loadSupervisorData();
    }

    // Hook que se ejecuta cuando se actualiza la propiedad selectedRole
    public function updatedSelectedRole($value)
    {
        // Si el rol seleccionado no es 'Conductor/Operador', reseteamos el supervisor_id
        if ($value != $this->conductorRoleId) {
            $this->supervisor_id = null;
        }
    }

    // Método para cargar los datos del supervisor
    private function loadSupervisorData()
    {
        // Obtenemos el ID del rol 'Conductor/Operador'
        $this->conductorRoleId = Role::where('name', 'Conductor/Operador')->first()->id;

        // Obtenemos todos los usuarios con el rol 'Supervisor'
        $this->supervisors = User::whereHas('roles', function ($query) {
            $query->where('name', 'Supervisor');
        })->get();
    }

    // Método para actualizar el usuario
    public function updateUser()
    {
        // Autorizar la acción de editar usuarios antes de guardar
        $this->authorize('editar usuarios');

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();

        if ($authenticatedUser->hasRole('Supervisor')) {
            $conductorRole = Role::where('name', 'Conductor/Operador')->first();

            if ($this->user->hasRole(['Administrador', 'Supervisor'])) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'No tiene permisos para editar a este usuario.'
                ]);
            }

            // La comprobación ahora usa this->selectedRole
            if (!$conductorRole || $this->selectedRole != $conductorRole->id) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'Como Supervisor, solo puede asignar el rol de Conductor/Operador.'
                ]);
            }
        }

        $this->validate(); // Valida los datos del formulario

        // Validación adicional para el supervisor_id
        if ($this->supervisor_id) {
            $supervisor = User::find($this->supervisor_id);
            if (!$supervisor || !$supervisor->hasRole('Supervisor')) {
                throw ValidationException::withMessages([
                    'supervisor_id' => 'El usuario seleccionado no es un supervisor válido.',
                ]);
            }
        }

        // Lógica de validación adicional para el supervisor
        if ($authenticatedUser->hasRole('Administrador') && $this->selectedRole == $this->conductorRoleId && is_null($this->supervisor_id)) {
            // Si es admin, el rol es conductor, pero no se ha seleccionado supervisor, lanzamos error.
            // Esto se puede ajustar si se permite "no asignar supervisor"
        } elseif ($this->selectedRole != $this->conductorRoleId) {
            $this->supervisor_id = null; // Asegurarse de que no se asigne supervisor si no es conductor
        }

        // Actualizar los datos del usuario
        $this->user->nombre = $this->nombre;
        $this->user->apellido = $this->apellido;
        $this->user->email = $this->email;
        $this->user->estado = $this->estado;
        $this->user->supervisor_id = $this->supervisor_id; // Guardar el supervisor

        // Si se ingresó una nueva contraseña, cifrarla y actualizarla
        if (!empty($this->password)) {
            $this->user->password = bcrypt($this->password);
        }

        $this->user->save();

        // Buscamos el modelo del rol por su ID
        $role = Role::find($this->selectedRole);
        // Sincronizamos el rol pasando el nombre del rol encontrado
        if ($role) {
            $this->user->syncRoles($role->name);
        }

        // Redirigir de vuelta a la lista de usuarios con un mensaje de éxito
        session()->flash('message', 'Usuario actualizado correctamente.');
        return redirect()->route('admin.users.index');
    }


    public function render()
    {
        $this->authorize('ver roles');

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();
        $roles = collect();

        if ($authenticatedUser->hasRole('Administrador')) {
            $roles = Role::all();
        } elseif ($authenticatedUser->hasRole('Supervisor')) {
            $roles = Role::where('name', 'Conductor/Operador')->get();
        }

        return view('livewire.user.user-edit', [
            'roles' => $roles,
        ]);
    }
}