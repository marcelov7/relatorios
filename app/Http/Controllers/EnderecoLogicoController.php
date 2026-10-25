<?php

namespace App\Http\Controllers;

use App\Models\EnderecoLogico;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnderecoLogicoController extends Controller
{
    /**
     * Listagem com busca em tempo real e filtros
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $area = $request->input('area', '');
        $perPage = (int) $request->input('per_page', 50);

        // Limitar per_page seguro
        if ($perPage <= 0 || $perPage > 200) {
            $perPage = 50;
        }

        $query = EnderecoLogico::query();

        if (!empty($search)) {
            $query->buscar($search);
        }

        if (!empty($area)) {
            $query->where('area', $area);
        }

        $enderecos = $query->orderBy('tag')
            ->paginate($perPage)
            ->withQueryString();

        // Obter todas as áreas distintas cadastradas para filtro rápido
        $areas = EnderecoLogico::whereNotNull('area')
            ->where('area', '!=', '')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        $totalGeral = EnderecoLogico::count();

        return Inertia::render('EnderecosLogicos/Index', [
            'enderecos' => $enderecos,
            'areas' => $areas,
            'filters' => [
                'search' => $search,
                'area' => $area,
                'per_page' => $perPage,
            ],
            'stats' => [
                'total' => $totalGeral,
            ],
        ]);
    }

    /**
     * Criar novo endereço lógico
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->canEditEnderecos()) {
            abort(403, 'Você não tem permissão para cadastrar endereços lógicos. Solicite autorização a um Administrador.');
        }

        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'endereco' => 'required|string|max:191',
            'descricao' => 'nullable|string|max:500',
            'area' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
        ], [
            'tag.required' => 'O campo TAG é obrigatório.',
            'endereco.required' => 'O endereço lógico é obrigatório.',
        ]);

        $endereco = EnderecoLogico::create($validated);

        // Registrar log de auditoria
        \App\Models\EnderecoLogicoLog::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'endereco_logico_id' => $endereco->id,
            'tag' => $endereco->tag,
            'acao' => 'criou',
            'dados_anteriores' => null,
            'dados_novos' => $validated,
            'ip' => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Endereço lógico {$validated['tag']} cadastrado com sucesso!");
    }

    /**
     * Atualizar endereço lógico existente
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->canEditEnderecos()) {
            abort(403, 'Você não tem permissão para alterar endereços lógicos. Solicite autorização a um Administrador.');
        }

        $endereco = EnderecoLogico::findOrFail($id);
        $dadosAnteriores = $endereco->only(['tag', 'endereco', 'descricao', 'area', 'observacoes']);

        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'endereco' => 'required|string|max:191',
            'descricao' => 'nullable|string|max:500',
            'area' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
        ], [
            'tag.required' => 'O campo TAG é obrigatório.',
            'endereco.required' => 'O endereço lógico é obrigatório.',
        ]);

        $endereco->update($validated);

        // Registrar log de auditoria
        \App\Models\EnderecoLogicoLog::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'endereco_logico_id' => $endereco->id,
            'tag' => $endereco->tag,
            'acao' => 'editou',
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $validated,
            'ip' => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Endereço {$endereco->tag} atualizado com sucesso!");
    }

    /**
     * Excluir endereço lógico
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->canDeleteEnderecos()) {
            abort(403, 'Você não tem permissão para excluir endereços lógicos. Apenas usuários expressamente autorizados por um Administrador podem excluir.');
        }

        $endereco = EnderecoLogico::findOrFail($id);
        $tag = $endereco->tag;
        $dadosAnteriores = $endereco->only(['tag', 'endereco', 'descricao', 'area', 'observacoes']);

        $endereco->delete();

        // Registrar log de auditoria
        \App\Models\EnderecoLogicoLog::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'endereco_logico_id' => $id,
            'tag' => $tag,
            'acao' => 'excluiu',
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => null,
            'ip' => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Endereço {$tag} excluído com sucesso!");
    }

    /**
     * Histórico de Logs / Auditoria de Alterações (Apenas Admins e Super Admins)
     */
    public function logs(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Acesso restrito: apenas Administradores podem visualizar o histórico de auditoria.');
        }

        $search = $request->input('search', '');
        $acao = $request->input('acao', '');

        $query = \App\Models\EnderecoLogicoLog::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tag', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        if (!empty($acao)) {
            $query->where('acao', $acao);
        }

        $logs = $query->latest()
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('EnderecosLogicos/Logs', [
            'logs' => $logs,
            'filters' => [
                'search' => $search,
                'acao' => $acao,
            ],
        ]);
    }


    /**
     * Exportar lista de endereços para CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $search = $request->input('search');
        $area = $request->input('area');

        $query = EnderecoLogico::query();
        if (!empty($search)) {
            $query->buscar($search);
        }
        if (!empty($area)) {
            $query->where('area', $area);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="enderecos_logicos_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($query) {
            $output = fopen('php://output', 'w');
            // BOM UTF-8 para Excel abrir sem quebrar acentos
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['TAG', 'DESCRIÇÃO', 'ENDEREÇO', 'ÁREA', 'OBSERVAÇÕES'], ';');

            $query->orderBy('tag')->chunk(500, function ($itens) use ($output) {
                foreach ($itens as $item) {
                    fputcsv($output, [
                        $item->tag,
                        $item->descricao,
                        $item->endereco,
                        $item->area,
                        $item->observacoes,
                    ], ';');
                }
            });

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Importar dados em massa a partir de CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'arquivo.required' => 'Por favor, selecione um arquivo CSV.',
            'arquivo.mimes' => 'O arquivo deve ser no formato CSV.',
        ]);

        $file = $request->file('arquivo');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return redirect()->back()->with('error', 'Não foi possível ler o arquivo.');
        }

        // Tentar detectar delimitador (; ou ,)
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($handle);

        // Pular cabeçalho
        fgetcsv($handle, 0, $delimiter);

        $imported = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty($row) || count($row) < 2) {
                continue;
            }

            $tag = trim($row[0] ?? '');
            $descricao = trim($row[1] ?? '');
            $endereco = trim($row[2] ?? '');
            $area = trim($row[3] ?? '');
            $observacoes = trim($row[4] ?? '');

            if (empty($tag) && empty($endereco)) {
                continue;
            }

            EnderecoLogico::updateOrCreate(
                [
                    'tag' => $tag,
                    'endereco' => $endereco,
                ],
                [
                    'descricao' => $descricao,
                    'area' => !empty($area) ? $area : null,
                    'observacoes' => !empty($observacoes) ? $observacoes : null,
                ]
            );

            $imported++;
        }

        fclose($handle);

        return redirect()->back()->with('success', "{$imported} endereços lógicos importados/atualizados com sucesso!");
    }
}
