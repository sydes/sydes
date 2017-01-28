<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Auth;

use App\Cmf;
use App\Event;

class Controller
{
    public static $routes = [
        ['GET',  '/auth/login', 'Auth@loginForm'],
        ['POST', '/auth/login', 'Auth@login'],
        ['POST', '/auth/logout', 'Auth@logout'],
        ['GET',  '/password/reset', 'Auth/Password@sendMail'],
        ['GET',  '/password/reset/{token:[a-f0-9]+}', 'Auth/Password@showResetForm'],
        ['POST', '/password/reset', 'Auth/Password@reset'],
    ];

    public function install()
    {
        Cmf::installModule('auth', [
            'handlers' => ['Module\Auth\Controller::handlers'],
        ]);
    }

    public function uninstall()
    {
        Cmf::uninstallModule('auth');
    }

    public function loginForm()
    {
        return view('auth/form', [
            'autoLogin' => app('editor')->autologin,
            'errors' => checkServer(),
            'title' => 'Log in to',
            'signUp' => false,
        ]);
    }

    public function login()
    {
        $r = app('request');

        if (!app('editor')->login($r->input('username'), $r->input('password'), $r->has('remember'))) {
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
        app('editor')->logout();
        return redirect();
    }

    public static function handlers(Event $events)
    {
        /**
         * Auth middleware :)
         */
        $events->on('route.found', 'admin/*', function () {
            if (!app('editor')->isLoggedIn()) {
                $_SESSION['entry'] = app('request')->getUri()->getPath();
                throw new \App\Exception\RedirectException('/auth/login');
            }
        });
    }
}
