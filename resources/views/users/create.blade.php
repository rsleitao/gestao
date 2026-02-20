@extends('layouts.app')

@section('title', ' - Novo utilizador')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Novo utilizador</h1>
</div>

<form method="post" action="{{ route('users.store') }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf

    <x-input label="Nome" name="name" :value="old('name')" required />
    <x-input label="Email" name="email" type="email" :value="old('email')" required />
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" id="password" required minlength="8"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            <p class="mt-1 text-xs text-slate-500">Mínimo 8 caracteres.</p>
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirmar password <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="CC" name="cc" :value="old('cc')" />
        <x-input label="NIF" name="nif" :value="old('nif')" />
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-input label="DGEG" name="dgeg" :value="old('dgeg')" />
        <x-input label="OET" name="oet" :value="old('oet')" />
        <x-input label="OE" name="oe" :value="old('oe')" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="id_role" class="mb-1 block text-sm font-medium text-slate-700">Papel</label>
            <select name="id_role" id="id_role" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">— Sem papel —</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('id_role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('id_role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="space-y-2">
            <x-checkbox label="Conta ativa" name="ativo" :checked="old('ativo', true)" />
            <x-checkbox label="Obrigar a alterar password no próximo login" name="must_change_password" :checked="old('must_change_password', false)" />
        </div>
    </div>

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Criar utilizador</button>
        <a href="{{ route('users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
