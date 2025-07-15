<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relatorio;
use App\Models\RelatorioAtualizacao;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RelatorioAtribuicaoController extends Controller
{
    /**
     * Atualiza o progresso do relatório
     * Qualquer usuário pode atualizar relatórios com progresso de 0 a 99
     */
    public function atualizarProgresso(Request $request)
    {
        \Log::info('DEBUG - Dados recebidos na atualização de progresso', [
            'all' => $request->all(),
            'files' => $request->allFiles(),
        ]);
        try {
            $request->validate([
                'relatorio_id' => 'required|exists:relatorios,id',
                'progresso_novo' => 'required|integer|min:0|max:100',
                'descricao' => 'required|string',
                'status_novo' => 'required|string',
                'imagens.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('DEBUG - Erro de validação na atualização de progresso', [
                'errors' => $e->errors(),
                'dados_recebidos' => $request->all()
            ]);
            throw $e;
        }

        $relatorio = Relatorio::findOrFail($request->relatorio_id);
        $user = Auth::user();

        // Verificar se o relatório pode ser atualizado (progresso 0-99)
        if ($relatorio->progresso >= 100) {
            Log::warning('Tentativa de atualizar relatório já concluído', ['relatorio_id' => $relatorio->id]);
            return response()->json(['error' => 'Relatório já concluído. Não é possível atualizar.'], 422);
        }

        // Não pode regredir
        if ($request->progresso_novo < $relatorio->progresso) {
            Log::warning('Tentativa de regredir progresso', ['progresso_atual' => $relatorio->progresso, 'progresso_novo' => $request->progresso_novo]);
            return response()->json(['error' => 'Não é permitido regredir o progresso.'], 422);
        }

        // Salvar imagens (opcional)
        $imagens = [];
        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $image) {
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('atualizacoes', $fileName, 'public');
                // Sincronizar para CloudPanel
                $this->syncImageToPublic($path);
                $imagens[] = [
                    'path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ];
            }
        }

        // Determinar status novo
        $statusNovo = $request->status_novo;
        if ($request->progresso_novo >= 100) {
            $statusNovo = 'Concluída';
        } elseif ($request->progresso_novo < 100 && $statusNovo === 'Concluída') {
            $statusNovo = 'Em Andamento';
        }

        // Salvar atualização
        $atualizacao = RelatorioAtualizacao::create([
            'relatorio_id' => $relatorio->id,
            'user_id' => $user->id,
            'progresso_anterior' => $relatorio->progresso,
            'progresso_novo' => $request->progresso_novo,
            'status_anterior' => $relatorio->status,
            'status_novo' => $statusNovo,
            'descricao' => $request->descricao,
            'imagens' => $imagens,
        ]);

        // Atualizar progresso e status do relatório principal
        $relatorio->progresso = $request->progresso_novo;
        $relatorio->status = $statusNovo;
        $relatorio->save();

        Log::info('Progresso atualizado com sucesso', [
            'relatorio_id' => $relatorio->id,
            'progresso_novo' => $relatorio->progresso,
            'status_novo' => $relatorio->status,
            'atualizacao_id' => $atualizacao->id,
            'user_id' => $user->id
        ]);

        return response()->json(['success' => true, 'atualizacao_id' => $atualizacao->id]);
    }

    /**
     * CLOUDPANEL FIX: Sincroniza imagem para pasta public/storage
     */
    private function syncImageToPublic($path)
    {
        try {
            $sourceFile = storage_path("app/public/{$path}");
            $targetFile = public_path("storage/{$path}");
            if (!file_exists($sourceFile)) {
                Log::warning("CLOUDPANEL SYNC: Arquivo fonte não encontrado: {$sourceFile}");
                return false;
            }
            $targetDir = dirname($targetFile);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            if (copy($sourceFile, $targetFile)) {
                chmod($targetFile, 0644);
                Log::info("CLOUDPANEL SYNC: Imagem sincronizada com sucesso", [
                    'source' => $sourceFile,
                    'target' => $targetFile,
                    'path' => $path
                ]);
                return true;
            } else {
                Log::error("CLOUDPANEL SYNC: Falha ao copiar arquivo", [
                    'source' => $sourceFile,
                    'target' => $targetFile,
                    'path' => $path
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("CLOUDPANEL SYNC: Erro na sincronização", [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
