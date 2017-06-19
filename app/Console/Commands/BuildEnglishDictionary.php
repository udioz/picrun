<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;
use App\Models\Dictionary;
use DB;
use Illuminate\Support\Facades\Log;

class BuildEnglishDictionary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'picrun:build-english-dictionary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build dictionary table using wordNet for Picrun';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::connection('mysql_wn')->table('word')
          ->orderBy('lemma')
          ->chunk('500',function($wnWords){

            foreach ($wnWords as $wnWord)
            {
                Log::info(static::class . ' Processing lemma: ',['lemma'=>$wnWord->lemma]);

                // Filtering some trash from wordNet DB
                // Skip lemmas that does not starts with a letter
                if (!preg_match("/^[A-Za-z]+/", $wnWord->lemma)) continue;

                // Skip lemmas that are short. 2 letters and below.
                if (strlen($wnWord->lemma) <= 2) continue;

                $result = DB::connection('mysql_wn')->table('word')
                    ->select(DB::raw("if (instr(group_concat(lexname),'noun'),1,0) as is_noun"))
                    ->leftJoin('sense','sense.wordno','=','word.wordno')
                    ->leftJoin('synset','synset.synsetno','=','sense.synsetno')
                    ->leftJoin('lexname','lexname.lexno','=','synset.lexno')
                    ->where('lemma','=',$wnWord->lemma)
                    ->groupBy('word.wordno')
                    ->first();

                try {
                  $dictionary = new Dictionary;
                  $dictionary->word = $wnWord->lemma;
                  $dictionary->language_code = 'en';
                  $dictionary->translation_of = 0;
                  $dictionary->is_noun = $result->is_noun;
                  $dictionary->save();
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == '23000') {
                        Log::info('Lemma is already in table. Skipping...');
                    } else {
                        Log::error($e);
                    }
                }

                Log::info(static::class . ' End Processing lemma: ',['lemma'=>$wnWord->lemma]);

            } // end foreach

        });
    } // end function handle
}
