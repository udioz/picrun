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
  $img = file_get_contents('https://i.ytimg.com/vi/iXAbte4QXKs/maxresdefault.jpg');
  Storage::disk('local')->put('file.jpg',$img);
//  dd();
  while (Storage::disk('local')->size('file.jpg') > 150000){
      $img = Image::make(Storage::disk('local')->get('file.jpg'));
      $img->stream('jpg',80);
      Storage::disk('local')->put('file.jpg',$img);
  }

  // $img = Image::make(Storage::disk('local')->get('file.jpg'));
  // $img->stream('jpg',100);
  // Storage::disk('local')->put('file2.jpg',$img);

  // $img = Image::make('http://img.mako.co.il/2016/08/23/eyalgolan_i.jpg')
  //           ->stream('jpg'); // <-- Key point
  //$img = Image::make(Storage::disk('local')->get('file.jpg'));
  //dd(get_class($img));

});
