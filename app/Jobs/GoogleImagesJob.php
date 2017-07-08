<?php

namespace App\Jobs;

use Curl;
use App\Models\WordImage;
use Illuminate\Support\Facades\Log;


class GoogleImagesJob extends Job
{
    public $tries = 3;
    public $timeout =   10;

    protected $page;
    protected $word;
    protected $phraseExtra;

    public function __construct($word,$page,$phraseExtra='')
    {
        $this->page = $page;
        $this->word = $word;
        $this->phraseExtra = $phraseExtra;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $phrase = trim(urldecode($this->word->name) . ' ' . $this->phraseExtra);
        $fileType = ($this->phraseExtra == 'gifs') ?  'gif' : 'jpg,png,jpeg';
        $imgSize  = ($this->phraseExtra == 'gifs') ?  'medium' : 'large';

        $data = [
          'q' => $phrase,
          'cx' => config('picrun.google_images_cx'),
          'key' => config('picrun.googleapis_key'),
          'searchType' => 'image',
          'imgType' => 'photo',
          'fields' => 'items(link,mime,image(byteSize))',
          'imgSize' => $imgSize,
          'fileType' => $fileType,
          'num' => 10,
          'start' => $this->page
        ];

        Log::info($this->word->id . ' Request Google Images API: ',$data);

        $response = Curl::to(config('picrun.googleapis_url'))
            ->withData($data)
            ->get();

        //Log::info('Response Google API: ',compact('response'));

        if (!isset(json_decode($response)->items)) return false;

        foreach(json_decode($response)->items as $item)
        {
            //Log::info($this->word->id . ' Size Item : ',['byteSize' => $item->image->byteSize,'minByteSize'=> config('picrun.google_min_bytesize')]);
            if (($item->image->byteSize > config('picrun.google_min_bytesize')))
            //     && ($item->image->byteSize < config('picrun.google_max_bytesize')))
            //if (true)
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
}
