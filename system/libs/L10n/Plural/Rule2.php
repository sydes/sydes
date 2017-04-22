<?php

namespace Sydes\L10n\Plural;

trait Rule2
{
    protected $pluralsCount = 2;

    final public function plural($n)
    {
        return $n > 1 ? 1 : 0;
    }
}
