<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

include "config.php";

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$sql = "SELECT c.customer_name,
               s.spx, s.anter, s.sicepat,
               s.jnt, s.jne, s.jnt_cargo,
               s.jne_cargo, s.lazada, s.pos,
               s.id_express, s.total
        FROM shipments s
        JOIN customers c ON s.customer_id = c.id
        WHERE s.shipment_date = ?
        ORDER BY c.customer_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Mutation_'.$date);

$sheet->mergeCells('A1:M1');
$sheet->setCellValue('A1', 'Mutation Report - '.$date);
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold'=>true, 'size'=>14, 'color'=>['rgb'=>'FFFFFF']],
    'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER, 'vertical'=>Alignment::VERTICAL_CENTER],
    'fill' => ['fillType'=>Fill::FILL_SOLID, 'startColor'=>['rgb'=>'1976D2']]
]);
$sheet->getRowDimension('1')->setRowHeight(25);

$headers = ['No','Customer Name','Spx','Anter','Sicepat','J&T','JNE','JNT Cargo','JNE Cargo','Lazada','Pos','ID Express','Total'];
$sheet->fromArray($headers, NULL, 'A2');

$sheet->getStyle('A2:M2')->applyFromArray([
    'font' => ['bold'=>true, 'color'=>['rgb'=>'FFFFFF']],
    'fill' => ['fillType'=>Fill::FILL_SOLID, 'startColor'=>['rgb'=>'4CAF50']],
    'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]
]);
$sheet->getRowDimension('2')->setRowHeight(20);

$rowNumber = 3;
$no = 1;

$totals = [
    'spx'=>0, 'anter'=>0, 'sicepat'=>0, 'jnt'=>0, 'jne'=>0,
    'jnt_cargo'=>0, 'jne_cargo'=>0, 'lazada'=>0, 'pos'=>0, 'id_express'=>0, 'total'=>0
];

while($row = $result->fetch_assoc()){
    $sheet->setCellValue('A'.$rowNumber, $no) // pakai nomor urut
          ->setCellValue('B'.$rowNumber, $row['customer_name'])
          ->setCellValue('C'.$rowNumber, $row['spx'])
          ->setCellValue('D'.$rowNumber, $row['anter'])
          ->setCellValue('E'.$rowNumber, $row['sicepat'])
          ->setCellValue('F'.$rowNumber, $row['jnt'])
          ->setCellValue('G'.$rowNumber, $row['jne'])
          ->setCellValue('H'.$rowNumber, $row['jnt_cargo'])
          ->setCellValue('I'.$rowNumber, $row['jne_cargo'])
          ->setCellValue('J'.$rowNumber, $row['lazada'])
          ->setCellValue('K'.$rowNumber, $row['pos'])
          ->setCellValue('L'.$rowNumber, $row['id_express'])
          ->setCellValue('M'.$rowNumber, $row['total']);

    $sheet->getStyle("A{$rowNumber}:M{$rowNumber}")->applyFromArray([
        'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]
    ]);

    $sheet->getStyle("B{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle("A{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("C{$rowNumber}:M{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Hitung total
    $totals['spx']        += (int)$row['spx'];
    $totals['anter']      += (int)$row['anter'];
    $totals['sicepat']    += (int)$row['sicepat'];
    $totals['jnt']        += (int)$row['jnt'];
    $totals['jne']        += (int)$row['jne'];
    $totals['jnt_cargo']  += (int)$row['jnt_cargo'];
    $totals['jne_cargo']  += (int)$row['jne_cargo'];
    $totals['lazada']     += (int)$row['lazada'];
    $totals['pos']        += (int)$row['pos'];
    $totals['id_express'] += (int)$row['id_express'];
    $totals['total']      += (int)$row['total'];

    $rowNumber++;
    $no++;
}

$sheet->setCellValue('B'.$rowNumber, 'TOTAL')
      ->setCellValue('C'.$rowNumber, $totals['spx'])
      ->setCellValue('D'.$rowNumber, $totals['anter'])
      ->setCellValue('E'.$rowNumber, $totals['sicepat'])
      ->setCellValue('F'.$rowNumber, $totals['jnt'])
      ->setCellValue('G'.$rowNumber, $totals['jne'])
      ->setCellValue('H'.$rowNumber, $totals['jnt_cargo'])
      ->setCellValue('I'.$rowNumber, $totals['jne_cargo'])
      ->setCellValue('J'.$rowNumber, $totals['lazada'])
      ->setCellValue('K'.$rowNumber, $totals['pos'])
      ->setCellValue('L'.$rowNumber, $totals['id_express'])
      ->setCellValue('M'.$rowNumber, $totals['total']);

$sheet->getStyle("A{$rowNumber}:M{$rowNumber}")->applyFromArray([
    'font' => ['bold'=>true, 'color'=>['rgb'=>'FFFFFF']],
    'fill' => ['fillType'=>Fill::FILL_SOLID, 'startColor'=>['rgb'=>'2196F3']],
    'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]
]);

$sheet->getStyle("M{$rowNumber}")->getFill()->setFillType(Fill::FILL_SOLID)
      ->getStartColor()->setRGB('FF9800');

foreach(range('A','M') as $col){
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$sheet->setAutoFilter("A2:M2");
$sheet->freezePane('A3');

$tanggalMutasi = date('d-m-Y', strtotime($date));

$filename = "mutasi_empty_" . $tanggalMutasi . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;