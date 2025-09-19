<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Exception;

class ImageUploadService
{
    private $imageManager;
    private $disk;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new GdDriver());
        $this->disk = Storage::disk('public');
    }

    /**
     * Faz upload de uma imagem para um relatório
     */
    public function uploadImageForRelatorio(UploadedFile $file, int $relatorioId, int $ordem = 0): array
    {
        try {
            Log::info('UPLOAD: Iniciando upload de imagem', [
                'relatorio_id' => $relatorioId,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            // Gerar nome único para o arquivo
            $timestamp = time();
            $uniqueId = uniqid();
            $extension = $file->getClientOriginalExtension();
            $fileName = "{$timestamp}_{$uniqueId}.{$extension}";

            // Definir caminhos
            $relatorioPath = "relatorios/{$relatorioId}";
            $originalPath = "{$relatorioPath}/original/{$fileName}";
            $thumbPath = "{$relatorioPath}/thumbs/{$timestamp}_{$uniqueId}_thumb.jpg";
            $mediumPath = "{$relatorioPath}/medium/{$timestamp}_{$uniqueId}_medium.jpg";

            // Criar diretórios se não existirem
            $this->ensureDirectoryExists(dirname($originalPath));
            $this->ensureDirectoryExists(dirname($thumbPath));
            $this->ensureDirectoryExists(dirname($mediumPath));

            // Salvar arquivo original
            $originalSaved = $this->disk->putFileAs(
                dirname($originalPath),
                $file,
                basename($originalPath)
            );

            if (!$originalSaved) {
                throw new Exception("Falha ao salvar arquivo original: {$originalPath}");
            }

            Log::info('UPLOAD: Arquivo original salvo', ['path' => $originalPath]);

            // Gerar thumbnails
            $thumbGenerated = $this->generateThumbnail($file, $thumbPath, 300, 200);
            $mediumGenerated = $this->generateThumbnail($file, $mediumPath, 800, 600);

            // Sincronizar para public/storage (CloudPanel)
            $this->syncToPublic($originalPath);
            if ($thumbGenerated) {
                $this->syncToPublic($thumbPath);
            }
            if ($mediumGenerated) {
                $this->syncToPublic($mediumPath);
            }

            $imageData = [
                'nome_original' => $file->getClientOriginalName(),
                'nome_arquivo' => $fileName,
                'caminho_original' => $originalPath,
                'caminho_thumb' => $thumbGenerated ? $thumbPath : null,
                'caminho_medium' => $mediumGenerated ? $mediumPath : null,
                'mime_type' => $file->getMimeType(),
                'tamanho' => $file->getSize(),
                'ordem' => $ordem,
            ];

            Log::info('UPLOAD: Upload concluído com sucesso', $imageData);

            return $imageData;

        } catch (Exception $e) {
            Log::error('UPLOAD: Erro no upload de imagem', [
                'relatorio_id' => $relatorioId,
                'original_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Limpar arquivos parciais em caso de erro
            $this->cleanupPartialFiles($originalPath, $thumbPath, $mediumPath);

            throw $e;
        }
    }

    /**
     * Gera thumbnail de uma imagem
     */
    private function generateThumbnail(UploadedFile $file, string $path, int $width, int $height): bool
    {
        try {
            $img = $this->imageManager->read($file)->cover($width, $height);
            $encoded = $img->encode(new JpegEncoder(85));
            
            $saved = $this->disk->put($path, $encoded->toString());
            
            if ($saved) {
                Log::info('UPLOAD: Thumbnail gerado', ['path' => $path, 'size' => "{$width}x{$height}"]);
                return true;
            }

            Log::warning('UPLOAD: Falha ao salvar thumbnail', ['path' => $path]);
            return false;

        } catch (Exception $e) {
            Log::error('UPLOAD: Erro ao gerar thumbnail', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Sincroniza arquivo para public/storage (CloudPanel)
     */
    private function syncToPublic(string $path): bool
    {
        try {
            $source = storage_path("app/public/{$path}");
            $dest = public_path("storage/{$path}");
            $destDir = dirname($dest);

            if (!file_exists($source)) {
                Log::warning('SYNC: Arquivo fonte não encontrado', ['source' => $source]);
                return false;
            }

            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (copy($source, $dest)) {
                chmod($dest, 0644);
                Log::info('SYNC: Arquivo sincronizado', ['path' => $path]);
                return true;
            }

            Log::error('SYNC: Falha ao copiar arquivo', ['source' => $source, 'dest' => $dest]);
            return false;

        } catch (Exception $e) {
            Log::error('SYNC: Erro na sincronização', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remove arquivos parciais em caso de erro
     */
    private function cleanupPartialFiles(string $originalPath, string $thumbPath, string $mediumPath): void
    {
        $files = [$originalPath, $thumbPath, $mediumPath];
        
        foreach ($files as $file) {
            if ($this->disk->exists($file)) {
                $this->disk->delete($file);
                Log::info('CLEANUP: Arquivo removido', ['path' => $file]);
            }
        }
    }

    /**
     * Remove imagem e seus arquivos - VERSÃO MELHORADA
     */
    public function deleteImage(array $imageData): bool
    {
        try {
            $files = [
                $imageData['caminho_original'],
                $imageData['caminho_thumb'],
                $imageData['caminho_medium']
            ];

            $allDeleted = true;

            foreach ($files as $file) {
                if ($file) {
                    // Primeiro tenta deletar do storage Laravel
                    if ($this->disk->exists($file)) {
                        try {
                            $this->disk->delete($file);
                            Log::info('DELETE: Arquivo removido do storage', ['path' => $file]);
                        } catch (Exception $e) {
                            Log::warning('DELETE: Falha ao remover do storage', ['path' => $file, 'error' => $e->getMessage()]);
                            $allDeleted = false;
                        }
                    }
                    
                    // Agora tenta deletar de public/storage usando métodos alternativos
                    $publicPath = public_path("storage/{$file}");
                    if (file_exists($publicPath)) {
                        $deleted = $this->deletePublicFile($publicPath);
                        if (!$deleted) {
                            $allDeleted = false;
                        }
                    }
                }
            }

            if ($allDeleted) {
                Log::info('DELETE: Imagem completamente removida', ['image_data' => $imageData]);
            } else {
                Log::warning('DELETE: Alguns arquivos não puderam ser removidos', ['image_data' => $imageData]);
            }

            return $allDeleted;

        } catch (Exception $e) {
            Log::error('DELETE: Erro ao remover imagem', [
                'image_data' => $imageData,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Tenta deletar arquivo de public/storage usando diferentes métodos
     */
    private function deletePublicFile(string $filePath): bool
    {
        try {
            // Método 1: unlink direto
            if (unlink($filePath)) {
                Log::info('DELETE: Arquivo público removido com unlink', ['path' => $filePath]);
                return true;
            }
        } catch (Exception $e) {
            Log::debug('DELETE: unlink falhou, tentando alternativas', ['path' => $filePath, 'error' => $e->getMessage()]);
        }

        try {
            // Método 2: usando exec com rm (mais confiável em ambientes com permissões complexas)
            $escapedPath = escapeshellarg($filePath);
            exec("rm -f {$escapedPath} 2>&1", $output, $returnCode);
            
            if ($returnCode === 0 && !file_exists($filePath)) {
                Log::info('DELETE: Arquivo público removido com exec rm', ['path' => $filePath]);
                return true;
            } else {
                Log::warning('DELETE: exec rm falhou', [
                    'path' => $filePath, 
                    'return_code' => $returnCode, 
                    'output' => implode("\n", $output)
                ]);
            }
        } catch (Exception $e) {
            Log::debug('DELETE: exec rm falhou', ['path' => $filePath, 'error' => $e->getMessage()]);
        }

        try {
            // Método 3: renomear para .deleted (para limpeza posterior)
            $deletedPath = $filePath . '.deleted_' . time();
            if (rename($filePath, $deletedPath)) {
                Log::info('DELETE: Arquivo marcado para deleção', ['path' => $filePath, 'renamed_to' => $deletedPath]);
                // Tentar deletar o arquivo renomeado
                @unlink($deletedPath);
                return true;
            }
        } catch (Exception $e) {
            Log::debug('DELETE: rename falhou', ['path' => $filePath, 'error' => $e->getMessage()]);
        }

        Log::error('DELETE: Todos os métodos de deleção falharam', ['path' => $filePath]);
        return false;
    }

    /**
     * Limpa arquivos marcados para deleção (pode ser executado via cron)
     */
    public function cleanupDeletedFiles(): int
    {
        $cleaned = 0;
        $publicStoragePath = public_path('storage/relatorios');
        
        if (!is_dir($publicStoragePath)) {
            return $cleaned;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publicStoragePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && strpos($file->getFilename(), '.deleted_') !== false) {
                    if (@unlink($file->getPathname())) {
                        $cleaned++;
                        Log::info('CLEANUP: Arquivo .deleted removido', ['path' => $file->getPathname()]);
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('CLEANUP: Erro na limpeza', ['error' => $e->getMessage()]);
        }

        return $cleaned;
    }

    /**
     * Verifica se uma imagem existe e retorna URL válida
     */
    public function getImageUrl(string $path, string $fallbackPath = null): string
    {
        // Verificar se existe em public/storage
        $publicPath = public_path("storage/{$path}");
        if (file_exists($publicPath)) {
            return asset("storage/{$path}");
        }

        // Verificar se existe em storage/app/public
        if ($this->disk->exists($path)) {
            // Tentar sincronizar
            $this->syncToPublic($path);
            return asset("storage/{$path}");
        }

        // Retornar fallback
        if ($fallbackPath) {
            return asset($fallbackPath);
        }

        return asset('images/no-image.png');
    }

    /**
     * Garante que um diretório existe, criando-o se necessário
     */
    private function ensureDirectoryExists(string $path): bool
    {
        try {
            // Usar o disk do Laravel para criar diretórios
            if (!$this->disk->exists($path)) {
                // Usar makeDirectory para criar diretórios recursivamente
                $fullPath = storage_path("app/public/{$path}");
                
                Log::info('DIRECTORY: Criando diretório', ['path' => $fullPath]);
                
                // Criar usando mkdir nativo para garantir criação
                if (!is_dir($fullPath)) {
                    $created = mkdir($fullPath, 0775, true);
                    if ($created) {
                        Log::info('DIRECTORY: Diretório criado com sucesso', ['path' => $fullPath]);
                        
                        // Definir permissões e proprietário corretos
                        chmod($fullPath, 0775);
                        
                        return true;
                    } else {
                        Log::error('DIRECTORY: Falha ao criar diretório', ['path' => $fullPath]);
                        return false;
                    }
                }
                
                return true;
            }
            
            return true;
        } catch (Exception $e) {
            Log::error('DIRECTORY: Erro ao criar diretório', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 