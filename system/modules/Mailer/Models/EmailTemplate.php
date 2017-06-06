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
    public function __construct(array $attrs = [])
    {
        $this->fields = [
            'event_id' => [
                'label' => t('email_event'),
                'type' => 'Reference',
                'settings' => [
                    'target' => 'email_events',
                    'many' => false,
                    'required' => true,
                ],
                'position' => 1,
            ],
            'status' => [
                'label' => t('status'),
                'type' => 'YesNo',
                'settings' => [
                    'default' => 1,
                ],
                'position' => 2,
            ],
            'from' => [
                'label' => t('email_from'),
                'type' => 'Email',
                'settings' => [
                    'default' => '{default_from}',
                    'required' => true,
                ],
                'position' => 3,
            ],
            'to' => [
                'label' => t('email_to'),
                'type' => 'Email',
                'settings' => [
                    'default' => '{default_to}',
                    'multiple' => true,
                    'required' => true,
                ],
                'position' => 4,
            ],
            'subject' => [
                'label' => t('email_subject'),
                'type' => 'Text',
                'settings' => [
                    'required' => true,
                ],
                'position' => 5,
            ],
            'message' => [
                'label' => t('email_message'),
                'type' => 'Text',
                'settings' => [
                    'rows' => 12,
                    'required' => true,
                ],
                'position' => 6,
            ],
            'messageType' => [
                'label' => t('email_type'),
                'type' => 'List',
                'settings' => [
                    'items' => [
                        'text' => t('text_email'),
                        'html' => t('html_email'),
                    ],
                ],
                'position' => 7,
            ],
            'cc' => [
                'label' => t('email_cc'),
                'type' => 'Email',
                'settings' => [
                    'multiple' => true,
                ],
                'position' => 3,
            ],
            'bcc' => [
                'label' => t('email_bcc'),
                'type' => 'Email',
                'settings' => [
                    'multiple' => true,
                ],
                'position' => 3,
            ],
            'replyTo' => [
                'label' => t('email_reply_to'),
                'type' => 'Email',
                'position' => 3,
            ],
        ];

        parent::__construct($attrs);
    }
}
