<?php

    use LanguageDetection\Language;


$app->group(['prefix' => 'api/v1'],function ($app) {
    $app->get('search/{phrase}','SearchController@index');
});


$app->get('/admin/wordImages/{phrase}', function ($phrase) use ($app) {
    $gs = app('App\Picrun\Google\GoogleService');
    $images = $gs->getImages($phrase);

    return view('admin.wordImages',compact('images'));

    return $app->version();
});
