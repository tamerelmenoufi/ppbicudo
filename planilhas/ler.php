<?php

require 'vendor/autoload.php'; // Caminho para o autoload gerado pelo Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

// Caminho para o arquivo XLSX que você deseja ler
$xlsxFilePath = $_POST['arquivo'];
// $xlsxFilePath = "xxx.xlsx";

// Carrega o arquivo XLSX
$spreadsheet = IOFactory::load($xlsxFilePath);

// Obtém a primeira planilha do arquivo
$worksheet = $spreadsheet->getActiveSheet();

// Obtém o número total de linhas e colunas
$highestRow = $worksheet->getHighestRow(); // Número total de linhas
$highestColumn = $worksheet->getHighestColumn(); // Última coluna

// Itera sobre as células
// echo "<table border = '1'>";

$cols = [
 'A',  'B',  'C',  'D',  'E',  'F',  'G',  'H',  'I',  'J',  'K',  'L',  'M',  'N',  'O',  'P',  'Q',  'R',  'S',  'T',  'U',  'V',  'W',  'Y',  'Z' /*,  'AA',  'AB',  'AC',  'AD',  'AE',  'AF',  'AG',  'AH',  'AI',  'AJ',  'AK',  'AL',  'AM',  'AN',  'AO',  'AP',  'AQ',  'AR',  'AS',  'AT',  'AU',  'AV',  'AW',  'AY',  'AZ'  */
];

$retorno = [];
for ($row = 1; $row <= $highestRow; $row++) {
    // echo "<tr>";
    // for ($col = 'A'; $col <= $highestColumn; $col++) {
    foreach ($cols as $i => $col) {
        // $cellValue = $worksheet->getCell($col . $row)->getValue();
        $cellValue = $worksheet->getCell($col . $row)->getValue();

        if($row == 1){
            if($cellValue){
                $campos[$col] = $cellValue;
            }
        }else{
            if($campos[$col]){
                if($col == 'A' or $col == 'B'){
                    $retorno[$row][$campos[$col]] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue)->format('d/m/Y H:i:s');
                }else{
                    $retorno[$row][$campos[$col]] = $cellValue;
                }
                
            }
        }

        // $retorno[$row][$col] = $cellValue;
        // Faça algo com o valor da célula, por exemplo, exiba-o
        // echo "Valor na célula {$col}{$row}: " . $cellValue . "<br>";
        // echo "<td>" . $cellValue . "</td>";
    }
    // echo "</tr>";
}

echo json_encode($retorno);

// echo "</table>";
?>