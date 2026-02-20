<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LojaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Loja::query()->orderBy('nome');
        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->q . '%');
        }
        if ($request->has('ativo') && $request->ativo !== '') {
            $query->where('ativo', (bool) $request->ativo);
        }
        $lojas = $query->paginate(15)->withQueryString();
        return view('lojas.index', compact('lojas'));
    }

    public function create(): View
    {
        return view('lojas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'ativo' => 'boolean',
            'imagem' => 'nullable|image|max:2048',
        ]);
        $validated['ativo'] = $request->boolean('ativo');

        if ($request->hasFile('imagem')) {
            $validated['imagem'] = $request->file('imagem')->store('lojas', 'public');
        }

        Loja::create($validated);
        return redirect()->route('lojas.index')->with('success', 'Loja criada com sucesso.');
    }

    public function edit(Loja $loja): View
    {
        return view('lojas.edit', compact('loja'));
    }

    public function update(Request $request, Loja $loja): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'ativo' => 'boolean',
            'imagem' => 'nullable|image|max:2048',
        ]);
        $validated['ativo'] = $request->boolean('ativo');

        if ($request->hasFile('imagem')) {
            if ($loja->imagem) {
                Storage::disk('public')->delete($loja->imagem);
            }
            $validated['imagem'] = $request->file('imagem')->store('lojas', 'public');
        }

        $loja->update($validated);
        return redirect()->route('lojas.index')->with('success', 'Loja atualizada com sucesso.');
    }

    public function destroy(Loja $loja): RedirectResponse
    {
        return redirect()->route('lojas.index')
            ->with('error', 'Não é possível eliminar lojas. Utilize Ativo/Inativo para as desativar.');
    }

    public function toggleAtivo(Loja $loja): RedirectResponse
    {
        $loja->update(['ativo' => !$loja->ativo]);
        $estado = $loja->ativo ? 'ativada' : 'desativada';
        return redirect()->route('lojas.index')->with('success', "Loja {$estado} com sucesso.");
    }
}
