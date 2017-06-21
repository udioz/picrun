<?php

    use LanguageDetection\Language;


$app->group(['prefix' => 'api/v1'],function ($app) {
    $app->get('search/{phrase}','SearchController@index');
});


$app->get('/', function () use ($app) {

    // $text = '^ ';
    // $after = trim(preg_replace('/[^\w\s]+/u','' , $text));
    // dd($after);


    $yandexService = app('App\Picrun\Yandex\YandexService');
    $response = $yandexService->translate('Chagatai','en-he');
dd($response);
    $response = json_decode($response);
    dd(preg_match("/[a-z]/i", $response->text[0]));
    //dd(strlen($response->text[0]));

    $ld = new Language;//(['en','he']);

    $langs = $ld->detect($response->text[0])->whitelist('en','he')->close();

    dd($langs);

    if (!(array_keys($langs)[0] == 'he' && $langs['en'] == 0))
      echo 'en';
    else {
      echo 'he';
    }
dd();
    return $app->version();
});
