<?php

namespace App\Listeners;

use App\Events\WordCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\WordImage;
use App\Models\WordVideo;

class CreateMediaForWord
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function handle(WordCreated $event)
    {
        $googleService = app('App\Picrun\Google\GoogleService');
        $media = $googleService->getMedia($event->word->name);
        $allWordImages = array_merge($media['images'],$media['gifs'],$media['stickers']);

        foreach ($allWordImages as $item)
        {
            $wordImage = new WordImage;
            $wordImage->word_id = $event->word->id;
            $wordImage->url = $item->link;
            $wordImage->image_file_size = $item->image->byteSize;
            $wordImage->image_content_type = $item->mime;
            $wordImage->image_file_name = basename($item->link);
            $wordImage->image_updated_at = date('Ymdhis');
            $wordImage->save();
        }

        foreach ($media['videos'] as $item)
        {
            $wordVideo = new WordVideo;
            $wordVideo->word_id = $event->word->id;
            $wordVideo->title = $item->title;
            $wordVideo->preview_url = $item->pagemap->cse_thumbnail[0]->src;
            $wordVideo->url = $item->link;
            $wordVideo->save();
        }

    }
}
