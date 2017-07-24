<?php

namespace App\Listeners;

use App\Events\WordCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Dictionary;
use Curl;


class CreateDictionaryForWord
{
    protected $languageDetector;
    protected $yandexService;

    public function __construct()
    {
        $this->yandexService = app('App\Picrun\Yandex\YandexService');
    }


    public function handle(WordCreated $event)
    {
        // Go to yandex only for words (not phrases) and when the input is 3 words and above.
        if (!$event->word->isPhrase && $event->word->phraseWordsCount >= 3) {

          //$dictionary = Dictionary::where('word',$event->word->name)->first();
          $dictionary = Dictionary::where([
              ['word','=',$event->word->name],
              ['language_code','=',$event->word->language]
            ])->first();


          if ($dictionary->is_noun === null) {

              $response = $this->yandexService->dictionary($event->word->englishTranslatedWord);

              $response = json_decode($response);

              if (is_object($response)) {
                  $isNoun = isset($response->def[0]->pos) ? $response->def[0]->pos : true;
              } else {
                  $isNoun = 'noun';
              }

              $dictionary->is_noun = ($isNoun == 'noun');
              $dictionary->save();

          }
          $event->word->is_noun = $dictionary->is_noun;
          $event->word->save();
        }
    } // end function handle
}
