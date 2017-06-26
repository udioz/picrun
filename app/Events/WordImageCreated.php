<?php

namespace App\Events;
use App\Models\WordImage;

class WordImageCreated extends Event
{

    public $wordImage;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WordImage $wordImage)
    {
        $this->wordImage = $wordImage;
    }
}
