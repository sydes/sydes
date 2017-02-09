<?php

namespace App\L10n\Plural;

trait Rule15
{
    protected $pluralsCount = 2;

    final public function plural($n)
    {
        return $n % 10 == 1 && $n % 100 != 11 ? 0 : 1;
    }
}
