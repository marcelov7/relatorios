<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipamento;
use Illuminate\Http\Request;

class EquipamentoApiController extends Controller
{
    /**
     * Listar todos os equipamentos
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Equipamento::with('setor');

        // Filtro por setor
        if ($request->has('setor_id') && $request->setor_id) {
            $query->where('setor_id', $request->setor_id);
        }

        // Filtro por nome
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            });
        }

        // Filtro por status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Incluir contagem de relatórios se solicitado
        if ($request->get('with_relatorios_count', false)) {
            $query->withCount('relatorios');
        }

        $equipamentos = $query->orderBy('nome')->get();

        return response()->json([
            'success' => true,
            'data' => ['equipamentos' => $equipamentos]
        ]);
    }

    /**
     * Listar equipamentos por setor
     * 
     * @param Request $request
     * @param int $setorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function bySetor(Request $request, $setorId)
    {
        $query = Equipamento::with('setor')->where('setor_id', $setorId);

        // Filtro por nome
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            });
        }

        // Filtro por status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Incluir contagem de relatórios se solicitado
        if ($request->get('with_relatorios_count', false)) {
            $query->withCount('relatorios');
        }

        $equipamentos = $query->orderBy('nome')->get();

        return response()->json([
            'success' => true,
            'data' => ['equipamentos' => $equipamentos]
        ]);
    }

    /**
     * Exibir equipamento específico
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $equipamento = Equipamento::with(['setor', 'relatorios' => function($query) {
            $query->with('autor')->orderBy('created_at', 'desc')->limit(10);
        }])->find($id);

        if (!$equipamento) {
            return response()->json([
                'success' => false,
                'message' => 'Equipamento não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['equipamento' => $equipamento]
        ]);
    }
}