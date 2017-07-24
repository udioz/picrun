<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
      'App\Events\WordCreated' => [
        'App\Listeners\TranslateWord',
        'App\Listeners\CreateDictionaryForWord',
        'App\Listeners\CreateMediaForWord',
      ],
      'App\Events\WordImageCreated' => [
        'App\Listeners\SaveWordImage',
      ],
    ];
}
