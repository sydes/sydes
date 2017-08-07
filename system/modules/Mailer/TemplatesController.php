<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Mailer;

use Module\Entity\Api\EntityController;
use Module\Mailer\Models\EmailTemplate;
use Sydes\Database\Entity\Manager;

class TemplatesController extends EntityController
{
    protected $basePath = '/admin/mailer/templates';
    protected $titles = [
        'index' => 'mailer_templates',
        'create' => 'template_creation',
        'edit' => 'template_editing',
    ];

    public function __construct(Manager $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(EmailTemplate::class);

        $this->indexHeaderActions = \H::a(t('mailer_events'), '/admin/mailer', ['button' => 'secondary']).' '.
            \H::a(t('add'), '/admin/mailer/templates/create', ['button' => 'primary']);
    }
}
