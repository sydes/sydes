<?php

namespace Sydes\L10n\Plural;

trait Rule14
{
    protected $pluralsCount = 3;

    final public function plural($n)
    {
        return $n % 10 == 1 ? 0 : ($n % 10 == 2 ? 1 : 2);
    }
}
