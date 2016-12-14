<?php

namespace App\Services\QrCode\Renderer;

use App\Services\QrCode\Renderer\Exception\GoogleChartsRendererException;
use GuzzleHttp\Client;

class GoogleChartsRenderer implements IGoogleChartsRenderer
{
    private $httpMethod = 'GET';
    private $baseApiUrl = 'https://chart.googleapis.com/chart?';

    /**
     * @param $message
     * @param $height
     * @param $width
     * @return string
     * @throws GoogleChartsRendererException
     */
    public function render($message, $height, $width)
    {
        $client = new Client();
        $url = $this->getUrl($message, $height, $width);

        try {
            $res = $client->request($this->httpMethod, $url);

            return $res->getBody()->getContents();
        } catch(\Exception $e) {
            throw new GoogleChartsRendererException();
        }
    }

    /**
     * Returns API url
     * @param string $message
     * @param string $height
     * @param string $width
     * @return string
     */
    private function getUrl($message, $height, $width)
    {
        return $this->baseApiUrl . http_build_query([
            'chs' => $height . 'x' . $width,
            'cht' => 'qr',
            'chl' => $message
        ]);
    }
}