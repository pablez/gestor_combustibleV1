<?php

namespace App\Livewire\User;

use App\Models\User; // Importa el modelo User
use Livewire\Component;
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado
use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie Permission
use Illuminate\Validation\Rule; // Para reglas de validación
use Illuminate\Validation\ValidationException; // Para manejar excepciones de validación
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Trait para autorizar acciones

class UserCreate extends Component
{
    use AuthorizesRequests; // Usamos el trait AuthorizesRequests

    public $nombre = '';
    public $apellido = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRole = null; // Almacena el ID del rol seleccionado
    public $supervisor_id = null; // Almacena el ID del supervisor seleccionado
    public $supervisors = []; // Almacena la lista de supervisores
    public $conductorRoleId = null; // Almacena el ID del rol Conductor/Operador
    public $estado = 'Activo'; // Valor por defecto para el estado

    // Reglas de validación para el formulario
    protected function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'selectedRole' => ['required', 'integer', 'exists:roles,id'], // Valida un solo rol
            // El supervisor_id es opcional, pero si se proporciona, debe ser un supervisor válido
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    // Hook que se ejecuta cuando la propiedad $selectedRole cambia.
    public function updatedSelectedRole($value)
    {
        // Si el rol seleccionado no es 'Conductor/Operador',
        // asegurarse de que no se asigne ningún supervisor.
        if ($value != $this->conductorRoleId) {
            $this->supervisor_id = null;
        }
    }

    // Método que se ejecuta al montar el componente
    public function mount()
    {
        // Autorizar la acción de crear usuarios al cargar el componente
        $this->authorize('crear usuarios');

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();

        // Obtener y almacenar el ID del rol 'Conductor/Operador' para usarlo en la vista y en los hooks
        $this->conductorRoleId = Role::where('name', 'Conductor/Operador')->first()?->id;

        // Si el usuario es Administrador, cargar la lista de supervisores
        if ($authenticatedUser->hasRole('Administrador')) {
            $this->supervisors = User::whereHas('roles', function ($query) {
                $query->where('name', 'Supervisor');
            })->get();
        }
    }

    // Método para guardar el nuevo usuario
    public function saveUser()
    {
        // Autorizar la acción de crear usuarios antes de guardar
        $this->authorize('crear usuarios');
        
        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();

        // Validación específica para el Supervisor
        if ($authenticatedUser->hasRole('Supervisor')) {
            $conductorRole = Role::where('name', 'Conductor/Operador')->first();
            // Si el rol de conductor no existe, o si el rol seleccionado no es el de Conductor/Operador
            if (!$conductorRole || $this->selectedRole != $conductorRole->id) {
                // Lanzar un error de validación personalizado
                throw ValidationException::withMessages([
                    'selectedRole' => 'Como Supervisor, solo puedes crear usuarios con el rol Conductor/Operador.',
                ]);
            }
            // Asignar automáticamente el supervisor_id al ID del supervisor autenticado
            $this->supervisor_id = $authenticatedUser->id;
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

        // Crear el nuevo usuario
        $newUser = User::create([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'password' => bcrypt($this->password), // Cifrar la contraseña
            'estado' => $this->estado,
            'supervisor_id' => $this->supervisor_id, // Asignar el supervisor
        ]);

        // Asignar el rol seleccionado al nuevo usuario
        $role = Role::find($this->selectedRole);
        if ($role) {
            $newUser->assignRole($role);
        }

        // Redirigir de vuelta a la lista de usuarios con un mensaje de éxito
        session()->flash('message', 'Usuario creado correctamente y rol asignado.');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        // Autorizar la acción de ver roles al renderizar la vista (para mostrar el selector de roles)
        $this->authorize('ver roles');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roles = collect(); // Inicializamos como una colección vacía

        if ($user->hasRole('Administrador')) {
            // El administrador puede ver y asignar todos los roles
            $roles = Role::all();
        } elseif ($user->hasRole('Supervisor')) {
            // El supervisor solo puede ver y asignar el rol "Conductor/Operador"
            $roles = Role::where('name', 'Conductor/Operador')->get();
        }

        return view('livewire.user.user-create', [
            'roles' => $roles,
        ]);
    }
}