<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\RequerenteController;
use App\Http\Controllers\ServicoController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('requerentes', RequerenteController::class);
Route::resource('servicos', ServicoController::class);
Route::resource('processos', ProcessoController::class);
