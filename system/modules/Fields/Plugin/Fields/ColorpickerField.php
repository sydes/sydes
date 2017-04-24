<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Plugin\Fields;

class ColorpickerField extends FieldBase
{
    public function getField()
    {
        return \H::colorInput($this->name, $this->value);
    }
}
