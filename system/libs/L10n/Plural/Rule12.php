<?php

namespace App\L10n\Plural;

trait Rule12
{
    private $pluralsCount = 6;

    final public function plural($n)
    {
        return $n == 0 ? 5 : ($n == 1 ? 0 : ($n == 2 ? 1 : ($n % 100 >= 3 && $n % 100 <= 10 ? 2 :
            ($n % 100 >= 11 && $n % 100 <= 99 ? 3 : 4))));
    }
}
