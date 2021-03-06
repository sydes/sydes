<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Auth\Controllers;

use Sydes\Contracts\Http\Request;

class IndexController
{
    public function loginForm()
    {
        return view('auth/main', [
            'url' => '/auth/login',
        ])->nest('form', 'auth/login', [
            'autoLogin' => app('auth')->getUser('autoLogin'),
        ]);
    }

    public function login(Request $r)
    {
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
