<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestionar Fotos de Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-700 p-4 rounded-md mb-4 text-white">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Formulario de búsqueda -->
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.manage-profile-pictures') }}" class="flex items-center">
                    <x-text-input id="search" name="search" type="text" class="w-full sm:w-1/2" placeholder="Buscar por nombre (ej. José, Rosario)" value="{{ request('search') }}" />
                    <x-primary-button class="ml-4">{{ __('Buscar') }}</x-primary-button>
                </form>
            </div>

            @forelse ($users as $user)
                <div class="bg-white dark:bg-gray-800 p-6 mb-4 rounded-lg shadow-sm flex items-center">
                    <img src="{{ $user->profile_picture ? Storage::url('profiles/' . $user->profile_picture) : 'https://via.placeholder.com/150' }}" alt="Profile Picture" class="w-16 h-16 rounded-full mr-4">
                    <div class="flex-1">
                        <p><strong>Usuario:</strong> {{ $user->name }} ({{ $user->role }})</p>
                        <form method="POST" action="{{ route('admin.update-profile-picture', $user->id) }}" enctype="multipart/form-data" class="mt-2">
                            @csrf
                            <div>
                                <x-input-label for="profile_picture_{{ $user->id }}" :value="__('Nueva foto de perfil')" />
                                <x-text-input id="profile_picture_{{ $user->id }}" name="profile_picture" type="file" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                            </div>
                            <x-primary-button class="mt-2">{{ __('Actualizar') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">No se encontraron usuarios.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
