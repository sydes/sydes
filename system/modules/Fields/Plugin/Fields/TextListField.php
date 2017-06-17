<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Plugin\Fields;

class TextListField extends FieldBase
{
    protected $contains = 'array';

    public function getInput()
    {
        return \H::textarea($this->name, implode("\n", $this->value), $this->settings);
    }

    public function defaultFormatter()
    {
        return '<div>'.implode(', ', $this->value).'</div>';
    }
}
