<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Mailer\Models;

use Module\Entity\Models\Entity;

class EmailEvent extends Entity
{
    protected $fields = [
        'code' => [
            'type' => 'Text',
            'settings' => [
                'label' => 'event_code',
                'required' => true,
            ],
            'position' => 1,
        ],
        'name' => [
            'type' => 'Text',
            'settings' => [
                'label' => 'event_name',
                'required' => true,
            ],
            'position' => 2,
        ],
        'fields' => [
            'type' => 'Text',
            'settings' => [
                'label' => 'event_fields',
                'rows' => 12,
                'required' => true,
            ],
            'position' => 3,
        ],
    ];
}
