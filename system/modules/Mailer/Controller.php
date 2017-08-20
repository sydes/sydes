<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Mailer;

use Module\Entity\Api\EntityController;
use Module\Mailer\Models\EmailEvent;
use Module\Mailer\Models\EmailTemplate;
use Sydes\AdminMenu;
use Sydes\Database\Entity\Manager;
use Sydes\Http\Request;

class Controller extends EntityController
{
    protected $basePath = '/admin/mailer';
    protected $titles = [
        'index' => 'mailer_events',
        'create' => 'event_creation',
        'edit' => 'event_editing',
    ];
    private $em;

    public function __construct(Manager $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(EmailEvent::class);

        $this->indexHeaderActions = \H::a(t('mailer_templates'), '/admin/mailer/templates', ['button' => 'secondary']).' '.
            \H::a(t('add'), '/admin/mailer/create', ['button' => 'primary']);
    }

    public function install(AdminMenu $menu, Request $req)
    {
        $menu->addItem('modules/services/mailer', [
            'title' => 'module_mailer',
            'url' => '/admin/mailer',
        ], 10);

        settings('mailer')->set([
            'default_from' => 'robot@'.$req->getUri()->getHost(),
            'default_to' => app('auth')->getUser('email'),
            'use_smtp' => 0,
            'smtp_host' => '',
            'smtp_port' => '',
            'smtp_user' => '',
            'smtp_password' => '',
            'send_also' => '',
        ])->save();

        $this->em->getSchemaTool(EmailTemplate::class)->create();
        $this->em->getSchemaTool(EmailEvent::class)->create();
    }

    public function uninstall(AdminMenu $menu)
    {
        $menu->removeItem('modules/services/mailer');

        $this->em->getSchemaTool(EmailTemplate::class)->drop();
        $this->em->getSchemaTool(EmailEvent::class)->drop();
    }

    public function settings()
    {
        $d = document([
            'title' => t('mailer_settings'),
            'header_actions' => \H::submitButton(t('save'), ['button' => 'primary', 'data-submit' => 'form-main']),
            'content' => view('mailer/settings', [
                'data' => settings('mailer')->get(),
                'options' => [
                    'method' => 'put',
                    'url' => '/admin/mailer/settings',
                    'form' => 'main',
                ],
            ]),
        ]);

        $d->addJs('mailer.js', 'mailer:js/mailer.js');

        return $d;
    }

    public function updateSettings(Request $req)
    {
        settings('mailer')->set($req->only(['default_from',
            'default_to', 'use_smtp', 'smtp_host', 'smtp_port',
            'smtp_user', 'smtp_password', 'send_also']))->save();

        return back();
    }

    public function autoComplete($target, $title, Request $req, Manager $em)
    {
        if ($target != 'events') {
            abort(404, t('page_not_found'));
        }

        return $em->getRepository(EmailEvent::class)->suggest($title, $req->input('term'));
    }
}
