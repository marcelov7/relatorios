<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigurarLogoPDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'configurar:logo-pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura a logo para o PDF';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎨 Configurando logo para PDF...');

        // Verificar se a pasta existe
        $imagesPath = public_path('images');
        if (!File::exists($imagesPath)) {
            File::makeDirectory($imagesPath, 0755, true);
            $this->info('✅ Pasta images criada em public/images');
        }

        // Criar um placeholder para a logo
        $logoPath = $imagesPath . '/logo-guerreiros-alagoas.png';
        
        if (!File::exists($logoPath)) {
            // Criar um arquivo placeholder simples
            $this->info('📝 Criando placeholder para logo...');
            
            // Criar uma imagem simples com GD
            $width = 400;
            $height = 200;
            $image = imagecreate($width, $height);
            
            // Cores
            $blue = imagecolorallocate($image, 30, 58, 138); // #1e3a8a
            $white = imagecolorallocate($image, 255, 255, 255);
            $lightBlue = imagecolorallocate($image, 59, 130, 246); // #3b82f6
            
            // Fundo gradiente
            for ($i = 0; $i < $height; $i++) {
                $ratio = $i / $height;
                $r = 30 + ($ratio * 29);
                $g = 58 + ($ratio * 72);
                $b = 138 + ($ratio * 108);
                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $i, $width, $i, $color);
            }
            
            // Texto
            $fontSize = 5;
            $text = "GUERREIROS DE ALAGOAS";
            $textWidth = imagefontwidth($fontSize) * strlen($text);
            $textHeight = imagefontheight($fontSize);
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2;
            
            imagestring($image, $fontSize, $x, $y, $text, $white);
            
            // Salvar como PNG
            imagepng($image, $logoPath);
            imagedestroy($image);
            
            $this->info('✅ Logo placeholder criada em: ' . $logoPath);
        } else {
            $this->info('✅ Logo já existe em: ' . $logoPath);
        }

        $this->info('');
        $this->info('🎯 Para usar sua logo personalizada:');
        $this->info('   1. Substitua o arquivo: ' . $logoPath);
        $this->info('   2. Use uma imagem PNG com fundo transparente');
        $this->info('   3. Dimensões recomendadas: 400x200px');
        $this->info('');
        $this->info('🧪 Para testar o PDF:');
        $this->info('   1. Acesse a listagem de relatórios');
        $this->info('   2. Selecione alguns relatórios');
        $this->info('   3. Clique em "Gerar PDF Técnico"');
        $this->info('');
        $this->info('🎉 Configuração concluída!');
    }
} 