<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

class Auth
{
    protected $user;
    protected $logged;

    public function __construct()
    {
        $this->user = model('Main/UserRepo')->get();
    }

    public function attempt($username, $pass, $remember = false)
    {
        if ($username != $this->user['username'] || !password_verify($pass, $this->user['password'])) {
            return false;
        }

        return $this->login($remember);
    }

    public function login($remember = false)
    {
        $_SESSION['hash'] = $this->hash();
        setcookie('entered', '1', time() + 604800, '/');

        if ($remember && $this->user['autoLogin'] == 1) {
            setcookie('hash', $_SESSION['hash'], time() + 604800);
        }

        return true;
    }

    public function logout()
    {
        session_destroy();
        setcookie('hash', '', 1, '/');
        setcookie('entered', '', 1, '/');
    }

    public function checkLogin()
    {
        $hash = $this->hash();

        if (isset($_SESSION['hash'])) { // already logged in
            if ($_SESSION['hash'] == $hash) {
                return true;
            } else {
                $this->logout();
            }
        } elseif ($this->user['autoLogin'] == 1 && isset($_COOKIE['hash'])) { // login by cookies
            if ($_COOKIE['hash'] == $hash) {
                return $this->login(true);
            } else {
                $this->logout();
            }
        }

        return false;
    }

    public function check()
    {
        if (is_null($this->logged)) {
            $this->logged = $this->checkLogin();
        }

        return $this->logged;
    }

    public function isDev()
    {
        $r = app('request');
        if ($r->has('mastercode') && password_verify($r->input('mastercode'), $this->user['mastercode'])) {
            $_SESSION['dev'] = 1;
        }

        return isset($_SESSION['dev']);
    }

    public function getUser($key = null)
    {
        return $key == null ? $this->user : $this->user[$key];
    }

    protected function hash()
    {
        return md5($this->user['username'].$this->user['password'].app('request')->getIp());
    }
}
