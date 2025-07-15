<?php
// Script para sincronizar imagens de relatorios para a pasta public

$base = __DIR__;
$origem = "$base/storage/app/public/relatorios";
$destino = "$base/public/storage/relatorios";

if (!is_dir($origem)) {
    echo "Diretório de origem não encontrado: $origem\n";
    exit(1);
}
if (!is_dir($destino)) {
    mkdir($destino, 0777, true);
    echo "Diretório de destino criado: $destino\n";
}

$arquivos = scandir($origem);
$total = 0;
foreach ($arquivos as $arquivo) {
    if ($arquivo === '.' || $arquivo === '..') continue;
    $src = "$origem/$arquivo";
    $dst = "$destino/$arquivo";
    if (is_file($src)) {
        if (!file_exists($dst)) {
            if (copy($src, $dst)) {
                echo "Copiado: $arquivo\n";
                $total++;
            } else {
                echo "Erro ao copiar: $arquivo\n";
            }
        }
    }
}
echo "Sincronização concluída. $total arquivo(s) copiado(s).\n"; 