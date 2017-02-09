<?php

namespace App\L10n\Plural;

trait Rule8
{
    protected $pluralsCount = 3;

    final public function plural($n)
    {
        return $n == 1 ? 0 : ($n >= 2 && $n <= 4 ? 1 : 2);
    }
}
