<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} @yield('title', '')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <nav class="bg-slate-800 text-white shadow">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-14 items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold">{{ config('app.name') }}</a>
                <div class="flex gap-4">
                    <a href="{{ route('dashboard') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Dashboard</a>
                    <a href="{{ route('requerentes.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Requerentes</a>
                    <a href="{{ route('servicos.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Serviços</a>
                    <a href="{{ route('processos.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Processos</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded-lg bg-emerald-100 p-4 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-800">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
