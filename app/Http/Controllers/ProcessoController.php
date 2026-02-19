<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\Requerente;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProcessoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Processo::query()->with(['requerente', 'servico'])->orderByDesc('data_abertura');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('referencia', 'like', "%{$q}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $processos = $query->paginate(15)->withQueryString();

        return view('processos.index', compact('processos'));
    }

    public function create(): View
    {
        $requerentes = Requerente::orderBy('nome')->get();
        $servicos = Servico::where('ativo', true)->orderBy('nome')->get();

        return view('processos.create', compact('requerentes', 'servicos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'referencia' => 'required|string|max:50|unique:processos,referencia',
            'requerente_id' => 'required|exists:requerentes,id',
            'servico_id' => 'nullable|exists:servicos,id',
            'estado' => 'required|in:' . implode(',', Processo::ESTADOS),
            'data_abertura' => 'required|date',
            'data_limite' => 'nullable|date',
            'data_conclusao' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        Processo::create($validated);

        return redirect()->route('processos.index')->with('success', 'Processo criado com sucesso.');
    }

    public function edit(Processo $processo): View
    {
        $requerentes = Requerente::orderBy('nome')->get();
        $servicos = Servico::where('ativo', true)->orderBy('nome')->get();

        return view('processos.edit', compact('processo', 'requerentes', 'servicos'));
    }

    public function update(Request $request, Processo $processo): RedirectResponse
    {
        $validated = $request->validate([
            'referencia' => 'required|string|max:50|unique:processos,referencia,' . $processo->id,
            'requerente_id' => 'required|exists:requerentes,id',
            'servico_id' => 'nullable|exists:servicos,id',
            'estado' => 'required|in:' . implode(',', Processo::ESTADOS),
            'data_abertura' => 'required|date',
            'data_limite' => 'nullable|date',
            'data_conclusao' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        $processo->update($validated);

        return redirect()->route('processos.index')->with('success', 'Processo atualizado com sucesso.');
    }

    public function destroy(Processo $processo): RedirectResponse
    {
        $processo->delete();

        return redirect()->route('processos.index')->with('success', 'Processo eliminado com sucesso.');
    }
}
