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
  $googleService = app('App\Picrun\Google\GoogleService');
  $media = $googleService->getImages('love gif');
  foreach($media as $image)
  {
      dump($image->link,$image->image->byteSize);
  }
  dd();






  // $wi = App\Models\WordImage::orderBy('created_at','desc  ')->first();
  $wi = App\Models\WordImage::find(84594);

  $img = Image::make($wi->url);

  // im image too big then resize
  if ($wi->image_file_size > config('picrun.max_image_size')) {
      // $newWidth = config('picrun.max_image_size') / $wi->image_file_size * $img->width();
      // $img->resize($newWidth,null, function ($constraint){
      //     $constraint->aspectRatio();
      // });
      $img->resize($img->width(),null, function ($constraint){
          $constraint->aspectRatio();
      });
  }

  return $img->response('jpg');

});
