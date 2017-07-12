<?php

//use Intervention\Image\Facades\Image;
//use Image;


$app->group(['prefix' => 'api/v1'],function ($app) {
    $app->get('search/{phrase}','ApiController@search');
    $app->post('removeImage/','ApiController@removeImage');
});


$app->get('/admin/search/{phrase}','ApiController@adminSearch');

$app->get('/admin/wordImages/{phrase}', function ($phrase) use ($app) {
    $gs = app('App\Picrun\Google\GoogleService');
    $images = $gs->getImages($phrase);

    return view('admin.wordImages',compact('images'));

});

$app->get('/', function () use ($app) {
  $data = [
    'q' => 'lenny kravitz',
    'api_key' => config('picrun.giphy_api_key'),
  ];

  $response = Curl::to(config('picrun.giphy_api_url'))
      ->withData($data)
      ->get();

  $response = json_decode($response);
  if (isset($response->data)) {
      foreach ($response->data as $gif) {
          dump($gif->images->downsized_small->mp4);
      }
  }

    dd($response);
  return $response;
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

$app->get('/natural/{phrase}', function ($phrase) use ($app) {
    $data = [
      'q' => $phrase,
      'cx' => config('picrun.google_videos_cx'),
      'key' => config('picrun.googleapis_key'),
      'fields' => 'items(title,link,pagemap/cse_thumbnail/src)',
      'num' => 10,
      'start' => 1
    ];

    //https://language.googleapis.com/v1beta2/documents:analyzeSyntax
});
