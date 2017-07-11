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

          $dictionary = Dictionary::where('word',$event->word->name)->first();

          if (!$dictionary) {
              $lang = detect_language($event->word->name);

              // $data = [
              //   'q' => $event->word->name,
              //   'key' => config('picrun.googleapis_key'),
              //   'target' => 'en',
              //   'format' => 'text',
              // ];
              //
              // $response = Curl::to(config('picrun.google_translate_api_url'))
              //     ->withData($data)
              //     ->get();
              //
              // dd($response);
              // if (isset($response->data->translations[0])) {
              //     $englishTranslatedWord = $response->data->translations[0]->translatedText;
              //     $lang = $response->data->translations[0]->detectedSourceLanguage;
              //     if ($lang='iw') $lang = 'he';
              // }

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

              if (is_object($response)) {
                  $isNoun = isset($response->def[0]->pos) ? $response->def[0]->pos : false;
              } else {
                  $isNoun = false;
              }

              $dictionary = new Dictionary;
              $dictionary->word = $event->word->name;
              $dictionary->language_code = $lang;
              $dictionary->is_noun = ($isNoun == 'noun');
              $dictionary->save();

          }
          $event->word->is_noun = $dictionary->is_noun;
          $event->word->save();
        }
    } // end function handle
}
