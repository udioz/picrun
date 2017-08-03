<?php

# app/Models/WordImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\WordImageCreated;
use Watson\Rememberable\Rememberable;

use Illuminate\Support\Facades\Log;
use App\Events\WordCreated;
use DB;

final class WordImage extends Model
{
    use Rememberable;

    protected $events = [
      "created" => WordImageCreated::class
    ];

    public static function getByWordAsync($word){
        $counter=1;
        $rawImages = [];

        while (count($rawImages) <= 10 && $counter < 40) {
          $counter++;
          if ($_SESSION['deviceOS'] == 1) { // iphone
              $rawImages = static::where([
                  ['word_id','=',$word->id],
                  ['image_type','!=','g'],
                  ['image_type','!=','gs']
                ])
                ->get();
          } else {
              $rawImages = static::where('word_id',$word->id)->get();
          }
          if (count($rawImages) <= 10) usleep(100000);
        }

        return static::normalize($rawImages);
    }

    public static function getByWord($word)
    {
        if ($_SESSION['deviceOS'] == 1) { // iphone
            $rawImages = static::where([
                ['word_id','=',$word->id],
                ['image_type','!=','g'],
                ['image_type','!=','gs']
              ])
              ->get();
        } else {
            $rawImages = static::where('word_id',$word->id)->get();
        }

        if (!$word->satisfied && !static::enough($word->id)){
            event(new WordCreated($word));
            return static::getByWordAsync($word);
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

        shuffle($images);
        return $images;
    }

    protected static function enough($wordID) {
      $enough = true;

      $imageTypeCounts = DB::table('word_images')
                   ->select(DB::raw('count(*) as count, image_type'))
                   ->where('word_id', $wordID)
                   ->groupBy('image_type')
                   ->get();

       $counts=[];
       foreach ($imageTypeCounts as $item){
         $counts[$item->image_type] = $item->count;
       }

       if (!isset($counts['gs'])
          || !isset($counts['i'])
          || !isset($counts['s'])) {
          $enough = false;
       }

       if ($_SESSION['deviceOS'] != 1 && !isset($counts['g'])){
          $enough = false;
       }


       if (isset($counts['i'])) {
         if ($counts['i'] < 10) {
           $enough = false;
         }
       }

       return $enough;
    }

}
