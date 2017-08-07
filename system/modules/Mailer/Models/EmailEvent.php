<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Mailer\Models;

use Sydes\Database\Entity\Model;

class EmailEvent extends Model
{
    protected $fields = [
        'code' => [
            'type' => 'Primary',
            'settings' => [
                'label' => 'event_code',
            ],
            'weight' => 1,
        ],
        'name' => [
            'type' => 'Text',
            'settings' => [
                'label' => 'event_name',
                'required' => true,
            ],
            'weight' => 2,
        ],
        'fields' => [
            'type' => 'Text',
            'settings' => [
                'label' => 'event_fields',
                'rows' => 12,
                'required' => true,
            ],
            'weight' => 3,
        ],
        'templates' => [
            'type' => 'EntityRelation',
            'settings' => [
                'relation' => 'has_many',
                'target'   => EmailTemplate::class,
                'on_key'   => 'event',
            ],
            'weight' => 3,
        ],
    ];
}
