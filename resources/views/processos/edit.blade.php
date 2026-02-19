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

    <x-input label="Referência" name="referencia" :value="$processo->referencia" required />
    <x-select label="Requerente" name="requerente_id" :options="$requerentes->pluck('nome', 'id')->all()" :selected="$processo->requerente_id" required placeholder="Selecione o requerente" />
    <x-select label="Serviço" name="servico_id" :options="$servicos->pluck('nome', 'id')->all()" :selected="$processo->servico_id" placeholder="Opcional" />
    <x-select label="Estado" name="estado" :options="array_combine(\App\Models\Processo::ESTADOS, array_map(fn($e) => ucfirst(str_replace('_', ' ', $e)), \App\Models\Processo::ESTADOS))" :selected="$processo->estado" required />
    <x-input label="Data abertura" name="data_abertura" type="date" :value="$processo->data_abertura->format('Y-m-d')" required />
    <x-input label="Data limite" name="data_limite" type="date" :value="$processo->data_limite?->format('Y-m-d')" />
    <x-input label="Data conclusão" name="data_conclusao" type="date" :value="$processo->data_conclusao?->format('Y-m-d')" />
    <x-textarea label="Observações" name="observacoes" :value="$processo->observacoes" />

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Atualizar</button>
        <a href="{{ route('processos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
