<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Field;

class TextString extends Field
{
    public function __construct($value)
    {
        $this->value = $value;
        parent::__construct($value);
    }

    public function getView($params)
    {
        return $this->value;
    }

    public function getInput($params)
    {
        return \H::text($this->name, $this->value, [
            'class' => 'form-control',
        ]);
    }
}
