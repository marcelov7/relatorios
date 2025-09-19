<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

class TestImageUpload extends Command
{
    protected $signature = 'test:image-upload';
    protected $description = 'Testa o sistema de upload de imagens';

    public function handle()
    {
        $this->info('🧪 Testando sistema de upload de imagens...');

        // Criar imagem de teste
        $testImagePath = storage_path('app/test-image.jpg');
        
        if (!file_exists($testImagePath)) {
            $image = imagecreate(100, 100);
            $bgColor = imagecolorallocate($image, 255, 255, 255);
            $textColor = imagecolorallocate($image, 0, 0, 0);
            imagestring($image, 5, 10, 40, 'TEST', $textColor);
            imagejpeg($image, $testImagePath);
            imagedestroy($image);
            $this->info('📸 Imagem de teste criada');
        }

        // Criar UploadedFile
        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test-image.jpg',
            'image/jpeg',
            null,
            true
        );

        try {
            $service = new ImageUploadService();
            $result = $service->uploadImageForRelatorio($uploadedFile, 1, 0);
            
            $this->info('✅ Upload realizado com sucesso!');
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Nome Original', $result['nome_original']],
                    ['Nome Arquivo', $result['nome_arquivo']],
                    ['Caminho Original', $result['caminho_original']],
                    ['Caminho Thumb', $result['caminho_thumb'] ?? 'N/A'],
                    ['Caminho Medium', $result['caminho_medium'] ?? 'N/A'],
                    ['MIME Type', $result['mime_type']],
                    ['Tamanho', $result['tamanho'] . ' bytes'],
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('❌ Erro no upload: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }

        // Limpar arquivo de teste
        if (file_exists($testImagePath)) {
            unlink($testImagePath);
        }
    }
} 