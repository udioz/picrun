<?php

namespace App\Jobs;

use Curl;
use App\Models\WordVideo;
use Illuminate\Support\Facades\Log;


class GoogleVideosJob extends Job
{
    public $tries = 3;
    public $timeout =   10;

    protected $page;
    protected $word;

    public function __construct($word,$page)
    {
        $this->page = $page;
        $this->word = $word;
    }

    public function handle()
    {
        $phrase = trim(urldecode($this->word->name));

        $data = [
          'q' => $phrase,
          'cx' => config('picrun.google_videos_cx'),
          'key' => config('picrun.googleapis_key'),
          'fields' => 'items(title,link,pagemap/cse_thumbnail/src)',
          'num' => 10,
          'start' => $this->page
        ];

        Log::info($this->word->id . ' Request Google Videos API: ',$data);

        $response = Curl::to(config('picrun.googleapis_url'))
            ->withData($data)
            ->get();

        //Log::info('Response Google API: ',compact('response'));

        if (!isset(json_decode($response)->items)) return false;

        foreach (json_decode($response)->items as $item) {
            if (isset($item->pagemap)) {
                $wordVideo = new WordVideo;
                $wordVideo->word_id = $this->word->id;
                $wordVideo->title = $item->title;
                $wordVideo->preview_url = $item->pagemap->cse_thumbnail[0]->src;
                $wordVideo->url = $item->link;
                $wordVideo->save();
            }
        }

    }
}
