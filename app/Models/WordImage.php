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

    public static function getByWord($wordId)
    {
        $images = array();
        $rawImages = static::where('word_id',$wordId)->get();

        foreach ($rawImages as $image) {
          $url = isset($image->s3_path) ? config('picrun.aws_path') . $image->s3_path : $image->url;

          $images[] = [
            'url' => $url
          ];

        }
        return $images;
    }


}
