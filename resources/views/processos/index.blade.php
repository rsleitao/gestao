@extends('layouts.app')

@section('title', ' - Processos')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Processos</h1>
        <p class="mt-1 text-slate-600">Gerir processos.</p>
    </div>
    <a href="{{ route('processos.create') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-700">
        Novo processo
    </a>
</div>

<form method="get" class="mt-6 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar por referência..."
        class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
    <select name="estado" class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <option value="">Todos os estados</option>
        @foreach(\App\Models\Processo::ESTADOS as $e)
            <option value="{{ $e }}" @selected(request('estado') === $e)>{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600">Pesquisar</button>
</form>

<div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Referência</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Requerente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Serviço</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Data abertura</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($processos as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $p->referencia }}</td>
                        <td class="px-4 py-3 text-slate-800">{{ $p->requerente->nome }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->servico?->nome ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $estadoLabels = ['aberto' => 'bg-blue-100 text-blue-800', 'em_analise' => 'bg-amber-100 text-amber-800', 'concluido' => 'bg-emerald-100 text-emerald-800', 'arquivado' => 'bg-slate-200 text-slate-600'];
                                $cls = $estadoLabels[$p->estado] ?? 'bg-slate-100 text-slate-800';
                            @endphp
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $cls }}">{{ ucfirst(str_replace('_', ' ', $p->estado)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->data_abertura->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('processos.edit', $p) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                            <form method="post" action="{{ route('processos.destroy', $p) }}" class="inline ml-2" onsubmit="return confirm('Eliminar este processo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">Nenhum processo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($processos->hasPages())
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $processos->links() }}
        </div>
    @endif
</div>
@endsection
