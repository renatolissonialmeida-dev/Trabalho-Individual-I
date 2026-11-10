<?php
function registrarLog($mensagem) {
    // Define o caminho para a pasta logs na raiz do projeto
    $diretorioLogs = __DIR__ . '/../../logs';
    
    // Cria a pasta logs se ela não existir
    if (!is_dir($diretorioLogs)) {
        mkdir($diretorioLogs, 0777, true);
    }

    $arquivo = $diretorioLogs . '/app.log';
    $dataHora = date('Y-m-d H:i:s');
    
    // Prepara a linha do log
    $linha = "[$dataHora] $mensagem" . PHP_EOL;
    
    // Adiciona a linha no arquivo (cria o arquivo se não existir)
    file_put_contents($arquivo, $linha, FILE_APPEND);
}
?>