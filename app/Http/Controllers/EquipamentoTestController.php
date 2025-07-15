<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipamentoTest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;

class EquipamentoTestController extends Controller
{
    // Adicionar restrição de admin futuramente (ex: $this->middleware('admin'))

    /**
     * Listar todos os equipamentos de teste, com busca global.
     */
    public function index(Request $request)
    {
        $query = EquipamentoTest::query();
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('tag', 'like', "%{$busca}%")
                  ->orWhere('nome', 'like', "%{$busca}%")
                  ->orWhere('setor', 'like', "%{$busca}%");
            });
        }
        $equipamentos = $query->orderBy('tag')->paginate(12);
        return response()->json($equipamentos);
    }

    /**
     * Criar novo equipamento de teste.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('EquipamentoTestController@store', ['payload' => $request->all()]);
            $validated = $request->validate([
                'tag' => 'required|string|max:255',
                'nome' => 'required|string|max:255',
                'setor' => 'required|string|max:255',
                'status' => 'required|in:Operacional,Manutenção,Inativo',
            ]);
            $equipamento = EquipamentoTest::create($validated);
            return response()->json($equipamento, 201);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar equipamento de teste', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Exibir um equipamento de teste.
     */
    public function show($id)
    {
        $equipamento = EquipamentoTest::findOrFail($id);
        return response()->json($equipamento);
    }

    /**
     * Atualizar um equipamento de teste.
     */
    public function update(Request $request, $id)
    {
        $equipamento = EquipamentoTest::findOrFail($id);
        $validated = $request->validate([
            'tag' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'setor' => 'required|string|max:255',
            'status' => 'required|in:Operacional,Manutenção,Inativo',
        ]);
        $equipamento->update($validated);
        return response()->json($equipamento);
    }

    /**
     * Excluir um equipamento de teste.
     */
    public function destroy($id)
    {
        $equipamento = EquipamentoTest::findOrFail($id);
        $equipamento->delete();
        return response()->json(['message' => 'Equipamento excluído com sucesso.']);
    }

    /**
     * Exportar todos os equipamentos como CSV.
     */
    public function exportCsv()
    {
        $equipamentos = EquipamentoTest::all();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="equipamentos_teste.csv"',
        ];
        $columns = ['tag', 'nome', 'setor', 'status'];
        $callback = function() use ($equipamentos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($equipamentos as $equip) {
                fputcsv($file, [
                    $equip->tag,
                    $equip->nome,
                    $equip->setor,
                    $equip->status,
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Importar equipamentos de teste a partir de um arquivo CSV (sempre cria novos).
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');
        $count = 0;
        $ignoradas = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) !== count($header)) {
                $ignoradas++;
                continue;
            }
            $data = array_combine($header, $row);
            if (!isset($data['tag'], $data['nome'], $data['setor'], $data['status'])) {
                $ignoradas++;
                continue;
            }
            EquipamentoTest::create([
                'tag' => $data['tag'],
                'nome' => $data['nome'],
                'setor' => $data['setor'],
                'status' => $data['status'],
            ]);
            $count++;
        }
        fclose($handle);
        return response()->json(['message' => "Importação concluída: $count registros criados. $ignoradas linhas ignoradas."]);
    }

    /**
     * Renderizar a página Inertia para Equipamentos de Teste.
     */
    public function indexPage()
    {
        return Inertia::render('Equipamentos/EquipamentoTest');
    }
}
