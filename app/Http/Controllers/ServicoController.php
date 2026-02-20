<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ServicoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Servico::query()->orderBy('codigo');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('codigo', 'like', "%{$q}%")
                    ->orWhere('nome', 'like', "%{$q}%");
            });
        }

        if ($request->has('ativo') && $request->ativo !== '') {
            $query->where('ativo', (bool) $request->ativo);
        }

        $servicos = $query->paginate(15)->withQueryString();

        return view('servicos.index', compact('servicos'));
    }

    public function create(): View
    {
        return view('servicos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:servicos,codigo',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'unidade' => 'nullable|string|max:50',
            'preco_base' => 'nullable|numeric|min:0',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        Servico::create($validated);

        return redirect()->route('servicos.index')->with('success', 'Serviço criado com sucesso.');
    }

    public function edit(Servico $servico): View
    {
        return view('servicos.edit', compact('servico'));
    }

    public function update(Request $request, Servico $servico): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:servicos,codigo,' . $servico->id,
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'unidade' => 'nullable|string|max:50',
            'preco_base' => 'nullable|numeric|min:0',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        $servico->update($validated);

        return redirect()->route('servicos.index')->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Servico $servico): RedirectResponse
    {
        $servico->delete();

        return redirect()->route('servicos.index')->with('success', 'Serviço eliminado com sucesso.');
    }
}
