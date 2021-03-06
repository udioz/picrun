<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      Commands\InitWords::class,
      Commands\BuildEnglishDictionary::class,
      Commands\TranslateDictionary::class,
      Commands\CompleteWordImages::class,
      Commands\Test::class,
      Commands\TransferWordsOld2New::class,
      Commands\UploadMissingImages::class,
      \Laravelista\LumenVendorPublish\VendorPublishCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
