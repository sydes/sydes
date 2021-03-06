<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Sites\Controllers;

use Sydes\AdminMenu;
use Sydes\Contracts\Http\Request;

class IndexController
{
    public function install(AdminMenu $menu)
    {
        $menu->addItem('system/settings/sites', [
            'title' => 'module_sites',
            'url' => '/admin/sites',
        ], 10);
    }

    public function index()
    {
        $sites = model('Sites')->getAll();

        $d = document([
            'title' => t('module_sites'),
            'header_actions' => \H::a(t('add_site'), '/admin/sites/create', ['button' => 'primary']),
            'content' => view('sites/list', ['sites' => $sites]),
        ]);

        return $d;
    }

    public function create(Request $r)
    {
        $themes = [];
        foreach (model('Themes')->getAll() as $theme => $data) {
            $themes[$theme] = $data['name'];
        }

        $d = document([
            'title' => t('site_creation'),
            'header_actions' => \H::submitButton(t('save'), ['button' => 'primary', 'data-submit' => 'form-main']),
            'content' => view('sites/form', [
                'site' => [
                    'domains' => [$r->getUri()->getHost()],
                    'onlyMainDomain' => 1,
                    'work' => 1,
                ],
                'themes' => $themes,
                'options' => [
                    'method' => 'post',
                    'url' => '/admin/sites',
                    'form' => 'main',
                ],
            ]),
        ]);

        $d->addJs('site.js', 'sites:js/sites.js');

        return $d;
    }

    public function store(Request $r)
    {
        $data = $r->only('name', 'theme', 'domains',
            'onlyMainDomain', 'locales', 'localeIn', 'host2locale', 'work');

        $data['domains'] = explode("\r\n", $data['domains']);
        $data['locales'] = explode("\r\n", $data['locales']);

        if (empty($data['host2locale'])) {
            unset($data['host2locale']);
        }

        model('Sites')->create($data);

        notify(t('saved'));

        return redirect('/admin/sites');
    }

    public function edit($id)
    {
        $themes = [];
        foreach (model('Themes')->getAll() as $theme => $data) {
            $themes[$theme] = $data['name'];
        }

        $d = document([
            'title' => t('site_editing'),
            'header_actions' => \H::submitButton(t('save'), ['button' => 'primary', 'data-submit' => 'form-main']),
            'content' => view('sites/form', [
                'site' => model('Sites')->get($id)->get(),
                'themes' => $themes,
                'options' => [
                    'method' => 'put',
                    'url' => '/admin/sites/'.$id,
                    'form' => 'main',
                ],
            ]),
        ]);

        $d->addJs('site.js', 'sites:js/sites.js');

        return $d;
    }

    public function update(Request $r, $id)
    {
        $data = $r->only('name', 'theme', 'domains',
            'onlyMainDomain', 'locales', 'localeIn', 'host2locale', 'work');

        $data['domains'] = explode("\r\n", $data['domains']);
        $data['locales'] = explode("\r\n", $data['locales']);

        if (empty($data['host2locale'])) {
            unset($data['host2locale']);
        }

        model('Sites')->save($id, $data);

        notify(t('saved'));

        return redirect('/admin/sites');
    }

    public function destroy($id)
    {
        model('Sites')->delete($id);
        notify(t('deleted'));

        return back();
    }

    public function go($id)
    {
        $_SESSION['site'] = $id;
        notify(t('site_selected', ['id' => $id]));

        return redirect('/admin');
    }
}
