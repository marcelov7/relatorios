<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Relatorio;
use App\Models\Setor;
use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RelatorioApiController extends Controller
{
    /**
     * Listar relatórios com paginação e filtros
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Relatorio::with(['user', 'autor', 'setor', 'equipamentos', 'equipamentosTeste', 'imagens']);

        // Filtros
        if ($request->has('setor_id') && $request->setor_id) {
            $query->where('setor_id', $request->setor_id);
        }

        if ($request->has('status') && $request->status) {
            $status = $request->status;
            if ($status === 'aberto' || $status === 'Aberta') {
                $query->whereIn('status', ['Aberta', 'aberto', 'aberta', 'Aberto']);
            } elseif ($status === 'em_andamento' || $status === 'Em Andamento') {
                $query->whereIn('status', ['Em Andamento', 'em_andamento', 'Em andamento', 'Andamento']);
            } elseif ($status === 'resolvido' || $status === 'concluido' || $status === 'Concluída') {
                $query->whereIn('status', ['Concluída', 'concluida', 'Concluida', 'resolvido', 'Resolvido', 'concluido']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('activity', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('detalhes', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $relatorios = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'relatorios' => $relatorios->items(),
                'pagination' => [
                    'current_page' => $relatorios->currentPage(),
                    'last_page' => $relatorios->lastPage(),
                    'per_page' => $relatorios->perPage(),
                    'total' => $relatorios->total(),
                    'from' => $relatorios->firstItem(),
                    'to' => $relatorios->lastItem(),
                ]
            ]
        ]);
    }

    /**
     * Criar novo relatório
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'activity' => 'required|string|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
            'cargo_responsavel' => 'nullable|string|max:255',
            'date_created' => 'nullable|date',
            'time_created' => 'nullable',
            'setor_id' => 'nullable|integer',
            'equipment_ids' => 'nullable|array',
            'detalhes' => 'required|string',
            'status' => 'nullable|string',
            'progresso' => 'nullable|integer|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Determinar setor
        $sector = $user->setor;
        if ($request->filled('setor_id')) {
            $setorObj = Setor::find($request->setor_id);
            if ($setorObj) {
                $sector = $setorObj->nome;
            }
        }

        $relatorio = Relatorio::create([
            'titulo' => $request->titulo,
            'activity' => $request->activity,
            'nome_responsavel' => $request->nome_responsavel ?: $user->name,
            'cargo_responsavel' => $request->cargo_responsavel ?: $user->cargo,
            'date_created' => $request->date_created ?: now()->toDateString(),
            'time_created' => $request->time_created ?: now()->format('H:i'),
            'sector' => $sector,
            'setor_id' => $request->setor_id,
            'detalhes' => $request->detalhes,
            'status' => $request->status ?: 'Aberta',
            'progresso' => $request->has('progresso') ? (int)$request->progresso : 0,
            'user_id' => $user->id,
            'autor_id' => $user->id,
        ]);

        // Sincronizar equipamentos
        $equipmentIds = $request->equipment_ids ?? [];
        if (!empty($equipmentIds)) {
            $relatorio->equipamentos()->sync($equipmentIds);
            $relatorio->equipamentosTeste()->sync($equipmentIds);
        }

        // Processar upload de imagens via ImageUploadService
        if ($request->hasFile('images')) {
            $arquivos = $request->file('images');
            if (!is_array($arquivos)) {
                $arquivos = [$arquivos];
            }
            $imageService = new \App\Services\ImageUploadService();
            foreach ($arquivos as $index => $image) {
                try {
                    $imageData = $imageService->uploadImageForRelatorio($image, $relatorio->id, $index);
                    $relatorio->imagens()->create($imageData);
                } catch (\Exception $e) {
                    \Log::error('Erro ao fazer upload de imagem (API store)', [
                        'relatorio_id' => $relatorio->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $relatorio->load(['user', 'autor', 'setor', 'equipamentos', 'equipamentosTeste', 'imagens']);

        return response()->json([
            'success' => true,
            'message' => 'Relatório criado com sucesso',
            'data' => $relatorio
        ], 201);
    }

    /**
     * Exibir relatório específico
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $relatorio = Relatorio::with(['user', 'autor', 'setor', 'equipamentos', 'equipamentosTeste', 'imagens', 'atualizacoes.usuario'])->find($id);

        if (!$relatorio) {
            return response()->json([
                'success' => false,
                'message' => 'Relatório não encontrado'
            ], 404);
        }

        // Formatar URLs completas de imagens
        $relatorio->imagens_formatadas = $relatorio->imagens->map(function($img) {
            return [
                'id' => $img->id,
                'nome_original' => $img->nome_original,
                'url' => asset('storage/' . ltrim($img->caminho_original, '/')),
                'thumb_url' => $img->caminho_thumb ? asset('storage/' . ltrim($img->caminho_thumb, '/')) : asset('storage/' . ltrim($img->caminho_original, '/')),
                'medium_url' => $img->caminho_medium ? asset('storage/' . ltrim($img->caminho_medium, '/')) : asset('storage/' . ltrim($img->caminho_original, '/')),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $relatorio
        ]);
    }

    /**
     * Atualizar relatório
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $relatorio = Relatorio::find($id);

        if (!$relatorio) {
            return response()->json([
                'success' => false,
                'message' => 'Relatório não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'nullable|string|max:255',
            'activity' => 'nullable|string|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
            'cargo_responsavel' => 'nullable|string|max:255',
            'setor_id' => 'nullable|integer',
            'equipment_ids' => 'nullable|array',
            'detalhes' => 'nullable|string',
            'status' => 'nullable|string',
            'progresso' => 'nullable|integer|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $dados = array_filter($request->only([
            'titulo', 'activity', 'nome_responsavel', 'cargo_responsavel',
            'setor_id', 'detalhes', 'status', 'progresso'
        ]), fn($v) => !is_null($v));

        if ($request->has('progresso') && (int)$request->progresso >= 100) {
            $dados['status'] = 'Concluída';
        }

        $relatorio->update($dados);

        if ($request->has('equipment_ids')) {
            $relatorio->equipamentos()->sync($request->equipment_ids);
            $relatorio->equipamentosTeste()->sync($request->equipment_ids);
        }

        // Upload de novas imagens
        if ($request->hasFile('images')) {
            $arquivos = $request->file('images');
            if (!is_array($arquivos)) $arquivos = [$arquivos];
            $imageService = new \App\Services\ImageUploadService();
            $nextOrder = $relatorio->imagens()->max('ordem') + 1;
            foreach ($arquivos as $index => $image) {
                try {
                    $imageData = $imageService->uploadImageForRelatorio($image, $relatorio->id, $nextOrder + $index);
                    $relatorio->imagens()->create($imageData);
                } catch (\Exception $e) {
                    \Log::error('Erro ao adicionar imagem no update', ['error' => $e->getMessage()]);
                }
            }
        }

        $relatorio->load(['user', 'autor', 'setor', 'equipamentos', 'equipamentosTeste', 'imagens']);

        return response()->json([
            'success' => true,
            'message' => 'Relatório atualizado com sucesso',
            'data' => $relatorio
        ]);
    }

    /**
     * Deletar relatório
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $relatorio = Relatorio::find($id);

        if (!$relatorio) {
            return response()->json([
                'success' => false,
                'message' => 'Relatório não encontrado'
            ], 404);
        }

        // Verificar permissões
        $user = $request->user();
        if (!$user->isAdmin() && $relatorio->autor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Sem permissão para deletar este relatório'
            ], 403);
        }

        // Deletar imagens associadas
        $images = json_decode($relatorio->images, true) ?? [];
        foreach ($images as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $relatorio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Relatório deletado com sucesso'
        ]);
    }

    /**
     * Upload de imagens para relatório existente
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImages(Request $request, $id)
    {
        $relatorio = Relatorio::find($id);

        if (!$relatorio) {
            return response()->json([
                'success' => false,
                'message' => 'Relatório não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $currentImages = json_decode($relatorio->images, true) ?? [];
        $newImagePaths = [];

        foreach ($request->file('images') as $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('relatorios', $filename, 'public');
            $newImagePaths[] = $path;
        }

        $allImages = array_merge($currentImages, $newImagePaths);
        
        // Limitar a 10 imagens
        if (count($allImages) > 10) {
            $allImages = array_slice($allImages, 0, 10);
        }

        $relatorio->update(['images' => json_encode($allImages)]);

        return response()->json([
            'success' => true,
            'message' => 'Imagens adicionadas com sucesso',
            'data' => [
                'images' => $allImages,
                'new_images' => $newImagePaths
            ]
        ]);
    }

    /**
     * Deletar imagem específica do relatório
     * 
     * @param Request $request
     * @param int $id
     * @param int $imageIndex
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage(Request $request, $id, $imageIndex)
    {
        $relatorio = Relatorio::find($id);

        if (!$relatorio) {
            return response()->json([
                'success' => false,
                'message' => 'Relatório não encontrado'
            ], 404);
        }

        $images = json_decode($relatorio->images, true) ?? [];

        if (!isset($images[$imageIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Imagem não encontrada'
            ], 404);
        }

        // Deletar arquivo físico
        Storage::disk('public')->delete($images[$imageIndex]);

        // Remover da lista
        unset($images[$imageIndex]);
        $images = array_values($images); // Reindexar array

        $relatorio->update(['images' => json_encode($images)]);

        return response()->json([
            'success' => true,
            'message' => 'Imagem deletada com sucesso',
            'data' => ['images' => $images]
        ]);
    }

    /**
     * Estatísticas para dashboard
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardStats(Request $request)
    {
        $user = $request->user();
        $userId = $user ? $user->id : null;
        
        $total = Relatorio::count();
        $abertos = Relatorio::whereIn('status', ['Aberta', 'aberto', 'aberta', 'Aberto'])->count();
        $emAndamento = Relatorio::whereIn('status', ['Em Andamento', 'em_andamento', 'Em andamento', 'Andamento'])->count();
        $resolvidos = Relatorio::whereIn('status', ['Concluída', 'concluida', 'Concluida', 'resolvido', 'Resolvido', 'concluido'])->count();
        $meus = $userId ? Relatorio::where(function($q) use ($userId) {
            $q->where('autor_id', $userId)->orWhere('user_id', $userId);
        })->count() : 0;

        $stats = [
            'total_relatorios' => $total,
            'relatorios_abertos' => $abertos,
            'relatorios_em_andamento' => $emAndamento,
            'relatorios_resolvidos' => $resolvidos,
            'meus_relatorios' => $meus,
            'relatorios_criticos' => Relatorio::where('progresso', '<', 50)->whereNotIn('status', ['Concluída', 'concluida', 'resolvido'])->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Relatórios recentes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentReports(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $relatorios = Relatorio::with(['user', 'autor', 'setor', 'equipamentos', 'equipamentosTeste', 'imagens'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $relatorios
        ]);
    }

    /**
     * Meus relatórios
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myReports(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        
        $relatorios = Relatorio::with(['autor', 'setor', 'equipamentos'])
            ->where('autor_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'relatorios' => $relatorios->items(),
                'pagination' => [
                    'current_page' => $relatorios->currentPage(),
                    'last_page' => $relatorios->lastPage(),
                    'per_page' => $relatorios->perPage(),
                    'total' => $relatorios->total(),
                ]
            ]
        ]);
    }

    /**
     * Gerar PDF do relatório
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function generatePdf($id)
    {
        $relatorio = Relatorio::with(['autor', 'setor', 'equipamentos'])->find($id);

        if (!$relatorio) {
            return response()->json([
                'success' => false,
                'message' => 'Relatório não encontrado'
            ], 404);
        }

        try {
            // Aqui você pode implementar a geração de PDF
            // Por enquanto, retornamos uma URL fictícia
            $pdfUrl = url("/api/relatorios/{$id}/pdf/download");

            return response()->json([
                'success' => true,
                'message' => 'PDF gerado com sucesso',
                'data' => [
                    'pdf_url' => $pdfUrl,
                    'relatorio' => $relatorio
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}