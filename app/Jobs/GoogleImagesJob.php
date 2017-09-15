<?php

namespace App\Jobs;

use Curl;
use App\Models\WordImage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;


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

    public function handle()
    {
        $phrase = trim(urldecode($this->word->name) . ' ' . $this->phraseExtra);
        $fileType  = ($this->phraseExtra == 'gifs') ?  'gif' : 'jpg,png,jpeg';
        $imgSize   = ($this->phraseExtra == 'gifs') ?  'medium' : 'large';
        $imageType = ($this->phraseExtra == '') ? 'i' : substr($this->phraseExtra,0,1);

        $data = [
          'q' => $phrase,
          'cx' => config('picrun.google_images_cx'),
          'key' => config('picrun.googleapis_key'),
          'searchType' => 'image',
          'imgType' => 'photo',
          'fields' => 'items(link,mime,image(byteSize,width))',
          // 'imgSize' => $imgSize,
          // 'fileType' => $fileType,
          'num' => 10,
          'start' => $this->page
        ];

        Log::info($this->word->id . ' Request Google Images API: ',$data);

        $response = Curl::to(config('picrun.googleapis_url'))
            ->withData($data)
            ->get();

        if (!isset(json_decode($response)->items)) return false;

        foreach(json_decode($response)->items as $item)
        {
            if ($item->mime == 'image/gif') continue;

            if ($item->mime == 'image/') {
              $item->mime = Image::make($item->link)->mime();
            }

            try {
              $wordImage = new WordImage;
              $wordImage->word_id = $this->word->id;
              $wordImage->url = $item->link;
              $wordImage->image_file_size = $item->image->byteSize;
              $wordImage->width = $item->image->width;
              $wordImage->image_content_type = $item->mime;
              $wordImage->image_file_name = basename($item->link);
              $wordImage->image_updated_at = date('Ymdhis');
              $wordImage->md5_duplicate_helper = md5($wordImage->word_id . $wordImage->url);
              $wordImage->image_type = $imageType;
              $wordImage->save();

            } catch (\Exception $e) {

                if ($e->getCode() == '23000') {
                    Log::info($this->word->id . ' Duplicate detected',['md5' => md5($wordImage->word_id . $wordImage->url)]);
                }
            }
        }

    }
}
