<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-700 p-4 rounded-md text-white">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Mostrar foto de perfil (solo visible, no editable) -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-center mb-4">
                    <img src="{{ $user->profile_picture ? Storage::url('profiles/' . $user->profile_picture) : 'https://via.placeholder.com/150' }}" alt="Profile Picture" class="w-24 h-24 rounded-full mr-4">
                    <div>
                        <h3 class="text-lg font-semibold dark:text-gray-200">{{ $user->name }}</h3>
                        <p class="text-sm dark:text-gray-400">{{ $user->role }}</p>
                    </div>
                </div>
            </div>

            <!-- Información editable -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')
                    <div>
                        <x-input-label for="name" :value="__('Nombre')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required :disabled="$user->email_verified_at ? true : false" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        @if (!$user->email_verified_at)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Verifica tu email para que sea visible y no editable.</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Información verificada -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold dark:text-gray-200 mb-4">Información verificada</h3>
                <p><strong>Dirección:</strong> {{ $user->address ?? 'Pendiente de verificación' }}</p>
                <p><strong>Teléfono:</strong> {{ $user->phone_number ?? 'No registrado' }}</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    <strong>Estado:</strong>
                    @if ($user->phone_verified)
                        <span class="text-green-600">Verificado</span>
                    @elseif ($user->phone_number)
                        Pendiente de verificación por el administrador
                    @else
                        El administrador se comunicará con usted en breve para confirmar su número
                    @endif
                </p>
            </div>

            <!-- Subida de documentos -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('profile.document') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="type" :value="__('Tipo de documento')" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="DNI">DNI</option>
                            <option value="Comprobante de dirección">Comprobante de dirección</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="document" :value="__('Documento')" />
                        <x-text-input id="document" name="document" type="file" class="mt-1 block w-full" />
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Formatos permitidos: PDF, JPG, PNG. Tamaño máximo: 2 MB.</p>
                        <x-input-error :messages="$errors->get('document')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Subir documento') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
