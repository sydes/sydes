<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Auth;

use Sydes\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/auth/login', 'Auth@loginForm');
        $r->post('/auth/login', 'Auth@login');
        $r->post('/auth/logout', 'Auth@logout');
        $r->get('/password/reset', 'Auth/Password@showForm');
        $r->post('/password/email', 'Auth/Password@sendMail');
        $r->get('/password/reset/{token}', 'Auth/Password@showResetForm');
        $r->post('/password/reset', 'Auth/Password@reset');
    }

    public function loginForm()
    {
        return view('auth/main', [
            'url' => '/auth/login',
        ])->nest('form', 'auth/login', [
            'autoLogin' => app('auth')->getUser('autoLogin'),
        ]);
    }

    public function login()
    {
        $r = app('request');

        if (!app('auth')->attempt($r->input('username'), $r->input('password'), $r->has('remember'))) {
            return back();
        }

        $entry = ifsetor($_SESSION['entry'], '/admin');
        unset($_SESSION['entry']);

        return redirect($entry);
    }

    public function logout()
    {
        app('auth')->logout();

        return redirect();
    }
}
