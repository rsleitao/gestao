<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\Processo;
use App\Models\Requerente;
use App\Models\Servico;
use App\Models\Trabalho;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $totalRequerentes = Requerente::count();
        $totalServicos = Servico::count();
        $totalProcessos = Processo::count();

        // Os meus trabalhos pendentes (não concluídos), ordenados por prazo (atrasados primeiro)
        $meusTrabalhosPendentes = collect();
        if ($user) {
            $meusTrabalhosPendentes = Trabalho::where('id_tecnico', $user->id)
                ->where('estado', '!=', Trabalho::ESTADO_CONCLUIDO)
                ->with(['negocio.processo', 'negocioItem', 'tecnico'])
                ->get()
                ->sortBy(fn (Trabalho $t) => $t->prazo_para_exibicao ? $t->prazo_para_exibicao->timestamp : PHP_INT_MAX)
                ->values();
        }

        // Negócios concluídos (status concluído), recentes primeiro
        $negociosConcluidosRecentes = Negocio::where('status', 'concluido')
            ->with(['processo', 'requerente'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        // Prazos para o calendário: trabalhos do utilizador (não concluídos) com prazo — inclui atrasados (até 60 dias atrás) e próximos (até 60 dias à frente)
        $hoje = Carbon::today();
        $inicioPeriodo = $hoje->copy()->subDays(60);
        $fimPeriodo = $hoje->copy()->addDays(60);
        $prazosPorDia = [];
        if ($user) {
            $trabalhosComPrazo = Trabalho::where('id_tecnico', $user->id)
                ->where('estado', '!=', Trabalho::ESTADO_CONCLUIDO)
                ->with(['negocio', 'negocioItem'])
                ->get()
                ->filter(fn (Trabalho $t) => $t->prazo_para_exibicao && $t->prazo_para_exibicao->between($inicioPeriodo, $fimPeriodo));
            foreach ($trabalhosComPrazo as $t) {
                $key = $t->prazo_para_exibicao->format('Y-m-d');
                if (!isset($prazosPorDia[$key])) {
                    $prazosPorDia[$key] = [];
                }
                $prazosPorDia[$key][] = $t;
            }
            ksort($prazosPorDia);
        }

        // Mes atual e próximo para o calendário (dias com prazos)
        $mesAtual = $hoje->format('Y-m');
        $mesProximo = $hoje->copy()->addMonth()->format('Y-m');

        return view('dashboard', [
            'totalRequerentes' => $totalRequerentes,
            'totalServicos' => $totalServicos,
            'totalProcessos' => $totalProcessos,
            'meusTrabalhosPendentes' => $meusTrabalhosPendentes,
            'negociosConcluidosRecentes' => $negociosConcluidosRecentes,
            'prazosPorDia' => $prazosPorDia,
            'mesAtual' => $mesAtual,
            'mesProximo' => $mesProximo,
            'hoje' => $hoje,
        ]);
    }
}
