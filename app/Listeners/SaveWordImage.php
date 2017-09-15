<?php

namespace App\Listeners;

use App\Events\WordImageCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
//use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;


class SaveWordImage implements ShouldQueue
{
    public $tries = 3;
    public $timeout = 20;


    public function __construct()
    {
        //
    }

    public function handle(WordImageCreated $event)
    {
        try {
          $suffix = imageSuffix($event->wordImage->image_content_type);
          $path = $event->wordImage->created_at->format('Y/m/d/') .
                 $event->wordImage->id . '.' . $suffix;
          $isGif = ($event->wordImage->image_type == 'g');
          $resized = false;

          if (!$isGif) {

              if ($event->wordImage->image_file_size > config('picrun.google_max_bytesize')) {
                $newWidth = ($event->wordImage->width > 600) ? 600 : 300;

                $img = Image::make($event->wordImage->url)->resize($newWidth,null,function ($constraint) {
                $constraint->aspectRatio();
                })->stream();

                $resized = true;

              } else {
                $img = Image::make($event->wordImage->url)
                          ->stream($suffix); // <-- Key point
              }

              Storage::put($path, (string) $img, 'public');

              if ($resized){
                $event->wordImage->image_file_size = Storage::size($path);
                $event->wordImage->width = $newWidth;
              }


          } else { // isGif == true

            Storage::put($path,file_get_contents($event->wordImage->url),'public');
          }

          $event->wordImage->s3_path = $path;
          $event->wordImage->save();
          unset($img);

        } catch (\Intervention\Image\Exception\NotReadableException $e) {

          Log::info('Not Readable Exception caught. Deleting Image ' , ['id' => $event->wordImage->id]);
          $event->wordImage->delete();

        } catch (\Exception $e) {
            Log::info('Exception!!! ' . $e->getMessage() , ['id' => $event->wordImage->id]);
        }
    }

}
