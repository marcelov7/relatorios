<?php

namespace App\Http\Controllers;

use App\Models\Motor;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class MotorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Motor::query();

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('tag', 'like', "%{$busca}%")
                  ->orWhere('equipamento', 'like', "%{$busca}%")
                  ->orWhere('fabricante', 'like', "%{$busca}%")
                  ->orWhere('carcaca_fabricante', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('local')) {
            $query->where('local', 'like', "%{$request->local}%");
        }

        if ($request->filled('reserva_almox')) {
            $query->where('reserva_almox', 'like', "%{$request->reserva_almox}%");
        }

        if ($request->filled('tipo_equipamento_modelo')) {
            $query->where('tipo_equipamento_modelo', 'like', "%{$request->tipo_equipamento_modelo}%");
        }

        if ($request->filled('fabricante')) {
            $query->where('fabricante', 'like', "%{$request->fabricante}%");
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === 'true');
        }

        // Filtros avançados
        if ($request->filled('potencia_min')) {
            $query->where('potencia_kw', '>=', $request->potencia_min);
        }

        if ($request->filled('potencia_max')) {
            $query->where('potencia_kw', '<=', $request->potencia_max);
        }

        if ($request->filled('potencia_cv_min')) {
            $query->where('potencia_cv', '>=', $request->potencia_cv_min);
        }

        if ($request->filled('potencia_cv_max')) {
            $query->where('potencia_cv', '<=', $request->potencia_cv_max);
        }

        if ($request->filled('rotacao_min')) {
            $query->where('rotacao', '>=', $request->rotacao_min);
        }

        if ($request->filled('rotacao_max')) {
            $query->where('rotacao', '<=', $request->rotacao_max);
        }

        if ($request->filled('corrente_placa_min')) {
            $query->where('corrente_placa', '>=', $request->corrente_placa_min);
        }

        if ($request->filled('corrente_placa_max')) {
            $query->where('corrente_placa', '<=', $request->corrente_placa_max);
        }

        if ($request->filled('corrente_configurada_min')) {
            $query->where('corrente_configurada', '>=', $request->corrente_configurada_min);
        }

        if ($request->filled('corrente_configurada_max')) {
            $query->where('corrente_configurada', '<=', $request->corrente_configurada_max);
        }

        if ($request->filled('carcaca_fabricante')) {
            $query->where('carcaca_fabricante', 'like', "%{$request->carcaca_fabricante}%");
        }

        $motores = $query->orderBy('tag')->paginate(12)->withQueryString();

        // Dados para filtros
        $tipos = Motor::select('tipo_equipamento_modelo')
            ->whereNotNull('tipo_equipamento_modelo')
            ->distinct()
            ->pluck('tipo_equipamento_modelo')
            ->filter()
            ->sort()
            ->values();

        $fabricantes = Motor::select('fabricante')
            ->whereNotNull('fabricante')
            ->distinct()
            ->pluck('fabricante')
            ->filter()
            ->sort()
            ->values();

        // Dados para filtros de local
        $locais = Motor::select('local')
            ->whereNotNull('local')
            ->distinct()
            ->pluck('local')
            ->filter()
            ->sort()
            ->values();

        // Dados para filtros de carcaça fabricante
        $carcacas = Motor::select('carcaca_fabricante')
            ->whereNotNull('carcaca_fabricante')
            ->distinct()
            ->pluck('carcaca_fabricante')
            ->filter()
            ->sort()
            ->values();

        return Inertia::render('Motores/Index', [
            'motores' => $motores,
            'filtros' => $request->only([
                'busca', 'local', 'reserva_almox', 'tipo_equipamento_modelo', 
                'fabricante', 'ativo', 'carcaca_fabricante',
                'potencia_min', 'potencia_max', 'potencia_cv_min', 'potencia_cv_max',
                'rotacao_min', 'rotacao_max', 
                'corrente_placa_min', 'corrente_placa_max',
                'corrente_configurada_min', 'corrente_configurada_max'
            ]),
            'tipos' => $tipos,
            'fabricantes' => $fabricantes,
            'locais' => $locais,
            'carcacas' => $carcacas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Motores/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:255|unique:motores',
            'equipamento' => 'required|string|max:255',
            'carcaca_fabricante' => 'nullable|string|max:255',
            'potencia_kw' => 'nullable|numeric|min:0|max:999999.99',
            'potencia_cv' => 'nullable|numeric|min:0|max:999999.99',
            'rotacao' => 'nullable|integer|min:0|max:999999',
            'corrente_placa' => 'nullable|numeric|min:0|max:999999.99',
            'corrente_configurada' => 'nullable|numeric|min:0|max:999999.99',
            'tipo_equipamento_modelo' => 'nullable|string|max:255',
            'fabricante' => 'nullable|string|max:255',
            'reserva_almox' => 'nullable|string|max:255',
            'local' => 'nullable|string|max:255',
            'armazenamento' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        // Upload da foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('motores', 'public');
            $validated['foto'] = $path;
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
        return Inertia::render('Motores/Show', [
            'motor' => $motor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Motor $motor)
    {
        return Inertia::render('Motores/Edit', [
            'motor' => $motor,
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
            'carcaca_fabricante' => 'nullable|string|max:255',
            'potencia_kw' => 'nullable|numeric|min:0|max:999999.99',
            'potencia_cv' => 'nullable|numeric|min:0|max:999999.99',
            'rotacao' => 'nullable|integer|min:0|max:999999',
            'corrente_placa' => 'nullable|numeric|min:0|max:999999.99',
            'corrente_configurada' => 'nullable|numeric|min:0|max:999999.99',
            'tipo_equipamento_modelo' => 'nullable|string|max:255',
            'fabricante' => 'nullable|string|max:255',
            'reserva_almox' => 'nullable|string|max:255',
            'local' => 'nullable|string|max:255',
            'armazenamento' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        // Upload da nova foto
        if ($request->hasFile('foto')) {
            // Remover foto antiga se existir
            if ($motor->foto) {
                Storage::disk('public')->delete($motor->foto);
            }
            $path = $request->file('foto')->store('motores', 'public');
            $validated['foto'] = $path;
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
     * API: Obter motores ativos
     */
    public function apiMotoresAtivos()
    {
        $motores = Motor::ativos()
            ->select('id', 'tag', 'equipamento', 'local', 'reserva_almox')
            ->orderBy('tag')
            ->get()
            ->map(function ($motor) {
                return [
                    'id' => $motor->id,
                    'nome' => $motor->nome_completo,
                    'tag' => $motor->tag,
                    'local' => $motor->local ?? '',
                    'reserva_almox' => $motor->reserva_almox,
                ];
            });

        return response()->json($motores);
    }

    /**
     * API: Obter motores por local
     */
    public function apiMotoresPorLocal(Request $request)
    {
        $local = $request->get('local');
        
        if (!$local) {
            return response()->json([]);
        }

        $motores = Motor::where('local', 'like', "%{$local}%")
            ->ativos()
            ->select('id', 'tag', 'equipamento', 'reserva_almox')
            ->orderBy('tag')
            ->get()
            ->map(function ($motor) {
                return [
                    'id' => $motor->id,
                    'nome' => $motor->nome_completo,
                    'tag' => $motor->tag,
                    'reserva_almox' => $motor->reserva_almox,
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

        $motores = Motor::where('fabricante', 'like', "%{$fabricante}%")
            ->ativos()
            ->select('id', 'tag', 'equipamento', 'fabricante', 'reserva_almox')
            ->orderBy('tag')
            ->get();

        return response()->json($motores);
    }

    /**
     * API: Obter motores em almoxarifado
     */
    public function apiMotoresAlmoxarifado()
    {
        $motores = Motor::reservaAlmox()
            ->ativos()
            ->select('id', 'tag', 'equipamento', 'fabricante', 'potencia_kw', 'potencia_cv')
            ->orderBy('tag')
            ->get()
            ->map(function ($motor) {
                return [
                    'id' => $motor->id,
                    'nome' => $motor->nome_completo,
                    'tag' => $motor->tag,
                    'fabricante' => $motor->fabricante,
                    'potencia' => $motor->potencia_kw ? "{$motor->potencia_kw} kW" : ($motor->potencia_cv ? "{$motor->potencia_cv} CV" : ''),
                ];
            });

        return response()->json($motores);
    }
} 