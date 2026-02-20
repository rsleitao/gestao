@extends('layouts.app')

@section('title', ' - Nova loja')

@section('content')
<div class="mb-6">
    <a href="{{ route('lojas.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Nova loja</h1>
</div>
<form method="post" action="{{ route('lojas.store') }}" enctype="multipart/form-data" class="max-w-md space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    <x-input label="Nome" name="nome" required />
    <x-checkbox label="Ativo" name="ativo" :checked="true" />
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Imagem</label>
        <input type="file" name="imagem" accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <p class="mt-1 text-xs text-slate-500">PNG, JPG até 2MB.</p>
        @error('imagem')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar</button>
        <a href="{{ route('lojas.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
