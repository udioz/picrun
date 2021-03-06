<?php

# app/Models/WordImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WordVideo extends Model
{
    public static function getByWordAsync($wordId)
    {
        $counter=1;
        $rawVideos = [];

        while (count($rawVideos) <= 10 && $counter < 40) {
          $counter++;
          $rawVideos = static::where('word_id',$wordId)->get();
          if (count($rawVideos) <= 10) usleep(100000);
        }

        return static::normalize($rawVideos);
    }

    public static function getByWord($wordId)
    {
        $rawVideos = static::where('word_id',$wordId)->get();
        return static::normalize($rawVideos);
    }

    protected static function normalize($rawVideos)
    {
        $videos = array();

        foreach ($rawVideos as $video) {
            $videos[] = [
                'url' => $video->url,
                'preview_url' => $video->preview_url,
                'title' => $video->title
            ];
        }
        return $videos;
    }

}
