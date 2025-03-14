<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contratar un Vigilador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    <form method="POST" action="{{ route('zonas.contract.store') }}">
                        @csrf
                        <div>
                            <x-input-label for="type" :value="__('Tipo de contrato')" class="text-gray-200" />
                            <select id="type" name="type" class="mt-1 block w-full bg-gray-700 text-gray-200 border-gray-600 rounded-md" required>
                                <option value="" disabled selected>Selecciona un tipo de contrato</option>
                                <option value="Diurno">Diurno (09:00am - 05:00pm)</option>
                                <option value="Nocturno">Nocturno (22:00pm - 06:00am)</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2 text-red-300" />
                        </div>
                        <div class="mt-4">
                            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('Solicitar Vigilador') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
