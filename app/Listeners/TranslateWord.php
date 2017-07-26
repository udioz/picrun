<?php

namespace App\Listeners;

use App\Events\WordCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Dictionary;
use Curl;


class TranslateWord
{

    public function __construct()
    {
      //
    }


    public function handle(WordCreated $event)
    {
        if (!$event->word->isPhrase || $event->word->phraseWordsCount == 2) {

          $needsTranslation = false;

          if (!isset($_SESSION['language']))
            $_SESSION['language'] = google_detect_language($_SESSION['phrase']);

          $event->word->language = $_SESSION['language'];

          if ($event->word->language == 'unknown') {
            $event->word->englishTranslatedWord = $event->word->name;
            return;
          }

          $dictionary = Dictionary::where([
              ['word','=',$event->word->name],
              ['language_code','=',$event->word->language]
            ])->first();
          
          if ($dictionary) {
            if ($event->word->language == 'en') {
                $event->word->englishTranslatedWord = $event->word->name;
            } elseif ($dictionary->translation_of > 0) {
                $translation = Dictionary::find($dictionary->translation_of);
                if ($translation) {
                    $event->word->englishTranslatedWord = $translation->word;
                } else {
                  $needsTranslation = true;
                }
            } else { // if translation_of == 0
              $needsTranslation = true;
            }
          }

          if (!$dictionary || $needsTranslation) {

              $data = [
                'q' => $event->word->name,
                'key' => config('picrun.googleapis_key'),
                'target' => 'en',
                'format' => 'text',
              ];

              $response = Curl::to(config('picrun.google_translate_api_url'))
                  ->withData($data)
                  ->get();

              $response = json_decode($response);

              if (isset($response->data->translations[0])) {
                  $event->word->englishTranslatedWord = $response->data->translations[0]->translatedText;
                  //$event->word->language = $response->data->translations[0]->detectedSourceLanguage;
                  //if ($event->word->language = 'iw') $event->word->language = 'he';
              } else {
                //$event->word->language = 'en';
                $event->word->englishTranslatedWord = $event->word->name;
              }
          }

          // Create dictionary for future use.
          if (!$dictionary) {
            // if not english. create english dict first and use id to create secind dict.
            if ($event->word->language != 'en') {
              $translation = Dictionary::where('word',$event->word->englishTranslatedWord)->first();
              if (!$translation) {
                $translation = new Dictionary;
                $translation->word = $event->word->englishTranslatedWord;
                $translation->language_code = 'en';
                $translation->save();
              }
            }

            $dictionary = new Dictionary;
            $dictionary->word = $event->word->name;
            $dictionary->language_code = $event->word->language;
            $dictionary->translation_of = isset($translation) ? $translation->id : 0;
            $dictionary->is_noun = isset($translation) ? $translation->is_noun : null;
            $dictionary->save();
          }
        }
    } // end function handle
}
