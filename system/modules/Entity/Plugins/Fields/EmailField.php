<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Api\Field;

class EmailField extends Field
{
    public function defaultInput()
    {
        return \H::textInput($this->name, $this->value, ['required'=>$this->settings('required')]);
        // TODO with custom validation rules like comma separated many mails and {tokens}
    }
}
