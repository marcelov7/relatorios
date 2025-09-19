<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use App\Models\Local;
use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use App\Models\User; // Adicionado para buscar autores
use Spatie\LaravelPdf\Facades\Pdf;

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

        // Filtro por setor (dos equipamentos de teste)
        if ($request->filled('setor_id')) {
            $query->whereHas('equipamentosTeste', function($q) use ($request) {
                $q->where('setor', $request->setor_id);
            });
        }

        // Filtro por autor
        if ($request->filled('autor_id')) {
            $query->where('autor_id', $request->autor_id);
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

        // Paginação com limite configurável
        $perPage = $request->get('per_page', 12);
        $allowedPerPage = [12, 30, 60, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 12;
        }

        $relatorios = $query->latest()->paginate($perPage)->withQueryString();

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

            // Adicionar informações extras para exibição
            $relatorio->setor_nome = $relatorio->setor ? $relatorio->setor->nome : 'Sem setor';
            $relatorio->tag_equipamento = optional($relatorio->equipamentos->first())->equipment_tag ?? 'Sem tag';
            
            // Calcular total de fotos
            $totalFotos = 0;
            if (is_array($relatorio->images)) {
                $totalFotos += count($relatorio->images);
            }
            
            // Calcular total de fotos das atualizações
            $relatorio->load('atualizacoes');
            foreach ($relatorio->atualizacoes as $atualizacao) {
                if (is_array($atualizacao->imagens)) {
                    $totalFotos += count($atualizacao->imagens);
                }
            }
            
            $relatorio->totalFotos = $totalFotos;
            $relatorio->totalHistoricos = $relatorio->atualizacoes->count();
            
            // Preparar equipamentos de teste para o frontend
            $relatorio->equipamentosTesteArr = $relatorio->equipamentosTeste->map(function($equip) {
                return [
                    'id' => $equip->id,
                    'tag' => $equip->tag,
                    'nome' => $equip->nome,
                    'setor' => $equip->setor,
                    'status' => $equip->status,
                ];
            });
        }

        // Dados para filtros
        $locais = Local::ativos()->select('id', 'nome', 'setor')->orderBy('nome')->get();
        
        // Buscar setores únicos dos equipamentos de teste (agrupando setores com mesmo nome)
        $setores = \App\Models\EquipamentoTest::select('setor')
            ->whereNotNull('setor')
            ->where('setor', '!=', '')
            ->distinct()
            ->orderBy('setor')
            ->pluck('setor')
            ->filter()
            ->values()
            ->map(function($setor) {
                return ['id' => $setor, 'nome' => $setor];
            });

        // Buscar autores (usuários que criaram relatórios)
        $autores = User::whereHas('relatorios', function($q) {
                $q->where('autor_id', '!=', null);
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Relatorios/Index', [
            'relatorios' => $relatorios,
            'filtros' => $request->only(['busca', 'status', 'setor_id', 'autor_id', 'data_inicio', 'data_fim', 'per_page']),
            'locais' => $locais,
            'setores' => $setores,
            'autores' => $autores,
            'perPageOptions' => $allowedPerPage,
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
            'time_created' => 'nullable|date_format:H:i',
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'integer|exists:equipamento_tests,id',
            'detalhes' => 'required|string',
            'status' => 'required|in:Aberta,Em Andamento,Concluída,Cancelada',
            'progresso' => 'nullable|integer|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Definir automaticamente o usuário logado como responsável e autor
        $validated['user_id'] = auth()->id();
        $validated['autor_id'] = auth()->id();
        $validated['sector'] = auth()->user()->setor;

        // Remover equipment_ids do validated para não tentar salvar na tabela relatorios
        $equipmentIds = $validated['equipment_ids'] ?? [];
        unset($validated['equipment_ids']);

        // Criar o relatório primeiro
        $relatorio = \App\Models\Relatorio::create($validated);

        // Sincronizar equipamentos de teste
        if (!empty($equipmentIds)) {
            $relatorio->equipamentosTeste()->sync($equipmentIds);
        }

        // Processar upload de imagens usando o service
        if ($request->hasFile('images')) {
            $arquivos = $request->file('images');
            \Log::info('RelatorioController@store - Quantidade de imagens recebidas:', ['count' => is_array($arquivos) ? count($arquivos) : 1]);
            $imageService = new \App\Services\ImageUploadService();
            
            foreach ($arquivos as $index => $image) {
                try {
                    $imageData = $imageService->uploadImageForRelatorio($image, $relatorio->id, $index);
                    
                    // Salvar no banco
                    $relatorio->imagens()->create($imageData);
                    
                } catch (\Exception $e) {
                    \Log::error('Erro ao fazer upload de imagem', [
                        'relatorio_id' => $relatorio->id,
                        'image_index' => $index,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Continuar com as outras imagens mesmo se uma falhar
                    continue;
                }
            }
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
            'equipamentosTeste', // Novo relacionamento
            'setor', // Adicionado para exibir o setor corretamente
            'imagens', // Novo relacionamento de imagens
            'atualizacoes' => function($query) {
                $query->with('usuario:id,name,cargo')->orderBy('created_at', 'desc');
            }
        ]);
        
        // Converter imagens para formato compatível com frontend
        $relatorio->images = $relatorio->imagens->map(function($imagem) {
            return [
                'path' => $imagem->caminho_original,
                'thumb' => $imagem->caminho_thumb,
                'medium' => $imagem->caminho_medium,
                'original_name' => $imagem->nome_original,
                'size' => $imagem->tamanho,
                'mime_type' => $imagem->mime_type,
                'id' => $imagem->id,
                'ordem' => $imagem->ordem,
                // Adicionar URLs completas
                'url' => url("/test-image/{$imagem->caminho_original}"),
                'thumb_url' => $imagem->caminho_thumb ? url("/test-image/{$imagem->caminho_thumb}") : url("/test-image/{$imagem->caminho_original}"),
                'medium_url' => $imagem->caminho_medium ? url("/test-image/{$imagem->caminho_medium}") : url("/test-image/{$imagem->caminho_original}"),
            ];
        })->toArray();
        
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
        
        // Buscar relatórios anterior e próximo para navegação (baseado na ordem de criação)
        $relatorioAnterior = null;
        $relatorioProximo = null;
        $relatorioAtual = 0;
        $totalRelatorios = 0;
        
        try {
            $relatorioAnterior = Relatorio::where('created_at', '<', $relatorio->created_at)
                ->orderBy('created_at', 'desc')
                ->first(['id', 'titulo', 'created_at']);
                
            $relatorioProximo = Relatorio::where('created_at', '>', $relatorio->created_at)
                ->orderBy('created_at', 'asc')
                ->first(['id', 'titulo', 'created_at']);
                
            // Calcular posição atual e total
            $relatorioAtual = Relatorio::where('created_at', '<=', $relatorio->created_at)->count();
            $totalRelatorios = Relatorio::count();
        } catch (\Exception $e) {
            // Se houver erro, usar valores padrão
            $relatorioAnterior = null;
            $relatorioProximo = null;
            $relatorioAtual = 1;
            $totalRelatorios = 1;
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
            'relatorioAnterior' => $relatorioAnterior,
            'relatorioProximo' => $relatorioProximo,
            'relatorioAtual' => $relatorioAtual,
            'totalRelatorios' => $totalRelatorios,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Relatorio $relatorio)
    {
        $this->authorize('update', $relatorio);
        
        $relatorio->load(['imagens', 'equipamentosTeste']);

        // Mapear imagens para o formato esperado pelo frontend
        $relatorio->images = $relatorio->imagens->map(function($imagem) {
            return [
                'id' => $imagem->id,
                'path' => $imagem->caminho_original,
                'thumb' => $imagem->caminho_thumb,
                'medium' => $imagem->caminho_medium,
                'original_name' => $imagem->nome_original,
                'size' => $imagem->tamanho,
                'mime_type' => $imagem->mime_type,
                'ordem' => $imagem->ordem,
            ];
        })->toArray();

        $locais = \App\Models\Local::ativos()->select('id', 'nome', 'setor')->orderBy('nome')->get();
        $equipamentos = []; // Carregue se necessário
        $setores = \App\Models\Setor::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
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

        // Validação
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'activity' => 'required|string|max:255',
            'nome_responsavel' => 'required|string|max:255',
            'cargo_responsavel' => 'nullable|string|max:255',
            'date_created' => 'required|date',
            'time_created' => 'nullable|date_format:H:i',
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipamento_tests,id',
            'detalhes' => 'required|string',
            'status' => 'required|in:Aberta,Em Andamento,Concluída,Cancelada',
            'progresso' => 'nullable|integer|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'imagens_remover' => 'nullable|array', // IDs das imagens a remover
            'imagens_remover.*' => 'integer|exists:relatorio_imagens,id',
        ]);

        // Atualizar campos básicos
        $relatorio->update([
            'titulo' => $validated['titulo'],
            'activity' => $validated['activity'],
            'nome_responsavel' => $validated['nome_responsavel'],
            'cargo_responsavel' => $validated['cargo_responsavel'] ?? null,
            'date_created' => $validated['date_created'],
            'time_created' => $validated['time_created'] ?? null,
            'detalhes' => $validated['detalhes'],
            'status' => $validated['status'],
            'progresso' => $validated['progresso'] ?? 0,
            'sector' => auth()->user()->setor,
        ]);

        // Sincronizar equipamentos de teste
        $equipmentIds = $validated['equipment_ids'] ?? [];
        $relatorio->equipamentosTeste()->sync($equipmentIds);

        // Remover imagens marcadas para exclusão
        if ($request->filled('imagens_remover')) {
            foreach ($request->imagens_remover as $imagemId) {
                $imagem = $relatorio->imagens()->find($imagemId);
                if ($imagem) {
                    $imagem->deleteWithFiles();
                }
            }
        }

        // Adicionar novas imagens
        if ($request->hasFile('images')) {
            $imageService = new \App\Services\ImageUploadService();
            $nextOrder = $relatorio->imagens()->max('ordem') + 1;
            foreach ($request->file('images') as $index => $image) {
                try {
                    $imageData = $imageService->uploadImageForRelatorio($image, $relatorio->id, $nextOrder + $index);
                    $imageData['relatorio_id'] = $relatorio->id;
                    $relatorio->imagens()->create($imageData);
                } catch (\Exception $e) {
                    \Log::error('Erro ao fazer upload de imagem (update)', [
                        'relatorio_id' => $relatorio->id,
                        'image_index' => $index,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
        }

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
     * Gera PDF personalizado de relatórios selecionados usando Spatie Laravel PDF.
     * Recebe os IDs via GET (?ids=1,2,3)
     */
    public function generatePdfSpatie(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        if (empty($ids)) {
            return back()->with('error', 'Selecione ao menos um relatório.');
        }
        $relatorios = \App\Models\Relatorio::with(['user', 'autor', 'local', 'equipamentosTeste', 'atualizacoes.usuario'])
            ->whereIn('id', $ids)
            ->get();
        if ($relatorios->isEmpty()) {
            return back()->with('error', 'Nenhum relatório encontrado.');
        }
        $data = [
            'relatorios' => $relatorios,
            'data_geracao' => now()->format('d/m/Y H:i'),
            'nome_sistema' => config('app.name', 'Sistema de Relatórios'),
        ];
        return Pdf::view('relatorios.pdf-spatie', $data)
            ->format('a4')
            ->name('relatorios-personalizados.pdf')
            ->download();
    }

    /**
     * Gera PDF personalizado de relatórios selecionados usando Browsershot (ambiente local Windows).
     * Recebe os IDs via GET (?ids=1,2,3)
     */
    public function generatePdfBrowsershot(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        if (empty($ids)) {
            return back()->with('error', 'Selecione ao menos um relatório.');
        }
        $relatorios = \App\Models\Relatorio::with(['user', 'autor', 'local', 'equipamentosTeste', 'atualizacoes.usuario'])
            ->whereIn('id', $ids)
            ->get();
        if ($relatorios->isEmpty()) {
            return back()->with('error', 'Nenhum relatório encontrado.');
        }
        $data = [
            'relatorios' => $relatorios,
            'data_geracao' => now()->format('d/m/Y H:i'),
            'nome_sistema' => config('app.name', 'Sistema de Relatórios'),
        ];

        $html = view('relatorios.pdf-browsershot', $data)->render();
        $pdfPath = storage_path('app/public/relatorios-personalizados-' . uniqid() . '.pdf');

        // Detecta ambiente e define o caminho do Chrome/Chromium
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Ambiente Windows
            $chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
            if (!file_exists($chromePath)) {
                $chromePath = 'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe';
            }
        } else {
            // Ambiente Linux (produção)
            $chromePath = '/snap/bin/chromium';
            if (!file_exists($chromePath)) {
                $chromePath = '/usr/bin/chromium-browser';
                if (!file_exists($chromePath)) {
                    $chromePath = '/usr/bin/chromium';
                }
            }
        }

        \Spatie\Browsershot\Browsershot::html($html)
            ->setChromePath($chromePath)
            ->addChromiumArguments([
                '--no-sandbox',
                '--disable-gpu',
                '--disable-dev-shm-usage',
                '--disable-setuid-sandbox',
                '--user-data-dir=/tmp/chromium-data',
                '--data-path=/tmp/chromium-data',
                '--disable-background-timer-throttling',
                '--disable-backgrounding-occluded-windows',
                '--disable-renderer-backgrounding',
                '--disable-features=TranslateUI',
                '--disable-ipc-flooding-protection'
            ])
            ->format('A4')
            ->showBackground()
            ->timeout(120)
            ->save($pdfPath);

        return response()->download($pdfPath, 'relatorios-personalizados.pdf')->deleteFileAfterSend(true);
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

    /**
     * Gera PDF de um relatório individual usando Browsershot
     */
    public function pdfBrowsershot($id)
    {
        try {
            $relatorio = Relatorio::with(['user', 'autor', 'local', 'equipamentosTeste', 'atualizacoes.usuario', 'imagens'])
                ->findOrFail($id);
            
            // Detectar ambiente e configurar caminho do Chrome/Chromium
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            $isLocal = app()->environment('local');
            
            if ($isWindows || $isLocal) {
                // Ambiente Windows/Local
                $chromePaths = [
                    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
                    'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe'
                ];
            } else {
                // Ambiente Linux/Produção - CloudPanel
                $chromePaths = [
                    '/snap/bin/chromium',
                    '/usr/bin/chromium-browser',
                    '/usr/bin/chromium',
                    '/usr/bin/google-chrome-stable',
                    '/usr/bin/google-chrome'
                ];
            }
            
            $chromePath = null;
            foreach ($chromePaths as $path) {
                if (file_exists($path)) {
                    $chromePath = $path;
                    break;
                }
            }
            
            if (!$chromePath) {
                Log::error('Chrome/Chromium não encontrado. Tentativa de usar Spatie Laravel PDF como fallback');
                return $this->pdfFallback($id);
            }
            
            Log::info("Gerando PDF com Browsershot", [
                'chrome_path' => $chromePath,
                'ambiente' => app()->environment(),
                'relatorio_id' => $id
            ]);
            
            // Preparar diretório temporário para produção
            $tempDir = '/tmp/browsershot-' . uniqid();
            if (!$isWindows && !$isLocal) {
                @mkdir($tempDir, 0755, true);
                @chmod($tempDir, 0755);
            }
            
            $html = view('relatorios.pdf-browsershot', compact('relatorio'))->render();
            
            $browsershot = \Spatie\Browsershot\Browsershot::html($html)
                ->setChromePath($chromePath)
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->showBackground()
                ->timeout(120);
            
            // Configurar argumentos específicos para ambiente
            if ($isWindows || $isLocal) {
                // Argumentos para Windows/Local
                $browsershot->addChromiumArguments([
                    '--no-sandbox',
                    '--disable-gpu',
                    '--disable-dev-shm-usage'
                ]);
            } else {
                // Argumentos robustos para produção Linux/CloudPanel
                $browsershot->addChromiumArguments([
                    '--no-sandbox',
                    '--disable-gpu',
                    '--disable-dev-shm-usage',
                    '--disable-setuid-sandbox',
                    '--disable-web-security',
                    '--disable-features=VizDisplayCompositor',
                    '--run-all-compositor-stages-before-draw',
                    '--disable-background-timer-throttling',
                    '--disable-backgrounding-occluded-windows',
                    '--disable-renderer-backgrounding',
                    '--disable-features=TranslateUI',
                    '--disable-ipc-flooding-protection',
                    '--disable-extensions',
                    '--disable-plugins',
                    '--disable-sync',
                    '--disable-default-apps',
                    '--no-first-run',
                    '--disable-background-networking',
                    '--user-data-dir=' . $tempDir,
                    '--data-path=' . $tempDir
                ]);
            }
            
            $pdf = $browsershot->pdf();
            
            // Limpar diretório temporário
            if (!$isWindows && !$isLocal && is_dir($tempDir)) {
                exec("rm -rf " . escapeshellarg($tempDir));
            }
                
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="relatorio-' . $relatorio->id . '.pdf"');
                
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF com Browsershot', [
                'error' => $e->getMessage(),
                'relatorio_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback para Spatie Laravel PDF
            Log::info('Tentando fallback com Spatie Laravel PDF');
            return $this->pdfFallback($id);
        }
    }

    /**
     * Fallback para geração de PDF usando Spatie Laravel PDF
     */
    private function pdfFallback($id)
    {
        try {
            $relatorio = Relatorio::with(['user', 'autor', 'local', 'equipamentosTeste', 'atualizacoes.usuario', 'imagens'])
                ->findOrFail($id);
            
            Log::info('Gerando PDF com fallback (Spatie Laravel PDF)', ['relatorio_id' => $id]);
            
            $data = [
                'relatorio' => $relatorio,
                'data_geracao' => now()->format('d/m/Y H:i'),
                'nome_sistema' => config('app.name', 'Sistema de Relatórios'),
            ];
            
            return Pdf::view('relatorios.pdf-spatie', $data)
                ->format('a4')
                ->name('relatorio-' . $relatorio->id . '.pdf')
                ->download();
                
        } catch (\Exception $e) {
            Log::error('Erro no fallback PDF', [
                'error' => $e->getMessage(),
                'relatorio_id' => $id
            ]);
            
            return response()->json([
                'error' => 'Erro ao gerar PDF. Tente novamente ou contate o suporte.',
                'details' => app()->environment('local') ? $e->getMessage() : 'Erro interno'
            ], 500);
        }
    }
}
