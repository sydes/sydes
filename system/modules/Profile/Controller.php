<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Profile;

class Controller
{
    public function edit()
    {
        $d = document([
            'title' => t('module_profile'),
            'content' => view('profile/form', ['data' => app('auth')->getUser()->toArray()]),
        ]);

        return $d;
    }

    public function update()
    {
        $r = app('request');
        $user = app('auth')->getUser();
        $user->set('username', $r->input('username'))
            ->set('email', $r->input('email'))
            ->set('autoLogin', $r->input('autoLogin'));

        model('Main/User')->save($user);

        app('auth')->login();
        notify(t('saved'));

        return back();
    }

    public function updatePassword()
    {
        $r = app('request');
        if ($r->input('password') != $r->input('password2')) {
            alert(t('password_mismatch'), 'warning');

            return back();
        }

        if (!app('auth')->getUser()->checkPassword($r->input('current_password'))) {
            alert(t('wrong_current_password'), 'warning');

            return back();
        }

        $user = app('auth')->getUser();
        $user->setPassword($r->input('password'));
        model('Main/User')->save($user);

        app('auth')->login();
        notify(t('saved'));

        return back();
    }
}
