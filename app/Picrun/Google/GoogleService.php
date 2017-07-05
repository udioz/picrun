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

    public function setImagesRequired($imagesRequired)
    {
        $this->imagesRequired = $imagesRequired;
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

    public function getAllImagesTypes($phrase)
    {
        return [
          'images'   => $this->getImages($phrase),
          'gifs'     => $this->getGifs($phrase),
          'stickers' => $this->getStickers($phrase),
        ];
    }

    protected function _getImages($phrase,$imagesRequired)
    {
        $weHaveEnough = false;
        $page = 0;
        $items = array();
        $onlyLinks = array();

        //dd(urldecode($phrase));

        while (!$weHaveEnough) {
          $page++;

          if ($page >= 5) break;

          // dump([
          //   'q' => $phrase,
          //   'cx' => $this->imagesCX,
          //   'key' => $this->apiKey,
          //   'searchType' => 'image',
          //   'imgType' => 'photo',
          //   'fields' => 'items(link,mime,image(byteSize))',
          //   'imgSize' => 'large',
          //   'fileType' => 'jpg,png,jpeg',
          //   'num' => 10,
          //   'start' => $page
          // ]);

          $response = Curl::to($this->apiUrl)
              ->withData([
                'q' => urldecode($phrase),
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

              //dump($response);

              if (!isset(json_decode($response)->items)) continue;

              foreach(json_decode($response)->items as $item)
              {
                  if (in_array($item->link,$onlyLinks)) continue;

                  if (($item->image->byteSize > $this->minByteSize)
                      && ($item->image->byteSize < $this->maxByteSize))
                  {

                      $onlyLinks[] = $item->link;
                      $items[] = $item;

                      if (count($items) == $imagesRequired) break;
                  }
              }

              $weHaveEnough = (count($items) >= $imagesRequired);
        }

        //dump($page);
        return $items;
    }

    public function getImages($phrase)
    {
        return $this->_getImages($phrase,$this->imagesRequired);
    }

    public function getGifs($phrase)
    {
        return $this->_getImages($phrase . ' gifs',$this->gifsRequired);
    }

    public function getStickers($phrase)
    {
      return $this->_getImages($phrase . ' stickers',$this->stickersRequired);
    }

    public function getVideos($phrase)
    {
        $items = array();

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

            if (!isset(json_decode($response)->items))
                continue;

            foreach (json_decode($response)->items as $item) {
                if (isset($item->pagemap)) {
                    $items[] = $item;
                }
            }


        }
        return $items;

    }


}
