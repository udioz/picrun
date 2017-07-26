<?php

# app/Models/WordImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\WordImageCreated;
use Watson\Rememberable\Rememberable;

use Illuminate\Support\Facades\Log;
use App\Events\WordCreated;

final class WordImage extends Model
{
    use Rememberable;

    protected $events = [
      "created" => WordImageCreated::class
    ];

    public static function getByWordAsync($word){
        $counter=1;
        $rawImages = [];

        while (count($rawImages) <= 10 && $counter < 20) {
          $counter++;
          if ($_SESSION['deviceOS'] == 1) { // iphone
              $rawImages = static::where([
                  ['word_id','=',$word->id],
                  ['image_type','!=','g']
                ])
                ->get();
          } else {
              $rawImages = static::where('word_id',$word->id)->get();
          }
          if (count($rawImages) <= 10) usleep(100000);
        }

        return static::normalize($rawImages);
    }

    public static function getByWord($word,$counter = 0)
    {
        Log::info($word->id .' '. $word->name ,compact('counter'));
        if ($_SESSION['deviceOS'] == 1) { // iphone
            $rawImages = static::where([
                ['word_id','=',$word->id],
                ['image_type','!=','g']
              ])
              ->get();
        } else {
            $rawImages = static::where('word_id',$word->id)->get();
        }
        if (count($rawImages) <= 10 ){
            event(new WordCreated($word));
            Log::info($word->id .' '. $word->name . ' 2nd',compact('counter'));
            if ($counter == 0)
              static::getByWord($word,1);
        }
        return static::normalize($rawImages);
    }

    protected static function normalize($rawImages)
    {
        $images = array();

        foreach ($rawImages as $image) {
          if (isset($image->s3_path)){
            $url = config('picrun.aws_path') . $image->s3_path;
          } else {
            $url = $image->url;
            event(new WordImageCreated($image));
          }

          $images[] = [
            'url' => $url
          ];

        }
        return $images;
    }


}
