<?php

namespace App\L10n\Plural;

trait Rule0
{
    protected $pluralsCount = 1;

    final public function plural($n)
    {
        return 0;
    }
}
