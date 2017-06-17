<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Plugin\Fields;

class YesNoField extends FieldBase
{
    public function getInput()
    {
        return \H::yesNo($this->name, $this->value);
    }
}
