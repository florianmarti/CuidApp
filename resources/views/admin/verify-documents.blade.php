<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Verificar Documentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de estado -->
            {{-- @if (session('status'))
                <div class="bg-green-700 p-4 rounded-md mb-4 text-white">
                    {{ session('status') }}
                </div>
            @endif --}}
            @if (session('error'))
                <div class="bg-red-700 p-4 rounded-md mb-4 text-white">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Formulario de búsqueda -->
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.verify-documents') }}" class="flex items-center">
                    <x-text-input id="search" name="search" type="text" class="w-full sm:w-1/2" placeholder="Buscar por nombre de usuario (ej. José, Rosario)" value="{{ request('search') }}" />
                    <x-primary-button class="ml-4">{{ __('Buscar') }}</x-primary-button>
                </form>
            </div>

            <!-- Sección de documentos -->
            <h3 class="text-lg font-semibold mb-4">Documentos pendientes</h3>
            @forelse($documents as $document)
                <div class="bg-white dark:bg-gray-800 p-6 mb-4 rounded-lg shadow-sm">
                    <p><strong>Usuario:</strong> {{ $document->user->name }}</p>
                    <p><strong>Tipo:</strong> {{ $document->type }}</p>
                    <div class="mt-2 flex items-center space-x-4">
                        <a href="{{ Storage::url('documents/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:underline">Ver/Descargar documento</a>
                        <form action="{{ route('admin.document.approve', $document->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:underline" onclick="return confirm('¿Estás seguro de aprobar este documento?')">Aprobar</button>
                        </form>
                        <form action="{{ route('admin.delete-document', $document->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('¿Estás seguro de eliminar este documento?')">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">No se encontraron documentos pendientes de verificación.</p>
            @endforelse

            <!-- Después del  documentos -->
            <h3 class="text-lg font-semibold mb-4 mt-8">Números de teléfono pendientes</h3>
            @forelse ($usersWithPendingPhone as $user)
                <div class="bg-white dark:bg-gray-800 p-6 mb-4 rounded-lg shadow-sm">
                    <p><strong>Usuario:</strong> {{ $user->name }}</p>
                    <p><strong>Teléfono:</strong> {{ $user->phone_number ?? 'No registrado' }}</p>
                    @if ($user->phone_number)
                        <div class="mt-2 flex items-center space-x-4">
                            <a href="https://wa.me/54{{ preg_replace('/\D/', '', $user->phone_number) }}" target="_blank" class="text-blue-600 hover:underline">Enviar WhatsApp</a>
                            <form action="{{ route('admin.phone.approve', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:underline">Aprobar</button>
                            </form>
                            <form action="{{ route('admin.phone.reject', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:underline">Rechazar</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('admin.phone.add', $user->id) }}" method="POST" class="mt-2">
                            @csrf
                            <x-input-label for="phone_number_{{ $user->id }}" :value="__('Agregar número')" />
                            <x-text-input id="phone_number_{{ $user->id }}" name="phone_number" type="text" class="mt-1 block w-full" placeholder="(341) 227 6213" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            <x-primary-button class="mt-2">{{ __('Agregar y verificar') }}</x-primary-button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">No hay números de teléfono pendientes de verificación.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
