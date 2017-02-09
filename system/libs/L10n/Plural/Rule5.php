<?php

namespace App\L10n\Plural;

trait Rule5
{
    protected $pluralsCount = 3;

    final public function plural($n)
    {
        return $n == 1 ? 0 : ($n == 0 || ($n % 100 > 0 && $n % 100 < 20) ? 1 : 2);
    }
}
