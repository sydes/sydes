<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class User
{
    private $isLoggedIn;
    public $email;
    public $username;
    public $password;
    public $mastercode;
    public $autoLogin;

    public function __construct($user)
    {
        foreach ($user as $k => $v) {
            $this->$k = $v;
        }
    }

    public function login($username, $pass, $remember = null)
    {
        if ($username != $this->username || !password_verify($pass, $this->password)) {
            return false;
        }

        $_SESSION['hash'] = md5($username.$this->password.app('request')->getIp());
        if (!empty($remember) && $this->autoLogin == 1) {
            setcookie('hash', $_SESSION['hash'], time() + 604800);
        }
        setcookie('entered', '1', time() + 604800, '/');
        return true;
    }

    public function logout()
    {
        session_destroy();
        setcookie('hash', '');
    }

    public function checkLogin()
    {
        // already logged in
        if (isset($_SESSION['hash'])) {
            if ($_SESSION['hash'] == md5($this->username.$this->password.app('request')->getIp())) {
                return true;
            } else { //hacker or credentials was changed
                $this->logout();
            }
            // login by cookies
        } elseif ($this->autoLogin == 1 && isset($_COOKIE['hash'])) {
            $hash = md5($this->username.$this->password.app('request')->getIp());
            if ($_COOKIE['hash'] == $hash) {
                $_SESSION['hash'] = $hash;
                return true;
            } else {
                $this->logout();
            }
        }
        return false;
    }

    public function isLoggedIn()
    {
        if (is_null($this->isLoggedIn)) {
            $this->isLoggedIn = $this->checkLogin();
        }
        return $this->isLoggedIn;
    }

    public function isAdmin()
    {
        $r = app('request');
        if ($r->has('mastercode') && password_verify($r->get('mastercode'), $this->mastercode)) {
            $_SESSION['admin'] = 1;
        }
        return isset($_SESSION['admin']);
    }
}
