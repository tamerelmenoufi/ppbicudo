<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json; charset=utf-8');

$arquivo = $_POST['arquivo'] ?? '';
$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

if(!$arquivo || !is_file($arquivo)){
    echo json_encode(['erro' => 'Arquivo não encontrado ou caminho inválido.']);
    exit;
}

function detectarDelimitadorCsv(string $linha): string {
    $candidatos = [',', ';', "\t", '|'];
    $melhor = ',';
    $maior = -1;
    foreach ($candidatos as $delimitador) {
        $contagem = substr_count($linha, $delimitador);
        if ($contagem > $maior) {
            $maior = $contagem;
            $melhor = $delimitador;
        }
    }
    return $melhor;
}

function removerBom(string $texto): string {
    if (substr($texto, 0, 3) === "\xEF\xBB\xBF") {
        return substr($texto, 3);
    }
    return $texto;
}

if($extensao === 'csv'){
    $handle = fopen($arquivo, 'r');
    if(!$handle){
        echo json_encode(['erro' => 'Falha ao abrir o arquivo CSV.']);
        exit;
    }

    $primeiraLinha = fgets($handle);
    if($primeiraLinha === false){
        fclose($handle);
        echo json_encode([]);
        exit;
    }

    $delimitador = detectarDelimitadorCsv($primeiraLinha);
    rewind($handle);

    $cabecalho = fgetcsv($handle, 0, $delimitador);
    if(!$cabecalho || count($cabecalho) === 0){
        fclose($handle);
        echo json_encode([]);
        exit;
    }

    $cabecalho[0] = removerBom((string)$cabecalho[0]);
    $campos = array_map(static fn($v) => trim((string)$v), $cabecalho);

    $negativo = [
        'ValorPedidoXquantidade',
        'CustoEnvio',
        'CustoEnvioSeller',
        'TarifaGatwayPagamento',
        'TarifaMarketplace',
        'PrecoCusto'
    ];

    $retorno = [];
    $linhaNumero = 2;
    while(($linha = fgetcsv($handle, 0, $delimitador)) !== false){
        if($linha === [null] || count($linha) === 0){
            $linhaNumero++;
            continue;
        }

        $registro = [];
        foreach($campos as $idx => $nomeCampo){
            if($nomeCampo === '') continue;
            $valor = $linha[$idx] ?? null;
            if($valor === null) continue;

            $valorStr = (string)$valor;
            if(in_array($nomeCampo, $negativo, true)){
                $valorTrim = ltrim($valorStr);
                if($valorTrim !== '' && $valorTrim[0] === '-'){
                    $valorStr = ltrim($valorStr, " \t\n\r\0\x0B-");
                }
            }
            $registro[$nomeCampo] = $valorStr;
        }

        if(count($registro) > 0){
            $retorno[$linhaNumero] = $registro;
        }
        $linhaNumero++;
    }
    fclose($handle);

    echo json_encode($retorno);
    exit;
}

if($extensao !== 'xlsx'){
    echo json_encode(['erro' => 'Extensão inválida. Envie um arquivo .xlsx ou .csv']);
    exit;
}

$xlsxFilePath = $arquivo;
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

                if ($worksheet->getCell($col . $row)->getDataType() === \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($worksheet->getCell($col . $row))) {
                    $retorno[$row][$campos[$col]] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue)->format('Y-m-d H:i:s');
                }else{
                    $negativo = [
                        'ValorPedidoXquantidade',
                        'CustoEnvio',
                        'CustoEnvioSeller',
                        'TarifaGatwayPagamento',
                        'TarifaMarketplace',
                        'PrecoCusto'
                    ];
                    if(in_array($campos[$col],$negativo) and (float)$cellValue*1 < 0) $cellValue = $cellValue*(-1);
                    $retorno[$row][$campos[$col]] = $cellValue;
                }
                
            }
        }

    }
}

echo json_encode($retorno);

?>
