<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Mailer;

use Module\Entity\Models\EntityController;
use Module\Entity\Models\Repository;
use Module\Mailer\Models\EmailEvent;

class EventsController extends EntityController
{
    protected $basePath = '/admin/mailer/events';
    protected $titles = [
        'index' => 'mailer_events',
        'create' => 'event_creation',
        'edit' => 'event_editing',
    ];

    public function __construct(Repository $repo)
    {
        $this->repo = $repo->forEntity(EmailEvent::class);

        $this->indexHeaderActions = \H::a(t('mailer_templates'), '/admin/mailer', ['button' => 'secondary']).' '.
            \H::a(t('add'), '/admin/mailer/events/create', ['button' => 'primary']);
    }

}
