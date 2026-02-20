@extends('layouts.app')

@section('title', ' - Nova freguesia')

@section('content')
<div class="mb-6">
    <a href="{{ route('freguesias.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Nova freguesia</h1>
</div>
<form method="post" action="{{ route('freguesias.store') }}" class="max-w-md space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    <x-input label="Nome" name="nome" required />
    <x-select label="Concelho" name="id_concelho" :options="$concelhos->pluck('nome', 'id')->all()" required placeholder="Selecione o concelho" />
    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar</button>
        <a href="{{ route('freguesias.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
