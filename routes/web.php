<?php

    use LanguageDetection\Language;


$app->group(['prefix' => 'api/v1'],function ($app) {
    $app->get('search/{phrase}','ApiController@search');
    $app->post('removeImage/','ApiController@removeImage');
});


$app->get('/admin/wordImages/{phrase}', function ($phrase) use ($app) {
    $gs = app('App\Picrun\Google\GoogleService');
    $images = $gs->getImages($phrase);

    return view('admin.wordImages',compact('images'));

    return $app->version();
});

$app->get('/', function () use ($app) {

  $img = Image::make('https://www.dev-metal.com/wp-content/uploads/2014/01/php-1.jpg')
      ->resize(300, 200);

  return $img->response('jpg');

});
