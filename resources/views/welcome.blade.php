<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">{{ config('app.name') }}</h1>
        <p class="text-slate-600 mb-8">Sistema de gestão de processos, requerentes e serviços.</p>
        <div class="flex gap-4 justify-center">
            <a href="{{ route('login') }}" class="rounded-lg bg-slate-800 px-6 py-2.5 text-white font-medium hover:bg-slate-700">Entrar</a>
            <a href="{{ route('register') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-slate-700 font-medium hover:bg-slate-100">Registar</a>
        </div>
    </div>
</body>
</html>
