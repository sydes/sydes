<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Profile;

use Sydes\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/profile', 'Profile@edit');
        $r->put('/admin/profile', 'Profile@update');
    }

    public function edit()
    {
        $d = document([
            'title' => t('module_profile'),
            'header_actions' => \H::submitButton(t('save'), ['button' => 'primary', 'data-submit' => 'form-main']),
            'content' => view('profile/form', ['autoLogin' => app('Auth')->getUser('autoLogin')]),
        ]);

        return $d;
    }

    public function update()
    {
        $r = app('request');

        if (!app('Auth')->isDev()) {
            abort(403, t('mastercode_needed'));
        }

        $user = [];
        $post = $r->only('newusername', 'newpassword', 'newmastercode', 'newemail');

        if ($post['newusername'] != '') {
            $user['username'] = $post['newusername'];
        }
        if ($post['newpassword'] != '') {
            $user['password'] = $post['newpassword'];
        }
        if ($post['newmastercode'] != '') {
            $user['mastercode'] = $post['newmastercode'];
        }
        if ($post['newemail'] != '') {
            $user['email'] = $post['newemail'];
        }
        $user['autoLogin'] = $r->input('autoLogin');

        model('Main/UserRepo')->update(app('Auth')->getUser('id'), $user);

        notify(t('saved'));

        return back();
    }
}
