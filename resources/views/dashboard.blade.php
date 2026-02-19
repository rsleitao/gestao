@extends('layouts.app')

@section('title', ' - Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="mt-1 text-slate-600">Visão geral da gestão de processos, requerentes e serviços.</p>
</div>
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-medium uppercase tracking-wide text-slate-500">Requerentes</h2>
        <p class="mt-2 text-3xl font-semibold text-slate-800">{{ $totalRequerentes }}</p>
        <p class="mt-1 text-sm text-slate-600">Total registado</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-medium uppercase tracking-wide text-slate-500">Serviços</h2>
        <p class="mt-2 text-3xl font-semibold text-slate-800">{{ $totalServicos }}</p>
        <p class="mt-1 text-sm text-slate-600">Catálogo ativo</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-medium uppercase tracking-wide text-slate-500">Processos</h2>
        <p class="mt-2 text-3xl font-semibold text-slate-800">{{ $totalProcessos }}</p>
        <p class="mt-1 text-sm text-slate-600">Total de processos</p>
    </div>
</div>
<div class="mt-10 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-semibold text-slate-800">Próximos passos</h2>
    <ul class="mt-4 list-inside list-disc space-y-2 text-slate-600">
        <li>Correr <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm">composer install</code> na raiz do projeto.</li>
        <li>Criar a base de dados <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm">gestao</code> no MySQL (phpMyAdmin ou linha de comando).</li>
        <li>Copiar <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm">.env.example</code> para <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm">.env</code> e ajustar <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm">DB_*</code> se necessário.</li>
        <li>Executar <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm">php artisan key:generate</code> e <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm">php artisan migrate</code>.</li>
        <li>Implementar CRUD de Requerentes, Serviços e Processos (rotas e controladores já preparados no esqueleto).</li>
    </ul>
</div>
@endsection
