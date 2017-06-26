<?php

# app/Models/WordImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\WordImageCreated;

final class WordImage extends Model
{
    protected $events = [
      "created" => WordImageCreated::class
    ];

    public static function getByWord($wordId)
    {
        $images = array();
        $rawImages = static::where('word_id',$wordId)->get();

        foreach ($rawImages as $image) {
          $images[] = [
            'url' => $image->url  //'http://159.203.126.231/images/' . $image->id . '.jpg'
          ];

        }
        return $images;
    }


}
