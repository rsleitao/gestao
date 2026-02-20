<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\NegocioItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NegocioItemController extends Controller
{
    public function store(Request $request, Negocio $negocio): RedirectResponse
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'quantidade' => 'required|numeric|min:0.01',
            'prazo_data' => 'nullable|date',
            'tipo_trabalho' => 'required|in:licenciamento,execucao',
        ]);

        // Determinar ordem (último item + 1)
        $ultimaOrdem = $negocio->itens()->max('ordem') ?? 0;
        $validated['ordem'] = $ultimaOrdem + 1;

        $negocio->itens()->create($validated);

        return redirect()->route('negocios.edit', $negocio)
            ->with('success', 'Item adicionado com sucesso.');
    }

    public function update(Request $request, Negocio $negocio, NegocioItem $item): RedirectResponse
    {
        // Verificar se o item pertence ao negócio
        if ($item->id_negocio !== $negocio->id) {
            abort(404);
        }

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'quantidade' => 'required|numeric|min:0.01',
            'prazo_data' => 'nullable|date',
            'tipo_trabalho' => 'required|in:licenciamento,execucao',
        ]);

        $item->update($validated);

        return redirect()->route('negocios.edit', $negocio)
            ->with('success', 'Item atualizado com sucesso.');
    }

    public function destroy(Negocio $negocio, NegocioItem $item): RedirectResponse
    {
        // Verificar se o item pertence ao negócio
        if ($item->id_negocio !== $negocio->id) {
            abort(404);
        }

        $item->delete();

        return redirect()->route('negocios.edit', $negocio)
            ->with('success', 'Item eliminado com sucesso.');
    }
}
