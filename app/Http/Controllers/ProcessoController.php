<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\Processo;
use App\Models\Requerente;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProcessoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Processo::query()
            ->with([
                'requerente',
                'imovel.loja',
                'imovel.distrito',
                'imovel.concelho',
            ])
            ->orderByDesc('data_abertura');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $codigo = null;
            if (preg_match('/^(\d{2}-)?(\d+)$/', $q, $m)) {
                $codigo = (int) $m[2];
            } elseif (is_numeric($q)) {
                $codigo = (int) $q;
            }
            if ($codigo !== null) {
                $query->where('codigo', $codigo);
            }
        }

        if ($request->filled('id_loja')) {
            $query->whereHas('imovel', function ($q) use ($request) {
                $q->where('id_loja', $request->id_loja);
            });
        }

        $processos = $query->paginate(15)->withQueryString();
        $lojas = Loja::where('ativo', true)->orderBy('nome')->get();

        return view('processos.index', compact('processos', 'lojas'));
    }

    public function create(): View
    {
        $requerentes = Requerente::orderBy('nome')->get();

        return view('processos.create', compact('requerentes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'requerente_id' => 'required|exists:requerentes,id',
            'designacao' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $validated['codigo'] = Processo::nextCodigo();
        $validated['data_abertura'] = now()->toDateString();
        Processo::create($validated);

        return redirect()->route('processos.index')->with('success', 'Processo criado com sucesso.');
    }

    public function show(Processo $processo): View
    {
        $processo->load([
            'requerente',
            'imovel.distrito',
            'imovel.concelho',
            'imovel.freguesia',
            'imovel.loja',
            'negocios' => fn ($q) => $q->with(['requerente', 'itens'])->orderByDesc('created_at'),
        ]);

        return view('processos.show', compact('processo'));
    }

    public function edit(Processo $processo): View
    {
        $requerentes = Requerente::orderBy('nome')->get();

        return view('processos.edit', compact('processo', 'requerentes'));
    }

    public function update(Request $request, Processo $processo): RedirectResponse
    {
        $validated = $request->validate([
            'requerente_id' => 'required|exists:requerentes,id',
            'designacao' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $processo->update($validated);

        return redirect()->route('processos.index')->with('success', 'Processo atualizado com sucesso.');
    }

    public function destroy(Processo $processo): RedirectResponse
    {
        // Processos não podem ser eliminados por questões de segurança e histórico
        return redirect()->route('processos.index')
            ->with('error', 'Não é possível eliminar processos. Eles são mantidos para histórico.');
    }
}
