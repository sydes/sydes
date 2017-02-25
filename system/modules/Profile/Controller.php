<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Profile;

use App\Cmf;

class Controller
{
    public static $routes = [
        ['GET',  '/admin/profile', 'Profile@edit'],
        ['POST', '/admin/profile', 'Profile@update'],
    ];

    public function install()
    {
        Cmf::installModule('profile', [
            'handlers' => ['Module\Profile\Handlers::init'],
        ]);
    }

    public function uninstall()
    {
        Cmf::uninstallModule('profile');
    }

    public function edit()
    {
        $d = document([
            'content'       => view('profile/form', ['autologin' => app('user')->autologin]),
            'sidebar_left'  => '',
            'sidebar_right' => \H::saveButton(DIR_APP.'/config.php').\H::mastercodeInput(),
            'form_url'      => '/admin/profile',
            'meta_title'    => t('module_profile'),
            'breadcrumbs'   => [
                ['title' => t('module_profile')],
            ],
        ]);
        return $d;
    }

    public function update()
    {
        restricted();

        $config = app('config');
        unset($config['site']);
        $post = app('request')->request;

        if ($post['newusername'] != ''){
            $config['user']['username'] = $post['newusername'];
        }
        if ($post['newpassword'] != ''){
            $config['user']['password'] = md5($post['newpassword']);
        }
        if ($post['newmastercode'] != ''){
            $config['user']['mastercode'] = md5($post['newmastercode']);
        }
        $config['user']['autologin'] = app('request')->has('autologin');

        array2file($config, DIR_APP.'/config.php');

        notify(t('saved'));
        return back();
    }
}
