<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use App\Models\Local;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipamento::with('setor');

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('equipment_tag', 'like', "%{$busca}%")
                  ->orWhere('nome', 'like', "%{$busca}%")
                  ->orWhere('marca', 'like', "%{$busca}%")
                  ->orWhere('modelo', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('setor_id')) {
            $query->where('setor_id', $request->setor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', 'like', "%{$request->tipo}%");
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === 'true');
        }

        $equipamentos = $query->orderBy('equipment_tag')->paginate(12)->withQueryString();

        // Dados para filtros
        $tipos = Equipamento::select('tipo')
            ->whereNotNull('tipo')
            ->distinct()
            ->pluck('tipo')
            ->filter()
            ->sort()
            ->values();

        return Inertia::render('Equipamentos/Index', [
            'equipamentos' => $equipamentos,
            'filtros' => $request->only(['busca', 'setor_id', 'status', 'tipo', 'ativo']),
            'tipos' => $tipos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locais = Local::ativos()->select('id', 'nome', 'setor')->orderBy('nome')->get();

        return Inertia::render('Equipamentos/Create', [
            'locais' => $locais,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_tag' => 'required|string|max:255|unique:equipamentos',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'setor_id' => 'required|exists:setores,id',
            'tipo' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'data_instalacao' => 'nullable|date',
            'data_ultima_manutencao' => 'nullable|date',
            'proxima_manutencao' => 'nullable|date',
            'status' => 'required|in:Operacional,Manutenção,Inativo,Defeito',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $equipamento = Equipamento::create($validated);

        return redirect()->route('equipamentos.index')
            ->with('success', 'Equipamento criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipamento $equipamento)
    {
        $equipamento->load(['local', 'relatorios' => function ($q) {
            $q->orderBy('relatorios.created_at', 'desc')->limit(10);
        }]);

        return Inertia::render('Equipamentos/Show', [
            'equipamento' => $equipamento,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipamento $equipamento)
    {
        $locais = Local::ativos()->select('id', 'nome', 'setor')->orderBy('nome')->get();

        return Inertia::render('Equipamentos/Edit', [
            'equipamento' => $equipamento,
            'locais' => $locais,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipamento $equipamento)
    {
        $validated = $request->validate([
            'equipment_tag' => 'required|string|max:255|unique:equipamentos,equipment_tag,' . $equipamento->id,
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'setor_id' => 'required|exists:setores,id',
            'tipo' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'data_instalacao' => 'nullable|date',
            'data_ultima_manutencao' => 'nullable|date',
            'proxima_manutencao' => 'nullable|date',
            'status' => 'required|in:Operacional,Manutenção,Inativo,Defeito',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $equipamento->update($validated);

        return redirect()->route('equipamentos.index')
            ->with('success', 'Equipamento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipamento $equipamento)
    {
        // Verificar se há relatórios associados
        if ($equipamento->relatorios()->count() > 0) {
            return back()->withErrors([
                'delete' => 'Não é possível excluir este equipamento pois há relatórios associados a ele.'
            ]);
        }

        $equipamento->delete();

        return redirect()->route('equipamentos.index')
            ->with('success', 'Equipamento excluído com sucesso!');
    }

    /**
     * API: Obter equipamentos por local
     */
    public function apiEquipamentosPorLocal(Request $request)
    {
        $localId = $request->get('local_id');
        
        if (!$localId) {
            return response()->json([]);
        }

        $equipamentos = Equipamento::where('local_id', $localId)
            ->ativos()
            ->operacionais()
            ->select('id', 'equipment_tag', 'nome')
            ->orderBy('equipment_tag')
            ->get()
            ->map(function ($equipamento) {
                return [
                    'id' => $equipamento->id,
                    'nome' => $equipamento->nome_completo,
                    'tag' => $equipamento->equipment_tag,
                ];
            });

        return response()->json($equipamentos);
    }

    /**
     * API: Obter todos equipamentos ativos para select
     */
    public function apiEquipamentosAtivos()
    {
        $equipamentos = Equipamento::with('local')
            ->ativos()
            ->select('id', 'equipment_tag', 'nome', 'local_id')
            ->orderBy('equipment_tag')
            ->get()
            ->map(function ($equipamento) {
                return [
                    'id' => $equipamento->id,
                    'nome' => $equipamento->nome_completo,
                    'tag' => $equipamento->equipment_tag,
                    'local' => $equipamento->local->nome ?? '',
                ];
            });

        return response()->json($equipamentos);
    }

    /**
     * API: Obter equipamentos por setor
     */
    public function apiEquipamentosPorSetor(Request $request)
    {
        $setorId = $request->get('setor_id');
        if (!$setorId) {
            return response()->json([]);
        }

        $equipamentos = Equipamento::where('setor_id', $setorId)
            ->ativos()
            ->operacionais()
            ->select('id', 'equipment_tag', 'nome', 'status', 'tipo', 'marca')
            ->orderBy('equipment_tag')
            ->get();

        return response()->json($equipamentos);
    }
}
