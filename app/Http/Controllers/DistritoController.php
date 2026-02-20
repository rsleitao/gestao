<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DistritoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Distrito::query()->orderBy('nome');
        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->q . '%');
        }
        $distritos = $query->paginate(15)->withQueryString();
        return view('distritos.index', compact('distritos'));
    }

    public function create(): View
    {
        return view('distritos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['nome' => 'required|string|max:255']);
        Distrito::create($request->only('nome'));
        return redirect()->route('distritos.index')->with('success', 'Distrito criado com sucesso.');
    }

    public function edit(Distrito $distrito): View
    {
        return view('distritos.edit', compact('distrito'));
    }

    public function update(Request $request, Distrito $distrito): RedirectResponse
    {
        $request->validate(['nome' => 'required|string|max:255']);
        $distrito->update($request->only('nome'));
        return redirect()->route('distritos.index')->with('success', 'Distrito atualizado com sucesso.');
    }

    public function destroy(Distrito $distrito): RedirectResponse
    {
        return redirect()->back()->with('error', 'Não é possível eliminar distritos. Eles são mantidos para histórico.');
    }
}
