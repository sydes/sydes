<?php

namespace App\L10n\Plural;

trait Rule0
{
    private $pluralsCount = 1;

    final public function plural($n)
    {
        return 0;
    }
}
