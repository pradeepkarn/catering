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

function generate_sheet()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Set cell colors
    $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFA07A'); // Light Salmon
    $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ADD8E6'); // Light Blue
    $sheet->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('98FB98'); // Pale Green
    $sheet->getStyle('D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('98FB98'); // Pale Green
    $sheet->getStyle('E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('98FB98'); // Pale Green
    $sheet->getStyle('F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('98FB98'); // Pale Green

    // Add data to the cells
    $sheet->setCellValue('A1', 'S NO.');
    $sheet->setCellValue('B1', 'IQMA NO');
    $sheet->setCellValue('C1', 'NATIONALITY');
    $sheet->setCellValue('D1', 'POSITION');
    $sheet->setCellValue('E1', 'COMPANY');
    $sheet->setCellValue('F1', 'MOBILE');

    $event = new Events_ctrl;
    $data = $event->generate_excel($content_id = 33717);


    // Set data and colors for each row
    for ($row = 2; $row <= count($data) + 1; $row++) {
        $sheet->fromArray($data[$row - 2], null, 'A' . $row);
        $color = $row % 2 == 0 ? 'FFFF00' : 'FFFFFF'; // Alternate row colors (yellow and white)
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
    }

     // Save the Excel file
     $writer = new Xlsx($spreadsheet);
     $writer->save('test.xlsx');
}

// Call the function to generate Excel with colors
generate_sheet();
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
