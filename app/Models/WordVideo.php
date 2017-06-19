<?php

# app/Models/WordImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WordVideo extends Model
{
    public static function getByWord($wordId)
    {
        $rawVideos = static::where('word_id',$wordId)->get();

        foreach($rawVideos as $video) {
          $videos[] = [
              'url' => $video->url,
              'preview_url' => $video->preview_url,
              'title' => $video->title
          ];
        }

        return $videos;
    }
}
