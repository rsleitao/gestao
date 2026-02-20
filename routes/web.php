<?php

use App\Http\Controllers\ConcelhoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\FreguesiaController;
use App\Http\Controllers\ImovelController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\NegocioItemController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\RequerenteController;
use App\Http\Controllers\ServicoController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('requerentes', RequerenteController::class);
Route::resource('servicos', ServicoController::class);
Route::resource('processos', ProcessoController::class);
Route::resource('negocios', NegocioController::class);
Route::get('negocios-kanban', [NegocioController::class, 'kanban'])->name('negocios.kanban');
Route::put('negocios/{negocio}/status', [NegocioController::class, 'updateStatus'])->name('negocios.update-status');
Route::post('negocios/{negocio}/itens', [NegocioItemController::class, 'store'])->name('negocios.itens.store');
Route::put('negocios/{negocio}/itens/{item}', [NegocioItemController::class, 'update'])->name('negocios.itens.update');
Route::delete('negocios/{negocio}/itens/{item}', [NegocioItemController::class, 'destroy'])->name('negocios.itens.destroy');

// Rotas API para cascata (necessárias para formulários)
Route::get('api/concelhos/distrito/{distrito}', [ConcelhoController::class, 'getByDistrito'])->name('api.concelhos.by-distrito');
Route::get('api/freguesias/concelho/{concelho}', [FreguesiaController::class, 'getByConcelho'])->name('api.freguesias.by-concelho');

// Apenas freguesias podem ser listadas e editadas (apenas editar nome)
Route::resource('freguesias', FreguesiaController::class)->only(['index', 'edit', 'update']);
Route::resource('lojas', LojaController::class);
Route::post('lojas/{loja}/toggle-ativo', [LojaController::class, 'toggleAtivo'])->name('lojas.toggle-ativo');
Route::resource('imoveis', ImovelController::class)->parameters(['imoveis' => 'imovel']);
