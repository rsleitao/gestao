@extends('layouts.app')

@section('title', ' - Processo ' . $processo->codigo_formatado . ($processo->designacao ? ' - ' . $processo->designacao : ''))

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <a href="{{ route('processos.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
        <h1 class="mt-2 text-2xl font-bold text-slate-800">Processo {{ $processo->codigo_formatado }}@if($processo->designacao) - {{ $processo->designacao }}@endif</h1>
        <p class="mt-1 text-sm text-slate-600">Aberto em {{ $processo->data_abertura->format('d/m/Y') }}</p>
    </div>
    <a href="{{ route('processos.edit', $processo) }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-700">Editar processo</a>
</div>

<div class="space-y-6">
    {{-- Dados do processo --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Dados do processo</h2>
        <dl class="grid gap-3 sm:grid-cols-2">
            @if($processo->designacao)
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium uppercase text-slate-500">Designação</dt>
                <dd class="mt-1 text-slate-800">{{ $processo->designacao }}</dd>
            </div>
            @endif
            @if($processo->observacoes)
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium uppercase text-slate-500">Observações</dt>
                <dd class="mt-1 text-slate-800">{{ $processo->observacoes }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Requerente --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Requerente</h2>
        @if($processo->requerente)
            <dl class="grid gap-3 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Nome</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->nome }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Email</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Telefone</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->telefone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">NIF</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->nif ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase text-slate-500">Morada</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->morada ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Código postal</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->codigo_postal ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Localidade</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->localidade ?? '—' }}</dd>
                </div>
                @if($processo->requerente->notas)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase text-slate-500">Notas</dt>
                    <dd class="text-slate-800">{{ $processo->requerente->notas }}</dd>
                </div>
                @endif
            </dl>
            <div class="mt-4">
                <a href="{{ route('requerentes.edit', $processo->requerente) }}" class="text-sm text-slate-600 hover:text-slate-900">Editar requerente →</a>
            </div>
        @else
            <p class="text-slate-500">Nenhum requerente associado.</p>
        @endif
    </div>

    {{-- Imóvel associado --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Imóvel associado</h2>
        @if($processo->imovel)
            @php $i = $processo->imovel; @endphp
            <dl class="grid gap-3 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase text-slate-500">Morada</dt>
                    <dd class="text-slate-800">{{ $i->morada }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">NIP</dt>
                    <dd class="text-slate-800">{{ $i->nip ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Código postal</dt>
                    <dd class="text-slate-800">{{ $i->cod_postal }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Localidade</dt>
                    <dd class="text-slate-800">{{ $i->localidade_imovel }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Distrito / Concelho / Freguesia</dt>
                    <dd class="text-slate-800">{{ $i->distrito?->nome ?? '—' }} / {{ $i->concelho?->nome ?? '—' }} / {{ $i->freguesia?->nome ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Coordenadas</dt>
                    <dd class="text-slate-800">{{ $i->coordenadas ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Potência / Tensão</dt>
                    <dd class="text-slate-800">{{ $i->potencia ?? '—' }} / {{ $i->tensao ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Área (m²) / Pisos</dt>
                    <dd class="text-slate-800">{{ $i->area_imovel ?? '—' }} / {{ $i->pisos ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Tipo de imóvel</dt>
                    <dd class="text-slate-800">{{ $i->tipo_imovel ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">Loja</dt>
                    <dd class="text-slate-800">{{ $i->loja?->nome ?? '—' }}</dd>
                </div>
                @if(is_array($i->pts) && count($i->pts))
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">PTS</dt>
                    <dd class="text-slate-800">{{ implode(', ', $i->pts) }}</dd>
                </div>
                @endif
                @if(is_array($i->ggs) && count($i->ggs))
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">GGS</dt>
                    <dd class="text-slate-800">{{ implode(', ', $i->ggs) }}</dd>
                </div>
                @endif
                @if(is_array($i->pcves) && count($i->pcves))
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">PCVEs</dt>
                    <dd class="text-slate-800">{{ implode(', ', $i->pcves) }}</dd>
                </div>
                @endif
                @if($i->descricao)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase text-slate-500">Descrição</dt>
                    <dd class="text-slate-800">{{ $i->descricao }}</dd>
                </div>
                @endif
            </dl>
            <div class="mt-4">
                <a href="{{ route('imoveis.edit', $i) }}" class="text-sm text-slate-600 hover:text-slate-900">Editar imóvel →</a>
            </div>
        @else
            <p class="text-slate-500">Nenhum imóvel associado a este processo.</p>
        @endif
    </div>

    {{-- Negócios do processo --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Negócios neste processo</h2>
        @if($processo->negocios->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-slate-600">Designação</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-slate-600">Requerente</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-slate-600">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-slate-600">Criado em</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-slate-600">Convertido</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase text-slate-600">Total</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($processo->negocios as $n)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 font-medium text-slate-800">{{ Str::limit($n->designacao, 35) }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $n->requerente->nome ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $statusCls = match($n->status) {
                                            'pendente' => 'bg-amber-100 text-amber-800',
                                            'aceite' => 'bg-emerald-100 text-emerald-800',
                                            'cancelado' => 'bg-red-100 text-red-800',
                                            'em_trabalho' => 'bg-blue-100 text-blue-800',
                                            'concluido' => 'bg-slate-100 text-slate-800',
                                            'faturado' => 'bg-slate-200 text-slate-700',
                                            default => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusCls }}">{{ \App\Models\Negocio::STATUS[$n->status] ?? $n->status }}</span>
                                </td>
                                <td class="px-4 py-2 text-slate-600">{{ $n->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $n->data_convertido ? $n->data_convertido->format('d/m/Y H:i') : '—' }}</td>
                                <td class="px-4 py-2 text-right font-medium text-slate-800">{{ $n->total_formatado }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('negocios.edit', $n) }}" class="text-slate-600 hover:text-slate-900">Ver / Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-slate-500">Ainda não há negócios associados a este processo.</p>
        @endif
    </div>
</div>
@endsection
