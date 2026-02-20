@extends('layouts.app')

@section('title', ' - Editar processo')

@section('content')
<div class="mb-6">
    <a href="{{ route('processos.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Editar processo</h1>
</div>

<form method="post" action="{{ route('processos.update', $processo) }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @method('PUT')

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Código do processo</label>
        <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-slate-800">{{ $processo->codigo_formatado }}</p>
        <p class="mt-1 text-xs text-slate-500">Identificador único. Não pode ser alterado.</p>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Data de abertura</label>
        <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800">{{ $processo->data_abertura->format('d/m/Y') }}</p>
        <p class="mt-1 text-xs text-slate-500">Data de criação do processo. Não pode ser alterada.</p>
    </div>
    <div>
        <label for="designacao" class="mb-1 block text-sm font-medium text-slate-700">Designação</label>
        <input type="text" name="designacao" id="designacao" value="{{ old('designacao', $processo->designacao) }}"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <p class="mt-1 text-xs text-slate-500">Gerada automaticamente quando criado a partir de um negócio (Loja - Concelho). Pode ser editada.</p>
        @error('designacao')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <x-select label="Requerente" name="requerente_id" :options="$requerentes->pluck('nome', 'id')->all()" :selected="$processo->requerente_id" required placeholder="Selecione o requerente" />
    <x-textarea label="Observações" name="observacoes" :value="$processo->observacoes" />

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Atualizar</button>
        <a href="{{ route('processos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
