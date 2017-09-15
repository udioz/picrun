<?php

use Illuminate\Http\File;
use App\Models\Dictionary;

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

  // $url = 'http://d310a9hpolx59w.cloudfront.net/product_photos/42759717/8_20happy_20dogs_400w.png';
  // $mime = 'image/png';
  // $img = Image::make($url)->resize(200,null,function ($constraint) {
  //   $constraint->aspectRatio();
  // })->stream();
  // dd(Image::make((string)$img)->filesize());
  // $suffix = imageSuffix($mime);
  // $tempFilename = "temp." . $suffix;
  //
  // Storage::disk('local')
  //           ->put($tempFilename, (string) $img, 'public');
  //
  // $size = Storage::disk('local')->size($tempFilename);
  // dd($size);

  // for ($i=1 ; $i < 4 ; $i++){
  //   $data = [
  //     'q' => 'cat',
  //     'cx' => config('picrun.google_images_cx'),
  //     'key' => config('picrun.googleapis_key'),
  //     'searchType' => 'image',
  //     'imgType' => 'photo',
  //     'fields' => 'items(link,mime,image(byteSize,width))',
  //     'num' => 5,
  //     'start' => $i
  //   ];
  //
  //   $response = Curl::to(config('picrun.googleapis_url'))
  //       ->withData($data)
  //       ->get();
  //
  //   if (!isset(json_decode($response)->items)) return false;
  //
  //   foreach(json_decode($response)->items as $item) {
  //     $urls[] = $item->link;
  //   }
  // }
  //
  // dd(array_unique($urls));
  //
  // $response = Curl::to(config('picrun.googleapis_url'))
  //     ->withData($data)
  //     ->get();
  //
  // if (!isset(json_decode($response)->items)) return false;
  //
  // foreach(json_decode($response)->items as $item)
  // {
  //   $url = $item->link;
  //   $mime = $item->mime;
  //   $size = $item->image->byteSize;
  //   $width = $item->image->width;
  //
  //   dump($url,$size,$width);
  //   //continue;
  //   dump("Initial Size: $size");
  //
  //   if ($size < 100000) continue;
  //
  //   if ($mime == 'image/') {
  //     $mime = Image::make($url)->mime();
  //     dump($mime);
  //   }
  //   $suffix = imageSuffix($mime);
  //
  //   $newWidth = ($width > 600) ? 600 : 300;
  //
  //
  //   $img = Image::make($url)->resize($newWidth,null,function ($constraint) {
  //   $constraint->aspectRatio();
  //   })->stream();

    //$uniqueName = md5($url);
    //$tempFilename = $uniqueName ."_". $quality . "." . $suffix;
    // $tempFilename = md5($url) . "." . $suffix;
    //
    // Storage::disk('local')
    //           ->put($tempFilename, (string) $img, 'public');
    //
    // $size = Storage::disk('local')->size($tempFilename);

    // while ($size > 250000){
    //   Storage::disk('local')->delete($tempFilename);
    //   $quality-=10;
    //   if ($quality == 0) $quality = 5;
    //   if ($quality == -5) $quality = 2;
    //   if ($quality < -5) {
    //
    //   }
    //
    //   $img = Image::make($url)->stream($suffix,$quality);
    //
    //   $tempFilename = $uniqueName . "_" . $quality . "." . $suffix;
    //
    //   Storage::disk('local')
    //             ->put($tempFilename, (string) $img, 'public');
    //
    //   $size = Storage::disk('local')->size($tempFilename);
    // }

    //dump("Final Size : $size");


  //}



});
//
// $app->get('/translate/{phrase}', function ($phrase) use ($app) {
//
//     // $data = [
//     //   'q' => urldecode($phrase),
//     //   'key' => config('picrun.googleapis_key'),
//     // ];
//     //
//     // $response = Curl::to(config('picrun.google_detect_api_url'))
//     //     ->withData($data)
//     //     ->post();
//     //
//     // $response = json_decode($response);
//     //
//     // dd($response);
//
//     $data = [
//       'q' => urldecode($phrase),
//       'key' => config('picrun.googleapis_key'),
//       'target' => 'en',
//       'format' => 'text',
//     ];
//
//     $response = Curl::to(config('picrun.google_translate_api_url'))
//         ->withData($data)
//         ->get();
//
//     $response = json_decode($response);
//
//     if (isset($response->data->translations[0])) {
//         dump($response->data->translations[0]->translatedText);
//         dump($response->data->translations[0]->detectedSourceLanguage);
//     }
//     dd($response);
//     return $response;
// });
//
// $app->get('/videos/{phrase}', function ($phrase) use ($app) {
//     $phrase = trim(urldecode($phrase));
//
//     $data = [
//       'q' => $phrase,
//       'key' => config('picrun.googleapis_key'),
//       'part' => 'snippet',
//       'maxResults' => 30
//     ];
//
//     $response = Curl::to(config('picrun.youtube_search_api_url'))
//         ->withData($data)
//         ->get();

//    $response = json_decode($response);

//     return $response;
// });
//
// $app->get('/gifs/{phrase}', function ($phrase) use ($app) {
//     $phrase = trim(urldecode($phrase));
//
//     $data = [
//       'q' => $phrase,
//       'api_key' => config('picrun.giphy_api_key'),
//     ];
//
//     $response = Curl::to(config('picrun.giphy_api_url'))
//         ->withData($data)
//         ->get();
//
//     return $response;
// });
//
// $app->get('/stickers/{phrase}', function ($phrase) use ($app) {
//     $phrase = trim(urldecode($phrase));
//
//     $data = [
//       'q' => $phrase,
//       'api_key' => config('picrun.giphy_api_key'),
//     ];
//
//     $response = Curl::to(config('picrun.giphy_stickers_api_url'))
//         ->withData($data)
//         ->get();
//
//     return $response;
// });
