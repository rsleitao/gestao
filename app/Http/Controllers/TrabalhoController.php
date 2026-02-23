<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\Negocio;
use App\Models\NegocioItem;
use App\Models\Trabalho;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrabalhoController extends Controller
{
    public function kanban(): View
    {
        $trabalhos = Trabalho::with(['negocio.requerente', 'negocio.imovel', 'negocio.processo', 'negocio.itens', 'tecnico', 'negocioItem'])
            ->orderBy('ordem')
            ->orderBy('id')
            ->get();

        $trabalhosPorEstado = [];
        foreach (array_keys(Trabalho::ESTADOS) as $estado) {
            $col = $trabalhos->where('estado', $estado);
            // Ordenar por urgência de prazo: prazos mais próximos/atrasados primeiro, sem prazo no fim
            $trabalhosPorEstado[$estado] = $col->sortBy(function ($t) {
                $p = $t->prazo_para_exibicao;
                return $p ? $p->timestamp : PHP_INT_MAX;
            })->values();
        }

        $tecnicosParaFiltro = $trabalhos->pluck('tecnico')->filter()->unique(fn ($u) => $u->id)->map(function ($u) {
            $parts = preg_split('/\s+/', trim($u->name ?? ''), 2);
            $initials = count($parts) >= 2
                ? mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1))
                : mb_strtoupper(mb_substr($u->name ?? '?', 0, 2));
            return ['id' => (string) $u->id, 'name' => $u->name, 'initials' => $initials ?: '?'];
        })->values()->all();
        if ($trabalhos->contains(fn ($t) => !$t->id_tecnico)) {
            $tecnicosParaFiltro[] = ['id' => 'em-aberto', 'name' => 'Em aberto', 'initials' => '—'];
        }

        $servicosParaFiltro = $trabalhos->map(fn ($t) => $t->descricao_servico)->filter()->unique()->values()->all();

        $idLojasPresentes = $trabalhos->map(fn ($t) => $t->negocio->imovel->id_loja ?? null)->filter()->unique()->values();
        $lojasParaFiltro = Loja::whereIn('id', $idLojasPresentes)->where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
        $temTrabalhosSemLoja = $trabalhos->contains(fn ($t) => !($t->negocio->imovel->id_loja ?? null));

        return view('trabalhos.kanban', compact('trabalhosPorEstado', 'tecnicosParaFiltro', 'servicosParaFiltro', 'lojasParaFiltro', 'temTrabalhosSemLoja'));
    }

    public function store(Request $request, Negocio $negocio): RedirectResponse
    {
        $validated = $request->validate([
            'id_negocio_item' => 'required|exists:negocio_itens,id',
            'id_tecnico' => 'nullable|exists:users,id',
        ]);

        if ((int) NegocioItem::where('id', $validated['id_negocio_item'])->value('id_negocio') !== (int) $negocio->id) {
            return redirect()->route('negocios.edit', $negocio)->with('error', 'Item inválido.');
        }
        if (Trabalho::where('id_negocio', $negocio->id)->where('id_negocio_item', $validated['id_negocio_item'])->exists()) {
            return redirect()->route('negocios.edit', $negocio)->with('error', 'Já existe um trabalho para este item.');
        }

        Trabalho::create([
            'id_negocio' => $negocio->id,
            'id_negocio_item' => $validated['id_negocio_item'],
            'id_tecnico' => $validated['id_tecnico'] ?? null,
            'estado' => Trabalho::ESTADO_A_FAZER,
            'ordem' => (int) Trabalho::where('id_negocio', $negocio->id)->max('ordem') + 1,
        ]);

        return redirect()->route('negocios.edit', $negocio)
            ->with('success', 'Trabalho adicionado ao negócio.');
    }

    public function updateEstado(Request $request, Trabalho $trabalho): JsonResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:' . implode(',', array_keys(Trabalho::ESTADOS)),
        ]);

        $estadoAnterior = $trabalho->estado;
        $trabalho->update(['estado' => $validated['estado']]);

        $negocio = $trabalho->negocio->fresh();

        if ($validated['estado'] === Trabalho::ESTADO_CONCLUIDO) {
            if ($negocio->todosTrabalhosConcluidos()) {
                $negocio->update(['status' => 'concluido']);
            }
        } elseif ($validated['estado'] !== Trabalho::ESTADO_A_FAZER) {
            // Pelo menos um trabalho saiu de "A fazer" (em execução, pendente ou concluído) → negócio passa a Em trabalho
            if ($negocio->status === 'aceite') {
                $negocio->update(['status' => 'em_trabalho']);
            }
            if ($estadoAnterior === Trabalho::ESTADO_CONCLUIDO && $negocio->status === 'concluido') {
                $negocio->update(['status' => 'em_trabalho']);
            }
        } else {
            // Trabalho voltou para "A fazer" → se todos estiverem em A fazer, negócio volta a Aceite
            if ($negocio->status === 'em_trabalho' && $negocio->todosTrabalhosEmAFazer()) {
                $negocio->update(['status' => 'aceite']);
            }
            if ($estadoAnterior === Trabalho::ESTADO_CONCLUIDO && $negocio->status === 'concluido') {
                $negocio->update(['status' => 'em_trabalho']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado atualizado.',
            'trabalho' => [
                'id' => $trabalho->id,
                'estado' => $trabalho->estado,
            ],
        ]);
    }

    /** Atualizar o técnico associado ao trabalho (chamado a partir do modal). */
    public function updateTecnico(Request $request, Trabalho $trabalho): JsonResponse
    {
        $validated = $request->validate([
            'id_tecnico' => 'nullable|exists:users,id',
        ]);

        $trabalho->update(['id_tecnico' => $validated['id_tecnico'] ?? null]);

        return response()->json([
            'success' => true,
            'tecnico_nome' => $trabalho->fresh()->tecnico?->name ?? 'Em aberto',
        ]);
    }

    public function destroy(Negocio $negocio, Trabalho $trabalho): RedirectResponse
    {
        if ((int) $trabalho->id_negocio !== (int) $negocio->id) {
            abort(404);
        }
        $trabalho->delete();

        $negocio->refresh();
        if ($negocio->trabalhos()->count() === 0) {
            if ($negocio->status === 'em_trabalho') {
                $negocio->update(['status' => 'aceite']);
            }
        } elseif ($negocio->todosTrabalhosEmAFazer() && $negocio->status === 'em_trabalho') {
            $negocio->update(['status' => 'aceite']);
        } elseif (!$negocio->todosTrabalhosConcluidos() && $negocio->status === 'concluido') {
            $negocio->update(['status' => 'em_trabalho']);
        }

        return redirect()->route('negocios.edit', $negocio)
            ->with('success', 'Trabalho removido.');
    }
}
