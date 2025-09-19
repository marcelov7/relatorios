<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\RelatorioAtribuicaoController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\InspecaoGeradorController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Página inicial - redireciona para dashboard se autenticado, senão para login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rotas de teste públicas (fora do middleware de auth)
Route::get('/teste-rota', [App\Http\Controllers\TesteController::class, 'testeRota'])->name('testeRota');

// Rota para teste de PDF com Browsershot (local, fora do grupo auth)
Route::get('/relatorios/pdf-browsershot', [App\Http\Controllers\RelatorioController::class, 'generatePdfBrowsershot'])->name('relatorios.pdfBrowsershot');

// Rota para PDF individual de relatório com Browsershot
Route::get('/relatorios/{id}/pdf-browsershot', [App\Http\Controllers\RelatorioController::class, 'pdfBrowsershot'])->name('relatorios.pdf-browsershot');

// Rota de teste para imagens
Route::get('/test-image/{path}', function ($path) {
    $fullPath = storage_path("app/public/{$path}");
    if (file_exists($fullPath)) {
        return response()->file($fullPath);
    }
    return response('Imagem não encontrada', 404);
})->where('path', '.*');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    // Nova rota para geração de PDF com Spatie Laravel PDF
    Route::get('/relatorios/pdf-spatie', [App\Http\Controllers\RelatorioController::class, 'generatePdfSpatie'])->name('relatorios.pdfSpatie');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Relatórios
    Route::resource('relatorios', RelatorioController::class);
    
    // Locais (apenas para admins)
    Route::resource('locais', LocalController::class)->middleware('admin');
    
    // Equipamentos (apenas para admins)
    Route::resource('equipamentos', EquipamentoController::class)->middleware('admin');
    
    // Motores (apenas para admins)
    Route::resource('motores', MotorController::class)
        ->parameters(['motores' => 'motor'])
        ->middleware('admin');
    
    // Página Inertia para Equipamentos de Teste
    Route::get('equipamento-tests', [App\Http\Controllers\EquipamentoTestController::class, 'indexPage'])->middleware('admin');
    // Endpoint JSON para listagem AJAX (agora aberto para todos autenticados)
    Route::get('equipamento-tests-list', [App\Http\Controllers\EquipamentoTestController::class, 'index']);
    // Resource, exceto index
    Route::resource('equipamento-tests', App\Http\Controllers\EquipamentoTestController::class)->except(['index'])->middleware('admin');
    
    // Exportação e importação CSV para equipamentos de teste
    Route::get('equipamento-tests-export', [App\Http\Controllers\EquipamentoTestController::class, 'exportCsv'])->middleware('admin');
    Route::post('equipamento-tests-import', [App\Http\Controllers\EquipamentoTestController::class, 'importCsv'])->middleware('admin');
    
    // Gerenciamento de usuários (apenas para admins)
    Route::resource('users', UserController::class)->middleware('admin');
    
    // Setores (apenas para admins)
    Route::resource('setores', SetorController::class)
        ->parameters(['setores' => 'setor'])
        ->middleware('admin');
    
    // Inspeções de Gerador
    Route::resource('inspecao-geradores', InspecaoGeradorController::class);
    
    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rotas de Atualização de Progresso
    Route::prefix('relatorios/{relatorio}')->group(function () {
        Route::post('/atualizar-progresso', [RelatorioAtribuicaoController::class, 'atualizarProgresso'])->name('relatorios.atualizar-progresso');
    });

    // APIs para selects dinâmicos
    Route::prefix('api')->group(function () {
        Route::get('locais-ativos', [LocalController::class, 'apiLocaisAtivos'])->name('api.locais.ativos');
        Route::get('equipamentos-ativos', [EquipamentoController::class, 'apiEquipamentosAtivos'])->name('api.equipamentos.ativos');
        Route::get('equipamentos-por-local', [EquipamentoController::class, 'apiEquipamentosPorLocal'])->name('api.equipamentos.por-local');
        Route::get('equipamentos-por-setor', [EquipamentoController::class, 'apiEquipamentosPorSetor'])->name('api.equipamentos.por-setor');
        Route::get('motores-ativos', [MotorController::class, 'apiMotoresAtivos'])->name('api.motores.ativos');
        Route::get('motores-por-local', [MotorController::class, 'apiMotoresPorLocal'])->name('api.motores.por-local');
        Route::get('motores-por-fabricante', [MotorController::class, 'apiMotoresPorFabricante'])->name('api.motores.por-fabricante');
        Route::get('motores-almoxarifado', [MotorController::class, 'apiMotoresAlmoxarifado'])->name('api.motores.almoxarifado');
        Route::get('setores-ativos', [SetorController::class, 'apiSetoresAtivos'])->name('api.setores.ativos');
        Route::get('setores/{setor}/equipamentos', [SetorController::class, 'apiEquipamentosPorSetor'])->name('api.setores.equipamentos');
    });
});

// Rotas de autenticação
require __DIR__.'/auth.php';

