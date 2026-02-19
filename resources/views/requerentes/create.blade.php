@extends('layouts.app')

@section('title', ' - Novo requerente')

@section('content')
<div class="mb-6">
    <a href="{{ route('requerentes.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Novo requerente</h1>
</div>

<form method="post" action="{{ route('requerentes.store') }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf

    <x-input label="Nome" name="nome" required />
    <x-input label="Email" name="email" type="email" />
    <x-input label="Telefone" name="telefone" />
    <x-input label="NIF" name="nif" />
    <x-input label="Morada" name="morada" />
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Código postal" name="codigo_postal" />
        <x-input label="Localidade" name="localidade" />
    </div>
    <x-textarea label="Notas" name="notas" :value="old('notas')" />

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar</button>
        <a href="{{ route('requerentes.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
