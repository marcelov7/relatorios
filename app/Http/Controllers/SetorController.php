<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use App\Models\Equipamento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SetorController extends Controller
{
    // Removido o construtor com o middleware

    public function index(Request $request)
    {
        $query = Setor::query();
        if ($request->filled('busca')) {
            $query->where('nome', 'like', "%{$request->busca}%");
        }
        $setores = $query->withCount('equipamentos')->orderBy('nome')->paginate(12)->withQueryString();
        return Inertia::render('Setores/Index', [
            'setores' => $setores,
            'filtros' => $request->only(['busca'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Setores/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);
        Setor::create($validated);
        return redirect()->route('setores.index')->with('success', 'Setor criado com sucesso!');
    }

    public function show(Setor $setor)
    {
        // Não carregar todos os equipamentos aqui, apenas o total
        $totalEquipamentos = $setor->equipamentos()->count();
        
        return Inertia::render('Setores/Show', [
            'setor' => $setor,
            'totalEquipamentos' => $totalEquipamentos,
        ]);
    }

    public function edit(Setor $setor)
    {
        return Inertia::render('Setores/Edit', [
            'setor' => $setor,
        ]);
    }

    public function update(Request $request, Setor $setor)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);
        $setor->update($validated);
        return redirect()->route('setores.index')->with('success', 'Setor atualizado com sucesso!');
    }

    public function destroy(Setor $setor)
    {
        if ($setor->equipamentos()->count() > 0) {
            return back()->withErrors(['delete' => 'Não é possível excluir este setor pois há equipamentos vinculados a ele.']);
        }
        $setor->delete();
        return redirect()->route('setores.index')->with('success', 'Setor excluído com sucesso!');
    }

    /**
     * API: Obter setores ativos para select
     */
    public function apiSetoresAtivos()
    {
        $setores = Setor::where('ativo', true)
            ->select('id', 'nome')
            ->orderBy('nome')
            ->get();
        return response()->json($setores);
    }

    /**
     * API: Obter equipamentos paginados de um setor
     */
    public function apiEquipamentosPorSetor(Request $request, Setor $setor)
    {
        $query = $setor->equipamentos();
        
        // Filtro de busca
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('equipment_tag', 'like', "%{$busca}%")
                  ->orWhere('nome', 'like', "%{$busca}%")
                  ->orWhere('marca', 'like', "%{$busca}%")
                  ->orWhere('modelo', 'like', "%{$busca}%");
            });
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $equipamentos = $query->orderBy('equipment_tag')
            ->paginate(12)
            ->withQueryString();
            
        return response()->json($equipamentos);
    }
} 