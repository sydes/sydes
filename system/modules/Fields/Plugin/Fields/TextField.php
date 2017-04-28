<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Plugin\Fields;

class TextField extends FieldBase
{
    public function getField()
    {
        return \H::textInput($this->name, $this->value, $this->settings);
    }
}
