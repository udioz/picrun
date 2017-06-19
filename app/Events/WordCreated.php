<?php

namespace App\Events;
use App\Models\Word;

class WordCreated extends Event
{

    public $word;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Word $word)
    {
        $this->word = $word;
    }
}
