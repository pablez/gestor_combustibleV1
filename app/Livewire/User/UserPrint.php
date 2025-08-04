<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\UnidadOrganizacional;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserPrint extends Component
{
    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $unidadFilter = '';
    public $users = [];
    public $unidadesOrganizacionales = [];

    public function mount()
    {
        $this->search = request()->query('search', '');
        $this->roleFilter = request()->query('roleFilter', '');
        $this->statusFilter = request()->query('statusFilter', '');
        $this->unidadFilter = request()->query('unidadFilter', '');

        $currentUser = Auth::user();
        $query = $this->getManagableUsersQuery($currentUser);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('apellido', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
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
        if ($this->statusFilter) {
            $query->where('estado', $this->statusFilter);
        }
        if ($this->unidadFilter) {
            $unidadId = (int) $this->unidadFilter;
            $query->where('unidad_organizacional_id', $unidadId);
        }

        $this->users = $query->with(['roles', 'unidadOrganizacional'])->get();
        $this->unidadesOrganizacionales = UnidadOrganizacional::all();
    }

    private function getManagableUsersQuery($currentUser)
    {
        if ($currentUser->hasRole('Admin General')) {
            return User::query();
        }
        if ($currentUser->hasRole('Admin')) {
            return User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Supervisor', 'Conductor/Operador']);
            })->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id);
        }
        if ($currentUser->hasRole('Supervisor')) {
            return User::where('supervisor_id', $currentUser->id)
                      ->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)
                      ->whereHas('roles', function ($q) {
                          $q->where('name', 'Conductor/Operador');
                      });
        }
        return User::where('id', -1);
    }

    public function render()
    {
        return view('livewire.user.user-print', [
            'users' => $this->users,
            'roleFilter' => $this->roleFilter,
            'unidadFilter' => $this->unidadFilter,
            'statusFilter' => $this->statusFilter,
            'search' => $this->search,
            'unidadesOrganizacionales' => $this->unidadesOrganizacionales,
        ]);
    }
}
