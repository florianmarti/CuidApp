<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Nueva Zona') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    <form method="POST" action="{{ route('zonas.store') }}">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Nombre de la zona')" class="text-gray-200" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-700 text-gray-200 border-gray-600" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-300" />
                        </div>
                        <div class="mt-4">
                            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('Crear Zona') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
