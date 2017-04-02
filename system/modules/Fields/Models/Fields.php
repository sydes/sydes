<?php

namespace Module\Fields\Models;

class Fields
{
    public function register($type, $params)
    {
        $app = app();
        $fields = isset($app['formFields']) ? $app['formFields'] : [];
        $fields[$type] = $params;
        app()['formFields'] = $fields;

        return $this;
    }
}
