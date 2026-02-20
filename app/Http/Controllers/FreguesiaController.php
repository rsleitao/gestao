<?php

namespace App\Http\Controllers;

use App\Models\Concelho;
use App\Models\Distrito;
use App\Models\Freguesia;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class FreguesiaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Freguesia::query()->with('concelho.distrito')->orderBy('nome');
        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('id_distrito')) {
            $query->whereHas('concelho', function ($q) use ($request) {
                $q->where('id_distrito', $request->id_distrito);
            });
        }
        if ($request->filled('id_concelho')) {
            $query->where('id_concelho', $request->id_concelho);
        }
        $freguesias = $query->paginate(15)->withQueryString();
        $distritos = Distrito::orderBy('nome')->get();
        $concelhos = Concelho::with('distrito')->orderBy('nome')->get();
        return view('freguesias.index', compact('freguesias', 'distritos', 'concelhos'));
    }

    public function create(): View
    {
        $concelhos = Concelho::with('distrito')->orderBy('nome')->get();
        return view('freguesias.create', compact('concelhos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'id_concelho' => 'required|exists:concelhos,id',
        ]);
        Freguesia::create($request->only('nome', 'id_concelho'));
        return redirect()->route('freguesias.index')->with('success', 'Freguesia criada com sucesso.');
    }

    public function edit(Freguesia $freguesia): View
    {
        $freguesia->load('concelho.distrito');
        return view('freguesias.edit', compact('freguesia'));
    }

    public function update(Request $request, Freguesia $freguesia): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);
        $freguesia->update($request->only('nome'));
        return redirect()->route('freguesias.index')->with('success', 'Freguesia atualizada com sucesso.');
    }

    public function destroy(Freguesia $freguesia): RedirectResponse
    {
        return redirect()->route('freguesias.index')->with('error', 'Não é possível eliminar freguesias. Eles são mantidos para histórico.');
    }

    public function getByConcelho(Request $request, int $concelhoId): JsonResponse
    {
        $freguesias = Freguesia::where('id_concelho', $concelhoId)
            ->orderBy('nome')
            ->get(['id', 'nome']);
        
        return response()->json($freguesias);
    }
}
