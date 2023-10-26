<?php

use PHPMailer\PHPMailer\PHPMailer;
use Endroid\QrCode\QrCode;

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/settings.php");
import("/includes/class-autoload.inc.php");
import("functions.php");
import("settings.php");
define("direct_access", 1);
############################################################################



    function generate()
    {
        // Data to be encoded in the QR code
        $data = "Hello, World s!";
        
        // Create a QR code instance
        $qrCode = new QrCode($data);
        
        // Set QR code options (optional)
        $qrCode->setSize(300)->setMargin(10);
        
        // Generate QR code image
        $qrCode->create('qrcode.png');
        
    }
    generate();
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
