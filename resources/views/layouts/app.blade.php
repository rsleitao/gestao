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
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative group" id="nav-negocios-dropdown">
                        <button type="button" id="nav-negocios-btn" class="flex items-center gap-1 rounded px-3 py-1.5 hover:bg-slate-700" aria-expanded="false" aria-haspopup="true">
                            Negócios
                            <svg class="h-4 w-4 transition-transform group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="nav-negocios-menu" class="absolute left-0 top-full z-50 mt-1 hidden min-w-[180px] rounded-lg border border-slate-600 bg-slate-800 py-1 shadow-lg">
                            <a href="{{ route('negocios.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Lista de negócios</a>
                            <a href="{{ route('negocios.create') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Novo negócio</a>
                            <a href="{{ route('negocios.kanban') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Kanban negócios</a>
                        </div>
                    </div>
                    <a href="{{ route('requerentes.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Requerentes</a>
                    <a href="{{ route('servicos.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Serviços</a>
                    <a href="{{ route('processos.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Processos</a>
                    <a href="{{ route('freguesias.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Freguesias</a>
                    <a href="{{ route('lojas.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Lojas</a>
                    <a href="{{ route('imoveis.index') }}" class="rounded px-3 py-1.5 hover:bg-slate-700">Imóveis</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto @yield('main_class', 'max-w-7xl')">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-emerald-100 p-4 text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-800">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>
    <script>
        (function() {
            var btn = document.getElementById('nav-negocios-btn');
            var menu = document.getElementById('nav-negocios-menu');
            var dropdown = document.getElementById('nav-negocios-dropdown');
            if (btn && menu) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var isOpen = !menu.classList.contains('hidden');
                    menu.classList.toggle('hidden');
                    dropdown.classList.toggle('open', !isOpen);
                    btn.setAttribute('aria-expanded', !isOpen);
                });
                document.addEventListener('click', function() {
                    menu.classList.add('hidden');
                    dropdown.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                });
                menu.addEventListener('click', function(e) { e.stopPropagation(); });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
