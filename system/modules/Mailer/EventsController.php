<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Mailer;

use Module\Entity\Api\EntityController;
use Module\Mailer\Models\EmailEvent;
use Sydes\Database\Entity\Manager;

class EventsController extends EntityController
{
    protected $basePath = '/admin/mailer/events';
    protected $titles = [
        'index' => 'mailer_events',
        'create' => 'event_creation',
        'edit' => 'event_editing',
    ];

    public function __construct(Manager $em)
    {
        $this->repo = $em->getRepository(EmailEvent::class);

        $this->indexHeaderActions = \H::a(t('mailer_templates'), '/admin/mailer', ['button' => 'secondary']).' '.
            \H::a(t('add'), '/admin/mailer/events/create', ['button' => 'primary']);
    }

}
