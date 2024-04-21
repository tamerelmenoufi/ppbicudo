<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$xlsxFilePath = $_POST['arquivo'];
$spreadsheet = IOFactory::load($xlsxFilePath);
$worksheet = $spreadsheet->getActiveSheet();
$highestRow = $worksheet->getHighestRow(); 
$highestColumn = $worksheet->getHighestColumn();

$cols = [
 'A',  'B',  'C',  'D',  'E',  'F',  'G',  'H',  'I',  'J',  'K',  'L',  'M',  'N',  'O',  'P',  'Q',  'R',  'S',  'T',  'U',  'V',  'W',  'Y',  'Z' /*,  'AA',  'AB',  'AC',  'AD',  'AE',  'AF',  'AG',  'AH',  'AI',  'AJ',  'AK',  'AL',  'AM',  'AN',  'AO',  'AP',  'AQ',  'AR',  'AS',  'AT',  'AU',  'AV',  'AW',  'AY',  'AZ'  */
];

$retorno = [];
for ($row = 1; $row <= $highestRow; $row++) {

    foreach ($cols as $i => $col) {
        $cellValue = $worksheet->getCell($col . $row)->getValue();
        if($row == 1){
            if($cellValue){
                $campos[$col] = $cellValue;
            }
        }else{
            if($campos[$col]){

                // if ($cell->getDataType() === \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                if($col == 'A' or $col == 'B'){
                    // $retorno[$row][$campos[$col]] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue)->format('Y-m-d H:i:s');
                    $retorno[$row][$campos[$col]] = $cell->getPlainText();
                }else{
                    if(in_array($campos[$col],['TarifaEnvio','TarifaMarketplace'])) $cellValue = $cellValue*(-1);
                    $retorno[$row][$campos[$col]] = $cellValue;
                }
                
            }
        }

    }
}

echo json_encode($retorno);

?>