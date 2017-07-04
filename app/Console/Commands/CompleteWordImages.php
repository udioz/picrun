<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;
use App\Models\WordImage;
use App\Events\WordImageCreated;
use DB;
use Illuminate\Support\Facades\Log;

class CompleteWordImages extends Command
{
    protected $signature = 'picrun:complete-word-images {start_id=1} {chunk=100} {--chunkonly}';

    protected $description = 'Complete Words Images and upload to s3. To do only one word set the second arg to 1.';

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
        DB::table('words')
          ->where('id','>=',$this->argument('start_id'))
          ->orderBy('id')->chunk($this->argument('chunk'),function($words){
            foreach ($words as $word)
            {
                Log::info('Processing word: ',['word'=> $word->name , 'id' => $word->id]);

                $images = DB::table('word_images')->where('word_id',$word->id)->get();
                Log::info('Count of images: ',['count'=> count($images)]);

                // Make sure all images are on s3
                foreach ($images as $image) {
                    if (!isset($image->s3_path)) {
                      $wordImage = WordImage::where('id',$image->id)->first();
                      event(new WordImageCreated($wordImage));
                    }
                }

                // Complete missing images
                if (count($images) < $this->imagesRequired)
                {
                    $googleService = app('App\Picrun\Google\GoogleService');
                    $googleService->setImagesRequired($this->imagesRequired - count($images));
                    $moreImages = $googleService->getImages($word->name);

                    foreach ($moreImages as $image) {
                        $wordImage = new WordImage;
                        $wordImage->word_id = $word->id;
                        $wordImage->url = $image->link;
                        $wordImage->image_file_size = $image->image->byteSize;
                        $wordImage->image_content_type = $image->mime;
                        $wordImage->image_file_name = basename($image->link);
                        $wordImage->image_updated_at = date('Ymdhis');
                        $wordImage->save();
                        unset($wordImage);
                    }

                }

                Log::info('End Processing word: ',['word'=>$word->name]);

            } // end foreach

            if ($this->option('chunkonly'))
                return false;
        });
    } // end function handle
}
