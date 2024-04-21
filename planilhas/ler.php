<?php
// Inclua o autoload.php do PhpSpreadsheet
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    // Carregue o arquivo Excel
    $spreadsheet = IOFactory::load('xxx.xlsx');
    
    // Obtenha a primeira planilha no arquivo
    $sheet = $spreadsheet->getActiveSheet();
    
    // Obtenha o número de linhas e colunas na planilha
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    
    // Converta a letra da coluna para o número correspondente
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    // Loop pelas células da planilha
    for ($row = 1; $row <= $highestRow; $row++) {
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            // Obtenha o valor da célula
            $value = $sheet->getCellByColumnAndRow($col, $row)->getValue();
            // Imprima o valor da célula
            echo "Valor da célula $col-$row: $value\n";
        }
    }
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    // Lidar com exceções de leitura de arquivo
    echo 'Exceção ao ler o arquivo: ', $e->getMessage();
}

?>