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
              config('picrun.google_videos_cx')
            );
        });
    }
}
