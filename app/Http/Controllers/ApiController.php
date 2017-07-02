<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Models\WordImage;
use App\Models\WordVideo;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
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

    public function search ($phrase)
    {
        // Clean phrase
        $phrase = urldecode($phrase);
        $phrase = preg_replace('/[^\w\s]+/u','' , $phrase);

        $words = explode(" ",$phrase);
        if (count($words) <= 4)
          $words[] = $phrase;

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
                'is_noun' => $word->is_noun,
                'images' => $images,
                'videos' => $videos
            ];


        }

        $response = ['response' => $response];

        // Use this line to return json without extra slashes.
         return json_encode($response,JSON_UNESCAPED_SLASHES);

        //return $response;

    } // end of function search

    public function removeImage(Request $request)
    {
        $this->validate($request,[
            'id'  => 'required'
        ]);

        return [
          'code' => '200'
        ];
    }

} // end of class
