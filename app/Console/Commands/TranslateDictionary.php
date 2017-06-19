<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dictionary;
use DB;
use Illuminate\Support\Facades\Log;
use LanguageDetection\Language;


class TranslateDictionary extends Command
{
    protected $signature = 'picrun:translate-dictionary {to_language_code} {start_id=1}';

    protected $description = 'Build dictionary table using wordNet for Picrun';

    protected $ld;

    public function __construct(Language $ld)
    {
        parent::__construct();

        $this->ld = $ld;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        DB::table('dictionary')
          ->where([
              ['language_code','=','en'],
              ['id','>=',$this->argument('start_id')]
          ])
          ->orderBy('word')
          ->chunk('200',function($words){
            foreach ($words as $word)
            {

                Log::info(static::class . ' Processing word: ',['word'=>$word->word]);

                // if there is more than one word, skip to the next word.
                if (str_word_count($word->word) > 1) continue;

                // if the word is all CAPS, probably an abbreviation, skip to the next word.
                if (mb_strtoupper($word->word, 'utf-8') == $word->word) continue;

                Log::info(static::class . ' Yandexing lemma: ',['word'=>$word->word]);
                $yandexService = app('App\Picrun\Yandex\YandexService');
                $response = $yandexService->translate($word->word,'en-'.$this->argument('to_language_code'));

                $response = json_decode($response);

                if ($response->code != 200) continue;

                // If there are no letters in response. skip to next one...
                Log::info(static::class . ' Response text: ',['word'=>$response->text[0]]);

                $translatedWord = urldecode($response->text[0]);

                // Clean speical characters from response.
                $translatedWord = preg_replace('/[^\w\s]+/u','' , $translatedWord);

                // if the translated Word is empty after cleaning.
                if (strlen($translatedWord) == 0) continue;

                // Checking if translation really ocuured. if not skip to next...
                $langs = $this->ld->detect($translatedWord)
                          ->whitelist('en',$this->argument('to_language_code'))
                          ->close();
                if (!(array_keys($langs)[0] == 'he' && $langs['en'] == 0)) continue;

                try {
                  $dictionary = new Dictionary;
                  $dictionary->word = $translatedWord;
                  $dictionary->language_code = $this->argument('to_language_code');
                  $dictionary->translation_of = $word->id;
                  $dictionary->is_noun = $word->is_noun;
                  $dictionary->save();
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == '23000') {
                        Log::info('Lemma is already in table. Skipping...');
                    } else {
                        Log::error($e);
                    }
                }

                Log::info(static::class . ' End Processing lemma: ',['lemma'=>$word->word]);
            } // end foreach

        });
    } // end function handle
}
