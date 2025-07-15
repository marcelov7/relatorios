<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Local::query();

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('setor', 'like', "%{$busca}%")
                  ->orWhere('responsavel', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('setor')) {
            $query->where('setor', 'like', "%{$request->setor}%");
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === 'true');
        }

        $locais = $query->with(['equipamentos' => function ($q) {
            $q->where('ativo', true);
        }])->paginate(12)->withQueryString();

        // Obter setores únicos para filtro
        $setores = Local::select('setor')
            ->whereNotNull('setor')
            ->distinct()
            ->pluck('setor')
            ->filter()
            ->sort()
            ->values();

        return Inertia::render('Locais/Index', [
            'locais' => $locais,
            'filtros' => $request->only(['busca', 'setor', 'ativo']),
            'setores' => $setores,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Locais/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'setor' => 'nullable|string|max:255',
            'responsavel' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'ativo' => 'boolean',
        ]);

        $local = Local::create($validated);

        return redirect()->route('locais.index')
            ->with('success', 'Local criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Local $local)
    {
        $local->load([
            'equipamentos:id,equipment_tag,nome,local_id',
            'relatorios:id,titulo,created_at,local_id'
        ]);

        return Inertia::render('Locais/Show', [
            'local' => $local,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Local $local)
    {
        return Inertia::render('Locais/Edit', [
            'local' => $local,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Local $local)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'setor' => 'nullable|string|max:255',
            'responsavel' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'ativo' => 'boolean',
        ]);

        // Filtrar valores null para evitar problemas
        $validated = array_filter($validated, function ($value) {
            return $value !== null;
        });

        $local->update($validated);

        return redirect()->route('locais.index')
            ->with('success', 'Local atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Local $local)
    {
        // Verificar se há equipamentos associados
        if ($local->equipamentos()->count() > 0) {
            return back()->withErrors([
                'delete' => 'Não é possível excluir este local pois há equipamentos associados a ele.'
            ]);
        }

        // Verificar se há relatórios associados
        if ($local->relatorios()->count() > 0) {
            return back()->withErrors([
                'delete' => 'Não é possível excluir este local pois há relatórios associados a ele.'
            ]);
        }

        $local->delete();

        return redirect()->route('locais.index')
            ->with('success', 'Local excluído com sucesso!');
    }

    /**
     * API: Obter locais ativos para select
     */
    public function apiLocaisAtivos()
    {
        $locais = Local::ativos()
            ->select('id', 'nome', 'setor')
            ->orderBy('nome')
            ->get()
            ->map(function ($local) {
                return [
                    'id' => $local->id,
                    'nome' => $local->nome_completo,
                ];
            });

        return response()->json($locais);
    }
}
