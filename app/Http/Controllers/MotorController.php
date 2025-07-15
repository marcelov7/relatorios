<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MotorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Motor::with('local');

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('tag', 'like', "%{$busca}%")
                  ->orWhere('equipamento', 'like', "%{$busca}%")
                  ->orWhere('fabricante', 'like', "%{$busca}%")
                  ->orWhere('tipo_equipamento', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('local_id')) {
            $query->where('local_id', $request->local_id);
        }

        if ($request->filled('armazenamento')) {
            $query->where('armazenamento', $request->armazenamento);
        }

        if ($request->filled('reserva_almox')) {
            $query->where('reserva_almox', $request->reserva_almox === 'true');
        }

        if ($request->filled('fabricante')) {
            $query->porFabricante($request->fabricante);
        }

        if ($request->filled('tipo_equipamento')) {
            $query->porTipoEquipamento($request->tipo_equipamento);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === 'true');
        }

        $motores = $query->orderBy('tag')->paginate(12)->withQueryString();

        // Dados para filtros
        $fabricantes = Motor::select('fabricante')
            ->whereNotNull('fabricante')
            ->distinct()
            ->pluck('fabricante')
            ->filter()
            ->sort()
            ->values();

        $tiposEquipamento = Motor::select('tipo_equipamento')
            ->whereNotNull('tipo_equipamento')
            ->distinct()
            ->pluck('tipo_equipamento')
            ->filter()
            ->sort()
            ->values();

        $locais = Local::ativos()->select('id', 'nome')->orderBy('nome')->get();

        return Inertia::render('Motores/Index', [
            'motores' => $motores,
            'filtros' => $request->only(['busca', 'local_id', 'armazenamento', 'reserva_almox', 'fabricante', 'tipo_equipamento', 'ativo']),
            'fabricantes' => $fabricantes,
            'tiposEquipamento' => $tiposEquipamento,
            'locais' => $locais,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locais = Local::ativos()->select('id', 'nome')->orderBy('nome')->get();

        return Inertia::render('Motores/Create', [
            'locais' => $locais,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:255|unique:motores',
            'equipamento' => 'required|string|max:255',
            'carcaca' => 'nullable|string|max:255',
            'potencia_kw' => 'nullable|numeric|min:0|max:999999.99',
            'potencia_cv' => 'nullable|numeric|min:0|max:999999.99',
            'rotacao' => 'nullable|integer|min:0',
            'corrente_placa' => 'nullable|numeric|min:0|max:999999.99',
            'corrente_configurada' => 'nullable|numeric|min:0|max:999999.99',
            'tipo_equipamento' => 'nullable|string|max:255',
            'fabricante' => 'nullable|string|max:255',
            'reserva_almox' => 'boolean',
            'local_id' => 'required|exists:locais,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'armazenamento' => 'required|in:Instalado,Almoxarifado,Manutenção,Descartado',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        // Upload da foto se fornecida
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('motores', 'public');
            $validated['foto'] = $fotoPath;
        }

        $motor = Motor::create($validated);

        return redirect()->route('motores.index')
            ->with('success', 'Motor criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Motor $motor)
    {
        $motor->load('local');

        return Inertia::render('Motores/Show', [
            'motor' => $motor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Motor $motor)
    {
        $locais = Local::ativos()->select('id', 'nome')->orderBy('nome')->get();

        return Inertia::render('Motores/Edit', [
            'motor' => $motor,
            'locais' => $locais,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Motor $motor)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:255|unique:motores,tag,' . $motor->id,
            'equipamento' => 'required|string|max:255',
            'carcaca' => 'nullable|string|max:255',
            'potencia_kw' => 'nullable|numeric|min:0|max:999999.99',
            'potencia_cv' => 'nullable|numeric|min:0|max:999999.99',
            'rotacao' => 'nullable|integer|min:0',
            'corrente_placa' => 'nullable|numeric|min:0|max:999999.99',
            'corrente_configurada' => 'nullable|numeric|min:0|max:999999.99',
            'tipo_equipamento' => 'nullable|string|max:255',
            'fabricante' => 'nullable|string|max:255',
            'reserva_almox' => 'boolean',
            'local_id' => 'required|exists:locais,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'armazenamento' => 'required|in:Instalado,Almoxarifado,Manutenção,Descartado',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        // Upload da nova foto se fornecida
        if ($request->hasFile('foto')) {
            // Remover foto antiga se existir
            if ($motor->foto) {
                Storage::disk('public')->delete($motor->foto);
            }
            
            $fotoPath = $request->file('foto')->store('motores', 'public');
            $validated['foto'] = $fotoPath;
        }

        $motor->update($validated);

        return redirect()->route('motores.index')
            ->with('success', 'Motor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Motor $motor)
    {
        // Remover foto se existir
        if ($motor->foto) {
            Storage::disk('public')->delete($motor->foto);
        }

        $motor->delete();

        return redirect()->route('motores.index')
            ->with('success', 'Motor excluído com sucesso!');
    }

    /**
     * API: Obter motores por local
     */
    public function apiMotoresPorLocal(Request $request)
    {
        $localId = $request->get('local_id');
        
        if (!$localId) {
            return response()->json([]);
        }

        $motores = Motor::where('local_id', $localId)
            ->ativos()
            ->select('id', 'tag', 'equipamento', 'armazenamento')
            ->orderBy('tag')
            ->get()
            ->map(function ($motor) {
                return [
                    'id' => $motor->id,
                    'nome' => $motor->nome_completo,
                    'tag' => $motor->tag,
                    'armazenamento' => $motor->armazenamento,
                ];
            });

        return response()->json($motores);
    }

    /**
     * API: Obter todos motores ativos para select
     */
    public function apiMotoresAtivos()
    {
        $motores = Motor::with('local')
            ->ativos()
            ->select('id', 'tag', 'equipamento', 'local_id', 'armazenamento')
            ->orderBy('tag')
            ->get()
            ->map(function ($motor) {
                return [
                    'id' => $motor->id,
                    'nome' => $motor->nome_completo,
                    'tag' => $motor->tag,
                    'local' => $motor->local->nome ?? '',
                    'armazenamento' => $motor->armazenamento,
                ];
            });

        return response()->json($motores);
    }

    /**
     * API: Obter motores por fabricante
     */
    public function apiMotoresPorFabricante(Request $request)
    {
        $fabricante = $request->get('fabricante');
        if (!$fabricante) {
            return response()->json([]);
        }

        $motores = Motor::porFabricante($fabricante)
            ->ativos()
            ->select('id', 'tag', 'equipamento', 'fabricante', 'armazenamento')
            ->orderBy('tag')
            ->get();

        return response()->json($motores);
    }

    /**
     * API: Obter motores em almoxarifado
     */
    public function apiMotoresAlmoxarifado()
    {
        $motores = Motor::almoxarifado()
            ->ativos()
            ->with('local')
            ->select('id', 'tag', 'equipamento', 'local_id', 'fabricante', 'potencia_kw', 'potencia_cv')
            ->orderBy('tag')
            ->get()
            ->map(function ($motor) {
                return [
                    'id' => $motor->id,
                    'nome' => $motor->nome_completo,
                    'tag' => $motor->tag,
                    'local' => $motor->local->nome ?? '',
                    'fabricante' => $motor->fabricante,
                    'potencia' => $motor->potencia_formatada,
                ];
            });

        return response()->json($motores);
    }
}
