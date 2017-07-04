<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;
use App\Models\WordImage;
use App\Events\WordImageCreated;
use DB;
use Illuminate\Support\Facades\Log;

class Test extends Command
{
    protected $signature = 'picrun:test';

    protected $description = 'Test';

    protected $imagesRequired = 30;

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
        $wordImage = WordImage::where('id',49)->first();
        event(new WordImageCreated($wordImage));
    } // end function handle
}
