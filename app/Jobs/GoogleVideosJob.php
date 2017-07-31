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

        // $data = [
        //   'q' => $phrase,
        //   'cx' => config('picrun.google_videos_cx'),
        //   'key' => config('picrun.googleapis_key'),
        //   'fields' => 'items(title,link,pagemap/cse_thumbnail/src)',
        //   'num' => 10,
        //   'start' => $this->page
        // ];

        $data = [
          'q' => $phrase,
          'key' => config('picrun.googleapis_key'),
          'part' => 'snippet',
          'maxResults' => 30
        ];

        Log::info($this->word->id . ' Request Google Videos API: ',$data);

        $response = Curl::to(config('picrun.youtube_search_api_url'))
            ->withData($data)
            ->get();

        //Log::info('Response Google API: ',compact('response'));

        if (!isset(json_decode($response)->items)) return false;

        foreach (json_decode($response)->items as $item) {
              try {
                $wordVideo = new WordVideo;
                $wordVideo->word_id = $this->word->id;
                $wordVideo->title = $item->snippet->title;
                $wordVideo->preview_url = $item->snippet->thumbnails->default->url;
                $wordVideo->url = 'https://www.youtube.com/watch?v=' . $item->id->videoId;
                $wordVideo->md5_duplicate_helper = md5($wordVideo->word_id . $wordVideo->url);
                $wordVideo->save();

              } catch (\Exception $e) {
                  if ($e->getCode() == '23000') {
                      Log::info($this->word->id . ' Duplicate video detected',['md5' => md5($wordVideo->word_id . $wordVideo->url)]);
                  }
              }
        }
    }
}
