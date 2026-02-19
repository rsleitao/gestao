<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

// Rotas para escalar: descomentar e criar controladores quando necessário
// Route::resource('requerentes', RequerenteController::class);
// Route::resource('servicos', ServicoController::class);
// Route::resource('processos', ProcessoController::class);
