<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Plugins\Fields;

class DateField extends DateTimeField
{
    protected $settings = [
        'format' => 'Y-m-d',
    ];

    public function defaultInput()
    {
        return \H::textInput(
            $this->name,
            $this->value->format($this->settings('format')),
            [
                'required' => $this->settings('required'),
                'class'    => ['date-picker'],
            ]
        );
    }
}
