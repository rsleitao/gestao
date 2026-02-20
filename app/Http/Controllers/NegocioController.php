<?php

namespace App\Http\Controllers;

use App\Models\Concelho;
use App\Models\Distrito;
use App\Models\Freguesia;
use App\Models\Imovel;
use App\Models\Loja;
use App\Models\Negocio;
use App\Models\Processo;
use App\Models\Requerente;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NegocioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Negocio::query()->with(['requerente', 'imovel', 'processo', 'tecnico'])->orderByDesc('created_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('designacao', 'like', "%{$q}%")
                ->orWhereHas('requerente', fn($q) => $q->where('nome', 'like', "%{$q}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $negocios = $query->paginate(15)->withQueryString();
        return view('negocios.index', compact('negocios'));
    }

    public function kanban(): View
    {
        $negocios = Negocio::with(['requerente', 'imovel', 'processo', 'itens'])->get();
        $negociosPorStatus = [];
        
        foreach (Negocio::STATUS as $key => $label) {
            $negociosPorStatus[$key] = $negocios->where('status', $key)->values();
        }
        
        return view('negocios.kanban', compact('negociosPorStatus'));
    }

    public function updateStatus(Request $request, Negocio $negocio): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Negocio::STATUS)),
        ]);

        $statusAnterior = $negocio->status;
        $novoStatus = $validated['status'];

        // Reverter para pendente: eliminar processo criado por este negócio, se existir
        if ($novoStatus === 'pendente' && $statusAnterior === 'aceite') {
            $processo = $negocio->processo;
            $negocio->update([
                'status' => 'pendente',
                'id_processo' => null,
                'data_convertido' => null,
            ]);
            if ($processo && (int) $processo->id_negocio_origem === (int) $negocio->id) {
                $processo->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Estado atualizado. O processo criado por este negócio foi removido.',
                'negocio' => [
                    'id' => $negocio->id,
                    'status' => $negocio->status,
                    'total_formatado' => $negocio->total_formatado,
                ]
            ]);
        }

        // Cancelar negócio: eliminar processo criado por este negócio, se existir
        if ($novoStatus === 'cancelado') {
            $processo = $negocio->processo;
            $processoCriadoPorEste = $processo && (int) $processo->id_negocio_origem === (int) $negocio->id;
            
            $negocio->update([
                'status' => 'cancelado',
                'id_processo' => null,
            ]);
            
            if ($processoCriadoPorEste) {
                $processo->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => $processoCriadoPorEste 
                    ? 'Negócio cancelado. O processo criado por este negócio foi removido.'
                    : 'Negócio cancelado com sucesso.',
                'negocio' => [
                    'id' => $negocio->id,
                    'status' => $negocio->status,
                    'total_formatado' => $negocio->total_formatado,
                ]
            ]);
        }

        // Passar a aceite: criar processo se for negócio novo (sem processo associado)
        if ($novoStatus === 'aceite') {
            if (!$negocio->id_processo) {
                $processo = Processo::create([
                    'codigo' => Processo::nextCodigo(),
                    'requerente_id' => $negocio->id_requerente,
                    'id_imovel' => $negocio->id_imovel,
                    'id_negocio_origem' => $negocio->id,
                    'designacao' => Processo::gerarDesignacao($negocio->id_imovel),
                    'data_abertura' => now()->toDateString(),
                ]);
                $negocio->update([
                    'status' => 'aceite',
                    'id_processo' => $processo->id,
                    'data_convertido' => now(),
                ]);
            } else {
                $negocio->update([
                    'status' => 'aceite',
                    'data_convertido' => $negocio->data_convertido ?? now(),
                ]);
            }
        } else {
            $negocio->update(['status' => $novoStatus]);
        }

        if ($novoStatus === 'faturado' && !$negocio->data_faturado) {
            $negocio->update(['data_faturado' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado atualizado com sucesso.',
            'negocio' => [
                'id' => $negocio->id,
                'status' => $negocio->status,
                'total_formatado' => $negocio->total_formatado,
            ]
        ]);
    }

    public function create(): View
    {
        $requerentes = Requerente::orderBy('nome')->get();
        $processos = Processo::with(['requerente', 'imovel'])->orderByDesc('created_at')->get();
        $servicos = Servico::where('ativo', true)->orderBy('nome')->get();
        $distritos = Distrito::orderBy('nome')->get();
        $concelhos = Concelho::with('distrito')->orderBy('nome')->get();
        $freguesias = Freguesia::with('concelho')->orderBy('nome')->get();
        $lojas = Loja::where('ativo', true)->orderBy('nome')->get();
        return view('negocios.create', compact('requerentes', 'processos', 'servicos', 'distritos', 'concelhos', 'freguesias', 'lojas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tipoNegocio = $request->input('tipo_negocio'); // 'novo' ou 'processo_existente'

        if ($tipoNegocio === 'processo_existente') {
            // Negócio de processo existente
            $validated = $request->validate([
                'id_processo' => 'required|exists:processos,id',
                'designacao' => 'required|string|max:255',
                'observacoes' => 'nullable|string',
                'id_tecnico' => 'nullable|exists:users,id',
                'itens' => 'required|array|min:1',
                'itens.*.descricao' => 'required|string|max:255',
                'itens.*.preco' => 'required|numeric|min:0',
                'itens.*.quantidade' => 'required|numeric|min:0.01',
                'itens.*.prazo_data' => 'nullable|date',
                'itens.*.tipo_trabalho' => 'required|in:licenciamento,execucao',
            ]);

            $processo = Processo::findOrFail($validated['id_processo']);
            $validated['id_requerente'] = $processo->requerente_id;
            $validated['id_imovel'] = $processo->id_imovel;
            $validated['id_requerente_fatura'] = $processo->requerente_id; // Por padrão, fatura ao requerente do processo
        } else {
            // Novo negócio
            $validated = $request->validate([
                'id_requerente' => 'required|exists:requerentes,id',
                'id_requerente_fatura' => 'required|exists:requerentes,id',
                'designacao' => 'required|string|max:255',
                'observacoes' => 'nullable|string',
                'id_tecnico' => 'nullable|exists:users,id',
                // Dados do novo imóvel
                'imovel_nip' => 'nullable|string|max:25',
                'imovel_morada' => 'required|string|max:255',
                'imovel_id_distrito' => 'nullable|exists:distritos,id',
                'imovel_id_concelho' => 'nullable|exists:concelhos,id',
                'imovel_id_freguesia' => 'nullable|exists:freguesias,id',
                'imovel_cod_postal' => 'required|string|max:255',
                'imovel_localidade' => 'required|string|max:255',
                'imovel_coordenadas' => 'nullable|string|max:255',
                'imovel_potencia' => 'nullable|numeric|min:0',
                'imovel_tensao' => 'nullable|string|max:20',
                'imovel_area_imovel' => 'nullable|numeric|min:0',
                'imovel_pisos' => 'nullable|integer|min:0',
                'imovel_tipo_imovel' => 'nullable|string|max:50',
                'imovel_id_loja' => 'nullable|exists:lojas,id',
                'imovel_descricao' => 'nullable|string',
                'itens' => 'required|array|min:1',
                'itens.*.descricao' => 'required|string|max:255',
                'itens.*.preco' => 'required|numeric|min:0',
                'itens.*.quantidade' => 'required|numeric|min:0.01',
                'itens.*.prazo_data' => 'nullable|date',
                'itens.*.tipo_trabalho' => 'required|in:licenciamento,execucao',
            ]);

            // Criar novo imóvel
            $imovel = Imovel::create([
                'nip' => $validated['imovel_nip'] ?? null,
                'morada' => $validated['imovel_morada'],
                'id_distrito' => $validated['imovel_id_distrito'] ?? null,
                'id_concelho' => $validated['imovel_id_concelho'] ?? null,
                'id_freguesia' => $validated['imovel_id_freguesia'] ?? null,
                'cod_postal' => $validated['imovel_cod_postal'],
                'localidade_imovel' => $validated['imovel_localidade'],
                'coordenadas' => $validated['imovel_coordenadas'] ?? null,
                'potencia' => $validated['imovel_potencia'] ?? null,
                'tensao' => $validated['imovel_tensao'] ?? null,
                'area_imovel' => $validated['imovel_area_imovel'] ?? null,
                'pisos' => $validated['imovel_pisos'] ?? null,
                'tipo_imovel' => $validated['imovel_tipo_imovel'] ?? null,
                'id_loja' => $validated['imovel_id_loja'] ?? null,
                'descricao' => $validated['imovel_descricao'] ?? null,
            ]);

            $validated['id_imovel'] = $imovel->id;
            
            // Limpar campos do imóvel do validated
            foreach (['imovel_nip', 'imovel_morada', 'imovel_id_distrito', 'imovel_id_concelho', 'imovel_id_freguesia', 'imovel_cod_postal', 'imovel_localidade', 'imovel_coordenadas', 'imovel_potencia', 'imovel_tensao', 'imovel_area_imovel', 'imovel_pisos', 'imovel_tipo_imovel', 'imovel_id_loja', 'imovel_descricao'] as $field) {
                unset($validated[$field]);
            }
        }

        $validated['status'] = 'pendente';
        if (!isset($validated['id_tecnico']) && auth()->check()) {
            $validated['id_tecnico'] = auth()->id();
        }

        $itens = $validated['itens'] ?? [];
        unset($validated['itens']);

        $negocio = Negocio::create($validated);

        // Criar itens
        foreach ($itens as $index => $item) {
            $negocio->itens()->create([
                'descricao' => $item['descricao'],
                'preco' => $item['preco'],
                'quantidade' => $item['quantidade'],
                'prazo_data' => $item['prazo_data'] ?? null,
                'tipo_trabalho' => $item['tipo_trabalho'],
                'ordem' => $index + 1,
            ]);
        }

        return redirect()->route('negocios.index')->with('success', 'Negócio criado com sucesso.');
    }

    public function edit(Negocio $negocio): View
    {
        $negocio->load('itens'); // Carregar itens para evitar N+1
        $requerentes = Requerente::orderBy('nome')->get();
        $imoveis = Imovel::orderBy('morada')->get();
        $servicos = Servico::where('ativo', true)->orderBy('nome')->get();
        return view('negocios.edit', compact('negocio', 'requerentes', 'imoveis', 'servicos'));
    }

    public function update(Request $request, Negocio $negocio): RedirectResponse
    {
        $validated = $request->validate([
            'id_requerente' => 'required|exists:requerentes,id',
            'id_requerente_fatura' => 'nullable|exists:requerentes,id',
            'id_imovel' => 'nullable|exists:imoveis,id',
            'designacao' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(Negocio::STATUS)),
        ]);

        // Se mudou para "aceite", registar data
        if ($validated['status'] === 'aceite' && $negocio->status !== 'aceite') {
            $validated['data_convertido'] = now();
        }

        // Se mudou para "faturado", registar data
        if ($validated['status'] === 'faturado' && $negocio->status !== 'faturado') {
            $validated['data_faturado'] = now();
        }

        $negocio->update($validated);

        return redirect()->route('negocios.index')->with('success', 'Negócio atualizado com sucesso.');
    }

    public function destroy(Negocio $negocio): RedirectResponse
    {
        $negocio->delete();
        return redirect()->route('negocios.index')->with('success', 'Negócio eliminado com sucesso.');
    }
}
