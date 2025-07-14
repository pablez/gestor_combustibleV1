<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserEdit extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public User $user;
    public $nombre = '';
    public $apellido = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRole = null;
    public $estado = '';
    public $foto_perfil = null;
    public $supervisor_id = null;
    public $supervisors;
    public $conductorRoleId;

    protected function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'selectedRole' => ['required', 'integer', 'exists:roles,id'],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'], // 2MB máximo
        ];
    }

    protected $messages = [
        'foto_perfil.image' => 'El archivo debe ser una imagen válida.',
        'foto_perfil.max' => 'La imagen no puede ser mayor a 2MB.',
    ];

    public function mount(User $user)
    {
        $this->authorize('editar usuarios');

        $this->user = $user;
        $this->nombre = $this->user->nombre;
        $this->apellido = $this->user->apellido;
        $this->email = $this->user->email;
        $this->estado = $this->user->estado;
        $this->selectedRole = $this->user->roles->first()->id ?? null;
        $this->supervisor_id = $this->user->supervisor_id;

        $this->loadSupervisorData();
    }

    public function updatedSelectedRole($value)
    {
        if ($value != $this->conductorRoleId) {
            $this->supervisor_id = null;
        }
    }

    private function loadSupervisorData()
    {
        $this->conductorRoleId = Role::where('name', 'Conductor/Operador')->first()->id ?? null;
        $this->supervisors = User::whereHas('roles', function ($query) {
            $query->where('name', 'Supervisor');
        })->get();
    }

    public function updateUser()
    {
        $this->authorize('editar usuarios');

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();

        // Validaciones de permisos
        if ($authenticatedUser->hasRole('Supervisor') && $this->user->estado === 'Pendiente') {
            throw ValidationException::withMessages([
                'estado' => 'No puede modificar un usuario que está pendiente de aprobación.'
            ]);
        }

        if ($authenticatedUser->hasRole('Supervisor')) {
            $conductorRole = Role::where('name', 'Conductor/Operador')->first();

            if ($this->user->hasRole(['Administrador', 'Supervisor'])) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'No tiene permisos para editar a este usuario.'
                ]);
            }

            if (!$conductorRole || $this->selectedRole != $conductorRole->id) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'Como Supervisor, solo puede asignar el rol de Conductor/Operador.'
                ]);
            }
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

        // Procesar la foto de perfil
        if ($this->foto_perfil) {
            // Eliminar la foto anterior si existe
            if ($this->user->foto_perfil && Storage::disk('public')->exists($this->user->foto_perfil)) {
                Storage::disk('public')->delete($this->user->foto_perfil);
            }
            
            // Guardar la nueva foto
            $fotoPath = $this->foto_perfil->store('fotos-perfil', 'public');
            $this->user->foto_perfil = $fotoPath;
        }

        // Actualizar los datos del usuario
        $this->user->nombre = $this->nombre;
        $this->user->apellido = $this->apellido;
        $this->user->email = $this->email;
        $this->user->estado = $this->estado;
        $this->user->supervisor_id = $this->supervisor_id;

        // Si se ingresó una nueva contraseña, cifrarla y actualizarla
        if (!empty($this->password)) {
            $this->user->password = bcrypt($this->password);
        }

        $this->user->save();

        // Actualizar el rol
        $role = Role::find($this->selectedRole);
        if ($role) {
            $this->user->syncRoles($role->name);
        }

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