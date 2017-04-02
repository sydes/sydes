<?php

namespace Module\Fields\Models;

class Fields
{
    public function register($type, $params)
    {
        app()['formFields'][$type] = $params;
    }
}
