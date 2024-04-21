<?php

require 'vendor/autoload.php'; // Caminho para o autoload gerado pelo Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

// Caminho para o arquivo XLSX que você deseja ler
// $xlsxFilePath = $_POST['arquivo'];
$xlsxFilePath = "modelo.xlsx";

// Carrega o arquivo XLSX
$spreadsheet = IOFactory::load($xlsxFilePath);

// Obtém a primeira planilha do arquivo
$worksheet = $spreadsheet->getActiveSheet();

// Obtém o número total de linhas e colunas
$highestRow = $worksheet->getHighestRow(); // Número total de linhas
$highestColumn = $worksheet->getHighestColumn(); // Última coluna

// Itera sobre as células
echo "<table border = '1'>";

for ($row = 1; $row <= $highestRow; $row++) {
    echo "<tr>";
    for ($col = 'A'; $col <= $highestColumn; $col++) {
        $cellValue = $worksheet->getCell($col . $row)->getValue();
        // Faça algo com o valor da célula, por exemplo, exiba-o
        // echo "Valor na célula {$col}{$row}: " . $cellValue . "<br>";
        echo "<td>" . $cellValue . "</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>