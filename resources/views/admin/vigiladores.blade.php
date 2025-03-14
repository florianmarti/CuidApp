<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Vigiladores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <h3 class="text-lg font-semibold mb-4">Vigiladores Registrados</h3>
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Rechazos</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vigiladores as $vigilador)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-6 py-4">{{ $vigilador->name }}</td>
                            <td class="px-6 py-4">{{ $vigilador->rejection_count }}</td>
                            <td class="px-6 py-4">
                                @if ($vigilador->is_inactive)
                                    <span class="text-red-600">Inactivo</span>
                                @elseif ($vigilador->is_suspended)
                                    <span class="text-yellow-600">Suspendido</span>
                                @else
                                    <span class="text-green-600">Activo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($vigilador->is_inactive)
                                    <span class="text-gray-500">Inactivo</span>
                                @else
                                    <form id="toggle-form-{{ $vigilador->id }}" action="{{ route('admin.vigilador.toggle', $vigilador->id) }}" method="POST" class="inline">
                                        @csrf
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                   name="is_suspended"
                                                   class="sr-only peer"
                                                   {{ !$vigilador->is_suspended ? 'checked' : '' }}
                                                   onchange="document.getElementById('toggle-form-{{ $vigilador->id }}').submit();">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </form>
                                    <form id="inactivate-form-{{ $vigilador->id }}" action="{{ route('admin.vigilador.inactivate', $vigilador->id) }}" method="POST" class="inline ml-4">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('¿Estás seguro de inactivar permanentemente a este vigilador?')">
                                            Inactivar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-gray-600 dark:text-gray-400 text-center">
                                No hay vigiladores registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
