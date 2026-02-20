@extends('layouts.app')

@section('title', ' - Editar utilizador')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Editar utilizador</h1>
</div>

<form method="post" action="{{ route('users.update', $user) }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @method('PUT')

    <x-input label="Nome" name="name" :value="old('name', $user->name)" required />
    <x-input label="Email" name="email" type="email" :value="old('email', $user->email)" required />

    <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
        <p class="text-sm text-amber-800">Deixe a password em branco para não alterar.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Nova password</label>
            <input type="password" name="password" id="password" minlength="8"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirmar password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="CC" name="cc" :value="old('cc', $user->cc)" />
        <x-input label="NIF" name="nif" :value="old('nif', $user->nif)" />
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-input label="DGEG" name="dgeg" :value="old('dgeg', $user->dgeg)" />
        <x-input label="OET" name="oet" :value="old('oet', $user->oet)" />
        <x-input label="OE" name="oe" :value="old('oe', $user->oe)" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="id_role" class="mb-1 block text-sm font-medium text-slate-700">Papel</label>
            @if($user->isFixedCeo())
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700">
                    {{ $user->role?->name ?? '—' }}
                    <p class="mt-1 text-xs text-slate-500">Conta fixa CEO: o papel não pode ser alterado.</p>
                </div>
                <input type="hidden" name="id_role" value="{{ $user->id_role }}">
            @else
                <select name="id_role" id="id_role" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">— Sem papel —</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('id_role', $user->id_role) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('id_role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            @endif
        </div>
        <div class="space-y-2">
            @if($user->isFixedCeo())
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700">
                    Conta sempre ativa (conta fixa CEO).
                </div>
                <input type="hidden" name="ativo" value="1">
            @else
                <x-checkbox label="Conta ativa" name="ativo" :checked="old('ativo', $user->ativo)" />
            @endif
            <x-checkbox label="Obrigar a alterar password no próximo login" name="must_change_password" :checked="old('must_change_password', $user->must_change_password)" />
        </div>
    </div>

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Atualizar</button>
        <a href="{{ route('users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>
@endsection
