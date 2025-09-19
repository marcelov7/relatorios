<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relatorio;

class TesteController extends Controller
{
    public function testeRota()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Rota de teste funcionando!',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'relatorios_count' => Relatorio::count()
        ]);
    }
} 