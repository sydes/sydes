<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Models\Field;

class NumberField extends Field
{
    public function input()
    {
        return \H::numberInput($this->name, $this->value, ['required'=>$this->getSettings('required')]);
    }
}
