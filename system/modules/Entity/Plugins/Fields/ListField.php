<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Sydes\Database\Entity\Field;

class ListField extends Field
{
    protected $settings = [
        'canDisplay' => ['select', 'checkboxList', 'radioList'],
        'display' => 'select',
        'items' => [],
    ];

    public function defaultInput()
    {
        $display = $this->settings('display');
        return \H::$display($this->name, $this->value, $this->settings('items'));
    }
}
