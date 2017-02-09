<?php

namespace App\L10n\Plural;

trait Rule11
{
    protected $pluralsCount = 5;

    final public function plural($n)
    {
        return $n == 1 ? 0 : ($n == 2 ? 1 : ($n >= 3 && $n <= 6 ? 2 : ($n >= 7 && $n <= 10 ? 3 : 4)));
    }
}
