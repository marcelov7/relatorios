<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use App\Models\Local;
use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Barryvdh\DomPDF\Facade\Pdf;
use Intervention\Image\Encoders\JpegEncoder;

class RelatorioController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Relatorio::with(['user', 'autor', 'local', 'equipamentos', 'atualizacoes']);

        // Filtros
        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        if ($request->filled('status')) {
            $query->status($request->status);
        }

        if ($request->filled('setor')) {
            $query->setor($request->setor);
        }

        if ($request->filled('local_id')) {
            $query->porLocal($request->local_id);
        }

        if ($request->filled('equipment_id')) {
            $query->whereHas('equipamentos', function($q) use ($request) {
                $q->where('equipamentos.id', $request->equipment_id);
            });
        }

        // Filtro por data de criação
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $relatorios = $query->latest()->paginate(12)->withQueryString();

        // Adicionar permissões e dados extras para cada relatório
        $user = auth()->user();
        foreach ($relatorios->items() as $relatorio) {
            $relatorio->podeEditar = $user->can('update', $relatorio);
            $relatorio->podeExcluir = $user->can('delete', $relatorio);
            $relatorio->ehAutor = $relatorio->autor_id === $user->id;
            // Calcular tempo restante para exclusão (se aplicável)
            if ($relatorio->ehAutor && !$user->isAdmin()) {
                $policy = new \App\Policies\RelatorioPolicy();
                $relatorio->tempoRestanteExclusao = $policy->getTimeRemainingForDeletion($relatorio);
            } else {
                $relatorio->tempoRestanteExclusao = null;
            }
            // Calcular total de fotos (galeria + históricos)
            $totalFotos = 0;
            if (is_array($relatorio->images)) {
                $totalFotos += count($relatorio->images);
            }
            if ($relatorio->relationLoaded('atualizacoes')) {
                foreach ($relatorio->atualizacoes as $atualizacao) {
                    if (is_array($atualizacao->imagens)) {
                        $totalFotos += count($atualizacao->imagens);
                    }
                }
            }
            $relatorio->totalFotos = $totalFotos;
            $relatorio->totalHistoricos = $relatorio->relationLoaded('atualizacoes') ? $relatorio->atualizacoes->count() : 0;
            // Adicionar lista de equipamentos de teste para o card
            $relatorio->loadMissing('equipamentosTeste');
            $relatorio->equipamentosTesteArr = $relatorio->equipamentosTeste->map(function($equip) {
                return [
                    'id' => $equip->id,
                    'tag' => $equip->tag,
                    'nome' => $equip->nome,
                    'setor' => $equip->setor,
                    'status' => $equip->status,
                ];
            });
            // Adicionar setor e tag do equipamento para exibição no card
            $relatorio->setor_nome = $relatorio->setor ? $relatorio->setor->nome : 'Sem setor';
            $relatorio->tag_equipamento = optional($relatorio->equipamentos->first())->equipment_tag ?? 'Sem tag';
        }

        // Dados para filtros
        $locais = Local::ativos()->select('id', 'nome', 'setor')->orderBy('nome')->get();
        $setores = Relatorio::select('sector')
            ->whereNotNull('sector')
            ->distinct()
            ->pluck('sector')
            ->filter()
            ->sort()
            ->values();

        return Inertia::render('Relatorios/Index', [
            'relatorios' => $relatorios,
            'filtros' => $request->only(['busca', 'status', 'setor', 'local_id', 'equipment_id', 'data_inicio', 'data_fim']),
            'locais' => $locais,
            'setores' => $setores,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locais = Local::ativos()->select('id', 'nome', 'setor')->orderBy('nome')->get();
        $user = auth()->user();

        return Inertia::render('Relatorios/Create', [
            'locais' => $locais,
            'userSetor' => $user->setor, // Setor do usuário para preencher automaticamente
            'userName' => $user->name, // Nome do usuário para preencher automaticamente
            'userCargo' => $user->cargo, // Cargo do usuário para preencher automaticamente
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'activity' => 'required|string|max:255',
            'nome_responsavel' => 'required|string|max:255',
            'cargo_responsavel' => 'nullable|string|max:255',
            'date_created' => 'required|date',
            // 'setor_id' => 'required|exists:setores,id', // removido
            // 'local_id' => 'nullable|exists:locais,id', // removido
            'equipment_ids' => 'nullable|array',
            'equipment_ids.*' => 'integer', // ou 'exists:equipamento_tests,id' se for tabela nova
            'detalhes' => 'required|string',
            'status' => 'required|in:Aberta,Em Andamento,Concluída,Cancelada',
            'progresso' => 'nullable|integer|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Definir automaticamente o usuário logado como responsável e autor
        $validated['user_id'] = auth()->id();
        $validated['autor_id'] = auth()->id();
        
        // Usar setor do usuário logado (se ainda fizer sentido)
        $validated['sector'] = auth()->user()->setor;

        // Processar upload de imagens
        $imagesPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('relatorios', $fileName, 'public');
                $thumbFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_thumb.jpg';
                $thumbPath = 'relatorios/thumbs/' . $thumbFileName;
                $manager = new ImageManager(new GdDriver());
                $img = $manager->read($image)->cover(600, 400);
                $encoded = $img->encode(new JpegEncoder(75));
                \Storage::disk('public')->put($thumbPath, $encoded->toString());
                $imagesPaths[] = [
                    'path' => $path,
                    'thumb' => $thumbPath,
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ];
                $this->syncImageToPublic($path);
                $this->syncImageToPublic($thumbPath);
            }
        }
        $validated['images'] = $imagesPaths;

        // Remover equipment_ids do validated para não tentar salvar na tabela relatorios
        $equipmentIds = $validated['equipment_ids'] ?? [];
        unset($validated['equipment_ids']);

        $relatorio = \App\Models\Relatorio::create($validated);

        // Sincronizar equipamentos de teste (relacionamento many-to-many)
        if (!empty($equipmentIds)) {
            $relatorio->equipamentosTeste()->sync($equipmentIds);
        }

        return redirect()->route('relatorios.index')
            ->with('success', 'Relatório criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Relatorio $relatorio)
    {
        $relatorio->load([
            'user', 
            'autor', 
            'local', 
            // 'equipamentos', // Remover equipamentos antigos
            'equipamentosTeste', // Novo relacionamento
            'setor', // Adicionado para exibir o setor corretamente
            'atualizacoes' => function($query) {
                $query->with('usuario:id,name,cargo')->orderBy('created_at', 'desc');
            }
        ]);
        // Fallback para imagens antigas: garantir array de objetos
        if (is_array($relatorio->images)) {
            $relatorio->images = array_map(function($img) {
                if (is_string($img)) {
                    return ['path' => $img];
                }
                return $img;
            }, $relatorio->images);
        }
        
        // Montar lista de equipamentos de teste com setor
        $equipamentosTeste = $relatorio->equipamentosTeste->map(function($equip) {
            return [
                'id' => $equip->id,
                'tag' => $equip->tag,
                'nome' => $equip->nome,
                'setor' => $equip->setor,
                'status' => $equip->status,
            ];
        });
        
        // Verificar permissões do usuário atual
        $user = auth()->user();
        $podeAtualizar = $relatorio->podeSerAtualizadoPor($user);
        $ehAutor = $relatorio->autor_id === $user->id;
        
        // Verificar permissões usando policies
        $podeEditar = $user->can('update', $relatorio);
        $podeExcluir = $user->can('delete', $relatorio);
        
        // Verificar se o relatório está concluído
        $relatorioConcluido = $relatorio->progresso >= 100;
        
        // Calcular tempo restante para exclusão (se aplicável)
        $tempoRestanteExclusao = null;
        if ($ehAutor && !$user->isAdmin()) {
            $policy = new \App\Policies\RelatorioPolicy();
            $tempoRestanteExclusao = $policy->getTimeRemainingForDeletion($relatorio);
        }
        
        return Inertia::render('Relatorios/Show', [
            'relatorio' => $relatorio,
            'equipamentosTeste' => $equipamentosTeste,
            'podeAtualizar' => $podeAtualizar,
            'ehAutor' => $ehAutor,
            'podeEditar' => $podeEditar,
            'podeExcluir' => $podeExcluir,
            'tempoRestanteExclusao' => $tempoRestanteExclusao,
            'relatorioConcluido' => $relatorioConcluido,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Relatorio $relatorio)
    {
        // Verificar permissão para editar
        $this->authorize('update', $relatorio);
        
        $relatorio->load(['equipamentosTeste']);
        
        $locais = Local::ativos()->select('id', 'nome', 'setor')->orderBy('nome')->get();
        $equipamentos = [];

        // Buscar setores ativos para o select
        $setores = \App\Models\Setor::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);

        // Montar lista de equipamentos de teste para o formulário
        $equipamentosTeste = $relatorio->equipamentosTeste->map(function($equip) {
            return [
                'id' => $equip->id,
                'tag' => $equip->tag,
                'nome' => $equip->nome,
                'setor' => $equip->setor,
                'status' => $equip->status,
            ];
        });

        return Inertia::render('Relatorios/Edit', [
            'relatorio' => $relatorio,
            'locais' => $locais,
            'equipamentos' => $equipamentos,
            'setores' => $setores,
            'equipamentosTeste' => $equipamentosTeste ?? [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Relatorio $relatorio)
    {
        // Verificar permissão para editar
        $this->authorize('update', $relatorio);
        
        // Log dos dados recebidos para debug
        \Log::info('Dados recebidos para update do relatório:', [
            'relatorio_id' => $relatorio->id,
            'titulo' => $request->titulo,
            'sector' => $request->sector,
            'activity' => $request->activity,
            'date_created' => $request->date_created,
            'date_created_type' => gettype($request->date_created),
            'local_id' => $request->local_id,
            'equipment_ids' => $request->equipment_ids,
            'status' => $request->status,
            'progresso' => $request->progresso,
            'detalhes' => $request->detalhes,
            'all_request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'activity' => 'required|string|max:255',
                'nome_responsavel' => 'required|string|max:255',
                'cargo_responsavel' => 'nullable|string|max:255',
                'date_created' => 'required|date',
                'local_id' => 'nullable|exists:locais,id',
                'equipment_ids' => 'nullable|array',
                'equipment_ids.*' => 'exists:equipamentos,id',
                'detalhes' => 'required|string',
                'status' => 'required|in:Aberta,Em Andamento,Concluída,Cancelada',
                'progresso' => 'nullable|integer|min:0|max:100',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max per image
                'keep_images' => 'nullable|array', // IDs das imagens existentes para manter
            ]);
            
            \Log::info('Validação passou com sucesso:', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação:', [
                'errors' => $e->errors(),
                'date_created_value' => $request->date_created,
                'date_created_raw' => $request->input('date_created')
            ]);
            throw $e;
        }

        // Processar imagens existentes que devem ser mantidas
        $existingImages = $relatorio->images ?? [];
        $keepImages = $request->input('keep_images', []);
        $imagesToKeep = [];

        foreach ($existingImages as $index => $image) {
            if (in_array($index, $keepImages)) {
                $imagesToKeep[] = $image;
            } else {
                // Excluir arquivo físico da imagem removida
                if (isset($image['path']) && Storage::disk('public')->exists($image['path'])) {
                    Storage::disk('public')->delete($image['path']);
                }
                // Excluir arquivo físico da miniatura removida
                if (isset($image['thumb']) && Storage::disk('public')->exists($image['thumb'])) {
                    Storage::disk('public')->delete($image['thumb']);
                }
            }
        }

        // Processar novas imagens
        $newImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Gerar nome único para o arquivo
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Salvar na pasta storage/app/public/relatorios
                $path = $image->storeAs('relatorios', $fileName, 'public');
                
                // Gerar thumbnail 600x400 JPG
                $thumbFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_thumb.jpg';
                $thumbPath = 'relatorios/thumbs/' . $thumbFileName;
                $manager = new ImageManager(new GdDriver());
                $img = $manager->read($image)->cover(600, 400);
                $encoded = $img->encode(new JpegEncoder(75));
                \Storage::disk('public')->put($thumbPath, $encoded->toString());

                $newImages[] = [
                    'path' => $path,
                    'thumb' => $thumbPath,
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ];
                $this->syncImageToPublic($path); // <-- cópia automática
                $this->syncImageToPublic($thumbPath); // <-- cópia automática para thumb
            }
        }

        // Combinar imagens mantidas com novas imagens
        $validated['images'] = array_merge($imagesToKeep, $newImages);
        
        // Usar setor do usuário logado
        $validated['sector'] = auth()->user()->setor;

        // Remover equipment_ids do validated para não tentar salvar na tabela relatorios
        $equipmentIds = $validated['equipment_ids'] ?? [];
        unset($validated['equipment_ids']);

        $relatorio->update($validated);

        // Sincronizar equipamentos (relacionamento many-to-many)
        $relatorio->equipamentos()->sync($equipmentIds);

        return redirect()->route('relatorios.index')
            ->with('success', 'Relatório atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Relatorio $relatorio)
    {
        // Verificar permissão para excluir
        $this->authorize('delete', $relatorio);
        
        $relatorio->delete();

        return redirect()->route('relatorios.index')
            ->with('success', 'Relatório excluído com sucesso!');
    }

    /**
     * Gera um PDF técnico personalizado de até 20 relatórios selecionados.
     */
    public function pdfLote(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) == 0) {
            return back()->with('error', 'Selecione ao menos um relatório.');
        }
        if (count($ids) > 20) {
            return back()->with('error', 'Selecione no máximo 20 relatórios por vez.');
        }
        $relatorios = Relatorio::with(['user', 'autor', 'local', 'equipamentosTeste'])
            ->whereIn('id', $ids)
            ->get();
        $data = [
            'relatorios' => $relatorios,
            'data_geracao' => now()->format('d/m/Y H:i'),
            'nome_sistema' => config('app.name', 'Sistema de Relatórios'),
            'logo_path' => public_path('storage/logo.png'), // ajuste se necessário
        ];
        $pdf = Pdf::loadView('relatorios.pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->download('relatorios-tecnicos.pdf');
    }

    // Função auxiliar para copiar imagem para public/storage/relatorios
    private function syncImageToPublic($path)
    {
        $source = storage_path('app/public/' . $path);
        $dest = public_path('storage/' . $path);
        $destDir = dirname($dest);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        if (file_exists($source) && !file_exists($dest)) {
            copy($source, $dest);
        }
    }
}
