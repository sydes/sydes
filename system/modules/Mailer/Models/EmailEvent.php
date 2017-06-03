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
    public function __construct(array $attrs = [])
    {
        $this->fields = [
            'code' => [
                'label' => t('event_code'),
                'type' => 'Text',
                'settings' => [
                    'required' => true,
                ],
                'position' => 1,
            ],
            'name' => [
                'label' => t('event_name'),
                'type' => 'Text',
                'settings' => [
                    'required' => true,
                ],
                'position' => 2,
            ],
            'fields' => [
                'label' => t('event_fields'),
                'type' => 'Text',
                'settings' => [
                    'rows' => 12,
                    'required' => true,
                ],
                'position' => 3,
            ],
        ];

        parent::__construct($attrs);
    }
}
