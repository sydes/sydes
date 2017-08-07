<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Sydes\Database\Entity\Field;

class TextListField extends Field
{
    protected $contains = 'array';

    public function defaultInput()
    {
        return \H::textarea($this->name, implode("\n", $this->value), ['required'=>$this->settings('required')]);
    }

    public function defaultOutput()
    {
        return '<div>'.implode(', ', $this->value).'</div>';
    }
}
