<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Picrun\Yandex\YandexService;

class YandexServiceProvider extends ServiceProvider
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

        $this->app->singleton(YandexService::class, function ($app){
            return new YandexService(
              config('picrun.yandexapis_translate_key'),
              config('picrun.yandexapis_translate_url'),
              config('picrun.yandexapis_dictionary_key'),
              config('picrun.yandexapis_dictionary_url')
            );
        });
    }
}
