<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

class Fields
{
    public function find()
    {
        $fields = app('cache')->remember('fields', function () {
            $core = glob(app('dir.system').'/modules/Entity/Plugins/Fields/*Field.php');
            $user = glob(app('dir.module').'/*/Plugins/Fields/*Field.php');
            $fields = array_merge($core, $user);
            $fieldNames = [];

            foreach ($fields as $field) {
                preg_match('!Fields/(\w*)Field!', $field, $name);
                preg_match('!/\w*/Plugins/Fields/\w*!', $field, $class);
                $fieldNames[$name[1]] = 'Module'.str_replace('/', '\\', $class[0]);
            }

            return $fieldNames;
        }, 3600);

        app()->set('entity.fieldTypes', $fields);
    }
}
