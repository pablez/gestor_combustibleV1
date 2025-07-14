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
        $this->user = $user->load(['roles', 'supervisor', 'conductores']);
        
        // Autorizar que el usuario actual puede ver este usuario
        $this->authorize('ver usuarios');
        
        // Verificar permisos específicos según el rol
        $currentUser = Auth::user();
        
        if ($currentUser->hasRole('Supervisor')) {
            // Un supervisor solo puede ver usuarios que supervisa
            if ($this->user->supervisor_id !== $currentUser->id) {
                abort(403, 'No tienes permisos para ver este usuario.');
            }
        }
    }

    public function render()
    {
        return view('livewire.user.user-show');
    }
}
