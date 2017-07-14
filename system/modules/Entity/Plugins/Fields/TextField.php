<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Models\Field;

class TextField extends Field
{
    protected $settings = [
        'rows' => 1,
    ];

    public function input()
    {
        if ($this->getSettings('rows') == 1) {
            return \H::textInput($this->name, $this->value, ['required'=>$this->getSettings('required')]);
        } else {
            return \H::textarea($this->name, implode("\n", $this->value), [
                'required' => $this->getSettings('required'),
                'rows' => $this->getSettings('rows'),
            ]);
        }
    }
}
