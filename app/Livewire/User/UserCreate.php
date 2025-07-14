<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserCreate extends Component
{
    use WithFileUploads;

    // Propiedades del formulario
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $password_confirmation;
    public $estado = 'Activo';
    public $selectedRole;
    public $supervisor_id;
    public $foto_perfil; // Nueva propiedad para la foto

    // Propiedades auxiliares
    public $roles;
    public $supervisors;
    public $conductorRoleId;

    /**
     * Inicializa el componente con los datos necesarios
     */
    public function mount()
    {
        // Cargar todos los roles disponibles
        $this->roles = Role::all();
        
        // Inicializar supervisors como colección vacía
        $this->supervisors = collect();
        
        // Obtener el ID del rol Conductor/Operador
        $conductorRole = Role::where('name', 'Conductor/Operador')->first();
        $this->conductorRoleId = $conductorRole ? $conductorRole->id : null;
        
        // Cargar supervisores disponibles si el usuario es administrador
        if (Auth::user()->hasRole('Administrador')) {
            $this->supervisors = User::whereHas('roles', function ($query) {
                $query->where('name', 'Supervisor');
            })->get();
        }
    }

    // Reglas de validación para el formulario
    protected function rules()
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'selectedRole' => ['required', 'integer', 'exists:roles,id'],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'], // Máximo 2MB
        ];

        // Solo validar estado si el usuario es Administrador
        if (Auth::user()->hasRole('Administrador')) {
            $rules['estado'] = ['required', Rule::in(['Activo', 'Inactivo'])];
        }

        return $rules;
    }

    // Hook para previsualizar la foto
    public function updatedFotoPerfil()
    {
        $this->validate([
            'foto_perfil' => 'image|max:2048',
        ]);
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
            if (!$conductorRole || $this->selectedRole != $conductorRole->id) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'Como Supervisor, solo puedes crear usuarios con el rol Conductor/Operador.',
                ]);
            }
            $this->supervisor_id = $authenticatedUser->id;
        }

        $this->validate();

        // Validación adicional para el supervisor_id
        if ($this->supervisor_id) {
            $supervisor = User::find($this->supervisor_id);
            if (!$supervisor || !$supervisor->hasRole('Supervisor')) {
                throw ValidationException::withMessages([
                    'supervisor_id' => 'El usuario seleccionado no es un supervisor válido.',
                ]);
            }
        }

        // Preparar los datos del usuario
        $userData = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'supervisor_id' => $this->supervisor_id,
        ];

        // Procesar la foto de perfil
        if ($this->foto_perfil) {
            $fotoPath = $this->foto_perfil->store('fotos-perfil', 'public');
            $userData['foto_perfil'] = $fotoPath;
        }

        // Asignar estado basado en el rol del creador
        if ($authenticatedUser->hasRole('Administrador')) {
            $userData['estado'] = $this->estado;
        } else {
            $userData['estado'] = 'Pendiente';
        }

        // Crear el nuevo usuario
        $newUser = User::create($userData);

        // Asignar el rol seleccionado
        $role = Role::find($this->selectedRole);
        if ($role) {
            $newUser->assignRole($role);
        }

        // Mensaje de éxito personalizado según el creador
        if ($authenticatedUser->hasRole('Administrador')) {
            session()->flash('message', 'Usuario creado correctamente con estado: ' . $this->estado);
        } else {
            session()->flash('message', 'Usuario creado correctamente. Está pendiente de aprobación por un administrador.');
        }

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.user.user-create');
    }
}