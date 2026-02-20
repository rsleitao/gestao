<?php

namespace App\Http\Controllers;

use App\Models\Concelho;
use App\Models\Distrito;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ConcelhoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Concelho::query()->with('distrito')->orderBy('nome');
        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('id_distrito')) {
            $query->where('id_distrito', $request->id_distrito);
        }
        $concelhos = $query->paginate(15)->withQueryString();
        $distritos = Distrito::orderBy('nome')->get();
        return view('concelhos.index', compact('concelhos', 'distritos'));
    }

    public function create(): View
    {
        $distritos = Distrito::orderBy('nome')->get();
        return view('concelhos.create', compact('distritos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'id_distrito' => 'required|exists:distritos,id',
        ]);
        Concelho::create($request->only('nome', 'id_distrito'));
        return redirect()->route('concelhos.index')->with('success', 'Concelho criado com sucesso.');
    }

    public function edit(Concelho $concelho): View
    {
        $distritos = Distrito::orderBy('nome')->get();
        return view('concelhos.edit', compact('concelho', 'distritos'));
    }

    public function update(Request $request, Concelho $concelho): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'id_distrito' => 'required|exists:distritos,id',
        ]);
        $concelho->update($request->only('nome', 'id_distrito'));
        return redirect()->route('concelhos.index')->with('success', 'Concelho atualizado com sucesso.');
    }

    public function destroy(Concelho $concelho): RedirectResponse
    {
        return redirect()->back()->with('error', 'Não é possível eliminar concelhos. Eles são mantidos para histórico.');
    }

    public function getByDistrito(Request $request, int $distritoId): JsonResponse
    {
        $concelhos = Concelho::where('id_distrito', $distritoId)
            ->orderBy('nome')
            ->get(['id', 'nome']);
        
        return response()->json($concelhos);
    }
}
