<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Models\Field;

class ListField extends Field
{
    protected $settings = [
        'canDisplay' => ['select', 'checkboxList', 'radioList'],
        'display' => 'select',
        'items' => [],
    ];

    public function formInput()
    {
        $display = $this->getSettings('display');
        return \H::$display($this->name, $this->value, $this->getSettings('items'));
    }
}
