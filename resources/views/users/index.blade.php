@extends('layouts.app')

@section('title', ' - Utilizadores')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Utilizadores</h1>
        <p class="mt-1 text-slate-600">Gerir contas de utilizadores (ativar/desativar, dados).</p>
    </div>
    <a href="{{ route('users.create') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-700">
        Novo utilizador
    </a>
</div>

<form method="get" class="mt-6 flex gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar por nome ou email..."
        class="flex-1 rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600">Pesquisar</button>
</form>

<div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Papel</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Conta</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($users as $u)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->role?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($u->ativo)
                                <span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">Ativa</span>
                            @else
                                <span class="rounded bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-600">Inativa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.edit', $u) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum utilizador encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
