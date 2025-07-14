<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApprovalQueue extends Component
{
    use AuthorizesRequests;

    public $selectedUser = null;
    public $showUserDetails = false;

    public function mount()
    {
        $this->authorize('aprobar usuarios');
    }

    /**
     * Aprueba un usuario cambiando su estado a 'Activo'.
     */
    public function approve($userId)
    {
        $user = User::findOrFail($userId);
        
        $user->update([
            'estado' => 'Activo'
        ]);
        
        session()->flash('success', "Usuario {$user->nombre} aprobado exitosamente.");
        
        // Cerrar el modal si está abierto
        if ($this->selectedUser && $this->selectedUser->id === $userId) {
            $this->closeUserDetails();
        }
    }

    /**
     * Rechaza y elimina permanentemente a un usuario.
     */
    public function reject($userId)
    {
        $user = User::findOrFail($userId);
        
        // Cerrar el modal si está abierto
        if ($this->selectedUser && $this->selectedUser->id === $userId) {
            $this->closeUserDetails();
        }
        
        $user->delete();
        
        session()->flash('success', "Usuario {$user->nombre} rechazado y eliminado.");
    }

    public function viewDetails($userId)
    {
        $this->selectedUser = User::with(['roles', 'supervisor'])->findOrFail($userId);
        $this->showUserDetails = true;
    }

    public function closeUserDetails()
    {
        $this->selectedUser = null;
        $this->showUserDetails = false;
    }

    /**
     * Renderiza la vista con la lista de usuarios pendientes.
     */
    public function render()
    {
        $users = User::where('estado', 'Pendiente')
            ->with(['roles', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.user.approval-queue', compact('users'));
    }
}
