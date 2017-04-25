<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Plugin\Fields;

class ListField extends FieldBase
{
    protected $fieldSettings = [
        'canDisplay' => ['select', 'checkboxList', 'radioList'],
        'display' => 'select',
        'items' => [],
    ];

    public function getField()
    {
        $display = $this->settings['display'];
        return \H::$display($this->name, $this->value, $this->settings['items']);
    }
}
