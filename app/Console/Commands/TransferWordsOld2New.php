<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;
use App\Models\WordImage;
use App\Models\WordVideo;
use DB;
use Illuminate\Support\Facades\Log;

class TransferWordsOld2New extends Command
{
    protected $signature = 'picrun:transfer-words-old-2-new {start_id=1} {chunk=100} {--chunkonly}';

    protected $description = 'Transfer words from old server to new. To do only one word set the second arg to 1.';

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
        DB::connection('old_server')->table('words')
          ->where('id','>=',$this->argument('start_id'))
          ->orderBy('id')->chunk($this->argument('chunk'),function($wordsFromOld){
            foreach ($wordsFromOld as $wordFromOld)
            {
                Log::info('Start processing',[$wordFromOld->id => $wordFromOld->name]);

                // Check if word exist in new server
                $wordFromNew = DB::table('words')->where('name',$wordFromOld->name)->first();
                if ($wordFromNew) continue;

                // Get all word images from old
                $wordImagesFromOld = DB::connection('old_server')->table('word_images')
                                       ->where('word_id',$wordFromOld->id)->get();

                if (count($wordImagesFromOld) == 0) continue;

                // Get all word videos from old
                $wordVideosFromOld = DB::connection('old_server')->table('word_videos')
                                       ->where('word_id',$wordFromOld->id)->get();


                if (count($wordVideosFromOld) == 0) continue;

                // if we reach this point it means there is no such word in new server
                // and that we have images and videos from old for this word.
                $ret = DB::table('words')->insert(
                  [
                    'name' => $wordFromOld->name,
                    'created_at' => date('Ymdhis',time()),
                    'updated_at' => date('Ymdhis',time()),
                    'is_noun' => $wordFromOld->is_noun,
                    'usage_counter' => 0,
                    'satisfied' => 1
                  ]
                );

                $newWord = DB::table('words')->where('name',$wordFromOld->name)->first();

                foreach ($wordImagesFromOld as $wordImageFromOld) {
                    // Handle image_content_type is null cases.
                    if (!$wordImageFromOld->image_content_type) {
                      $exploded = explode('.',$wordImageFromOld->url);
                      $wordImageFromOld->image_content_type = 'image/'.end($exploded);
                    }

                    $imageType = ($wordImageFromOld->image_content_type == 'image/gif') ? 'g' : 'i';

                    try {
                      $wordImage = new WordImage;
                      $wordImage->word_id = $newWord->id;
                      $wordImage->url = $wordImageFromOld->url;
                      $wordImage->image_file_size = $wordImageFromOld->image_file_size;
                      $wordImage->image_content_type = $wordImageFromOld->image_content_type;
                      $wordImage->image_file_name = $wordImageFromOld->image_file_name;
                      $wordImage->image_updated_at = date('Ymdhis');
                      $wordImage->md5_duplicate_helper = md5($wordImage->word_id . $wordImage->url);
                      $wordImage->image_type = $imageType;

                      $ret = $wordImage->save();

                    } catch (\Exception $e) {
                        if ($e->getCode() == '23000') {
                            Log::info('Image not saved');
                        }
                    }
                }

                foreach ($wordVideosFromOld as $wordVideoFromOld) {
                  if (!str_contains($wordVideoFromOld->url, 'watch?v=')) continue;

                  try {
                    $wordVideo = new WordVideo;
                    $wordVideo->word_id = $newWord->id;
                    $wordVideo->title = $wordVideoFromOld->title;
                    $wordVideo->preview_url = $wordVideoFromOld->preview_url;
                    $wordVideo->url = $wordVideoFromOld->url;
                    $wordVideo->md5_duplicate_helper = md5($wordVideo->word_id . $wordVideo->url);
                    $wordVideo->save();

                  } catch (\Exception $e) {
                      if ($e->getCode() == '23000') {
                          Log::info('Video not saved');
                      }
                  }


                }

                Log::info('End processing',[$wordFromOld->id => $wordFromOld->name]);
            } // end foreach

            if ($this->option('chunkonly'))
                return false;
        });
    } // end function handle
}
