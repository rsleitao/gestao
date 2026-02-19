<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\Requerente;
use App\Models\Servico;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'totalRequerentes' => Requerente::count(),
            'totalServicos' => Servico::count(),
            'totalProcessos' => Processo::count(),
        ]);
    }
}
