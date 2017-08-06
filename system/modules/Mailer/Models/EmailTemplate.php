<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Mailer\Models;

use Sydes\Database\Entity\Model;

class EmailTemplate extends Model
{
    protected $fields = [
        'event'    => [
            'type'     => 'EntityRelation',
            'settings' => [
                'label'    => 'email_event',
                'standalone' => 0,
                'relation' => 'belongs_to',
                'target'   => EmailEvent::class,
                'on_key'   => 'code',
                'title'    => 'name',
                'required' => true,
            ],
            'weight' => 1,
        ],
        'status'      => [
            'type'     => 'YesNo',
            'settings' => [
                'label'   => 'status',
                'default' => 1,
            ],
            'weight' => 2,
        ],
        'from'        => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_from',
                'default'  => '{default_from}',
                'required' => true,
            ],
            'weight' => 3,
        ],
        'to'          => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_to',
                'default'  => '{default_to}',
                'multiple' => true,
                'required' => true,
            ],
            'weight' => 4,
        ],
        'subject'     => [
            'type'     => 'Text',
            'settings' => [
                'label'    => 'email_subject',
                'required' => true,
                'translatable' => 1,
            ],
            'weight' => 5,
        ],
        'message'     => [
            'type'     => 'Text',
            'settings' => [
                'label'    => 'email_message',
                'rows'     => 12,
                'required' => true,
                'translatable' => true,
            ],
            'weight' => 6,
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
            'weight' => 7,
        ],
        'cc'          => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_cc',
                'multiple' => true,
            ],
            'weight' => 3,
        ],
        'bcc'         => [
            'type'     => 'Email',
            'settings' => [
                'label'    => 'email_bcc',
                'multiple' => true,
            ],
            'weight' => 3,
        ],
        'reply_to'     => [
            'type'     => 'Email',
            'settings' => [
                'label' => 'email_reply_to',
            ],
            'weight' => 3,
        ],
    ];
}
