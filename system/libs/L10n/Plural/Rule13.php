<?php

namespace App\L10n\Plural;

trait Rule13
{
    private $pluralsCount = 4;

    final public function plural($n)
    {
        return $n == 1 ? 0 : ($n == 0 || $n % 100 > 0 && $n % 100 <= 10 ? 1 : ($n % 100 > 10 && $n % 100 < 20 ? 2 : 3));
    }
}
