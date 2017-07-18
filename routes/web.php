<?php

use Illuminate\Http\File;


$app->group(['prefix' => 'api/v1'],function ($app) {
    $app->get('search/{phrase}/device_os/{deviceOS}','ApiController@search');
    $app->post('removeImage/','ApiController@removeImage');
});


$app->get('/admin/search/{phrase}','ApiController@adminSearch');

$app->get('/admin/wordImages/{phrase}', function ($phrase) use ($app) {
    $gs = app('App\Picrun\Google\GoogleService');
    $images = $gs->getImages($phrase);

    return view('admin.wordImages',compact('images'));

});

$app->get('/', function () use ($app) {
    $url = 'https://media2.giphy.com/media/hpnvqznpFPFlK/giphy-downsized-small.mp4';
    //Storage::disk('local')->put()
    Storage::put('2017/07/16/udi.mp4',file_get_contents($url),'public');
});

$app->get('/translate/{phrase}', function ($phrase) use ($app) {

    $data = [
      'q' => urldecode($phrase),
      'key' => config('picrun.googleapis_key'),
      'target' => 'en',
      'format' => 'text',

    ];

    $response = Curl::to(config('picrun.google_translate_api_url'))
        ->withData($data)
        ->get();

    $response = json_decode($response);

    if (isset($response->data->translations[0])) {
        dump($response->data->translations[0]->translatedText);
        dump($response->data->translations[0]->detectedSourceLanguage);
    }
    dd($response);
    return $response;
});

$app->get('/videos/{phrase}', function ($phrase) use ($app) {
    $phrase = trim(urldecode($phrase));

    $data = [
      'q' => $phrase,
      'cx' => config('picrun.google_videos_cx'),
      'key' => config('picrun.googleapis_key'),
      'fields' => 'items(title,link,pagemap/cse_thumbnail/src)',
      'num' => 10,
      'start' => 1
    ];

    $response = Curl::to(config('picrun.googleapis_url'))
        ->withData($data)
        ->get();

    return $response;
});
