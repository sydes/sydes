<?php

namespace App\L10n\Plural;

trait Rule3
{
    private $pluralsCount = 3;

    final public function plural($n)
    {
        return $n % 10 == 1 && $n % 100 != 11 ? 1 : ($n != 0 ? 2 : 0);
    }
}
