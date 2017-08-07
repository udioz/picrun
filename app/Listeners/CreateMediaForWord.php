<?php

namespace App\Listeners;

use App\Events\WordCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\GoogleImagesJob;
use App\Jobs\GoogleVideosJob;
use App\Jobs\GiphyJob;

use Illuminate\Support\Facades\Log;

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
        switch ($event->word->phraseWordsCount) {
            case 1:
                $getImages = true;
                $getVideos = true;
                break;
            case 2:
                $getImages = true;
                $getVideos = true; //$event->word->isPhrase;
                break;
            case 3:
                $getImages = true; //$event->word->is_noun || $event->word->isPhrase;
                $getVideos = true; //$event->word->isPhrase;
                break;
            default: // 4 and above
                $getImages = !$event->word->isPhrase; //$event->word->is_noun && !$event->word->isPhrase;
                $getVideos = true; //$event->word->isPhrase;
                break;
        }

        if ($getImages) {
            for ($i=1 ; $i <= 4 ; $i++) {
                $job = new GoogleImagesJob($event->word,$i);
                $job->onQueue('google_curls');
                dispatch($job);
            }

            // for ($i=1 ; $i <= 2 ; $i++) {
            //   $job = new GoogleImagesJob($event->word,$i,'gifs');//->onQueue('google_curls');
            //   $job->onQueue('google_curls');
            //   dispatch($job);
            // }

            $job = new GiphyJob($event->word);
            $job->onQueue('google_curls');
            dispatch($job);

            // $job = new GiphyJob($event->word,'stickers');
            // $job->onQueue('google_curls');
            // dispatch($job);


            for ($i=1 ; $i <= 2 ; $i++) {
              $job = new GoogleImagesJob($event->word,$i,'stickers');//->onQueue('google_curls');
              $job->onQueue('google_curls');
              dispatch($job);
            }
        }

        if ($getVideos) {
          $job = new GoogleVideosJob($event->word);
          $job->onQueue('google_curls');
          dispatch($job);
        }

        $event->word->satisfied = true;
        $event->word->save();

    } // end function handle
}
