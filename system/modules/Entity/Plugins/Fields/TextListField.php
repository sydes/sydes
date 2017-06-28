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

    public function input()
    {
        return \H::textarea($this->name, implode("\n", $this->value), ['required'=>$this->getSettings('required')]);
    }

    public function defaultFormatter()
    {
        return '<div>'.implode(', ', $this->value).'</div>';
    }
}
