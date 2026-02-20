@extends('layouts.app')

@section('title', ' - Permissões')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Utilizadores</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Gestão de permissões</h1>
    <p class="mt-1 text-slate-600">Atribua a cada papel as permissões de acesso. As alterações aplicam-se a todos os utilizadores com esse papel.</p>
</div>

<form method="post" action="{{ route('permissions.update-all') }}">
    @csrf
    @method('PUT')
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Papel</th>
                    @foreach($permissions as $perm)
                        <th class="px-3 py-3 text-center text-xs font-medium text-slate-600" title="{{ $perm->name }}">{{ Str::limit($perm->name, 25) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @foreach($roles as $role)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $role->name }}</td>
                        @foreach($permissions as $perm)
                            <td class="px-3 py-2 text-center">
                                @if($role->isCEO())
                                    <span class="text-emerald-600" title="CEO tem todas as permissões">✓</span>
                                @else
                                    <input type="checkbox" name="roles[{{ $role->id }}][]" value="{{ $perm->id }}"
                                        {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-slate-300 text-slate-800 focus:ring-slate-500">
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex justify-end">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar todas as permissões</button>
    </div>
</form>
@endsection
