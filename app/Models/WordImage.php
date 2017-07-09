<?php

# app/Models/WordImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\WordImageCreated;
use Watson\Rememberable\Rememberable;

final class WordImage extends Model
{
    use Rememberable;

    protected $events = [
      "created" => WordImageCreated::class
    ];

    public static function getByWordAsync($wordId){
        do {
          $rawImages = static::where('word_id',$wordId)->get();
        } while (count($rawImages) <= 12);

        return static::normalize($rawImages);
    }

    public static function getByWord($wordId)
    {
        $rawImages = static::where('word_id',$wordId)->get();
        //if (count($rawImages) < 30)
        return static::normalize($rawImages);
    }

    protected static function normalize($rawImages)
    {
        $images = array();

        foreach ($rawImages as $image) {
          $url = isset($image->s3_path) ? config('picrun.aws_path') . $image->s3_path : $image->url;

          $images[] = [
            'url' => $url
          ];

        }
        return $images;
    }


}
