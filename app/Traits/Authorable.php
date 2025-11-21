<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait Authorable
{
    public function author(): MorphTo
    {
        return $this->morphTo();
    }
}