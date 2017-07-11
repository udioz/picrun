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

    public function __construct($word,$page)
    {
        $this->word = $word;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $phrase = trim(urldecode($this->word->name));

        $data = [
          'q' => $phrase,
          'api_key' => config('picrun.giphy_api_key'),
        ];

        Log::info($this->word->id . ' Request Giphy API: ',$data);

        $response = Curl::to(config('picrun.giphy_api_url'))
            ->withData($data)
            ->get();

        //Log::info('Response Google API: ',compact('response'));

        if (!isset(json_decode($response)->items)) return false;

        foreach(json_decode($response)->items as $item)
        {
            try {
              //Log::info($this->word->id . ' Item Link: ',['link' => $item->link]);
              $wordImage = new WordImage;
              $wordImage->word_id = $this->word->id;
              $wordImage->url = $item->link;
              $wordImage->image_file_size = $item->image->byteSize;
              $wordImage->image_content_type = $item->mime;
              $wordImage->image_file_name = basename($item->link);
              $wordImage->image_updated_at = date('Ymdhis');
              $wordImage->md5_duplicate_helper = md5($wordImage->word_id . $wordImage->url);
              $wordImage->save();
            } catch (\Exception $e) {
                if ($e->getCode() == '23000') {
                    Log::info($this->word->id . ' Duplicate detected',['md5' => md5($wordImage->word_id . $wordImage->url)]);
                }

            }

        }

    }
}