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
use Module\Mailer\Models\EmailTemplate;
use Sydes\AdminMenu;
use Sydes\Http\Request;

class Controller extends EntityController
{
    protected $basePath = '/admin/mailer';
    protected $titles = [
        'index' => 'mailer_templates',
        'create' => 'template_creation',
        'edit' => 'template_editing',
    ];

    public function __construct(Repository $repo)
    {
        $this->repo = $repo->forEntity(EmailTemplate::class);

        $this->indexHeaderActions = \H::a(t('mailer_events'), '/admin/mailer/events', ['button' => 'secondary']).' '.
            \H::a(t('add'), '/admin/mailer/create', ['button' => 'primary']);
    }

    public function install(AdminMenu $menu, Repository $repo)
    {
        $menu->addItem('modules/services/mailer', [
            'title' => 'module_mailer',
            'url' => '/admin/mailer',
        ], 10);

        $repo->forEntity(EmailTemplate::class)->makeTable();
        $repo->forEntity(EmailEvent::class)->makeTable();
    }

    public function uninstall(AdminMenu $menu, Repository $repo)
    {
        $menu->removeItem('modules/services/mailer');

        $repo->forEntity(EmailTemplate::class)->dropTable();
        $repo->forEntity(EmailEvent::class)->dropTable();
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
}
