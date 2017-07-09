<?php

namespace App\Listeners;

use App\Events\WordCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Dictionary;
use LanguageDetection\Language;

class CreateDictionaryForWord
{
    protected $languageDetector;
    protected $yandexService;

    public function __construct(Language $ld)
    {
        $this->languageDetector = $ld;
        $this->yandexService = app('App\Picrun\Yandex\YandexService');

    }


    public function handle(WordCreated $event)
    {
        $wholePhrase = false;
        if (count_words($event->word->name) > 1){
            $isNoun = 1;
            $wholePhrase = true;
        }

        if (!$wholePhrase) {

          $dictionary = Dictionary::where('word',$event->word->name)->first();

          if (!$dictionary) {
              $langs = $this->languageDetector->detect($event->word->name)
                        ->close();
              $lang = isset(array_keys($langs)[0]) ? array_keys($langs)[0] : 'en';

              if ($lang != 'en') {
                  $response = $this->yandexService
                      ->translate($event->word->name,$lang.'-en');

                  $response = json_decode($response);
                  $englishTranslatedWord = isset($response->text[0]) ? $response->text[0] : 'never';
              } else {
                  $englishTranslatedWord = $event->word->name;
              }

              $response = $this->yandexService->dictionary($englishTranslatedWord);

              $response = json_decode($response);

              if (is_object($response))
                  $isNoun = isset($response->def[0]->pos) ? $response->def[0]->pos : false;
              else
                  $isNoun = false;

              $dictionary = new Dictionary;
              $dictionary->word = $event->word->name;
              $dictionary->language_code = $lang;
              $dictionary->is_noun = ($isNoun == 'noun');
              $dictionary->save();

          }
          $isNoun = $dictionary->is_noun;

        }

        $event->word->is_noun = $isNoun;
        $event->word->save();
    }
}
