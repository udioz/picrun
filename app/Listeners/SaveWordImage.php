<?php

namespace App\Listeners;

use App\Events\WordImageCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;


class SaveWordImage implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(WordImageCreated $event)
    {
        $suffix = imageSuffix($event->wordImage->image_content_type);

        try {
            $img = Image::make($event->wordImage->url)
                       ->stream($suffix); // <-- Key point
        } catch (Exception $e) {
            //
        }

        $path = $event->wordImage->created_at->format('Y/m/d/') .
                $event->wordImage->id . '.' . $suffix;
        //
        Storage::put($path, (string) $img, 'public');
        $event->wordImage->s3_path = $path;
        $event->wordImage->save();
        $img->destroy();
    }

    // protected function format($wordImage)
    // {
    //     $format = explode("/",$wordImage->image_content_type);
    //     $format = $format[1];
    //     if ($format == 'jpeg') $format = 'jpg';
    //     return $format;
    // }
}
