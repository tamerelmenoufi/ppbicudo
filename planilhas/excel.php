<?php

require 'vendor/autoload.php'; //autoload do projeto


use PhpOffice\PhpSpreadsheet\Spreadsheet; //classe responsável pela manipulação da planilha

use PhpOffice\PhpSpreadsheet\Writer\Xlsx; //classe que salvará a planilha em .xlsx

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


$spreadsheet = new Spreadsheet(); //instanciando uma nova planilha

$sheet = $spreadsheet->getActiveSheet(); //retornando a aba ativa


// Carrega a imagem que você deseja incluir
$imagePath = 'img.jpg';
$drawing = new Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath($imagePath);
$drawing->setHeight(100); // Altura da imagem em pixels
$drawing->setWorksheet($sheet);


// Cria um objeto Drawing (imagem)
$drawing = new Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo da empresa');
$drawing->setPath('img.jpg'); // Caminho para a imagem que você deseja adicionar
$drawing->setCoordinates('F1'); // Célula onde a imagem será inserida
$drawing->setHeight(100); // Altura da imagem em pixels
$drawing->setWorksheet($sheet);


$sheet->setCellValue('A1', 'Nome'); //Definindo a célula A1

$sheet->setCellValue('B1', 'Nota 1'); //Definindo a célula B1

$sheet->setCellValue('C1', 'Nota 2');

$sheet->setCellValue('D1', 'Media');

$sheet->setCellValue('A2', 'pokemaobr');

$sheet->setCellValue('B2', 5);

$sheet->setCellValue('C2', 3.5);

$sheet->setCellValue('D2', '=((B2+C2)/2)'); //Definindo a fórmula para o cálculo da média

$sheet->setCellValue('A3', 'bob');

$sheet->setCellValue('B3', 7);

$sheet->setCellValue('C3', 8);

$sheet->setCellValue('D3', '=((B3+C3)/2)');

$sheet->setCellValue('A4', 'boina');

$sheet->setCellValue('B4', 9);

$sheet->setCellValue('C4', 9);

$sheet->setCellValue('D4', '=((B4+C4)/2)');


$writer = new Xlsx($spreadsheet); //Instanciando uma nova planilha

$writer->save('spreadsheet1.xlsx'); //salvando a planilha na extensão definida