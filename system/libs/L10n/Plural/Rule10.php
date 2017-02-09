<?php

namespace App\L10n\Plural;

trait Rule10
{
    protected $pluralsCount = 4;

    final public function plural($n)
    {
        return $n % 100 == 1 ? 0 : ($n % 100 == 2 ? 1 : ($n % 100 == 3 || $n % 100 == 4 ? 2 : 3));
    }
}
