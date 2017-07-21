<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Models\Field;

class EmailField extends Field
{
    public function input()
    {
        return \H::textInput($this->name, $this->value, ['required'=>$this->getSettings('required')]);
        // TODO with custom validation rules like comma separated many mails and {tokens}
    }
}
