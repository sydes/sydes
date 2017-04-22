<?php

namespace Sydes\L10n\Plural;

trait Rule6
{
    protected $pluralsCount = 3;

    final public function plural($n)
    {
        return $n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && ($n % 100 < 10 || $n % 100 >= 20) ? 2 : 1);
    }
}
