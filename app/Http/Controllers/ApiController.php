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

    public function search ($phrase,$deviceOS)
    {
        session_start();
        if (isset($_SESSION['language'])) unset($_SESSION['language']);
        $_SESSION['deviceOS'] = $deviceOS;


        $phrase = phrase_sanitize($phrase);
        $_SESSION['phrase'] = $phrase;

        $words = explode(" ",$phrase);
        $wordsCount = count($words);

        if ($wordsCount > 1)
            $words[] = $phrase;

        $words = array_unique($words);

        foreach($words as $phrasePart)
        {
            $word = Word::where('name',$phrasePart)->first();

            if (!$word) {
                // New phrase - add it to DB
                $word = new Word;
                $word->name = $phrasePart;
                $word->usage_counter = 1;
                $word->phraseWordsCount = $wordsCount;
                if (count_words($phrasePart) > 1)
                  $word->isPhrase = true;
                $word->save();
                $isNew = true;
            } else {
                // Word already exist in DB
                $word->usage_counter++;
                $word->save();
                $word->phraseWordsCount = $wordsCount;
                if (count_words($phrasePart) > 1)
                  $word->isPhrase = true;
                $isNew = false;
            }

            switch ($word->phraseWordsCount) {
                case 1:
                    $getImages = true;
                    $getVideos = true;
                    $isJsonNoun = false;
                    break;
                case 2:
                    $getImages = true;
                    $getVideos = $word->isPhrase;
                    $isJsonNoun = !$word->isPhrase;
                    break;
                case 3:
                    $getImages = ($word->is_noun || $word->isPhrase) || !$isNew;
                    $getVideos = $word->isPhrase;
                    $isJsonNoun = !$word->isPhrase;
                    break;
                default: // 4 and above
                    $getImages = ($word->is_noun && !$word->isPhrase) || !$isNew;
                    $getVideos = $word->isPhrase;
                    $isJsonNoun = !$word->isPhrase;
                    break;
            }

            if ($getImages) {
                if ($isNew) {
                  $images = WordImage::getByWordAsync($word);
                } else {
                  $images = WordImage::getByWord($word);
                }
            } else {
              $images = [];
            }

            if ($getVideos) {
                if ($isNew) {
                  $videos = WordVideo::getByWordAsync($word->id);
                } else {
                  $videos = WordVideo::getByWord($word->id);
                }
            } else {
              $videos=[];
            }

            if (($getImages && count($images) > 0) || ($getVideos && count($videos) > 0)) {
              $response[] = [
                  'en' => $word->englishTranslatedWord,
                  'original' => $phrasePart,
                  'is_noun' => $isJsonNoun,
                  'images' => $images,
                  'videos' => $videos
              ];
            }
            //dd($response);

        }
        if (isset($response))
          $response = ['response' => $response];
        else
          $response = ['error' => 'no response found'];
        // Use this line to return json without extra slashes.
        //dd($response);
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
        $json = $this->search($phrase,3);
        $data = json_decode($json,true);
        return view('admin.search',compact('data'));
    }
} // end of class
