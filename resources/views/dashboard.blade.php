<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mostrar mensajes de estado -->
            @if (session('status'))
                <div class="bg-green-700 p-4 rounded-md mb-4 text-white">
                    {{ session('status') }}
                </div>
            @endif
            <!-- Mostrar mensajes de error -->
            @if (session('error'))
                <div class="bg-red-700 p-4 rounded-md mb-4 text-white">
                    {{ session('error') }}
                </div>
            @endif
            <!-- Bloque de notificaciones persistentes -->
            @if ($notifications->isNotEmpty())
                <div class="bg-gray-700 p-4 rounded-md mb-4">
                    <h3 class="text-lg font-semibold text-white">Notificaciones</h3>
                    <ul class="list-disc pl-5 text-gray-200">
                        @foreach ($notifications as $notification)
                            <li>
                                {{ $notification->message }}
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-blue-400 hover:text-blue-600 ml-2">Marcar como leída</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    {{ __("Estás Logeado!") }}

                    @if ($role === 'admin' && $pendingContractsCount > 0)
                        <div class="mt-4">
                            <p class="text-yellow-300">Hay {{ $pendingContractsCount }} solicitud(es) pendiente(s) por revisar.</p>
                            <a href="{{ route('admin.contratos') }}"
                               class="inline-block bg-blue-600 text-white font-semibold px-4 py-2 mt-2 rounded-md hover:bg-blue-700">
                                Revisar Contratos Pendientes
                            </a>
                        </div>
                    @endif

                    @if ($role === 'user')
                        @if ($zona)
                            <p class="mt-4">Tu zona: {{ $zona->name }}</p>
                            @if (!$zona->contratos()->whereIn('status', ['pending', 'active'])->exists())
                                <a href="{{ route('zonas.contract') }}" class="text-blue-300 mt-2 inline-block">Contratar un vigilador</a>
                            @endif
                            <a href="{{ route('zonas.edit') }}" class="text-blue-300 mt-2 inline-block ml-4">Editar zona</a>
                            <!-- Mostrar contratos de la zona -->
                            @if ($userContracts->isNotEmpty())
                                <h2 class="text-lg font-semibold mt-4">Estado de tus Zonas</h2>
                                <ul class="list-disc pl-5 mt-2">
                                    @foreach ($userContracts as $contract)
                                        <li>
                                            Zona: {{ $contract->zona->name }} -
                                            Vigilador: {{ $contract->vigilador->name ?? 'No asignado' }} -
                                            Estado: {{ $contract->is_working ? 'Trabajando' : 'Inactivo' }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @else
                            <p class="mt-4">Aún no has creado una zona. <a href="{{ route('zonas.create') }}" class="text-blue-300">Crea una ahora</a>.</p>
                        @endif
                    @elseif ($role === 'vigilador')
                        <p class="mt-4">Bienvenido, vigilador. Revisa tus asignaciones pendientes:</p>
                        @if ($offers->isNotEmpty())
                            <h2 class="text-lg font-semibold mt-4">Ofertas Pendientes</h2>
                            <ul class="list-disc pl-5 mt-2">
                                @foreach ($offers as $offer)
                                    <li>
                                        {{ $offer->zona->name }} (Turno: {{ $offer->type }}) desde {{ $offer->start_date ? $offer->start_date->toDateString() : 'sin fecha' }} hasta {{ $offer->end_date ? $offer->end_date->toDateString() : 'sin fecha' }}
                                        <form action="{{ route('vigilador.accept', $offer->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-400 hover:text-green-600 ml-2">Aceptar</button>
                                        </form>
                                        <form action="{{ route('vigilador.reject', $offer->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-400 hover:text-red-600 ml-2">Rechazar</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2">No hay ofertas pendientes.</p>
                        @endif

                        @if ($activeContracts->isNotEmpty())
                            <h2 class="text-lg font-semibold mt-4">Contratos Activos</h2>
                            <ul class="list-disc pl-5 mt-2">
                                @foreach ($activeContracts as $contract)
                                    <li class="flex items-center">
                                        {{ $contract->zona->name }} (Turno: {{ $contract->type }})
                                        desde {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->toDateString() : 'sin fecha' }}
                                        hasta {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->toDateString() : 'sin fecha' }}
                                        <form action="{{ route('vigilador.toggle-working', $contract->id) }}" method="POST" class="inline ml-4">
                                            @csrf
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_working" value="1" {{ $contract->is_working ? 'checked' : '' }} onchange="if(confirm('¿Cambiar estado?')) this.form.submit();" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:bg-blue-600 transition-colors duration-300"></div>
                                                <div class="absolute w-5 h-5 bg-white rounded-full top-0.5 left-0.5 peer-checked:translate-x-5 transition-transform duration-300"></div>
                                            </label>
                                            <span class="ml-2 text-gray-200">{{ $contract->is_working ? 'En la zona' : 'No en la zona' }}</span>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
