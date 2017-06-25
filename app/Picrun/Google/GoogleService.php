<?php

namespace App\Picrun\Google;
use Curl;

class GoogleService
{
    protected $apiKey;
    protected $apiUrl;
    protected $imagesCX;
    protected $videosCX;

    public function __construct($apiKey,$apiUrl,$imagesCX,$videosCX)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->imagesCX = $imagesCX;
        $this->videosCX = $videosCX;
    }

    public function getMedia($phrase)
    {
        return [
          'images' => $this->getImages($phrase),
          'videos' => $this->getVideos($phrase)
        ];
    }

    public function getImages($phrase)
    {
        for ($page=1 ; $page <= 3 ; $page ++)
        {
          $response = Curl::to($this->apiUrl)
            ->withData([
              'q' => $phrase,
              'cx' => $this->imagesCX,
              'key' => $this->apiKey,
              'searchType' => 'image',
              'imgType' => 'photo',
              'fields' => 'items(link,mime,image(byteSize))',
              'imgSize' => 'medium',
              'num' => 10,
              'start' => $page * 10
            ])
            ->get();

            if (json_decode($response)->items)
              $items[] = json_decode($response)->items;

        }
        return array_dot($items);
    }

    public function getVideos($phrase)
    {
        for ($page=1 ; $page <= 3 ; $page ++)
        {
            $response = Curl::to($this->apiUrl)
              ->withData([
                'q' => $phrase,
                'cx' => $this->videosCX,
                'key' => $this->apiKey,
                'fields' => 'items(title,link,pagemap/cse_thumbnail/src)',
                'num' => 10,
                'start' => ($page == 1) ? 1 : $page * 10
              ])
              ->get();

            if (json_decode($response)->items)
              $items[] = json_decode($response)->items;
        }

        return array_dot($items);

    }


}
