<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Models\Field;

class TextListField extends Field
{
    protected $contains = 'array';

    public function formInput()
    {
        return \H::textarea($this->name, implode("\n", $this->value), $this->settings);
    }

    public function defaultFormatter()
    {
        return '<div>'.implode(', ', $this->value).'</div>';
    }
}
