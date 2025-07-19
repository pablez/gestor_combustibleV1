<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-2">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        <span class="hidden md:block text-xl font-bold text-gray-800 dark:text-gray-200">
                            Gestor Combustible
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v1H8V5z"></path>
                            </svg>
                            <span>{{ __('Dashboard') }}</span>
                        </div>
                    </x-nav-link>

                    @can('ver usuarios')
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" wire:navigate>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a3 3 0 003-3m0 0a3 3 0 100-6 3 3 0 000 6m-7 9a7 7 0 1114 0H5z"></path>
                            </svg>
                            <span>{{ __('Usuarios') }}</span>
                        </div>
                    </x-nav-link>
                    @endcan

                    @can('ver unidades')
                    <x-nav-link :href="route('admin.units.index')" :active="request()->routeIs('admin.units.*')" wire:navigate>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" viewBox="0 0 256 256">
                                <path d="M255.42,117l-14-35A15.93,15.93,0,0,0,226.58,72H192V64a8,8,0,0,0-8-8H32A16,16,0,0,0,16,72V184a16,16,0,0,0,16,16H49a32,32,0,0,0,62,0h50a32,32,0,0,0,62,0h17a16,16,0,0,0,16-16V120A7.94,7.94,0,0,0,255.42,117ZM192,88h34.58l9.6,24H192ZM32,72H176v64H32ZM80,208a16,16,0,1,1,16-16A16,16,0,0,1,80,208Zm81-24H111a32,32,0,0,0-62,0H32V152H176v12.31A32.11,32.11,0,0,0,161,184Zm31,24a16,16,0,1,1,16-16A16,16,0,0,1,192,208Zm48-24H223a32.06,32.06,0,0,0-31-24V128h48Z">
                                </path>
                            </svg>
                            <span>{{ __('Unidades') }}</span>
                        </div>
                    </x-nav-link>
                    @endcan

                    @if(auth()->user()->hasRole('Admin General|Admin'))
                        @php
                            $currentUser = auth()->user();
                            
                            // Filtrar usuarios pendientes según el rol
                            if ($currentUser->hasRole('Admin General')) {
                                // Admin General ve todos los usuarios pendientes
                                $pendingCount = \App\Models\User::where('estado', 'Pendiente')->count();
                            } else {
                                // Admin solo ve usuarios pendientes de su unidad organizacional
                                $pendingCount = \App\Models\User::where('estado', 'Pendiente')
                                    ->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)
                                    ->count();
                            }
                        @endphp
                        <x-nav-link :href="route('admin.approvals')" :active="request()->routeIs('admin.approvals')" wire:navigate>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('Aprobaciones') }}</span>
                                @if($pendingCount > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">{{ $pendingCount }}</span>
                                @endif
                            </div>
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="64">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out shadow-sm">
                            {{-- Foto de perfil --}}
                            <div class="flex-shrink-0 me-3">
                                <img src="{{ auth()->user()->foto_perfil_url }}" alt="Foto de perfil" class="h-8 w-8 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                            </div>
                            {{-- Información del usuario --}}
                            <div class="text-left">
                                <div class="font-semibold text-gray-800 dark:text-gray-200 truncate max-w-32">
                                    {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                                </div>
                                <div class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                                    {{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}
                                </div>
                                {{-- Agregar unidad organizacional --}}
                                @if(auth()->user()->unidadOrganizacional)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-32">
                                        {{ auth()->user()->unidadOrganizacional->siglas }}
                                    </div>
                                @endif
                            </div>
                            {{-- Flecha del dropdown --}}
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Header del dropdown con foto grande --}}
                        <div class="px-4 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <img src="{{ auth()->user()->foto_perfil_url }}" alt="Foto de perfil" class="h-12 w-12 rounded-full object-cover border-3 border-white dark:border-gray-500 shadow-md">
                                    <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-green-400 border-2 border-white rounded-full"></div>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ auth()->user()->email }}
                                    </div>
                                    <div class="inline-flex items-center px-2 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                                        {{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}
                                    </div>
                                    {{-- Agregar unidad organizacional en el dropdown --}}
                                    @if(auth()->user()->unidadOrganizacional)
                                        <div class="mt-2 inline-flex items-center px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900 rounded-full">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            {{ auth()->user()->unidadOrganizacional->siglas }} - {{ auth()->user()->unidadOrganizacional->nombre_unidad }}
                                        </div>
                                    @else
                                        <div class="mt-2 inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-900 rounded-full">
                                            Sin unidad asignada
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Enlaces del dropdown --}}
                        <div class="py-2">
                            <x-dropdown-link :href="route('profile')" wire:navigate class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 me-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('Mi Perfil') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link class="flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    <span>{{ __('Dashboard') }}</span>
                </div>
            </x-responsive-nav-link>

            @can('ver usuarios')
            <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" wire:navigate>
                <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a3 3 0 003-3m0 0a3 3 0 100-6 3 3 0 000 6m-7 9a7 7 0 1114 0H5z"></path>
                    </svg>
                    <span>{{ __('Usuarios') }}</span>
                </div>
            </x-responsive-nav-link>
            @endcan

            @can('ver unidades')
            <x-responsive-nav-link :href="route('admin.units.index')" :active="request()->routeIs('admin.units.*')" wire:navigate>
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" viewBox="0 0 256 256">
                        <path d="M255.42,117l-14-35A15.93,15.93,0,0,0,226.58,72H192V64a8,8,0,0,0-8-8H32A16,16,0,0,0,16,72V184a16,16,0,0,0,16,16H49a32,32,0,0,0,62,0h50a32,32,0,0,0,62,0h17a16,16,0,0,0,16-16V120A7.94,7.94,0,0,0,255.42,117ZM192,88h34.58l9.6,24H192ZM32,72H176v64H32ZM80,208a16,16,0,1,1,16-16A16,16,0,0,1,80,208Zm81-24H111a32,32,0,0,0-62,0H32V152H176v12.31A32.11,32.11,0,0,0,161,184Zm31,24a16,16,0,1,1,16-16A16,16,0,0,1,192,208Zm48-24H223a32.06,32.06,0,0,0-31-24V128h48Z">
                        </path>
                    </svg>
                    <span>{{ __('Unidades') }}</span>
                </div>
            </x-responsive-nav-link>
            @endcan

            @if(auth()->user()->hasRole('Admin General|Admin'))
                @php
                    $currentUser = auth()->user();
                    
                    // Filtrar usuarios pendientes según el rol (mismo código que arriba)
                    if ($currentUser->hasRole('Admin General')) {
                        $pendingCount = \App\Models\User::where('estado', 'Pendiente')->count();
                    } else {
                        $pendingCount = \App\Models\User::where('estado', 'Pendiente')
                            ->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)
                            ->count();
                    }
                @endphp
                <x-responsive-nav-link :href="route('admin.approvals')" :active="request()->routeIs('admin.approvals')" wire:navigate>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ __('Aprobaciones') }}</span>
                        </div>
                        @if($pendingCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </div>
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            {{-- Información del usuario en móvil --}}
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <img src="{{ auth()->user()->foto_perfil_url }}" alt="Foto de perfil" class="h-12 w-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                        <div class="absolute -bottom-1 -right-1 h-3 w-3 bg-green-400 border-2 border-white rounded-full"></div>
                    </div>
                    <div>
                        <div class="font-semibold text-base text-gray-800 dark:text-gray-200">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ auth()->user()->email }}
                        </div>
                        <div class="inline-flex items-center px-2 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                            {{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}
                        </div>
                        {{-- Agregar unidad organizacional en móvil --}}
                        @if(auth()->user()->unidadOrganizacional)
                            <div class="mt-2 inline-flex items-center px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ auth()->user()->unidadOrganizacional->siglas }}
                            </div>
                        @else
                            <div class="mt-2 inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-900 rounded-full">
                                Sin unidad asignada
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>{{ __('Mi Perfil') }}</span>
                    </div>
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        <div class="flex items-center space-x-3 text-red-600 dark:text-red-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>{{ __('Cerrar Sesión') }}</span>
                        </div>
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
