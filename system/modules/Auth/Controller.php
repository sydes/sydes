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
        $r->get('/password/reset', 'Auth/Password@sendMail');
        $r->get('/password/reset/{token:[a-f0-9]+}', 'Auth/Password@showResetForm');
        $r->post('/password/reset', 'Auth/Password@reset');
    }

    public function loginForm()
    {
        return view('auth/form', [
            'autoLogin' => app('user')->autologin,
            'errors' => checkServer(),
            'title' => 'Log in to',
            'signUp' => false,
        ]);
    }

    public function login()
    {
        $r = app('request');

        if (!app('user')->login($r->input('username'), $r->input('password'), $r->has('remember'))) {
            app('logger')->info("{name} is not logged on. {pass} - wrong password", [
                'name' => $r->input('username'),
                'pass' => $r->input('password'),
            ]);
            return back();
        }

        app('logger')->info('{name} is logged on with a password', [
            'name' => $r->input('username'),
        ]);

        $entry = ifsetor($_SESSION['entry'], '/admin');
        unset($_SESSION['entry']);
        return redirect($entry);
    }

    public function logout()
    {
        app('user')->logout();
        return redirect();
    }
}
