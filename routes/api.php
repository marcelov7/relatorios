<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RelatorioApiController;
use App\Http\Controllers\Api\SetorApiController;
use App\Http\Controllers\Api\EquipamentoApiController;
use App\Http\Controllers\Api\UserApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui estão as rotas de API para o aplicativo mobile React Native.
| Todas as rotas são protegidas por autenticação Sanctum, exceto login/registro.
|
*/

// Rotas públicas (sem autenticação)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Rotas protegidas (requerem autenticação Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticação
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
    });

    // Relatórios
    Route::prefix('relatorios')->group(function () {
        Route::get('/', [RelatorioApiController::class, 'index']);
        Route::post('/', [RelatorioApiController::class, 'store']);
        Route::get('/{id}', [RelatorioApiController::class, 'show']);
        Route::put('/{id}', [RelatorioApiController::class, 'update']);
        Route::delete('/{id}', [RelatorioApiController::class, 'destroy']);
        Route::post('/{id}/images', [RelatorioApiController::class, 'uploadImages']);
        Route::delete('/{id}/images/{imageIndex}', [RelatorioApiController::class, 'deleteImage']);
        Route::get('/{id}/pdf', [RelatorioApiController::class, 'generatePdf']);
    });

    // Setores
    Route::prefix('setores')->group(function () {
        Route::get('/', [SetorApiController::class, 'index']);
        Route::get('/{id}', [SetorApiController::class, 'show']);
    });

    // Equipamentos
    Route::prefix('equipamentos')->group(function () {
        Route::get('/', [EquipamentoApiController::class, 'index']);
        Route::get('/setor/{setorId}', [EquipamentoApiController::class, 'bySetor']);
        Route::get('/{id}', [EquipamentoApiController::class, 'show']);
    });

    // Usuários (apenas para admins)
    Route::middleware('admin')->prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index']);
        Route::post('/', [UserApiController::class, 'store']);
        Route::get('/{id}', [UserApiController::class, 'show']);
        Route::put('/{id}', [UserApiController::class, 'update']);
        Route::delete('/{id}', [UserApiController::class, 'destroy']);
    });

    // Estatísticas e Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [RelatorioApiController::class, 'dashboardStats']);
        Route::get('/recent', [RelatorioApiController::class, 'recentReports']);
        Route::get('/my-reports', [RelatorioApiController::class, 'myReports']);
    });
});