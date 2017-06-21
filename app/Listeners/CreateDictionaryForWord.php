<?php

namespace App\Listeners;

use App\Events\WordCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Dictionary;

class CreateDictionaryForWord
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function handle(WordCreated $event)
    {

        $dictionary = Dictionary::where('word',$event->word->name)->first();

        if (!$dictionary) {

            $yandexService = app('App\Picrun\Yandex\YandexService');
            $response = $yandexService->dictionary($event->word->name);

            $response = json_decode($response);

            if (is_object($response))
                $isNoun = isset($response->def[0]->pos) ? $response->def[0]->pos : false;
            else
                $isNoun = false;

            $dictionary = new Dictionary;
            $dictionary->word = $this->name;
            $dictionary->language_code = 'en';
            $dictionary->is_noun = ($isNoun == 'noun');
            $dictionary->save();
        }

        $event->word->is_noun = $dictionary->is_noun;
        $event->word->save();
    }
}
