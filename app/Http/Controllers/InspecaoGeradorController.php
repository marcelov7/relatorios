<?php

namespace App\Http\Controllers;

use App\Models\InspecaoGerador;
use App\Models\User;
use App\Models\Setor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InspecaoGeradorController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InspecaoGerador::with(['user', 'colaborador', 'setor']);

        // Filtros
        if ($request->filled('busca')) {
            $query->busca($request->busca);
        }

        if ($request->filled('situacao')) {
            $query->situacao($request->situacao);
        }

        if ($request->filled('setor_id')) {
            $query->setor($request->setor_id);
        }

        if ($request->filled('colaborador_id')) {
            $query->where('colaborador_id', $request->colaborador_id);
        }

        if ($request->filled('data_inicio')) {
            $query->porData($request->data_inicio, $request->data_fim ?? null);
        }

        // Paginação
        $perPage = $request->get('per_page', 12);
        $allowedPerPage = [12, 30, 60, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 12;
        }

        $inspecoes = $query->latest('data_inspecao')->paginate($perPage)->withQueryString();

        // Adicionar dados extras para cada inspeção
        $user = auth()->user();
        foreach ($inspecoes->items() as $inspecao) {
            $inspecao->podeEditar = $user->can('update', $inspecao);
            $inspecao->podeExcluir = $user->can('delete', $inspecao);
            $inspecao->ehAutor = $inspecao->user_id === $user->id;
        }

        // Dados para filtros
        $colaboradores = User::orderBy('name')->get(['id', 'name']);
        $setores = Setor::orderBy('nome')->get(['id', 'nome']);

        return Inertia::render('InspecaoGerador/Index', [
            'inspecoes' => $inspecoes,
            'colaboradores' => $colaboradores,
            'setores' => $setores,
            'filters' => $request->only(['busca', 'situacao', 'setor_id', 'colaborador_id', 'data_inicio', 'data_fim', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        $colaboradores = User::orderBy('name')->get(['id', 'name']);
        $setores = Setor::orderBy('nome')->get(['id', 'nome']);

        return Inertia::render('InspecaoGerador/Create', [
            'colaboradores' => $colaboradores,
            'setores' => $setores,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'defaultColaborador' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_inspecao' => 'required|date',
            'colaborador_id' => 'required|exists:users,id',
            'nivel_oleo_motor_parado' => 'required|in:MÍNIMO,NORMAL',
            'nivel_agua_parado' => 'required|in:MÍNIMO,NORMAL',
            'sync_gerador' => 'nullable|numeric|min:0|max:999.99',
            'sync_rede' => 'nullable|numeric|min:0|max:999.99',
            'temperatura_agua' => 'nullable|numeric|min:0|max:999.99',
            'pressao_oleo' => 'nullable|numeric|min:0|max:999.99',
            'frequencia' => 'nullable|numeric|min:0|max:999.99',
            'tensao_a' => 'nullable|numeric|min:0|max:9999.99',
            'tensao_b' => 'nullable|numeric|min:0|max:9999.99',
            'tensao_c' => 'nullable|numeric|min:0|max:9999.99',
            'rpm_1800' => 'nullable|integer|min:0|max:9999',
            'tensao_bateria_parado' => 'nullable|numeric|min:0|max:999.99',
            'tensao_alternador_marcha' => 'nullable|numeric|min:0|max:999.99',
            'nivel_combustivel' => 'nullable|integer|min:0|max:100',
            'iluminacao_sala_deficiente' => 'boolean',
            'limpeza_sala_realizada' => 'boolean',
            'observacoes' => 'nullable|string|max:1000',
            'setor_id' => 'nullable|exists:setores,id',
            'setor_text' => 'nullable|string|max:255',
        ]);

        $inspecao = InspecaoGerador::create([
            'data_inspecao' => $request->data_inspecao,
            'colaborador_id' => $request->colaborador_id,
            'nivel_oleo_motor_parado' => $request->nivel_oleo_motor_parado,
            'nivel_agua_parado' => $request->nivel_agua_parado,
            'sync_gerador' => $request->sync_gerador,
            'sync_rede' => $request->sync_rede,
            'temperatura_agua' => $request->temperatura_agua,
            'pressao_oleo' => $request->pressao_oleo,
            'frequencia' => $request->frequencia,
            'tensao_a' => $request->tensao_a,
            'tensao_b' => $request->tensao_b,
            'tensao_c' => $request->tensao_c,
            'rpm_1800' => $request->rpm_1800,
            'tensao_bateria_parado' => $request->tensao_bateria_parado,
            'tensao_alternador_marcha' => $request->tensao_alternador_marcha,
            'nivel_combustivel' => $request->nivel_combustivel,
            'iluminacao_sala_deficiente' => $request->iluminacao_sala_deficiente ?? false,
            'limpeza_sala_realizada' => $request->limpeza_sala_realizada ?? false,
            'observacoes' => $request->observacoes,
            'user_id' => auth()->id(),
            'setor_id' => $request->setor_id,
            'setor_text' => $request->setor_text,
        ]);

        return redirect()->route('inspecao-geradores.index')
            ->with('success', 'Inspeção de gerador criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(InspecaoGerador $inspecaoGerador)
    {
        $inspecaoGerador->load(['user', 'colaborador', 'setor']);

        // Debug temporário
        \Log::info('=== SHOW CONTROLLER DEBUG ===');
        \Log::info('ID: ' . $inspecaoGerador->id);
        \Log::info('Data: ' . $inspecaoGerador->data_inspecao);
        \Log::info('Situação: ' . $inspecaoGerador->situacao);
        \Log::info('Situação Class: ' . $inspecaoGerador->situacao_class);
        \Log::info('Situação Icon: ' . $inspecaoGerador->situacao_icon);
        \Log::info('Colaborador: ' . ($inspecaoGerador->colaborador ? $inspecaoGerador->colaborador->name : 'null'));
        \Log::info('Setor: ' . ($inspecaoGerador->setor ? $inspecaoGerador->setor->nome : 'null'));
        \Log::info('JSON: ' . $inspecaoGerador->toJson());
        \Log::info('=== END DEBUG ===');

        return Inertia::render('InspecaoGerador/Show', [
            'inspecao' => $inspecaoGerador,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InspecaoGerador $inspecaoGerador)
    {
        $inspecaoGerador->load(['user', 'colaborador', 'setor']);
        
        $colaboradores = User::orderBy('name')->get(['id', 'name']);
        $setores = Setor::orderBy('nome')->get(['id', 'nome']);

        return Inertia::render('InspecaoGerador/Edit', [
            'inspecao' => $inspecaoGerador,
            'colaboradores' => $colaboradores,
            'setores' => $setores,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InspecaoGerador $inspecaoGerador)
    {
        $request->validate([
            'data_inspecao' => 'required|date',
            'colaborador_id' => 'required|exists:users,id',
            'nivel_oleo_motor_parado' => 'required|in:MÍNIMO,NORMAL',
            'nivel_agua_parado' => 'required|in:MÍNIMO,NORMAL',
            'sync_gerador' => 'nullable|numeric|min:0|max:999.99',
            'sync_rede' => 'nullable|numeric|min:0|max:999.99',
            'temperatura_agua' => 'nullable|numeric|min:0|max:999.99',
            'pressao_oleo' => 'nullable|numeric|min:0|max:999.99',
            'frequencia' => 'nullable|numeric|min:0|max:999.99',
            'tensao_a' => 'nullable|numeric|min:0|max:9999.99',
            'tensao_b' => 'nullable|numeric|min:0|max:9999.99',
            'tensao_c' => 'nullable|numeric|min:0|max:9999.99',
            'rpm_1800' => 'nullable|integer|min:0|max:9999',
            'tensao_bateria_parado' => 'nullable|numeric|min:0|max:999.99',
            'tensao_alternador_marcha' => 'nullable|numeric|min:0|max:999.99',
            'nivel_combustivel' => 'nullable|integer|min:0|max:100',
            'iluminacao_sala_deficiente' => 'boolean',
            'limpeza_sala_realizada' => 'boolean',
            'observacoes' => 'nullable|string|max:1000',
            'setor_id' => 'nullable|exists:setores,id',
            'setor_text' => 'nullable|string|max:255',
        ]);

        $inspecaoGerador->update([
            'data_inspecao' => $request->data_inspecao,
            'colaborador_id' => $request->colaborador_id,
            'nivel_oleo_motor_parado' => $request->nivel_oleo_motor_parado,
            'nivel_agua_parado' => $request->nivel_agua_parado,
            'sync_gerador' => $request->sync_gerador,
            'sync_rede' => $request->sync_rede,
            'temperatura_agua' => $request->temperatura_agua,
            'pressao_oleo' => $request->pressao_oleo,
            'frequencia' => $request->frequencia,
            'tensao_a' => $request->tensao_a,
            'tensao_b' => $request->tensao_b,
            'tensao_c' => $request->tensao_c,
            'rpm_1800' => $request->rpm_1800,
            'tensao_bateria_parado' => $request->tensao_bateria_parado,
            'tensao_alternador_marcha' => $request->tensao_alternador_marcha,
            'nivel_combustivel' => $request->nivel_combustivel,
            'iluminacao_sala_deficiente' => $request->iluminacao_sala_deficiente ?? false,
            'limpeza_sala_realizada' => $request->limpeza_sala_realizada ?? false,
            'observacoes' => $request->observacoes,
            'setor_id' => $request->setor_id,
            'setor_text' => $request->setor_text,
        ]);

        return redirect()->route('inspecao-geradores.index')
            ->with('success', 'Inspeção de gerador atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InspecaoGerador $inspecaoGerador)
    {
        $inspecaoGerador->delete();

        return redirect()->route('inspecao-geradores.index')
            ->with('success', 'Inspeção de gerador excluída com sucesso!');
    }

    /**
     * API para buscar colaboradores ativos
     */
    public function apiColaboradoresAtivos()
    {
        $colaboradores = User::orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            });

        return response()->json($colaboradores);
    }

    /**
     * API para buscar setores ativos
     */
    public function apiSetoresAtivos()
    {
        $setores = Setor::orderBy('nome')
            ->get(['id', 'nome'])
            ->map(function ($setor) {
                return [
                    'id' => $setor->id,
                    'name' => $setor->nome,
                ];
            });

        return response()->json($setores);
    }
}
