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
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequerenteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServicoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
})->name('home');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:access-gestao')->group(function () {
        Route::resource('servicos', ServicoController::class);
        Route::resource('negocios', NegocioController::class);
        Route::get('negocios-kanban', [NegocioController::class, 'kanban'])->name('negocios.kanban');
        Route::put('negocios/{negocio}/status', [NegocioController::class, 'updateStatus'])->name('negocios.update-status');
        Route::post('negocios/{negocio}/itens', [NegocioItemController::class, 'store'])->name('negocios.itens.store');
        Route::put('negocios/{negocio}/itens/{item}', [NegocioItemController::class, 'update'])->name('negocios.itens.update');
        Route::delete('negocios/{negocio}/itens/{item}', [NegocioItemController::class, 'destroy'])->name('negocios.itens.destroy');
    });

    Route::middleware('can:access-projetos')->group(function () {
        Route::resource('processos', ProcessoController::class);
        Route::resource('requerentes', RequerenteController::class);
    });

    Route::middleware('can:access-exploracao')->group(function () {
        Route::resource('lojas', LojaController::class);
        Route::post('lojas/{loja}/toggle-ativo', [LojaController::class, 'toggleAtivo'])->name('lojas.toggle-ativo');
        Route::resource('imoveis', ImovelController::class)->parameters(['imoveis' => 'imovel']);
    });

    Route::get('api/concelhos/distrito/{distrito}', [ConcelhoController::class, 'getByDistrito'])->name('api.concelhos.by-distrito');
    Route::get('api/freguesias/concelho/{concelho}', [FreguesiaController::class, 'getByConcelho'])->name('api.freguesias.by-concelho');

    Route::middleware('can:access-admin')->group(function () {
        Route::resource('freguesias', FreguesiaController::class)->only(['index', 'edit', 'update']);
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::put('permissions', [PermissionController::class, 'updateAll'])->name('permissions.update-all');
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update'])->middleware('can:manage-users');
    });
});

require __DIR__.'/auth.php';
