<?php

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/settings.php");
import("/includes/class-autoload.inc.php");
import("functions.php");
import("settings.php");
define("direct_access", 1);

############################################################################

$event = new Events_ctrl;
$data = $event->event_report($event_id=33717, $month=11);
print_r($data);
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
