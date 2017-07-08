<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Picrun\Google\GoogleService;

class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

      $this->app->singleton(GoogleService::class, function ($app){
          return new GoogleService(
            config('picrun.googleapis_key'),
            config('picrun.googleapis_url'),
            config('picrun.google_images_cx'),
            config('picrun.google_videos_cx'),
            config('picrun.google_images_required'),
            config('picrun.google_gifs_required'),
            config('picrun.google_stickers_required'),
            config('picrun.google_min_bytesize'),
            config('picrun.google_max_bytesize')
          );
      });

      $this->app->singleton(GoogleServiceAsync::class, function ($app){
                  return new GoogleServiceAsync(
                    config('picrun.googleapis_key'),
                    config('picrun.googleapis_url'),
                    config('picrun.google_images_cx'),
                    config('picrun.google_videos_cx'),
                    config('picrun.google_images_required'),
                    config('picrun.google_gifs_required'),
                    config('picrun.google_stickers_required'),
                    config('picrun.google_min_bytesize'),
                    config('picrun.google_max_bytesize')
                  );
              });
    }
}
