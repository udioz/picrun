<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;
use DB;
use Illuminate\Support\Facades\Log;

class InitWords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'picrun:init-words';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize Words Images and Videos for Picrun';

    //protected $word;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        //$this->word = $word;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::connection('mysql_wn')->table('word')->orderBy('lemma')->chunk('100',function($wnWords){
            foreach ($wnWords as $wnWord)
            {
                // TODO sanitize words before processing!!

                Log::info('Processing lemma: ',['lemma'=>$wnWord->lemma]);

                try {
                    DB::beginTransaction();

                    $word = new Word;
                    $word->name = $wnWord->lemma;
                    $word->usage_counter = 0;
                    $word->save();

                    DB::commit();
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == '23000') {
                        Log::info('Lemma is already in table. Skipping...');
                    } else {
                        Log::error($e);
                    }
                }

                Log::info('End Processing lemma: ',['lemma'=>$wnWord->lemma]);
            } // end foreach
            return false;
        });
    } // end function handle
}
