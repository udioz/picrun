<?php

namespace App\Listeners;

use App\Events\WordImageCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class SaveWordImage
{
    public function __construct()
    {
        //
    }

    public function handle(WordImageCreated $event)
    {
        // $img = Image::make($event->wordImage->url)
        //   ->resize(300, 200);
        // Storage::put('images/',$event->wordImage->id, $img);
        // dd();

    }
}
