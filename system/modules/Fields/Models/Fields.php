<?php

namespace Module\Fields\Models;

class Fields
{
    public function find()
    {
        $fields = app('cache')->remember('fields', function () {
            $core = glob(app('dir.system').'/modules/*/Plugin/Fields/*Field.php');
            $user = glob(app('dir.module').'/*/Plugin/Fields/*Field.php');
            $fields = array_merge($core, $user);
            $fieldNames = [];

            foreach ($fields as $field) {
                preg_match('!Fields/(\w*)Field!', $field, $name);
                preg_match('!/(\w*)/Plugin/Fields/(\w*)!', $field, $class);
                $fieldNames[$name[1]] = 'Module'.str_replace('/', '\\', $class[0]);
            }

            return $fieldNames;
        }, 3600);

        app()->set('formFields', $fields);
    }
}
