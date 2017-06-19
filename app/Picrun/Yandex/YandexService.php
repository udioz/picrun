<?php

namespace App\Picrun\Yandex;
use Curl;

class YandexService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct($apiKey,$apiUrl)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    public function translate($phrase,$fromToLanguage)
    {
        $response = Curl::to($this->apiUrl)
          ->withData([
            'key' => $this->apiKey,
            'text' => urlencode($phrase),
            'lang' => $fromToLanguage,
          ])
          ->get();

          return $response;

    }

}
