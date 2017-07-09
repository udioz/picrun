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

        if (count($words) <= 4 && count($words) > 1)
          $words[] = $phrase;

        $oneWordOnly = (count($words) == 1) ? true : false;

        foreach($words as $phrasePart)
        {
            $word = Word::where('name',$phrasePart)->first();

            if (!$word) {
                // New phrase - add it to DB
                $word = new Word;
                $word->name = $phrasePart;
                $word->usage_counter = 1;
                $word->save();

                $images = WordImage::getByWordAsync($word->id);

                if (count_words($word->name) > 1 || $oneWordOnly) {
                    $videos = WordVideo::getByWordAsync($word->id);
                } else {
                    $videos = [];
                }

                $isNoun = $word->is_noun;
            } else {
                // Word already exist in DB
                $word->usage_counter++;
                $word->save();

                $images = WordImage::getByWord($word->id);

                if (count_words($word->name) > 1 || $oneWordOnly) {
                    $videos = WordVideo::getByWord($word->id);
                } else {
                    $videos = [];
                }
                $isNoun = 1;
            }


            $response[] = [
                'en' => $phrasePart,
                'original' => $phrasePart,
                'is_noun' => $isNoun,
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

    public function adminSearch($phrase)
    {
        $json = $this->search($phrase);
        $data = json_decode($json,true);
        return view('admin.search',compact('data'));
    }
} // end of class
