<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Field;

class TextField extends BaseField
{
    public function getField()
    {
        return \H::textInput($this->name, $this->value);
    }

    public function getConfigurator()
    {
        // TODO: Implement getConfigurator() method.
    }
}
