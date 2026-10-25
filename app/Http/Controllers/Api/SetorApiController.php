<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setor;
use Illuminate\Http\Request;

class SetorApiController extends Controller
{
    /**
     * Listar todos os setores
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Setor::query();

        // Filtro por nome
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nome', 'like', "%{$search}%");
        }

        // Incluir contagem de equipamentos se solicitado
        if ($request->get('with_equipamentos_count', false)) {
            $query->withCount('equipamentos');
        }

        // Incluir contagem de relatórios se solicitado
        if ($request->get('with_relatorios_count', false)) {
            $query->withCount('relatorios');
        }

        $setores = $query->orderBy('nome')->get();

        return response()->json([
            'success' => true,
            'data' => ['setores' => $setores]
        ]);
    }

    /**
     * Exibir setor específico
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $setor = Setor::with(['equipamentos', 'relatorios' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->find($id);

        if (!$setor) {
            return response()->json([
                'success' => false,
                'message' => 'Setor não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['setor' => $setor]
        ]);
    }
}