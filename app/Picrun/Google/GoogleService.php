<?php

namespace App\Picrun\Google;
use Curl;

class GoogleService
{
    protected $apiKey;
    protected $apiUrl;
    protected $imagesCX;
    protected $videosCX;

    protected $imagesRequired;
    protected $gifsRequired;
    protected $stickersRequired;
    protected $minByteSize;
    protected $maxByteSize;

    public function __construct($apiKey,$apiUrl,$imagesCX,$videosCX,
                                $imagesRequired,$gifsRequired,$stickersRequired,
                                $minByteSize,$maxByteSize)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->imagesCX = $imagesCX;
        $this->videosCX = $videosCX;
        $this->imagesRequired = $imagesRequired;
        $this->gifsRequired = $gifsRequired;
        $this->stickersRequired = $stickersRequired;
        $this->minByteSize = $minByteSize;
        $this->maxByteSize = $maxByteSize;
    }

    public function getMedia($phrase)
    {
        return [
          'images'   => $this->getImages($phrase),
          'gifs'     => $this->getGifs($phrase),
          'stickers' => $this->getStickers($phrase),
          'videos'   => $this->getVideos($phrase)
        ];
    }

    public function getImages($phrase)
    {
        $weHaveEnough = false;
        $page = 0;

        while (!$weHaveEnough) {
          $page++;
          $response = Curl::to($this->apiUrl)
              ->withData([
                'q' => $phrase,
                'cx' => $this->imagesCX,
                'key' => $this->apiKey,
                'searchType' => 'image',
                'imgType' => 'photo',
                'fields' => 'items(link,mime,image(byteSize))',
                'imgSize' => 'large',
                'fileType' => 'jpg,png,jpeg',
                'num' => 10,
                'start' => $page
              ])
              ->get();

              foreach(json_decode($response)->items as $item)
              {
                  if (($item->image->byteSize > $this->minByteSize)
                      && ($item->image->byteSize < $this->maxByteSize))
                  {
                      $items[] = $item;

                      if (count($items) == $this->imagesRequired) break;
                  }
              }

              $weHaveEnough = (count($items) >= $this->imagesRequired);
        }

        return $items;
    }

    public function getGifs($phrase)
    {
        $weHaveEnough = false;
        $page = 0;

        while (!$weHaveEnough) {
          $page++;
          $response = Curl::to($this->apiUrl)
              ->withData([
                'q' => $phrase . ' gifs',
                'cx' => $this->imagesCX,
                'key' => $this->apiKey,
                'searchType' => 'image',
                'imgType' => 'photo',
                'fields' => 'items(link,mime,image(byteSize))',
                'imgSize' => 'large',
                'fileType' => 'gif',
                'num' => 10,
                'start' => $page
              ])
              ->get();

              foreach(json_decode($response)->items as $item)
              {
                  if (($item->image->byteSize > $this->minByteSize)
                      && ($item->image->byteSize < $this->maxByteSize))
                  {
                      $items[] = $item;

                      if (count($items) == $this->gifsRequired) break;
                  }
              }

              if ($items)
                $weHaveEnough = (count($items) >= $this->gifsRequired);
        }

        return $items;
    }

    public function getStickers($phrase)
    {
        $weHaveEnough = false;
        $page = 0;

        while (!$weHaveEnough) {
          $page++;
          $response = Curl::to($this->apiUrl)
              ->withData([
                'q' => $phrase . ' sticker',
                'cx' => $this->imagesCX,
                'key' => $this->apiKey,
                'searchType' => 'image',
                'imgType' => 'photo',
                'fields' => 'items(link,mime,image(byteSize))',
                'imgSize' => 'large',
                'num' => 10,
                'start' => $page
              ])
              ->get();

              foreach(json_decode($response)->items as $item)
              {
                  if (($item->image->byteSize > $this->minByteSize)
                      && ($item->image->byteSize < $this->maxByteSize))
                  {
                      $items[] = $item;

                      if (count($items) == $this->stickersRequired) break;
                  }
              }

              $weHaveEnough = (count($items) >= $this->stickersRequired);
        }

        return $items;
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
