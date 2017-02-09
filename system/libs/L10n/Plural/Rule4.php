<?php

namespace App\L10n\Plural;

trait Rule4
{
    protected $pluralsCount = 4;

    final public function plural($n)
    {
        return $n == 1 || $n == 11 ? 0 : ($n == 2 || $n == 12 ? 1 : ($n > 0 && $n < 20 ? 2 : 3));
    }
}
