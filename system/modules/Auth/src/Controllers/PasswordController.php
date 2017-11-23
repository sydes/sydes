<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Auth\Controllers;

use Sydes\Contracts\Http\Request;

class PasswordController
{
    private $storage;

    public function __construct()
    {
        $this->storage = app('dir.storage').'/password_restore.php';
    }

    public function showForm()
    {
        return view('auth/main', [
            'url' => '/password/email',
        ])->nest('form', 'auth/link');
    }

    public function sendMail(Request $req)
    {
        $email = $req->input('email');
        if (app('auth')->getUser('email') != $email) {
            return view('auth/main', [
                'url' => '',
                'form' => t('wrong_email'),
            ]);
        }

        if (file_exists($this->storage)) {
            $tokens = include $this->storage;
            if ((time() - current($tokens)) < 3600) {
                return view('auth/main', [
                    'url' => '',
                    'form' => t('already_requested'),
                ]);
            }
        }

        $token = bin2hex(openssl_random_pseudo_bytes(16));
        array2file([$token => time()], $this->storage);

        $uri = $req->getUri();
        $url = $uri->getScheme().'://'.$uri->getHost().'/password/reset/'.$token;

        // TODO заменить на менеджер почты
        app('mailer')->init([
            'useSmtp' => false,
            'defaultFrom' => 'admin@site.ru',
            'defaultTo' => 'me@human.tld',
            'sendAlso' => '',
        ])->send([
            'to' => $email,
            'subject' => t('password_restore'),
            'body' => 'Click here to reset your password: '.$url.'',
        ]);

        return view('auth/main', [
            'url' => '',
            'form' => t('email_was_sent'),
        ]);
    }

    public function showResetForm($token)
    {
        return view('auth/main', [
            'url' => '/password/reset',
        ])->nest('form', 'auth/pass', [
            'token' => $token,
        ]);
    }

    public function reset(Request $req)
    {
        if (!file_exists($this->storage)) {
            abort(403);
        }

        if ($req->input('password') != $req->input('password2')) {
            return back();
        }

        $tokens = include $this->storage;
        if (!isset($tokens[$req->input('token')])) {
            abort(403);
        }
        unlink($this->storage);

        $repo = model('Main/User');
        $user = $repo->get();
        $user->setPassword($req->input('password'));
        $repo->save($user);

        app('auth')->login();

        return redirect('/admin');
    }
}
