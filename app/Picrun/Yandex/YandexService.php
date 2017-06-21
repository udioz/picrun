<?php

namespace App\Picrun\Yandex;
use Curl;

class YandexService
{
  protected $apiTranslateKey;
  protected $apiTranslateUrl;

  protected $apiDictionaryKey;
  protected $apiDictionaryUrl;

    public function __construct($apiTranslateKey,$apiTranslateUrl,$apiDictionaryKey,$apiDictionaryUrl)
    {
        $this->apiTranslateKey = $apiTranslateKey;
        $this->apiTranslateUrl = $apiTranslateUrl;
        $this->apiDictionaryKey = $apiDictionaryKey;
        $this->apiDictionaryUrl = $apiDictionaryUrl;
    }

    public function translate($phrase,$fromToLanguage)
    {
        $response = Curl::to($this->apiTranslateUrl)
          ->withData([
            'key' => $this->apiTranslateKey,
            'text' => urlencode($phrase),
            'lang' => $fromToLanguage,
          ])
          ->get();

          return $response;

    }

    function dictionary($phrase,$fromToLanguage = 'en-en')
    {
        $response = Curl::to($this->apiDictionaryUrl)
          ->withData([
            'key' => $this->apiDictionaryKey,
            'text' => urlencode($phrase),
            'lang' => $fromToLanguage,
          ])
          ->get();

          return $response;

    }

}
