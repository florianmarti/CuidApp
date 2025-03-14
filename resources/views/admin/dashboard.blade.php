<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard del Administrador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de estado -->
            @if (session('status'))
                <div class="bg-green-700 p-4 rounded-md mb-4 text-white">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-700 p-4 rounded-md mb-4 text-white">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Accesos Rápidos -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Accesos Rápidos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('admin.verify-documents') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-center hover:bg-blue-700">Verificar Documentos</a>
                        <a href="{{ route('admin.manage-profile-pictures') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-center hover:bg-blue-700">Fotos de Perfil</a>
                        <a href="{{ route('admin.contratos') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-center hover:bg-blue-700">Gestionar Contratos</a>
                        <a href="{{ route('admin.vigiladores') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-center hover:bg-blue-700">Lista de Vigiladores</a>
                    </div>
                </div>
            </div>

            <!-- Sección de notificaciones -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Notificaciones</h3>
                    @forelse ($notifications as $notification)
                        <div class="p-4 mb-2 bg-gray-700 rounded-md">
                            <p>{{ $notification->message }}</p>
                            @if (preg_match('/Documento ID: (\d+)/', $notification->message, $matches))
                                @php $documentId = (int)$matches[1]; @endphp
                                @if (isset($documents[$documentId]))
                                    <a href="{{ Storage::url('documents/' . $documents[$documentId]->file_path) }}" target="_blank" class="text-blue-400 hover:underline">Ver/Descargar documento</a>
                                @else
                                    <p class="text-red-400">Documento no encontrado (ID: {{ $documentId }})</p>
                                @endif
                            @endif
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline ml-4">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-blue-400 hover:underline">Marcar como leída</button>
                            </form>
                        </div>
                    @empty
                        <p>No hay notificaciones nuevas.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sección de zonas sin vigilador -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Zonas sin vigilador asignado</h3>
                    @if ($zonas->isEmpty())
                        <p>No hay zonas disponibles para asignar.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Zona</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach ($zonas as $zona)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $zona->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $zona->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.offer', $zona->id) }}"
                                               class="inline-block bg-indigo-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-indigo-700">
                                                Ofrecer a vigilador
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            <!-- Después de "Zonas sin vigilador asignado" -->
<div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
    <div class="p-6 text-gray-100">
        <h3 class="text-lg font-semibold mb-4">Contratos Activos</h3>
        @php
            $activeContratos = \App\Models\Contrato::where('status', 'active')->with('zona', 'vigilador')->get();
        @endphp
        @if ($activeContratos->isEmpty())
            <p>No hay contratos activos actualmente.</p>
        @else
            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Zona</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Vigilador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700">
                    @foreach ($activeContratos as $contrato)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->vigilador->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-100">
                                {{ $contrato->is_working ? 'Trabajando' : 'No trabajando' }}
                                <span class="ml-2 {{ $contrato->is_working ? 'text-green-400' : 'text-red-400' }}">●</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

        </div>
    </div>
</x-app-layout>
