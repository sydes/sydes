<?php

namespace App\L10n\Plural;

trait Rule1
{
    protected $pluralsCount = 2;

    final public function plural($n)
    {
        return $n != 1 ? 1 : 0;
    }
}
