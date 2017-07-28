<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Api\Field;

class TextField extends Field
{
    protected $settings = [
        'rows' => 1,
    ];

    public function defaultInput()
    {
        if ($this->settings('rows') == 1) {
            return \H::textInput($this->name, $this->value, ['required'=>$this->settings('required')]);
        } else {
            return \H::textarea($this->name, $this->value, [
                'required' => $this->settings('required'),
                'rows' => $this->settings('rows'),
            ]);
        }
    }
}
