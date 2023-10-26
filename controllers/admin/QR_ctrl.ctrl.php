<?php

use Endroid\QrCode\QrCode;

class QR_ctrl
{
    public $db;
    function  __construct()
    {
        $this->db = new DB_ctrl;
    }

    function generate()
    {
        // Data to be encoded in the QR code
        $data = "Hello, World!";
        
        // Create a QR code instance
        $qrCode = new QrCode($data);
        
        // Set QR code options (optional)
        $qrCode->setSize(300)->setMargin(10);
        
        // Generate QR code image
        $qrCode->create('qrcode.png');
        
        echo "QR code generated successfully!";
    }
}
