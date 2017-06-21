<?php

# app/Models/Word.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\WordCreated;
use App\Models\Dictionary;

final class Word extends Model
{
    protected $events = [
      "created" => WordCreated::class
    ];

}
