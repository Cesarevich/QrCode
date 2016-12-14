<?php

namespace App\Services\QrCode\Renderer;

interface IGoogleChartsRenderer
{
    /**
     * @param string $message
     * @param string $height
     * @param string $width
     * @return mixed
     */
    public function render($message, $height, $width);
}