@extends('layouts.app')

@section('title', ' - Novo processo')

@section('content')
<div class="mb-6">
    <a href="{{ route('processos.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Novo processo</h1>
</div>

<form method="post" action="{{ route('processos.store') }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf

    <p class="text-sm text-slate-600">Será atribuído um <strong>código único</strong> ao processo (ex.: 0014). A referência para organização é <strong>AA-NNNN</strong> (ano do trabalho + código), ex.: {{ now()->format('y') }}-0014 para trabalhos de {{ now()->year }}.</p>

    <div>
        <label for="designacao" class="mb-1 block text-sm font-medium text-slate-700">Designação</label>
        <input type="text" name="designacao" id="designacao" value="{{ old('designacao') }}"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <p class="mt-1 text-xs text-slate-500">Opcional. Quando criado a partir de um negócio, será gerada automaticamente (Loja - Concelho).</p>
        @error('designacao')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <x-select label="Requerente" name="requerente_id" :options="$requerentes->pluck('nome', 'id')->all()" required placeholder="Selecione o requerente" />
    <x-textarea label="Observações" name="observacoes" :value="old('observacoes')" />

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar</button>
        <a href="{{ route('processos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
