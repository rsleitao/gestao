<?php

namespace App\Http\Controllers;

use App\Models\Concelho;
use App\Models\Distrito;
use App\Models\Freguesia;
use App\Models\Imovel;
use App\Models\Loja;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ImovelController extends Controller
{
    private function parseArrayField(?string $value): array
    {
        if (empty(trim((string) $value))) {
            return [];
        }
        return array_map('trim', explode(',', $value));
    }

    public function index(Request $request): View
    {
        $query = Imovel::query()->with(['distrito', 'concelho', 'freguesia', 'loja'])->orderByDesc('id');
        if ($request->filled('q')) {
            $query->where('morada', 'like', '%' . $request->q . '%')
                ->orWhere('nip', 'like', '%' . $request->q . '%')
                ->orWhere('localidade_imovel', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('id_distrito')) {
            $query->where('id_distrito', $request->id_distrito);
        }
        if ($request->filled('id_loja')) {
            $query->where('id_loja', $request->id_loja);
        }
        $imoveis = $query->paginate(15)->withQueryString();
        $distritos = Distrito::orderBy('nome')->get();
        $lojas = Loja::where('ativo', true)->orderBy('nome')->get();
        return view('imoveis.index', compact('imoveis', 'distritos', 'lojas'));
    }

    public function create(): View
    {
        $distritos = Distrito::orderBy('nome')->get();
        $concelhos = Concelho::with('distrito')->orderBy('nome')->get();
        $freguesias = Freguesia::with('concelho')->orderBy('nome')->get();
        $lojas = Loja::where('ativo', true)->orderBy('nome')->get();
        return view('imoveis.create', compact('distritos', 'concelhos', 'freguesias', 'lojas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:25',
            'morada' => 'required|string|max:255',
            'id_distrito' => 'nullable|exists:distritos,id',
            'id_concelho' => 'nullable|exists:concelhos,id',
            'id_freguesia' => 'nullable|exists:freguesias,id',
            'cod_postal' => 'required|string|max:255',
            'localidade_imovel' => 'required|string|max:255',
            'coordenadas' => 'nullable|string|max:255',
            'potencia' => 'nullable|numeric|min:0',
            'tensao' => 'nullable|string|max:20',
            'area_imovel' => 'nullable|numeric|min:0',
            'pisos' => 'nullable|integer|min:0',
            'tipo_imovel' => 'nullable|string|max:50',
            'id_loja' => 'nullable|exists:lojas,id',
            'pts' => 'nullable|string',
            'ggs' => 'nullable|string',
            'pcves' => 'nullable|string',
            'descricao' => 'nullable|string',
        ]);

        $validated['pts'] = $this->parseArrayField($request->pts);
        $validated['ggs'] = $this->parseArrayField($request->ggs);
        $validated['pcves'] = $this->parseArrayField($request->pcves);

        Imovel::create($validated);
        return redirect()->route('imoveis.index')->with('success', 'Imóvel criado com sucesso.');
    }

    public function edit(Imovel $imovel): View
    {
        $distritos = Distrito::orderBy('nome')->get();
        $concelhos = Concelho::with('distrito')->orderBy('nome')->get();
        $freguesias = Freguesia::with('concelho')->orderBy('nome')->get();
        $lojas = Loja::where('ativo', true)->orderBy('nome')->get();
        return view('imoveis.edit', compact('imovel', 'distritos', 'concelhos', 'freguesias', 'lojas'));
    }

    public function update(Request $request, Imovel $imovel): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:25',
            'morada' => 'required|string|max:255',
            'id_distrito' => 'nullable|exists:distritos,id',
            'id_concelho' => 'nullable|exists:concelhos,id',
            'id_freguesia' => 'nullable|exists:freguesias,id',
            'cod_postal' => 'required|string|max:255',
            'localidade_imovel' => 'required|string|max:255',
            'coordenadas' => 'nullable|string|max:255',
            'potencia' => 'nullable|numeric|min:0',
            'tensao' => 'nullable|string|max:20',
            'area_imovel' => 'nullable|numeric|min:0',
            'pisos' => 'nullable|integer|min:0',
            'tipo_imovel' => 'nullable|string|max:50',
            'id_loja' => 'nullable|exists:lojas,id',
            'pts' => 'nullable|string',
            'ggs' => 'nullable|string',
            'pcves' => 'nullable|string',
            'descricao' => 'nullable|string',
        ]);

        $validated['pts'] = $this->parseArrayField($request->pts);
        $validated['ggs'] = $this->parseArrayField($request->ggs);
        $validated['pcves'] = $this->parseArrayField($request->pcves);

        $imovel->update($validated);
        return redirect()->route('imoveis.index')->with('success', 'Imóvel atualizado com sucesso.');
    }

    public function destroy(Imovel $imovel): RedirectResponse
    {
        try {
            $imovelId = $imovel->id;
            
            // Verificar se está associado a processos usando DB::table diretamente
            $processosCount = DB::table('processos')->where('id_imovel', $imovelId)->count();
            if ($processosCount > 0) {
                return redirect()->route('imoveis.index')
                    ->with('error', "Não é possível eliminar: existem {$processosCount} processo(s) associado(s) a este imóvel.");
            }

            // Verificar se está associado a negócios usando DB::table diretamente
            $negociosCount = DB::table('negocios')->where('id_imovel', $imovelId)->whereNotNull('id_imovel')->count();
            if ($negociosCount > 0) {
                return redirect()->route('imoveis.index')
                    ->with('error', "Não é possível eliminar: existem {$negociosCount} negócio(s) associado(s) a este imóvel.");
            }

            // Eliminar o imóvel usando DB::table para garantir que funciona
            $deleted = DB::table('imoveis')->where('id', $imovelId)->delete();
            
            // Verificar se foi realmente eliminado
            if ($deleted > 0) {
                return redirect()->route('imoveis.index')->with('success', 'Imóvel eliminado com sucesso.');
            } else {
                // Se DB::table não funcionou, tentar com o modelo
                $imovel->delete();
                // Verificar novamente se foi eliminado
                $stillExists = DB::table('imoveis')->where('id', $imovelId)->exists();
                if (!$stillExists) {
                    return redirect()->route('imoveis.index')->with('success', 'Imóvel eliminado com sucesso.');
                }
                return redirect()->route('imoveis.index')
                    ->with('error', 'Não foi possível eliminar o imóvel. Pode haver uma restrição na base de dados.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar erros de foreign key constraint
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'foreign key constraint') || 
                str_contains($errorMessage, 'Cannot delete') ||
                str_contains($errorMessage, '1451')) {
                return redirect()->route('imoveis.index')
                    ->with('error', 'Não é possível eliminar: este imóvel está associado a outros registos na base de dados.');
            }
            // Log do erro completo para debug
            \Log::error('Erro ao eliminar imóvel: ' . $errorMessage);
            return redirect()->route('imoveis.index')
                ->with('error', 'Erro ao eliminar o imóvel: ' . substr($errorMessage, 0, 150));
        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar imóvel: ' . $e->getMessage());
            return redirect()->route('imoveis.index')
                ->with('error', 'Erro ao eliminar o imóvel: ' . substr($e->getMessage(), 0, 150));
        }
    }
}
