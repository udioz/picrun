<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Models\WordImage;
use App\Models\WordVideo;
use DB;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index ($phrase)
    {
        $words = str_word_count($phrase,1);
        if (count($words) <= 4)
          $word[] = $phrase;

        foreach($words as $phrasePart)
        {
            $word = Word::where('name',$phrasePart)->first();
            if (!$word) {
                DB::beginTransaction();

                // New phrase - add it to DB
                $word = new Word;
                $word->name = $phrasePart;
                $word->usage_counter = 1;
                $word->save();

                DB::commit();
            } else {
                $word->usage_counter++;
                $word->save();
            }

            // word in DB - get images and videos.
            $images = WordImage::getByWord($word->id);
            $videos = WordVideo::getByWord($word->id);

            $response[] = [
                'en' => $phrasePart,
                'original' => $phrasePart,
                'is_noun' => false,
                'images' => $images,
                'videos' => $videos
            ];


        }

        $response = ['response' => $response];

        return $response;
    } // end of function index

} // end of class
