<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WordImage;
use DB;
use Illuminate\Support\Facades\Log;
use App\Events\WordImageCreated;
use Illuminate\Support\Facades\Storage;


class UploadMissingImages extends Command
{
    protected $signature = 'picrun:upload-missing-images {start_id=1} {chunk=100} {--chunkonly}';

    protected $description = 'Upload Missing Images to S3. To do only one word set the second arg to 1.';

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
        DB::table('word_images')
          ->where('id','>=',$this->argument('start_id'))
          ->orderBy('id')->chunk($this->argument('chunk'),function($wordImages){
            foreach ($wordImages as $wordImage)
            {
                Log::info('Start processing',[$wordImage->id => $wordImage->id]);

                if ($wordImage->s3_path == null) continue;

                if (!Storage::exists($wordImage->s3_path)) {
                  Log::info('Uploading ',[$wordImage->id => $wordImage->id]);
                  event(new WordImageCreated(WordImage::find($wordImage->id)));
                } else {
                  Log::info('Exists ',[$wordImage->id => $wordImage->id]);
                }

                Log::info('End processing',[$wordImage->id => $wordImage->id]);
            } // end foreach

            if ($this->option('chunkonly'))
                return false;
        });
    } // end function handle
}
