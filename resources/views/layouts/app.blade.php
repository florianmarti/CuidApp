<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar solo para administradores -->
        @if (auth()->check() && auth()->user()->role === 'admin')
            <aside class="fixed top-0 left-0 w-64 h-full bg-gray-800 shadow-lg p-4">
                <h1 class="text-2xl font-bold text-white mb-6">{{ config('app.name', 'Cuidapp') }}</h1>
                <nav>
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 text-gray-200 hover:bg-gray-700 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.verify-documents') }}" class="block py-2 px-4 text-gray-200 hover:bg-gray-700 rounded {{ request()->routeIs('admin.verify-documents') ? 'bg-gray-700' : '' }}">Verificar Documentos</a>
                    <a href="{{ route('admin.manage-profile-pictures') }}" class="block py-2 px-4 text-gray-200 hover:bg-gray-700 rounded {{ request()->routeIs('admin.manage-profile-pictures') ? 'bg-gray-700' : '' }}">Fotos de Perfil</a>
                    <a href="{{ route('admin.contratos') }}" class="block py-2 px-4 text-gray-200 hover:bg-gray-700 rounded {{ request()->routeIs('admin.contratos') ? 'bg-gray-700' : '' }}">Contratos</a>
                    <a href="{{ route('admin.vigiladores') }}" class="block py-2 px-4 text-gray-200 hover:bg-gray-700 rounded {{ request()->routeIs('admin.vigiladores') ? 'bg-gray-700' : '' }}">Vigiladores</a>
                </nav>
            </aside>
            <div class="flex-1 ml-64">
        @else
            <div class="flex-1">
        @endif

        @include('layouts.navigation')
        <header class="bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        <main>
            @if (session('status'))
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="bg-green-700 p-4 rounded-md text-white">
                {{ session('status') }}
            </div>
        </div>
    @endif
            {{ $slot }}
        </main>
    </div>
</body>
</html>
