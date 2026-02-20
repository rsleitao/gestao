@extends('layouts.app')

@section('title', ' - Editar concelho')

@section('content')
<div class="mb-6">
    <a href="{{ route('concelhos.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Editar concelho</h1>
</div>
<form method="post" action="{{ route('concelhos.update', $concelho) }}" class="max-w-md space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @method('PUT')
    <x-input label="Nome" name="nome" :value="$concelho->nome" required />
    <x-select label="Distrito" name="id_distrito" :options="$distritos->pluck('nome', 'id')->all()" :selected="$concelho->id_distrito" required placeholder="Selecione o distrito" />
    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Atualizar</button>
        <a href="{{ route('concelhos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
