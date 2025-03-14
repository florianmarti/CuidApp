<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestionar Contratos') }}
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

            <!-- Contratos Pendientes -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Solicitudes Pendientes</h3>
                    @if ($contratosPendientes->isEmpty())
                        <p>No hay solicitudes pendientes.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Zona</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach ($contratosPendientes as $contrato)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.contratos.assign', $contrato->id) }}"
                                               class="inline-block bg-indigo-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-indigo-700">
                                                Asignar Vigilador
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Contratos Ofertados -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Contratos Ofertados</h3>
                    @if ($contratosOfertados->isEmpty())
                        <p>No hay contratos ofertados.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Zona</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Vigilador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach ($contratosOfertados as $contrato)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->vigilador->name ?? 'Sin asignar' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->type }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Contratos Activos -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Contratos Activos</h3>
                    @if ($contratosActivos->isEmpty())
                        <p>No hay contratos activos.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Zona</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Vigilador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Inicio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Fin</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach ($contratosActivos as $contrato)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->vigilador->name ?? 'Sin asignar' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->start_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->end_date?->format('d/m/Y') ?? 'Sin definir' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Contratos Finalizados -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Contratos Finalizados</h3>
                    @if ($contratosFinalizados->isEmpty())
                        <p>No hay contratos finalizados.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Zona</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Vigilador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Inicio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Fin</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach ($contratosFinalizados as $contrato)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->vigilador->name ?? 'Sin asignar' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->start_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->end_date?->format('d/m/Y') ?? 'Sin definir' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Contratos Rechazados -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Contratos Rechazados</h3>
                    @if ($contratosRechazados->isEmpty())
                        <p>No hay contratos rechazados.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Zona</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Vigilador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach ($contratosRechazados as $contrato)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->zona->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->vigilador->name ?? 'Sin asignar' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $contrato->type }}</td>
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
