<?php

class UserController
{
    public function loginForm()
    {
        return view('user/login-signup', [
            'autoLogin' => app('user')->autologin,
            'errors' => checkServer(),
            'title' => 'Log in to',
            'signUp' => false,
        ]);
    }

    public function login()
    {
        $r = app('request');
        if (!app('user')->login($r->get('username'), $r->get('password'), $r->has('remember'))) {
            return back();
        }
        $entry = $_SESSION['entry'];
        unset($_SESSION['entry']);
        return redirect($entry);
    }

    public function logout(){
        app('user')->logout();
        return redirect();
    }

    public function edit()
    {
        $d = document([
            'content'       => view('user/form', ['autologin' => app('user')->autologin]),
            'sidebar_left'  => '',
            'sidebar_right' => HTML::saveButton(DIR_APP.'/config.php').HTML::mastercodeInput(),
            'form_url'      => 'admin/user/update',
            'meta_title'    => t('module_profile'),
            'breadcrumbs'   => [
                ['url' => 'admin/config', 'title' => t('settings')],
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

        arr2file($config, DIR_APP.'/config.php');

        notify(t('saved'));
        return back();
    }

}
