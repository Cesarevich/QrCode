<?php
namespace App;

use App\Services\QrCode\QrCode;
use App\Services\QrCode\Renderer\GoogleChartsRenderer;

require_once __DIR__.'/../../vendor/autoload.php';

try {
    $qrCode = new QrCode('TrekkSoft', 50, 50); // text, width, height
    $qrCode->setRenderer(new GoogleChartsRenderer());
    $qrCodeData = $qrCode->generate(); // should return the image data, not the URL
} catch(\Exception $e) {
    echo $e->getMessage();
}





