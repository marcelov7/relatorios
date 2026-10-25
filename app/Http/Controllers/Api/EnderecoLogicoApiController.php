<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnderecoLogico;
use Illuminate\Http\Request;

class EnderecoLogicoApiController extends Controller
{
    /**
     * Listar e buscar endereços lógicos via API
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $area = $request->input('area');
        $perPage = (int) $request->input('per_page', 50);

        $query = EnderecoLogico::query();

        if (!empty($search)) {
            $query->buscar($search);
        }

        if (!empty($area)) {
            $query->where('area', $area);
        }

        $enderecos = $query->orderBy('tag')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'enderecos' => $enderecos->items(),
                'pagination' => [
                    'current_page' => $enderecos->currentPage(),
                    'last_page' => $enderecos->lastPage(),
                    'per_page' => $enderecos->perPage(),
                    'total' => $enderecos->total(),
                ]
            ]
        ]);
    }

    /**
     * Obter detalhes de um endereço lógico
     */
    public function show($id)
    {
        $endereco = EnderecoLogico::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => ['endereco' => $endereco]
        ]);
    }

    /**
     * Criar endereço lógico
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'endereco' => 'required|string|max:191',
            'descricao' => 'nullable|string|max:500',
            'area' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        $endereco = EnderecoLogico::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Endereço lógico criado com sucesso!',
            'data' => ['endereco' => $endereco]
        ], 201);
    }

    /**
     * Atualizar endereço lógico
     */
    public function update(Request $request, $id)
    {
        $endereco = EnderecoLogico::findOrFail($id);

        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'endereco' => 'required|string|max:191',
            'descricao' => 'nullable|string|max:500',
            'area' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        $endereco->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Endereço lógico atualizado com sucesso!',
            'data' => ['endereco' => $endereco]
        ]);
    }

    /**
     * Excluir endereço lógico
     */
    public function destroy($id)
    {
        $endereco = EnderecoLogico::findOrFail($id);
        $endereco->delete();

        return response()->json([
            'success' => true,
            'message' => 'Endereço lógico excluído com sucesso!'
        ]);
    }
}
