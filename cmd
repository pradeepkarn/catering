<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/settings.php");
import("/includes/class-autoload.inc.php");
import("functions.php");
import("settings.php");
define("direct_access", 1);

############################################################################

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

function generate_excel_from_data($event)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set cell colors
    // $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFA07A'); // Light Salmon
    // $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ADD8E6'); // Light Blue
    // $sheet->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('98FB98'); // Pale Green
    // $sheet->getStyle('D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD700'); // Gold
    // $sheet->getStyle('E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('87CEFA'); // Light Sky Blue
    // $sheet->getStyle('F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFA500'); // Orange
    // $sheet->getStyle('G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF69B4'); // Hot Pink
    $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB("D3D3D3");

    // Set headers
    $sheet->setCellValue('A1', 'S NO');
    $sheet->setCellValue('B1', 'NAME');
    $sheet->setCellValue('C1', 'IQMA NO');
    $sheet->setCellValue('D1', 'NATIONALITY');
    $sheet->setCellValue('E1', 'POSITION');
    $sheet->setCellValue('F1', 'COMPANY');
    $sheet->setCellValue('G1', 'MOB NO');
    $sheet->setCellValue('H1', 'CHECK IN');
    $sheet->setCellValue('I1', 'CHECK OUT');
    $sheet->setCellValue('J1', 'FOOD CATEGORY');

    // Set headers in cells K1 to AF1
    for ($i = 0; $i < 31; $i++) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex(11 + $i) . '1', $i + 1);
    }
    $sheet->setCellValue('AP1', 'TOTAL DAYS');
    // for ($col = 'A'; $col <= 'J'; $col++) {
    //     $sheet->getColumnDimension($col)->setAutoSize(true);
    // }

    // Set data
    $row = 2; // Start from row 2
    $emps = array_merge($event['employees'], $event['managers']);
    foreach ($emps as $key => $employee) {
        $mobile = strval($employee['isd_code'] . $employee['mobile']);
        $days = [];
        if (isset($emps['attendence'])) {
            foreach ($emps['attendence'] as $key => $atn) {
                $days[] = $atn['day'];
            }
        }

        $color = $row % 2 == 0 ?  'FFFFFF' : 'D3D3D3'; // Alternate row colors (yellow and white)
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

        $sheet->setCellValue('A' . $row, $key + 1);
        $sheet->setCellValue('B' . $row, $employee['first_name'] . " " . $employee['last_name']);
        $sheet->setCellValue('C' . $row, $employee['nid_no']);
        $sheet->setCellValue('D' . $row, $employee['country']);
        $sheet->setCellValue('E' . $row, $employee['position']);
        $sheet->setCellValue('F' . $row, $employee['company']);
        $sheet->setCellValue('G' . $row, $mobile);
        $sheet->setCellValue('H' . $row, "");
        $sheet->setCellValue('I' . $row, "");
        $sheet->setCellValue('J' . $row, $employee['food_category']);
        for ($i = 0; $i < 31; $i++) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex(11 + $i) . '1', $i + 1);
        }
        $sheet->setCellValue('AP' . $row, 0);
        $row++;
    }


    // Save the Excel file
    $writer = new Xlsx($spreadsheet);
    $writer->save('example.xlsx');

    echo 'Excel file generated successfully.';
}

$event = new Events_ctrl;
$data = $event->generate_excel($content_id = 33717);
print_r($data);
return;
// Call the function to generate Excel from data
generate_excel_from_data($data = $data);
exit;



exit;
function updateProgressBar($current, $total)
{
    $percent = ($current / $total) * 100;
    $barWidth = 50;
    $numBars = (int) ($percent / (100 / $barWidth));
    $progressBar = "[" . str_repeat("=", $numBars) . str_repeat(" ", $barWidth - $numBars) . "] $percent%";
    echo "\r$progressBar";
    // flush();
}


echo "\nTask complete!\n";
