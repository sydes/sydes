<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Mailer\Models;

use Module\Entity\Models\Entity;

class EmailTemplate extends Entity
{
    protected $fields = [
        'event'    => [
            'type'     => 'EntityRef',
            'settings' => [
                'label'    => 'email_event',
                'target'   => EmailEvent::class,
                'many'     => false,
                'required' => true,
            ],
            'position' => 1,
        ],
        'status'      => [
            'type'     => 'YesNo',
            'settings' => [
                'label'   => 'status',
                'default' => 1,
            ],
            'position' => 2,
        ],
        'from'        => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_from',
                'default'  => '{default_from}',
                'required' => true,
            ],
            'position' => 3,
        ],
        'to'          => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_to',
                'default'  => '{default_to}',
                'multiple' => true,
                'required' => true,
            ],
            'position' => 4,
        ],
        'subject'     => [
            'type'     => 'Text',
            'settings' => [
                'label'    => 'email_subject',
                'required' => true,
            ],
            'position' => 5,
        ],
        'message'     => [
            'type'     => 'Text',
            'settings' => [
                'label'    => 'email_message',
                'rows'     => 12,
                'required' => true,
            ],
            'position' => 6,
        ],
        'message_type' => [
            'type'     => 'List',
            'settings' => [
                'label' => 'email_type',
                'items' => [
                    'text' => 'text',
                    'html' => 'html',
                ],
            ],
            'position' => 7,
        ],
        'cc'          => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_cc',
                'multiple' => true,
            ],
            'position' => 3,
        ],
        'bcc'         => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_bcc',
                'multiple' => true,
            ],
            'position' => 3,
        ],
        'reply_to'     => [
            'type'     => 'Email',
            'settings' => [
                'label' => 'email_reply_to',
            ],
            'position' => 3,
        ],
    ];
}
