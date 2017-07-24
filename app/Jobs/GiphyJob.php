<?php

namespace App\Jobs;

use Curl;
use App\Models\WordImage;
use Illuminate\Support\Facades\Log;


class GiphyJob extends Job
{
    public $tries = 3;
    public $timeout =   10;

    protected $word;
    protected $englishTranslatedWord;

    public function __construct($word)
    {
        $this->word = $word;
        $this->englishTranslatedWord = $this->word->englishTranslatedWord;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $phrase = trim($this->englishTranslatedWord);

        if (empty($phrase)) return;

        $data = [
          'q' => $phrase,
          'api_key' => config('picrun.giphy_api_key'),
        ];

        Log::info($this->word->id . ' Request Giphy API: ',$data);

        $response = Curl::to(config('picrun.giphy_api_url'))
            ->withData($data)
            ->get();

        //Log::info('Response Google API: ',compact('response'));

        if (!isset(json_decode($response)->data)) return false;

        foreach(json_decode($response)->data as $item)
        {
            try {
              //Log::info($this->word->id . ' Item Link: ',['link' => $item->link]);
              $wordImage = new WordImage;
              $wordImage->word_id = $this->word->id;
              $wordImage->url = $item->images->downsized_small->mp4;
              $wordImage->image_file_size = $item->images->downsized_small->mp4_size;
              $wordImage->image_content_type = 'video/mp4';
              $wordImage->image_file_name = basename($item->images->downsized_small->mp4);
              $wordImage->image_updated_at = date('Ymdhis');
              $wordImage->md5_duplicate_helper = md5($wordImage->word_id . $wordImage->url);
              $wordImage->image_type = 'g';
              $wordImage->save();
            } catch (\Exception $e) {
                if ($e->getCode() == '23000') {
                    Log::info($this->word->id . ' Duplicate detected',['md5' => md5($wordImage->word_id . $wordImage->url)]);
                }
            }

        }

    }
}
