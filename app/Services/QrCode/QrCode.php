<?php

namespace App\Services\QrCode;

use App\Services\QrCode\Renderer\Exception\QrCodeException;

class QrCode
{
    public $height;
    public $width;
    public $message;
    public $renderer;

    public function __construct($message, $height, $width)
    {
        $this->height = $height;
        $this->width = $width;
        $this->message = $message;
    }

    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }

    public function generate()
    {
        try {
            return $this->renderer->render($this->message, $this->height, $this->width);
        } catch(\Exception $e) {
            throw new QrCodeException('Render exception happened');
        }
    }
}