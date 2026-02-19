@extends('layouts.app')

@section('title', ' - Novo serviço')

@section('content')
<div class="mb-6">
    <a href="{{ route('servicos.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Novo serviço</h1>
</div>

<form method="post" action="{{ route('servicos.store') }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf

    <x-input label="Código" name="codigo" required />
    <x-input label="Nome" name="nome" required />
    <x-textarea label="Descrição" name="descricao" :value="old('descricao')" />
    <x-input label="Unidade (ex: por processo, por hora)" name="unidade" />
    <x-input label="Preço base (€)" name="preco_base" type="number" step="0.01" min="0" :value="old('preco_base')" />
    <x-checkbox label="Ativo" name="ativo" :checked="true" />

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar</button>
        <a href="{{ route('servicos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
