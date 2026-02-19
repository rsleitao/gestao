@extends('layouts.app')

@section('title', ' - Requerentes')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Requerentes</h1>
        <p class="mt-1 text-slate-600">Gerir lista de requerentes.</p>
    </div>
    <a href="{{ route('requerentes.create') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-700">
        Novo requerente
    </a>
</div>

<form method="get" class="mt-6 flex gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar por nome, email ou NIF..."
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
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Telefone</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">NIF</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($requerentes as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $r->nome }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $r->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $r->telefone ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $r->nif ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('requerentes.edit', $r) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                            <form method="post" action="{{ route('requerentes.destroy', $r) }}" class="inline ml-2" onsubmit="return confirm('Eliminar este requerente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum requerente encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requerentes->hasPages())
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $requerentes->links() }}
        </div>
    @endif
</div>
@endsection
