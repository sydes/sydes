<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Api\Field;

class NumberField extends Field
{
    public function defaultInput()
    {
        return \H::numberInput($this->name, $this->value, ['required'=>$this->settings('required')]);
    }
}
